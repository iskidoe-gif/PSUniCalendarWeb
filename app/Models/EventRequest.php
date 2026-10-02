<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'title',
        'venue_name',
        'campus',
        'is_university_wide',
        'sdg_number',
        'description',
        'start_datetime',
        'end_datetime',
        'status',
        'planning_note',
        'google_event_id',
        'digital_documents',
        'read_at',
    ];

    protected $casts = [
        'digital_documents' => 'array',
        'sdg_number' => 'integer',
        'is_university_wide' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * Statuses that mean "this office can still edit or cancel this request".
     */
    public const EDITABLE_STATUSES = ['pending', 'conflict'];

    /**
     * Statuses that represent a Planning Office decision the office hasn't seen yet.
     */
    public const NOTIFIABLE_STATUSES = ['approved', 'rejected', 'conflict'];

    /**
     * Campus where the event is held (guessed from the venue name for older requests).
     */
    public function venueCampus(): string
    {
        return $this->campus ?: (User::campusFromText($this->venue_name) ?? 'All Campus');
    }

    /**
     * Should this event appear on the given office's calendar?
     * Yes if it's the office's own request, if it's for all campuses, or if the
     * office can see events at the venue's campus.
     */
    public function isVisibleTo(User $user): bool
    {
        return $this->email === $user->email
            || $this->is_university_wide
            || $user->canSeeCampusEvent($this->venueCampus());
    }
}
