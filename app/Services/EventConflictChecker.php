<?php

namespace App\Services;

use App\Models\EventRequest;
use Illuminate\Database\Eloquent\Collection;
use DateTimeInterface;

class EventConflictChecker
{
    public function find(string $venue, ?string $campus, string|DateTimeInterface $start, string|DateTimeInterface $end, ?int $excludeId = null): Collection
    {
        return EventRequest::query()
            ->whereIn('status', ['approved', 'pending', 'conflict'])
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
}