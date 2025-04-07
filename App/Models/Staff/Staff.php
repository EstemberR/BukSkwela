<?php

namespace App\Models\Staff;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Course\Course;
use App\Models\Student\Student;
use App\Models\Department;
use App\Traits\HasTenantConnection;

class Staff extends Authenticatable
{
    use Notifiable, HasTenantConnection;

    protected $connection = 'tenant';
    
    protected $table = 'staff';

    protected $fillable = [
        'id',
        'staff_id',
        'name',
        'email',
        'role',
        'department_id',
        'status',
        'password',
        'tenant_id',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

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
    
    // Boot method to ensure tenant_id is set
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (tenant()) {
                $model->tenant_id = tenant('id');
            }
        });
    }
}