<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

class StudentProfileController extends Controller
{
    /**
     * Update the student's personal information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePersonalInfo(Request $request)
    {
        try {
            // Ensure the students_informations table exists
            if (!$this->ensureStudentInformationTableExists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not ensure required database tables exist'
                ], 500);
            }
            
            // Use tenant connection for all database operations
            DB::connection('tenant')->beginTransaction();
            
            try {
                // Get the authenticated student
                $student = Auth::guard('student')->user();
                
                if (!$student) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Student not found'
                    ], 404);
                }
                
                // Log the student ID for debugging
                Log::info('Student ID before update:', [
                    'id' => $student->id,
                    'student_id_field' => $student->student_id,
                    'request_student_id' => $request->input('student_id'),
                    'database' => DB::connection('tenant')->getDatabaseName()
                ]);
                
                // Check if student exists in the students table
                $studentExists = DB::connection('tenant')->table('students')->where('id', $student->id)->exists();
                if (!$studentExists) {
                    Log::error('Student does not exist in students table', [
                        'student_id' => $student->id,
                        'database' => DB::connection('tenant')->getDatabaseName()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Student record not found in database'
                    ], 404);
                }
                
                // Validate the request data
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'suffix' => 'nullable|string|max:50',
                    'sex' => 'required|in:MALE,FEMALE',
                    'birth_date' => 'required|date',
                    'civil_status' => 'required|string|max:50',
                    'religion' => 'nullable|string|max:100',
                    'blood_type' => 'nullable|string|max:10',
                    'contact_number' => 'nullable|string|max:20',
                    'email' => 'required|email|max:255',
                    'facebook' => 'nullable|string|max:255',
                    'has_indigenous' => 'required|boolean',
                    'indigenous_group' => 'nullable|string|max:100',
                    'other_indigenous' => 'nullable|string|max:100',
                    'dswd_4ps' => 'nullable|string|max:100',
                    'disability' => 'nullable|string|max:100',
                    'student_id' => 'nullable|integer' // Allow student_id to be passed from frontend
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                // Update the student name in the main students table
                $student->name = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);
                $student->email = $request->email;
                $student->save();
                
                // Check if a record already exists
                $studentInfo = StudentInformation::on('tenant')->where('student_id', $student->id)->first();
                
                if (!$studentInfo) {
                    // Create a new record if none exists
                    $studentInfo = new StudentInformation();
                    $studentInfo->setConnection('tenant');
                    $studentInfo->student_id = $student->id;
                }
                
                // Update student information fields
                $studentInfo->first_name = $request->first_name;
                $studentInfo->middle_name = $request->middle_name;
                $studentInfo->last_name = $request->last_name;
                $studentInfo->suffix = $request->suffix;
                $studentInfo->sex = $request->sex;
                $studentInfo->birth_date = $request->birth_date;
                $studentInfo->civil_status = $request->civil_status;
                $studentInfo->religion = $request->religion;
                $studentInfo->blood_type = $request->blood_type;
                $studentInfo->contact_number = $request->contact_number;
                $studentInfo->email = $request->email;
                $studentInfo->facebook = $request->facebook;
                $studentInfo->has_indigenous = $request->has_indigenous;
                $studentInfo->indigenous_group = $request->indigenous_group;
                $studentInfo->other_indigenous = $request->other_indigenous;
                $studentInfo->dswd_4ps = $request->dswd_4ps;
                $studentInfo->disability = $request->disability;
                
                $studentInfo->save();
                
                DB::connection('tenant')->commit();
                
                // Return additional debug information in the response
                $debugInfo = [
                    'student_id' => $student->id,
                    'student_record_exists' => $studentExists,
                    'database' => DB::connection('tenant')->getDatabaseName(),
                    'table_exists' => Schema::connection('tenant')->hasTable('students_informations'),
                ];
                
                // Log that an update was made
                Log::info('Student profile updated successfully', [
                    'student_id' => $student->id,
                    'info_id' => $studentInfo->id,
                    'database' => DB::connection('tenant')->getDatabaseName()
                ]);
                
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Personal information updated successfully',
                    'debug' => $debugInfo
                ]);
                
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                
                // Log the error with detailed information
                Log::error('Error in transaction when updating student profile: ' . $e->getMessage(), [
                    'student_id' => Auth::guard('student')->id(),
                    'database' => DB::connection('tenant')->getDatabaseName(),
                    'error_code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error updating student profile: ' . $e->getMessage(), [
                'student_id' => Auth::guard('student')->id(),
                'database' => DB::connection('tenant')->getDatabaseName(),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your personal information: ' . $e->getMessage(),
                'debug' => [
                    'error_code' => $e->getCode(),
                    'database' => DB::connection('tenant')->getDatabaseName()
                ]
            ], 500);
        }
    }
    
    /**
     * Update the student's academic information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAcademicInfo(Request $request)
    {
        try {
            // Ensure the students_informations table exists
            if (!$this->ensureStudentInformationTableExists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not ensure required database tables exist'
                ], 500);
            }
            
            // Log the request data for debugging
            Log::info('Academic info update request received', [
                'request_data' => $request->all(),
                'database' => DB::connection('tenant')->getDatabaseName()
            ]);
            
            // Get the authenticated student
            $student = Auth::guard('student')->user();
            
            if (!$student) {
                Log::warning('Student not found during academic info update');
                return response()->json([
                    'success' => false, 
                    'message' => 'Student not found'
                ], 404);
            }
            
            // Log the student ID for debugging
            Log::info('Student ID before academic update:', [
                'id' => $student->id,
                'database' => DB::connection('tenant')->getDatabaseName()
            ]);
            
            // Check if student exists in the students table
            $studentExists = DB::connection('tenant')->table('students')->where('id', $student->id)->exists();
            if (!$studentExists) {
                Log::error('Student does not exist in students table for academic update', [
                    'student_id' => $student->id,
                    'database' => DB::connection('tenant')->getDatabaseName()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student record not found in database'
                ], 404);
            }
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'educational_status' => 'required|string|max:100',
                'lrn' => 'nullable|string|max:50',
                'school_name' => 'required|string|max:255',
                'year_from' => 'required|string|max:10',
                'year_to' => 'required|string|max:10',
                'academic_level' => 'required|string|max:100',
                'school_type' => 'required|string|max:50',
                'strand' => 'nullable|string|max:100',
                'region' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'barangay' => 'required|string|max:100',
                'street' => 'nullable|string|max:255',
            ]);
            
            if ($validator->fails()) {
                Log::warning('Academic info validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Begin transaction for database updates
            DB::connection('tenant')->beginTransaction();
            try {
                // Check if a record already exists
                $studentInfo = StudentInformation::on('tenant')->where('student_id', $student->id)->first();
                
                if (!$studentInfo) {
                    // Create a new record if none exists
                    $studentInfo = new StudentInformation();
                    $studentInfo->setConnection('tenant');
                    $studentInfo->student_id = $student->id;
                }
                
                // Update academic information fields
                $studentInfo->educational_status = $request->educational_status;
                $studentInfo->lrn = $request->lrn;
                $studentInfo->school_name = $request->school_name;
                $studentInfo->year_from = $request->year_from;
                $studentInfo->year_to = $request->year_to;
                $studentInfo->academic_level = $request->academic_level;
                $studentInfo->school_type = $request->school_type;
                $studentInfo->strand = $request->strand;
                $studentInfo->region = $request->region;
                $studentInfo->province = $request->province;
                $studentInfo->city = $request->city;
                $studentInfo->barangay = $request->barangay;
                $studentInfo->street = $request->street;
                
                $studentInfo->save();
                DB::connection('tenant')->commit();
                
                // Log that an update was made
                Log::info('Student academic info updated successfully', [
                    'student_id' => $student->id,
                    'educational_status' => $studentInfo->educational_status,
                    'school_name' => $studentInfo->school_name,
                    'database' => DB::connection('tenant')->getDatabaseName()
                ]);
                
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Academic information updated successfully',
                    'debug' => [
                        'database' => DB::connection('tenant')->getDatabaseName(),
                        'student_id' => $student->id
                    ]
                ]);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error updating student academic info: ' . $e->getMessage(), [
                'student_id' => Auth::guard('student')->id(),
                'database' => DB::connection('tenant')->getDatabaseName(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your academic information: ' . $e->getMessage(),
                'debug' => [
                    'database' => DB::connection('tenant')->getDatabaseName(),
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
    
    /**
     * Get the current student data for the dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function getStudentData()
    {
        try {
            // Ensure the students_informations table exists
            if (!$this->ensureStudentInformationTableExists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not ensure required database tables exist',
                    'debug' => [
                        'database' => DB::connection('tenant')->getDatabaseName(),
                        'students_table_exists' => Schema::connection('tenant')->hasTable('students')
                    ]
                ], 500);
            }
            
            // Get the authenticated student
            $student = Auth::guard('student')->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student not found'
                ], 404);
            }
            
            // Check if we're in the tenant database
            $currentDB = DB::connection('tenant')->getDatabaseName();
            $isUsingTenantDB = strpos($currentDB, 'tenant_') === 0 || strpos($currentDB, 'bukskwela_') === 0;
            
            // Get tenant info if available
            $tenantId = tenant('id') ?? 'unknown';
            
            // Log the student ID and database info for debugging
            Log::info('Student data retrieved', [
                'id' => $student->id,
                'student_id_field' => $student->student_id,
                'database' => $currentDB,
                'using_tenant_db' => $isUsingTenantDB,
                'tenant_id' => $tenantId,
                'students_table_exists' => Schema::connection('tenant')->hasTable('students'),
                'students_info_table_exists' => Schema::connection('tenant')->hasTable('students_informations')
            ]);
            
            // Check if student exists in the students table
            $studentExists = DB::connection('tenant')->table('students')->where('id', $student->id)->exists();
            if (!$studentExists) {
                Log::error('Student does not exist in students table', [
                    'student_id' => $student->id,
                    'database' => $currentDB
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Student record not found in database',
                    'debug' => [
                        'database' => $currentDB,
                        'student_id' => $student->id,
                        'using_tenant_db' => $isUsingTenantDB,
                        'tenant_id' => $tenantId
                    ]
                ], 404);
            }
            
            // Load student information
            $studentInfo = StudentInformation::on('tenant')->where('student_id', $student->id)->first();
            
            // Get structure of students_informations table if it exists
            $tableStructure = null;
            if (Schema::connection('tenant')->hasTable('students_informations')) {
                $tableStructure = DB::connection('tenant')->select('DESCRIBE students_informations');
            }
            
            // Return the student data with information
            return response()->json([
                'success' => true,
                'student' => $student,
                'information' => $studentInfo,
                'debug' => [
                    'database' => $currentDB,
                    'student_id' => $student->id,
                    'student_id_field' => $student->student_id,
                    'using_tenant_db' => $isUsingTenantDB,
                    'tenant_id' => $tenantId,
                    'students_table_exists' => Schema::connection('tenant')->hasTable('students'),
                    'students_info_table_exists' => Schema::connection('tenant')->hasTable('students_informations'),
                    'table_structure' => $tableStructure
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting student data: ' . $e->getMessage(), [
                'student_id' => Auth::guard('student')->id(),
                'database' => DB::connection('tenant')->getDatabaseName(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving your data: ' . $e->getMessage(),
                'debug' => [
                    'database' => DB::connection('tenant')->getDatabaseName(),
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Ensures that the students_informations table exists in the current tenant database
     * and creates it if needed.
     *
     * @return bool Whether the table exists or was created successfully
     */
    private function ensureStudentInformationTableExists()
    {
        try {
            // Get the current tenant ID
            $tenantId = tenant('id');
            
            if (!$tenantId) {
                Log::error('No tenant ID found when ensuring students_informations table exists');
                return false;
            }
            
            $tenantDb = 'tenant_' . strtolower($tenantId);
            
            // Log tenant database information for debugging
            Log::info('Ensuring students_informations table exists in tenant database', [
                'tenant_id' => $tenantId,
                'database_name' => $tenantDb,
                'current_connection' => DB::connection()->getDatabaseName()
            ]);
            
            // Configure the tenant database connection
            Config::set('database.connections.tenant.database', $tenantDb);
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Check if table exists in the tenant database
            if (!Schema::connection('tenant')->hasTable('students_informations')) {
                Log::info('students_informations table does not exist in tenant database, creating it now...', [
                    'tenant_id' => $tenantId,
                    'database' => $tenantDb
                ]);
                
                // Create the table in the tenant database
                Schema::connection('tenant')->create('students_informations', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                    $table->string('first_name')->nullable();
                    $table->string('middle_name')->nullable();
                    $table->string('last_name')->nullable();
                    $table->string('suffix')->nullable();
                    $table->enum('sex', ['MALE', 'FEMALE'])->nullable();
                    $table->date('birth_date')->nullable();
                    $table->enum('civil_status', ['SINGLE', 'MARRIED', 'DIVORCED', 'WIDOWED'])->nullable();
                    $table->string('religion')->nullable();
                    $table->string('blood_type')->nullable();
                    $table->string('contact_number')->nullable();
                    $table->string('email')->nullable();
                    $table->string('facebook')->nullable();
                    $table->boolean('has_indigenous')->default(false);
                    $table->string('indigenous_group')->nullable();
                    $table->string('other_indigenous')->nullable();
                    $table->string('dswd_4ps')->nullable();
                    $table->string('disability')->nullable();
                    // Academic information
                    $table->string('educational_status')->nullable();
                    $table->string('lrn')->nullable(); // Learner's Reference Number
                    $table->string('school_name')->nullable();
                    $table->year('year_from')->nullable();
                    $table->year('year_to')->nullable();
                    $table->string('academic_level')->nullable();
                    $table->string('school_type')->nullable();
                    $table->string('strand')->nullable();
                    // School address fields
                    $table->string('region')->nullable();
                    $table->string('province')->nullable();
                    $table->string('city')->nullable();
                    $table->string('barangay')->nullable();
                    $table->string('street')->nullable();
                    $table->timestamps();
                });
                
                Log::info('students_informations table created successfully in tenant database', [
                    'tenant_id' => $tenantId,
                    'database' => $tenantDb
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error ensuring students_informations table exists: ' . $e->getMessage(), [
                'database' => DB::connection()->getDatabaseName(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
} 