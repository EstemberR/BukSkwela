<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'department_id'
    ];

    /**
     * Get the students for the course.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
} 