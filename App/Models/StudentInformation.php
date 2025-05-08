<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInformation extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students_informations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex',
        'birth_date',
        'civil_status',
        'religion',
        'blood_type',
        'contact_number',
        'email',
        'facebook',
        'has_indigenous',
        'indigenous_group',
        'other_indigenous',
        'dswd_4ps',
        'disability',
        // Academic information
        'educational_status',
        'lrn',
        'school_name',
        'year_from',
        'year_to',
        'academic_level',
        'school_type',
        'strand',
        // School address fields
        'region',
        'province',
        'city',
        'barangay',
        'street',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
        'has_indigenous' => 'boolean',
        'year_from' => 'integer',
        'year_to' => 'integer',
    ];
    
    /**
     * Get the student that owns the information.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
