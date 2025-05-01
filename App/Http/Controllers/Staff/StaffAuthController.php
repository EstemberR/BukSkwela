<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Check tenant approval status
        if (tenant()) {
            $tenant = \App\Models\Tenant::find(tenant('id'));
            if (!$tenant || $tenant->status !== 'approved') {
                return back()->withErrors([
                    'email' => 'This tenant account is not active. Please contact the administrator.',
                ])->withInput($request->except('password'));
            }
        }

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Check if in tenant context and redirect accordingly
        if (tenant()) {
            return redirect()->route('tenant.login');
        }
        return redirect()->route('login');
    }
    
    public function tenantLogout(Request $request)
    {
        // Logout the staff user
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to tenant login page using the current host
        return redirect()->route('tenant.login');
    }
}