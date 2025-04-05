<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use App\Models\Requirements\StudentRequirement;
use App\Models\Requirements\Requirement;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'password',
        'course_id',
        'status',
        'tenant_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function staff()
    {
        return $this->hasOneThrough(Staff::class, Course::class, 'id', 'id', 'course_id', 'staff_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class, 'student_requirements')
            ->withPivot(['file_path', 'status', 'remarks'])
            ->withTimestamps();
    }

    public function studentRequirements()
    {
        return $this->hasMany(StudentRequirement::class);
    }
}
