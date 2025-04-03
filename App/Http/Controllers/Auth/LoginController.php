<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\Tenant;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\Hash;

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

        if (tenant()) {
            // For tenant domains
            $admin = TenantAdmin::where('email', $request->email)
                              ->where('tenant_id', tenant('id'))
                              ->first();
        } else {
            // For central domain
            $admin = TenantAdmin::where('email', $request->email)->first();
        }
        
        if (!$admin) {
            return back()->withErrors([
                'email' => 'No tenant admin found with this email.',
            ])->withInput($request->except('password'));
        }

        if (Hash::check($request->password, $admin->password)) {
            $request->session()->regenerate();
            Auth::guard('admin')->login($admin);
            
            if (tenant()) {
                return redirect()->route('tenant.admin.dashboard');
            } else {
                $domain = $admin->tenant->domains->first()->domain;
                $port = request()->getPort();
                $url = $domain;
                if ($port && $port != 80) {
                    $url .= ':' . $port;
                }
                return redirect()->to('http://' . $url . '/admin/dashboard');
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
            return redirect()->route('tenant.login');
        }
        return redirect()->route('login');
    }
}