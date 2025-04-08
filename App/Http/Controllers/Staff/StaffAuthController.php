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
        
        if (tenant()) {
            return $this->tenantLogout($request);
        }
        
        return redirect()->route('login');
    }
    
    public function tenantLogout(Request $request)
    {
        // Log additional information to help troubleshoot
        \Log::info('Tenant logout triggered', [
            'tenant_id' => tenant('id'),
            'current_host' => request()->getHost(),
            'intended_redirect' => 'http://127.0.0.1:8000/login'
        ]);
        
        // Force client-side redirect to an absolute URL using the tenant_logout.html file
        // This completely bypasses Laravel's routing system
        $centralDomain = '127.0.0.1:8000';
        $redirectUrl = "http://{$centralDomain}/tenant_logout.html";
        
        return redirect()->away($redirectUrl);
    }
}