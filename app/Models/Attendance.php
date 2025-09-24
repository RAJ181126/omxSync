<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'punctual',
        'minutes_late',
    ];

    protected $casts = [
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];
}
