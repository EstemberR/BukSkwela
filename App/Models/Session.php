<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'sessions';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'title',
        'description',
        'scheduled_date',
        'start_time',
        'end_time',
        'location',
        'status'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the course associated with the session.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor associated with the session.
     */
    public function instructor()
    {
        return $this->belongsTo(Staff::class, 'instructor_id');
    }
} 