<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('staff.auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:staff',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $staff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => tenant('id')
        ]);

        // Log in the newly created staff member
        Auth::guard('staff')->login($staff);
        
        return redirect()->route('staff.dashboard')
            ->with('success', 'Registration successful! Welcome to your dashboard.');
    }
}