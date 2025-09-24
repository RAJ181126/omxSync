<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
     protected $fillable = [
        'user_id',
        'task_performance_total',
        'attendance_points',
        'final_score',
        'grade',
        'calculated_at',
    ];
}
