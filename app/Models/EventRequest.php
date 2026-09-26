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
        'sdg_number',
        'description',
        'start_datetime',
        'end_datetime',
        'status',
        'planning_note',
        'google_event_id',
        'digital_documents',
    ];

    protected $casts = [
        'digital_documents' => 'array',
        'sdg_number' => 'integer',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}
