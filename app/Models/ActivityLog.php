<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'email',
        'action',
        'description',
    ];

    /**
     * Convenience helper so controllers can log an action in one line:
     * ActivityLog::record($email, 'submitted', 'Submitted "Foo" for Room 101');
     */
    public static function record(string $email, string $action, string $description): self
    {
        return static::create([
            'email' => $email,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
