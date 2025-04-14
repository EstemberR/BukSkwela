<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\Tenant;
use App\Models\TenantAdmin;
use App\Models\Student\Student;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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
    
    /**
     * Configure the database connection for the current tenant
     * 
     * @param string $tenantId
     * @return void
     */
    private function configureTenantConnection($tenantId = null)
    {
        // If no tenant ID is provided, use the current tenant
        if (!$tenantId && tenant()) {
            $tenantId = tenant('id');
        }
        
        if ($tenantId) {
            // Log the tenant database configuration
            Log::info('Configuring tenant connection', [
                'tenant_id' => $tenantId,
                'database' => 'tenant_' . $tenantId
            ]);
            
            // Set the tenant database connection configuration
            Config::set('database.connections.tenant.database', 'tenant_' . $tenantId);
            
            // Reconnect to use the new configuration
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
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

        // Check if email is a student email
        $isStudentEmail = Str::contains($request->email, '@student.buksu.edu.ph');
        Log::info('Email check', [
            'email' => $request->email,
            'isStudentEmail' => $isStudentEmail,
            'tenant_id' => tenant() ? tenant('id') : 'none',
            'contains' => Str::contains($request->email, '@student.buksu.edu.ph')
        ]);

        // Special handling to remove approval modal session for student emails
        if ($isStudentEmail && session('show_approval_modal')) {
            session()->forget('show_approval_modal');
        }

        // If tenant is set, configure the proper tenant connection
        if (tenant()) {
            $this->configureTenantConnection();
            
            // If this is a student email, try to authenticate as student first
            // This happens BEFORE any tenant approval checks
            if ($isStudentEmail) {
                try {
                    // Find the student by email in the tenant database
                    Log::info('Looking for student in tenant database', [
                        'tenant_id' => tenant('id'),
                        'email' => $request->email,
                        'connection' => 'tenant',
                        'database' => Config::get('database.connections.tenant.database')
                    ]);
                    
                    $student = Student::where('email', $request->email)->first();
                    
                    if ($student) {
                        Log::info('Student found', [
                            'student_id' => $student->id,
                            'student_email' => $student->email,
                            'status' => $student->status
                        ]);
                        
                        // Check if the student is active
                        if ($student->status !== 'active') {
                            Log::warning('Student account is not active', [
                                'email' => $request->email,
                                'status' => $student->status
                            ]);
                            return back()->withErrors([
                                'email' => 'Your student account is not active. Please contact your administrator.',
                            ])->withInput($request->except('password'));
                        }
                        
                        // Verify password
                        if (Hash::check($request->password, $student->password)) {
                            // Login the student
                            Auth::guard('student')->login($student);
                            $request->session()->regenerate();
                            
                            Log::info('Student login successful', [
                                'student_id' => $student->id,
                                'student_email' => $student->email,
                                'tenant_id' => tenant('id'),
                                'route' => 'tenant.student.dashboard'
                            ]);
                            
                            return redirect()->intended(route('tenant.student.dashboard'));
                        } else {
                            Log::warning('Invalid password for student', [
                                'email' => $request->email,
                                'tenant_id' => tenant('id')
                            ]);
                            return back()->withErrors([
                                'email' => 'Invalid student credentials.',
                            ])->withInput($request->except('password'));
                        }
                    } else {
                        Log::warning('Student not found with email in this tenant', [
                            'email' => $request->email,
                            'tenant_id' => tenant('id')
                        ]);
                        return back()->withErrors([
                            'email' => 'No student account found with this email in this school.',
                        ])->withInput($request->except('password'));
                    }
                } catch (\Exception $e) {
                    Log::error('Student authentication error', [
                        'email' => $request->email,
                        'tenant_id' => tenant('id'),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return back()->withErrors([
                        'email' => 'An error occurred during authentication. Please try again or contact support.',
                    ])->withInput($request->except('password'));
                }
            }

            // If not student email or student auth failed, proceed with tenant authentication
            
            // Check if the tenant is active before trying to log in
            // Skip this check for student emails
            $tenant = Tenant::find(tenant('id'));
            if (!$isStudentEmail && (!$tenant || $tenant->status !== 'approved')) {
                Log::warning('Tenant not approved and not student email', [
                    'email' => $request->email,
                    'tenant_id' => tenant('id'),
                    'tenant_status' => $tenant ? $tenant->status : 'not found'
                ]);
                return back()->with('show_approval_modal', true)
                    ->withInput($request->except('password'));
            }
            
            // For tenant domains - only for non-student emails
            $admin = TenantAdmin::where('email', $request->email)
                            ->where('tenant_id', tenant('id'))
                            ->first();
                              
            // Try to find a student if no admin found (for non-student emails)
            if (!$admin && !$isStudentEmail) {
                try {
                    // Find the student by email in the tenant database
                    $student = Student::where('email', $request->email)->first();
                    
                    if ($student) {
                        // Check if the student is active
                        if ($student->status !== 'active') {
                            return back()->withErrors([
                                'email' => 'Your student account is not active. Please contact your administrator.',
                            ])->withInput($request->except('password'));
                        }
                        
                        // Verify password
                        if (Hash::check($request->password, $student->password)) {
                            // Login the student
                            Auth::guard('student')->login($student);
                            $request->session()->regenerate();
                            
                            Log::info('Student login successful from main login page', [
                                'student_id' => $student->id,
                                'student_email' => $student->email,
                                'tenant_id' => tenant('id'),
                                'url' => route('tenant.student.dashboard')
                            ]);
                            
                            return redirect()->intended(route('tenant.student.dashboard'));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Student authentication error', [
                        'email' => $request->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            // Proceed with admin authentication if a valid admin was found
            if ($admin && Hash::check($request->password, $admin->password)) {
                $request->session()->regenerate();
                Auth::guard('admin')->login($admin);
                
                return redirect()->intended(route('tenant.dashboard'));
            }
        } else {
            // For central domain
            $admin = TenantAdmin::where('email', $request->email)->first();
            
            // Check if admin exists and if their tenant is approved
            if ($admin && $admin->tenant) {
                if (!$isStudentEmail && $admin->tenant->status !== 'approved') {
                    Log::warning('Central domain - Tenant not approved and not student email', [
                        'email' => $request->email,
                        'tenant_id' => $admin->tenant->id,
                        'tenant_status' => $admin->tenant->status
                    ]);
                    return back()->with('show_approval_modal', true)
                        ->withInput($request->except('password'));
                }
                
                if (Hash::check($request->password, $admin->password)) {
                    $request->session()->regenerate();
                    Auth::guard('admin')->login($admin);
                    
                    $domain = $admin->tenant->domains->first()->domain;
                    $port = request()->getPort();
                    $url = $domain;
                    if ($port && $port != 80) {
                        $url .= ':' . $port;
                    }
                    return redirect()->to('http://' . $url . '/dashboard');
                }
            }
            
            // If this is a student email at the central domain, check if we can find their tenant
            if ($isStudentEmail) {
                // Look for student record in all tenant databases
                Log::info('Checking for student record across tenants', [
                    'email' => $request->email
                ]);
                
                // Get all active tenants
                $tenants = Tenant::where('status', 'approved')->get();
                
                foreach ($tenants as $tenant) {
                    try {
                        // Configure connection for this tenant
                        $this->configureTenantConnection($tenant->id);
                        
                        // Check if student exists in this tenant's database
                        $student = Student::where('email', $request->email)->first();
                        
                        if ($student) {
                            // Found student, now check password
                            if (Hash::check($request->password, $student->password)) {
                                Log::info('Found student in tenant', [
                                    'email' => $request->email,
                                    'tenant_id' => $tenant->id,
                                    'student_id' => $student->id
                                ]);
                                
                                // Redirect to student's tenant subdomain
                                $domain = $tenant->domains->first()->domain;
                                $port = request()->getPort();
                                $url = $domain;
                                if ($port && $port != 80) {
                                    $url .= ':' . $port;
                                }
                                
                                return redirect()->to('http://' . $url . '/student/dashboard');
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error checking tenant database for student', [
                            'tenant_id' => $tenant->id,
                            'email' => $request->email,
                            'error' => $e->getMessage()
                        ]);
                        continue; // Try next tenant
                    }
                }
                
                // If we get here, we couldn't find the student in any tenant
                Log::warning('Student not found in any tenant database', [
                    'email' => $request->email
                ]);
                
                return back()->withErrors([
                    'email' => 'No student account found with this email in any school.',
                ])->withInput($request->except('password'));
            }
        }

        // If we reach here, no valid login was found
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Check which guard is active and logout accordingly
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } else if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if (tenant()) {
            return redirect()->route('tenant.login');
        }
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (tenant()) {
            if (Auth::guard('student')->check()) {
                return redirect()->route('tenant.student.dashboard');
            }
            return redirect()->route('tenant.dashboard', ['tenant' => tenant('id')]);
        }
        
        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }
        
        return redirect()->away('http://127.0.0.1:8000');
    }
}