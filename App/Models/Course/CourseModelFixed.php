<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Traits\HasTenantConnection;

class CourseModelFixed extends Model
{
    use HasFactory, HasTenantConnection;

    protected $connection = 'tenant';
    
    protected $table = 'courses';

    protected $fillable = [
        'title',   // Note: Added title column to match the query requirements
        'name',
        'code',
        'description',
        'status',
        'staff_id',
        'tenant_id'
    ];

    protected $casts = [
        'status' => 'string',
        'staff_id' => 'integer'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    // Example of a properly fixed relationship
    public function students()
    {
        // No need for ->on() method because the HasTenantConnection trait 
        // overrides hasMany to handle the connection properly
        return $this->hasMany(Student::class);
    }

    // Example of another fixed relationship
    public function staff()
    {
        // No need for ->on() method
        return $this->belongsTo(Staff::class);
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