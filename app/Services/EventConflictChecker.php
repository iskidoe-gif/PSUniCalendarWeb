<?php

namespace App\Services;

use App\Models\EventRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use DateTimeInterface;

class EventConflictChecker
{
    /** Statuses that hold a venue (block other requests from the same slot). */
    public const BLOCKING_STATUSES = ['approved', 'pending', 'conflict'];

    public function find(string $venue, ?string $campus, string|DateTimeInterface $start, string|DateTimeInterface $end, ?int $excludeId = null): Collection
    {
        return EventRequest::query()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->where('venue_name', $venue)
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->get()
            ->filter(function (EventRequest $event) use ($campus) {
                return !$campus
                    || !$event->campus
                    || $campus === 'All Campus'
                    || $event->campus === 'All Campus'
                    || $event->campus === $campus;
            })
            ->values();
    }

    /**
     * Earliest free slot at the venue with the same duration as the requested one,
     * starting at or after the requested start. Returns [start, end] or null if
     * nothing is free within $searchDays.
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public function nextAvailableSlot(string $venue, ?string $campus, DateTimeInterface $start, DateTimeInterface $end, ?int $excludeId = null, int $searchDays = 14): ?array
    {
        $start = Carbon::instance($start);
        $end = Carbon::instance($end);
        $duration = $end->getTimestamp() - $start->getTimestamp();
        $searchUntil = $start->copy()->addDays($searchDays);

        $busy = $this->find($venue, $campus, $start, $searchUntil, $excludeId)
            ->sortBy('start_datetime')
            ->values();

        // Keep pushing the candidate past any booking it overlaps until it fits
        $candidate = $start->copy();
        do {
            $moved = false;
            $candidateEnd = $candidate->copy()->addSeconds($duration);

            foreach ($busy as $event) {
                if ($event->start_datetime < $candidateEnd && $event->end_datetime > $candidate) {
                    $candidate = $event->end_datetime->copy();
                    $candidateEnd = $candidate->copy()->addSeconds($duration);
                    $moved = true;
                }
            }
        } while ($moved);

        if ($candidate->gte($searchUntil)) {
            return null;
        }

        return [$candidate, $candidate->copy()->addSeconds($duration)];
    }
}
