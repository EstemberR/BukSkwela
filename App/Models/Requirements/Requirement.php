<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student\Student;

class Requirement extends Model
{
    use HasFactory;

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

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_requirements')
            ->withPivot(['file_path', 'status', 'remarks'])
            ->withTimestamps();
    }

    public function studentRequirements()
    {
        return $this->hasMany(StudentRequirement::class);
    }
}