<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student\Student;

class StudentRequirement extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    
    protected $table = 'student_requirements';

    protected $fillable = [
        'student_id',
        'requirement_id',
        'file_path',
        'status',
        'remarks',
        'tenant_id'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->on($this->getConnectionName());
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class)->on($this->getConnectionName());
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