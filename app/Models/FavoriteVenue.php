<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteVenue extends Model
{
    protected $fillable = [
        'email',
        'venue_name',
        'campus',
    ];
}
