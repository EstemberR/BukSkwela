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

class StaffController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenantId = tenant('id');
            
            // Start with a query scoped to the current tenant
            $query = Staff::where('tenant_id', $tenantId);

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

            $staffMembers = $query->paginate(10);
            $departments = Department::where('tenant_id', $tenantId)->get();
            
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
            
            Log::info('Storing new staff member', [
                'tenant_id' => $tenantId
            ]);

            $request->validate([
                'staff_id' => 'required|unique:staff,staff_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email',
                'role' => 'required|in:instructor,admin,staff',
                'department' => 'required|string|max:255',
            ]);

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
            Mail::to($staff->email)->send(new StaffRegistered($staff, $password));

            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])->with('success', 'Staff member created successfully. Login credentials have been sent to their email.');
            
        } catch (\Exception $e) {
            Log::error('Error in staff store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while creating the staff member: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $tenantId = tenant('id');
            
            $staff = Staff::findOrFail($id);
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

            $staff = Staff::findOrFail($id);

            $request->validate([
                'staff_id' => 'required|unique:staff,staff_id,' . $id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email,' . $id,
                'role' => 'required|in:instructor,admin,staff',
                'department' => 'required|string|max:255',
                'status' => 'required|in:active,inactive'
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

            $staff->update([
                'staff_id' => $request->staff_id,
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'department_id' => $department->id,
                'status' => $request->status
            ]);

            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])->with('success', 'Staff member updated successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error in staff update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while updating the staff member: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tenantId = tenant('id');
            
            $staff = Staff::findOrFail($id);
            $staff->delete();

            return redirect()->route('tenant.staff.index', ['tenant' => $tenantId])->with('success', 'Staff member deleted successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error in staff destroy', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while deleting the staff member.');
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