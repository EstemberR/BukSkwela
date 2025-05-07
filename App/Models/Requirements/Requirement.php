<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student\Student;

class Requirement extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    
    protected $table = 'requirements';

    protected $fillable = [
        'name',
        'description',
        'student_category',
        'file_type',
        'is_required',
        'tenant_id'
    ];

    protected $casts = [
        'is_required' => 'boolean'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_requirements')
            ->on($this->getConnectionName())
            ->withPivot(['file_path', 'status', 'remarks'])
            ->withTimestamps();
    }

    public function studentRequirements()
    {
        return $this->hasMany(StudentRequirement::class)->on($this->getConnectionName());
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