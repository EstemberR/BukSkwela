<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'courses';

    protected $fillable = [
        'name',
        'code',
        'description',
        'instructor_id',
        'credits',
        'status'
    ];

    /**
     * Get the instructor for the course.
     */
    public function instructor()
    {
        return $this->belongsTo(Staff::class, 'instructor_id');
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id')
            ->withPivot('enrollment_date', 'status')
            ->withTimestamps();
    }

    /**
     * Get the sessions for the course.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
} 