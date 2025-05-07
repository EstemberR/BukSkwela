<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Student\Student;
use App\Models\Course\Course;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\Log;

class StudentApplication extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'program_id',
        'year_level',
        'notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the student that owns the application.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the program/course that the student is applying to.
     */
    public function program()
    {
        try {
            return $this->belongsTo(Course::class, 'program_id')->withDefault([
                'id' => 0,
                'name' => 'Unknown Program',
                'title' => 'Unknown Program',
                'description' => 'Program details not available'
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading program for application: ' . $e->getMessage(), [
                'application_id' => $this->id,
                'program_id' => $this->program_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Get the admin who reviewed this application.
     */
    public function reviewer()
    {
        return $this->belongsTo(TenantAdmin::class, 'reviewed_by');
    }
}
