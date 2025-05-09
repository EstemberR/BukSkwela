<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Student\Student;
use App\Models\Course\Course;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Traits\HasTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentApplication extends Model
{
    use HasFactory, BelongsToTenant, HasTenantConnection;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'tenant';

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'student_applications';

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
        'document_files',
        'tenant_id',
        'school_year_start',
        'school_year_end'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'document_files' => 'array',
        'school_year_start' => 'integer',
        'school_year_end' => 'integer'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            // First check if the table exists
            if (!Schema::connection('tenant')->hasTable('student_applications')) {
                return;
            }
            
            // Only add tenant_id filter if the column exists
            if (Schema::connection('tenant')->hasColumn('student_applications', 'tenant_id')) {
                $tenantId = tenant('id');
                if ($tenantId) {
                    $builder->where('tenant_id', $tenantId);
                }
            }
        });
    }

    /**
     * Force tenant connection for this model
     */
    public function getConnectionName()
    {
        return 'tenant';
    }

    /**
     * Override save method to ensure connection is properly set
     */
    public function save(array $options = [])
    {
        // Ensure connection is set to tenant
        $this->setConnection('tenant');
        
        // Log save operation
        Log::info('Saving StudentApplication', [
            'connection' => $this->getConnectionName(),
            'database' => DB::connection('tenant')->getDatabaseName(),
            'id' => $this->id,
            'tenant_id' => $this->tenant_id ?? tenant('id')
        ]);
        
        // Make sure tenant_id is set
        if (tenant() && !$this->tenant_id) {
            $this->tenant_id = tenant('id');
        }
        
        return parent::save($options);
    }

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

    /**
     * Get the student status from the document_files JSON
     *
     * @return string
     */
    public function getStudentStatusAttribute()
    {
        if (is_array($this->document_files) && isset($this->document_files['student_status'])) {
            return $this->document_files['student_status'];
        }
        
        return 'Regular'; // Default status if not specified
    }
    
    /**
     * Get the document files array from the document_files JSON
     *
     * @return array
     */
    public function getDocumentFilesListAttribute()
    {
        if (is_array($this->document_files)) {
            if (isset($this->document_files['files'])) {
                return $this->document_files['files'];
            }
            
            // For backwards compatibility with old format
            if (!isset($this->document_files['student_status'])) {
                return $this->document_files;
            }
        }
        
        return [];
    }
}
