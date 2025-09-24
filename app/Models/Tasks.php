<?php

namespace App\Models;

use App\Enums\TaskStatus;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'status'       => TaskStatus::class,
        'due_date'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
