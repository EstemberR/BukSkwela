<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\StaffRegistered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\PasswordGenerator;
use App\Mail\StaffCredentialsUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenantId = tenant('id');
            if (!$tenantId) {
                Log::error('No tenant ID found in staff index');
                return redirect()->back()->with('error', 'No tenant context found.');
            }
            
            Log::info('Loading staff index for tenant', ['tenant_id' => $tenantId]);
            
            // Set up the database connection for the tenant
            $tenantDBName = 'tenant_' . $tenantId;
            Config::set('database.connections.tenant.database', $tenantDBName);
            
            // Purge and reconnect to ensure we're using the right connection
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Verify database connection
            try {
                DB::connection('tenant')->getPdo();
            } catch (\Exception $e) {
                Log::error('Failed to connect to tenant database', [
                    'tenant_id' => $tenantId,
                    'database' => $tenantDBName,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'Unable to connect to tenant database.');
            }
            
            // Log the current database connection
            Log::info('Current database connection', [
                'database' => Config::get('database.connections.tenant.database'),
                'connection' => DB::connection('tenant')->getDatabaseName()
            ]);
            
            // Start with a query scoped to the current tenant
            $query = Staff::query();

            // Apply search filter
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('staff_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Apply role filter
            if ($request->has('role') && $request->get('role') !== '') {
                $query->where('role', $request->get('role'));
            }

            // Log the SQL query
            Log::info('Staff query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
            
            $staffMembers = $query->paginate(10);
            
            // Log the results
            Log::info('Retrieved staff members', [
                'count' => $staffMembers->count(),
                'total' => $staffMembers->total(),
                'first_item' => $staffMembers->firstItem(),
                'last_item' => $staffMembers->lastItem()
            ]);
            
            // Check if departments table exists and handle if it doesn't
            $departments = [];
            if (Schema::connection('tenant')->hasTable('departments')) {
                $departments = Department::query()->get();
            } else {
                // Create departments table if it doesn't exist
                Schema::connection('tenant')->create('departments', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code');
                    $table->text('description')->nullable();
                    $table->string('tenant_id');
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->timestamps();
                });
                
                // Create a default department
                Department::create([
                    'name' => 'General',
                    'code' => 'GEN',
                    'description' => 'General department',
                    'tenant_id' => $tenantId,
                    'status' => 'active'
                ]);
                
                $departments = Department::query()->get();
            }
            
            return view('tenant.staff.index', compact('staffMembers', 'departments'));
            
        } catch (\Exception $e) {
            Log::error('Error in staff index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while fetching staff members: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $tenantId = tenant('id');
            
            // Check if departments table exists and handle if it doesn't
            if (!Schema::connection('tenant')->hasTable('departments')) {
                // Create departments table if it doesn't exist
                Schema::connection('tenant')->create('departments', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code');
                    $table->text('description')->nullable();
                    $table->string('tenant_id');
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->timestamps();
                });
                
                // Create a default department
                Department::create([
                    'name' => 'General',
                    'code' => 'GEN',
                    'description' => 'General department',
                    'tenant_id' => $tenantId,
                    'status' => 'active'
                ]);
            }
            
            $departments = Department::where('tenant_id', $tenantId)->get();
            return view('tenant.staff.create', compact('departments'));
            
        } catch (\Exception $e) {
            Log::error('Error in staff create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $tenantId = tenant('id');
            if (!$tenantId) {
                Log::error('No tenant ID found in staff store');
                return response()->json(['error' => 'No tenant context found.'], 422);
            }
            
            Log::info('Storing new staff member', ['tenant_id' => $tenantId]);
            
            // Set up the database connection for the tenant
            $tenantDBName = 'tenant_' . $tenantId;
            Config::set('database.connections.tenant.database', $tenantDBName);
            
            // Purge and reconnect to ensure we're using the right connection
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Verify database connection
            try {
                DB::connection('tenant')->getPdo();
            } catch (\Exception $e) {
                Log::error('Failed to connect to tenant database', [
                    'tenant_id' => $tenantId,
                    'database' => $tenantDBName,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Unable to connect to tenant database.'], 500);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|unique:staff,staff_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email',
                'role' => 'required|in:instructor,admin,staff',
                'department' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Generate a secure password
            $password = PasswordGenerator::generate(random_int(10, 15));
            
            // Find or create the department
            $department = Department::firstOrCreate(
                [
                    'name' => $request->department,
                    'tenant_id' => $tenantId
                ],
                [
                    'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->department), 0, 5)),
                    'description' => 'Department for ' . $request->department,
                    'status' => 'active'
                ]
            );
            
            Log::info('Department created or found', [
                'department_id' => $department->id,
                'department_name' => $department->name
            ]);

            // Create the staff member
            $staff = Staff::create([
                'staff_id' => $request->staff_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => $request->role,
                'department_id' => $department->id,
                'status' => 'active',
                'tenant_id' => $tenantId
            ]);
            
            Log::info('Staff member created', [
                'staff_id' => $staff->id,
                'email' => $staff->email
            ]);

            // Send email with credentials
            try {
                Mail::to($staff->email)->send(new StaffRegistered($staff, $password));
                Log::info('Staff credentials email sent', ['email' => $staff->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send staff credentials email', [
                    'error' => $e->getMessage(),
                    'staff_email' => $staff->email
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff member created successfully. Login credentials have been sent to their email.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in staff store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while creating the staff member: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $tenantId = tenant('id');
            
            $staff = Staff::findOrFail($id);
            
            // Check if departments table exists and handle if it doesn't
            if (!Schema::connection('tenant')->hasTable('departments')) {
                // Create departments table if it doesn't exist
                Schema::connection('tenant')->create('departments', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code');
                    $table->text('description')->nullable();
                    $table->string('tenant_id');
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->timestamps();
                });
                
                // Create a default department
                Department::create([
                    'name' => 'General',
                    'code' => 'GEN',
                    'description' => 'General department',
                    'tenant_id' => $tenantId,
                    'status' => 'active'
                ]);
            }
            
            $departments = Department::where('tenant_id', $tenantId)->get();
            
            return view('tenant.staff.edit', compact('staff', 'departments'));
            
        } catch (\Exception $e) {
            Log::error('Error in staff edit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while loading the edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tenantId = tenant('id');
            Log::info('Updating staff member', [
                'staff_id' => $id,
                'tenant_id' => $tenantId
            ]);
            
            // Set up the database connection for the tenant
            $tenantDBName = 'tenant_' . $tenantId;
            Config::set('database.connections.tenant.database', $tenantDBName);
            
            // Purge and reconnect to ensure we're using the right connection
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Find staff directly using the ID
            $staff = Staff::where('id', $id)
                         ->where('tenant_id', $tenantId)
                         ->first();
            
            if (!$staff) {
                Log::warning('Staff member not found for update', [
                    'id' => $id,
                    'tenant_id' => $tenantId
                ]);
                return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                                 ->with('error', 'Staff member not found.');
            }

            // Validate the request data
            $request->validate([
                'staff_id' => 'required|unique:staff,staff_id,'.$staff->id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email,'.$staff->id,
                'role' => 'required|in:instructor,admin,staff',
                'department' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);
            
            // Find or create the department
            $department = Department::firstOrCreate(
                [
                    'name' => $request->department,
                    'tenant_id' => $tenantId
                ],
                [
                    'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->department), 0, 5)),
                    'description' => 'Department for ' . $request->department,
                    'status' => 'active'
                ]
            );

            // Update staff data
            $updateData = [
                'staff_id' => $request->staff_id,
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'department_id' => $department->id,
                'status' => $request->status
            ];
            
            // Only update password if it's provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                
                // Send email with updated credentials
                Mail::to($staff->email)->send(new StaffCredentialsUpdated($staff, $request->password));
                
                Log::info('Staff credentials updated and email sent', [
                    'staff_id' => $staff->id,
                    'email' => $staff->email
                ]);
            }
            
            $staff->update($updateData);
            
            Log::info('Staff member updated successfully', [
                'id' => $id,
                'tenant_id' => $tenantId
            ]);
            
            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                             ->with('success', 'Staff member updated successfully.');
                             
        } catch (\Exception $e) {
            Log::error('Error in staff update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                             ->with('error', 'An error occurred while updating the staff member: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tenantId = tenant('id');
            Log::info('Deleting staff member', [
                'staff_id' => $id,
                'tenant_id' => $tenantId
            ]);
            
            // Set up the database connection for the tenant
            $tenantDBName = 'tenant_' . $tenantId;
            Config::set('database.connections.tenant.database', $tenantDBName);
            
            // Purge and reconnect to ensure we're using the right connection
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Find staff directly using the ID
            $staff = Staff::where('id', $id)
                         ->where('tenant_id', $tenantId)
                         ->first();
            
            if (!$staff) {
                Log::warning('Staff member not found for deletion', [
                    'id' => $id,
                    'tenant_id' => $tenantId
                ]);
                return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                                 ->with('error', 'Staff member not found.');
            }
            
            // Delete the staff member
            $staff->delete();
            
            Log::info('Staff member deleted successfully', [
                'id' => $id,
                'tenant_id' => $tenantId
            ]);
            
            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                             ->with('success', 'Staff member deleted successfully.');
                             
        } catch (\Exception $e) {
            Log::error('Error in staff deletion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])
                             ->with('error', 'An error occurred while deleting the staff member: ' . $e->getMessage());
        }
    }

    public function resetPassword($id)
    {
        try {
            $tenantId = tenant('id');
            
            $staff = Staff::findOrFail($id);
            
            // Generate a new secure password
            $newPassword = PasswordGenerator::generate(random_int(10, 15));
            
            // Update the staff password
            $staff->update([
                'password' => Hash::make($newPassword)
            ]);
            
            // Send email with new credentials
            Mail::to($staff->email)->send(new StaffCredentialsUpdated($staff, $newPassword));
            
            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])->with('success', 'Staff password reset successfully. New login credentials have been sent to their email.');
            
        } catch (\Exception $e) {
            Log::error('Error in staff reset password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while resetting the staff password.');
        }
    }
} 