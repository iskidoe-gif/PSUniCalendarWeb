<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds everything the Planning Office dashboard shows, from real event requests.
 *
 * Filters:
 *  - campus: one of User::MAIN_CAMPUSES, or null for all campuses. A campus filter keeps
 *    that campus's events plus university-wide events (they concern every campus).
 *  - from / to: events whose schedule overlaps the range.
 *
 * University-wide events are counted once, in their own bucket, never once per campus.
 */
class PlanningOfficeDashboard
{
    public const UNIVERSITY_WIDE_LABEL = 'University-wide events';

    /** Event-related activity shown in "Recent activity" (not profile/password changes). */
    private const ACTIVITY_ACTIONS = ['submitted', 'edited', 'cancelled', 'approved', 'rejected', 'returned', 'rescheduled', 'conflict_resolved'];

    private const SDG_NAMES = [
        1 => 'No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality',
        'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth',
        'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities',
        'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land',
        'Peace, Justice and Strong Institutions', 'Partnerships for the Goals',
    ];

    public function __construct(private EventConflictChecker $conflictChecker)
    {
    }

    public function build(?string $campus, ?Carbon $from, ?Carbon $to): array
    {
        $now = now();
        $from = $from?->copy()->startOfDay();
        $to = $to?->copy()->endOfDay();

        $inCampus = fn (EventRequest $e) => ! $campus
            || $e->scope() === EventRequest::SCOPE_UNIVERSITY_WIDE
            || $e->venueCampus() === $campus;
        $inRange = fn (EventRequest $e) => (! $from || $e->end_datetime >= $from)
            && (! $to || $e->start_datetime <= $to);

        $campusEvents = EventRequest::orderBy('start_datetime')->get()->filter($inCampus)->values();
        $filtered = $campusEvents->filter($inRange)->values();
        $approved = $filtered->where('status', 'approved')->values();

        // ---- Review queue + conflicts ----
        $queue = $filtered->whereIn('status', EventRequest::REVIEW_STATUSES)
            ->sortBy([
                fn ($a, $b) => ($a->status === 'conflict' ? 0 : 1) <=> ($b->status === 'conflict' ? 0 : 1),
                fn ($a, $b) => $a->created_at <=> $b->created_at,
            ])
            ->values()
            ->each(function (EventRequest $e) {
                $e->setAttribute('conflicts', $this->conflictChecker->find(
                    $e->venue_name, $e->campus, $e->start_datetime, $e->end_datetime, $e->id
                ));
            });

        $conflicts = $queue->filter(fn ($e) => $e->status === 'conflict' || $e->conflicts->isNotEmpty())
            ->map(fn (EventRequest $e) => [
                'request' => $e,
                'with' => $e->conflicts,
                'type' => match (true) {
                    $e->conflicts->contains('status', 'approved') => 'Overlaps an approved event',
                    $e->conflicts->isNotEmpty() => 'Competing requests for the venue',
                    default => 'Flagged at submission (no longer overlapping)',
                },
            ])
            ->values();

        return [
            'summary' => [
                'total' => $approved->count(),
                'upcoming' => $approved->filter(fn ($e) => $e->start_datetime > $now)->count(),
                'ongoing' => $approved->filter(fn ($e) => $e->start_datetime <= $now && $e->end_datetime > $now)->count(),
                'completed' => $approved->filter(fn ($e) => $e->end_datetime <= $now)->count(),
                'pending' => $queue->count(),
                'conflicts' => $conflicts->count(),
            ],
            'queue' => $queue,
            'conflicts' => $conflicts,
            'upcoming' => $approved->filter(fn ($e) => $e->start_datetime > $now)->take(7)->values(),
            // The calendar is navigated by month, so it ignores the date range (campus filter still applies)
            'calendarEvents' => $this->calendarEvents($campusEvents),
            'charts' => $this->charts($approved, $conflicts, $from, $to),
            'resolvedConflicts' => ActivityLog::where('action', 'conflict_resolved')
                ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
                ->count(),
            'activity' => ActivityLog::whereIn('action', self::ACTIVITY_ACTIONS)->latest()->limit(10)->get(),
            'accountNames' => User::whereIn('role', ['office', 'planning_office'])->pluck('name', 'email'),
        ];
    }

    public static function categoryLabel(?int $sdg): string
    {
        return $sdg && isset(self::SDG_NAMES[$sdg]) ? "SDG {$sdg}: " . self::SDG_NAMES[$sdg] : 'Not aligned to an SDG';
    }

    public static function scopeLabel(string $scope): string
    {
        return $scope === EventRequest::SCOPE_UNIVERSITY_WIDE ? 'University-wide' : 'Campus-specific';
    }

    /** Campus column value: "All campuses" for university-wide events held for "All Campus". */
    public static function campusLabel(EventRequest $e): string
    {
        return $e->venueCampus() === 'All Campus' ? 'All campuses' : $e->venueCampus();
    }

    /**
     * Approved events only, coloured by scope. Submissions still under review (pending /
     * conflict) never go on the calendar; they're in Pending Approvals and Scheduling Conflicts.
     */
    private function calendarEvents(Collection $events): Collection
    {
        return $events->where('status', 'approved')
            ->map(function (EventRequest $e) {
                $universityWide = $e->scope() === EventRequest::SCOPE_UNIVERSITY_WIDE;
                [$kind, $color] = $universityWide ? ['university', '#047857'] : ['campus', '#4a5690'];

                return [
                    'title' => $e->title,
                    'start' => $e->start_datetime,
                    'end' => $e->end_datetime,
                    'description' => $e->description,
                    'venue' => $e->venue_name,
                    'campus' => $e->venueCampus(),
                    'university_wide' => $universityWide,
                    'sdg_number' => $e->sdg_number,
                    'office' => $e->name,
                    'status_label' => EventRequest::statusLabel($e->status),
                    'color' => $color,
                    'class_names' => ['po-ev--' . $kind],
                ];
            })
            ->values();
    }

    private function charts(Collection $approved, Collection $conflicts, ?Carbon $from, ?Carbon $to): array
    {
        $bucketOf = fn (EventRequest $e) => match (true) {
            $e->scope() === EventRequest::SCOPE_UNIVERSITY_WIDE => self::UNIVERSITY_WIDE_LABEL,
            in_array($e->venueCampus(), User::MAIN_CAMPUSES, true) => $e->venueCampus(),
            default => 'Other campuses',
        };
        $buckets = [...User::MAIN_CAMPUSES, self::UNIVERSITY_WIDE_LABEL];
        if ($approved->contains(fn ($e) => $bucketOf($e) === 'Other campuses')) {
            $buckets[] = 'Other campuses';
        }

        // Events by campus (each event in exactly one bucket)
        $byCampus = collect($buckets)->mapWithKeys(fn ($b) => [$b => $approved->filter(fn ($e) => $bucketOf($e) === $b)->count()]);

        // Events by category (SDG)
        $byCategory = $approved->groupBy(fn ($e) => $e->sdg_number ? 'SDG ' . $e->sdg_number : 'Not aligned')
            ->map->count()
            ->sortDesc();

        // Monthly trends: the selected range, or the 12 months around today
        $start = ($from ?? now()->subMonths(6))->copy()->startOfMonth();
        $end = ($to ?? now()->addMonths(5))->copy()->startOfMonth();
        if ($start->diffInMonths($end) > 23) {
            $start = $end->copy()->subMonths(23);
        }
        $months = [];
        for ($m = $start->copy(); $m <= $end; $m->addMonth()) {
            $months[$m->format('Y-m')] = $m->format('M Y');
        }
        $trendSeries = collect($buckets)->mapWithKeys(fn ($b) => [
            $b => collect(array_keys($months))->map(fn ($key) => $approved
                ->filter(fn ($e) => $bucketOf($e) === $b && $e->start_datetime->format('Y-m') === $key)
                ->count())->all(),
        ]);

        // Open conflicts by campus
        $conflictBucket = fn (EventRequest $e) => match (true) {
            $e->venueCampus() === 'All Campus' => 'All campuses',
            in_array($e->venueCampus(), User::MAIN_CAMPUSES, true) => $e->venueCampus(),
            default => 'Other campuses',
        };
        $conflictsByCampus = $conflicts->countBy(fn ($row) => $conflictBucket($row['request']));
        $conflictsByCampus = collect([...User::MAIN_CAMPUSES, 'All campuses', 'Other campuses'])
            ->filter(fn ($c) => in_array($c, User::MAIN_CAMPUSES, true) || $conflictsByCampus->has($c))
            ->mapWithKeys(fn ($c) => [$c => $conflictsByCampus->get($c, 0)]);

        return [
            'byCampus' => ['labels' => $byCampus->keys()->all(), 'values' => $byCampus->values()->all()],
            'byCategory' => ['labels' => $byCategory->keys()->all(), 'values' => $byCategory->values()->all()],
            'trends' => ['labels' => array_values($months), 'series' => $trendSeries->all()],
            'conflicts' => ['labels' => $conflictsByCampus->keys()->all(), 'values' => $conflictsByCampus->values()->all()],
        ];
    }
}
