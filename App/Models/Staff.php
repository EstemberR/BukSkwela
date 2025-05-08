<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'tenant';
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'department_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the courses associated with the staff member.
     */
    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class, 'instructor_id');
    }

    /**
     * Get the department associated with the staff.
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    /**
     * Get the sessions associated with the staff member.
     */
    public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class, 'instructor_id');
    }
} 