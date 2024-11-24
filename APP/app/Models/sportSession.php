<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sportSession extends Model
{
    protected $table = 'sport_sessions';

    protected $fillable = [
        'user_id',
        'date',
        'duration',
        'details',
    ];

    public $timestamps = true;
}
