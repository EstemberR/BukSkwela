<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student\Student;

class StudentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'requirement_id',
        'file_path',
        'status',
        'remarks',
        'tenant_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }
} 