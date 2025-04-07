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

class StaffController extends Controller
{
    public function index(Request $request)
    {
        // Start with a query scoped to the current tenant
        $query = Staff::where('tenant_id', tenant('id'));

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
        $departments = Department::where('tenant_id', tenant('id'))->get();

        return view('tenant.staff.index', compact('staffMembers', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|unique:staff,staff_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'role' => 'required|in:instructor,admin,staff',
            'department' => 'required|string|max:255',
        ]);

        Log::info('Creating new staff member', [
            'staff_id' => $request->staff_id,
            'email' => $request->email
        ]);

        // Generate a secure password
        $password = PasswordGenerator::generate(random_int(10, 15));

        // Find or create the department
        $department = Department::firstOrCreate(
            [
                'name' => $request->department,
                'tenant_id' => tenant('id')
            ],
            [
                'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->department), 0, 5)),
                'description' => 'Department for ' . $request->department,
                'status' => 'active'
            ]
        );

        $staff = Staff::create([
            'staff_id' => $request->staff_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department_id' => $department->id,
            'password' => Hash::make($password),
            'tenant_id' => tenant('id'),
            'status' => 'active',
        ]);

        // Send welcome email to the staff member with their password
        try {
            Log::info('Attempting to send welcome email', [
                'to' => $staff->email,
                'staff_id' => $staff->staff_id
            ]);
            
            Mail::to($staff->email)->send(new StaffRegistered($staff, $password));
            
            Log::info('Welcome email sent successfully', [
                'to' => $staff->email,
                'staff_id' => $staff->staff_id
            ]);

            return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                ->with('success', 'Staff member added successfully and welcome email sent');
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to staff', [
                'staff_id' => $staff->id,
                'email' => $staff->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                ->with('warning', 'Staff member added successfully but failed to send welcome email. Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Staff $staff)
    {
        // Ensure the staff member belongs to the current tenant
        if ($staff->tenant_id != tenant('id')) {
            return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                ->with('error', 'Unauthorized access to staff member from another tenant');
        }

        $request->validate([
            'staff_id' => 'required|unique:staff,staff_id,' . $staff->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'role' => 'required|in:instructor,admin,staff',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Verify the department belongs to this tenant
        $department = Department::find($request->department_id);
        if (!$department || $department->tenant_id != tenant('id')) {
            return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                ->with('error', 'The selected department is invalid');
        }

        // Track what fields were updated
        $updatedFields = [];
        
        if ($staff->staff_id != $request->staff_id) {
            $updatedFields['staff_id'] = $request->staff_id;
        }
        
        if ($staff->name != $request->name) {
            $updatedFields['name'] = $request->name;
        }
        
        if ($staff->email != $request->email) {
            $updatedFields['email'] = $request->email;
        }
        
        if ($staff->role != $request->role) {
            $updatedFields['role'] = ucfirst($request->role);
        }
        
        if ($staff->department_id != $request->department_id) {
            $updatedFields['department'] = $department->name;
        }

        // Update staff information
        $staff->update([
            'staff_id' => $request->staff_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        // Password update
        if ($request->filled('password')) {
            $staff->update([
                'password' => Hash::make($request->password),
            ]);
            $updatedFields['password'] = 'Password has been updated';
        }

        // Send email notification if anything was updated
        try {
            if (!empty($updatedFields)) {
                Log::info('Sending staff credential update email', [
                    'staff_id' => $staff->id,
                    'email' => $staff->email,
                    'updated_fields' => array_keys($updatedFields)
                ]);
                
                Mail::to($staff->email)->send(new StaffCredentialsUpdated($staff, $updatedFields));
                
                Log::info('Staff credential update email sent successfully', [
                    'staff_id' => $staff->id,
                    'email' => $staff->email
                ]);
                
                return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                    ->with('success', 'Staff member updated successfully and notification email sent');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send staff credential update email', [
                'staff_id' => $staff->id,
                'email' => $staff->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
                ->with('warning', 'Staff member updated successfully but failed to send notification email');
        }

        return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
            ->with('success', 'Staff member updated successfully');
    }

    public function destroy(Staff $staff)
    {
        // Ensure the staff member belongs to the current tenant
        if ($staff->tenant_id != tenant('id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to staff member from another tenant'
            ], 403);
        }

        $staff->delete();
        return response()->json(['success' => true]);
    }
} 