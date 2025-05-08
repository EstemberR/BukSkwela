<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Traits\HasTenantConnection;

class Course extends Model
{
    use HasFactory, HasTenantConnection;

    protected $connection = 'tenant';
    
    protected $table = 'courses';

    protected $fillable = [
        'title',
        'name',
        'code',
        'description',
        'status',
        'staff_id',
        'tenant_id',
        'school_year_start',
        'school_year_end'
    ];

    protected $casts = [
        'status' => 'string',
        'staff_id' => 'integer',
        'school_year_start' => 'integer',
        'school_year_end' => 'integer'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    public function staff()
    {
        try {
            return $this->belongsTo(Staff::class)->withDefault([
                'name' => 'No Instructor Assigned'
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
    public function getSchoolYearAttribute()
    {
        if ($this->school_year_start && $this->school_year_end) {
            return $this->school_year_start . ' - ' . $this->school_year_end;
        }
        return 'Not set';
    }
}