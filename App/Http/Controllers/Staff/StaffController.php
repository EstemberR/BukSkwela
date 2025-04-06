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

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = Staff::with('department')
            ->where('tenant_id', tenant('id'))
            ->paginate(10);
            
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
        $request->validate([
            'staff_id' => 'required|unique:staff,staff_id,' . $staff->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'role' => 'required|in:instructor,admin,staff',
            'department_id' => 'required|exists:departments,id',
        ]);

        $staff->update([
            'staff_id' => $request->staff_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        if ($request->filled('password')) {
            $staff->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('tenant.staff.index', ['tenant' => tenant('id')])
            ->with('success', 'Staff member updated successfully');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return response()->json(['success' => true]);
    }
} 