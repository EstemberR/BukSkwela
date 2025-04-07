<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\Tenant;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (tenant()) {
            return view('tenant.login')->with([
                'tenant' => tenant('id'),
                '_token' => csrf_token()
            ]);
        }
        return view('login');
    }

    public function login(Request $request)
    {
        // Validate CSRF token first
        $request->validate([
            '_token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // First try to authenticate as superadmin using the web guard
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            // Check if the authenticated user is a superadmin
            if (Auth::user()->role === 'superadmin') {
                $request->session()->regenerate();
                return redirect()->route('superadmin.dashboard');
            }
            // If not superadmin, logout
            Auth::logout();
        }

        // If not superadmin, proceed with tenant admin authentication
        if (tenant()) {
            // Check if the tenant is active before trying to log in
            $tenant = Tenant::find(tenant('id'));
            if (!$tenant || $tenant->status !== 'approved') {
                return back()->withErrors([
                    'email' => 'This tenant account is not active. Please contact the administrator.',
                ])->withInput($request->except('password'));
            }
            
            // For tenant domains
            $admin = TenantAdmin::where('email', $request->email)
                              ->where('tenant_id', tenant('id'))
                              ->first();
        } else {
            // For central domain
            $admin = TenantAdmin::where('email', $request->email)->first();
            
            // Check if admin exists and if their tenant is approved
            if ($admin && $admin->tenant) {
                if ($admin->tenant->status !== 'approved') {
                    return back()->withErrors([
                        'email' => 'This tenant account has not been approved yet. Please contact the administrator.',
                    ])->withInput($request->except('password'));
                }
            }
        }
        
        if (!$admin) {
            return back()->withErrors([
                'email' => 'No admin found with this email.',
            ])->withInput($request->except('password'));
        }

        if (Hash::check($request->password, $admin->password)) {
            $request->session()->regenerate();
            Auth::guard('admin')->login($admin);
            
            if (tenant()) {
                return redirect()->intended(route('tenant.dashboard'));
            } else {
                $domain = $admin->tenant->domains->first()->domain;
                $port = request()->getPort();
                $url = $domain;
                if ($port && $port != 80) {
                    $url .= ':' . $port;
                }
                return redirect()->to('http://' . $url . '/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if (tenant()) {
            return redirect()->away('http://127.0.0.1:8000');
        }
        return redirect()->route('login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (tenant()) {
            return redirect()->route('tenant.dashboard', ['tenant' => tenant('id')]);
        }
        
        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }
        
        return redirect()->away('http://127.0.0.1:8000');
    }
}