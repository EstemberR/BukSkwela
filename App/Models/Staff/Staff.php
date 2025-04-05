<?php

namespace App\Models\Staff;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Course\Course;
use App\Models\Student\Student;
use App\Models\Department;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'status',
        'tenant_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Add relationships
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}