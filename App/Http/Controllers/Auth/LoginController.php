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
use Illuminate\Support\Facades\Schema;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        // Check if this is a tenant subdomain
        $host = request()->getHost();
        $parts = explode('.', $host);
        $subdomain = null;
        
        // For localhost with port (e.g., testing.localhost:8000)
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            if (count($parts) >= 2) {
                $subdomain = $parts[0];
            }
        } else if (count($parts) > 2) {
            $subdomain = $parts[0];
        }
        
        // If we have a subdomain, check if the tenant is disabled
        if ($subdomain && $subdomain != 'www') {
            try {
                $tenant = \App\Models\Tenant::find($subdomain);
                
                if ($tenant && in_array($tenant->status, ['disabled', 'rejected', 'denied'])) {
                    return view('disabled');
                }
            } catch (\Exception $e) {
                \Log::error('Error checking tenant status: ' . $e->getMessage());
            }
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

        // Check if we're on a tenant subdomain by parsing the host
        $host = $request->getHost();
        $isPotentialTenantDomain = false;
        $tenantId = null;
        
        // Special handling for localhost tenant detection
        // Example: yawaksd.localhost will be identified as tenant 'yawaksd'
        if (strpos($host, '.localhost') !== false) {
            $parts = explode('.', $host);
            if (count($parts) >= 2) {
                $isPotentialTenantDomain = true;
                $tenantId = $parts[0];
                Log::info('Detected tenant domain in localhost environment', [
                    'host' => $host,
                    'tenantId' => $tenantId
                ]);
                
                // Look up tenant
                $tenant = Tenant::find($tenantId);
                if ($tenant) {
                    // Configure tenant DB connection
                    $tenantDatabaseName = 'tenant_' . $tenantId;
                    config(['database.connections.tenant.database' => $tenantDatabaseName]);
                    DB::purge('tenant');
                    DB::reconnect('tenant');
                    
                    // We found a tenant, now look for credentials in the tenant's own database
                    try {
                        Log::info('Looking for credentials in tenant database', [
                            'tenant_id' => $tenantId,
                            'tenant_db' => 'tenant_' . $tenantId,
                            'email' => $request->email
                        ]);
                        
                        // Ensure table exists first
                        if (!Schema::connection('tenant')->hasTable('tenant_user_credentials')) {
                            Log::warning('tenant_user_credentials table doesn\'t exist in tenant database. Creating it...', [
                                'tenant_id' => $tenantId
                            ]);
                            
                            DB::connection('tenant')->statement("
                                CREATE TABLE IF NOT EXISTS tenant_user_credentials (
                                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    email VARCHAR(255) NOT NULL,
                                    password VARCHAR(255) NOT NULL, 
                                    user_type ENUM('admin', 'staff', 'student') DEFAULT 'admin',
                                    user_id BIGINT UNSIGNED NULL,
                                    is_active TINYINT(1) DEFAULT 1,
                                    remember_token VARCHAR(100) NULL,
                                    created_at TIMESTAMP NULL,
                                    updated_at TIMESTAMP NULL,
                                    UNIQUE(email)
                                ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                            ");
                        }
                        
                        // Check for credentials
                        $credential = DB::connection('tenant')
                            ->table('tenant_user_credentials')
                            ->where('email', $request->email)
                            ->first();
                            
                        Log::info('Credential check result', [
                            'credential_found' => $credential ? 'yes' : 'no',
                            'email' => $request->email
                        ]);
                                
                        // First try to check if this is a staff member directly from staff table
                        $staff = \App\Models\Staff\Staff::on('tenant')
                            ->where('email', $request->email)
                            ->first();
                            
                        if ($staff && Hash::check($request->password, $staff->password)) {
                            $request->session()->regenerate();
                            Auth::guard('staff')->login($staff);
                            
                            Log::info('Staff login successful via direct staff table lookup', [
                                'staff_id' => $staff->id,
                                'staff_email' => $staff->email,
                                'staff_role' => $staff->role,
                                'tenant_id' => $tenantId
                            ]);
                            
                            // Redirect based on role
                            if ($staff->role === 'instructor') {
                                return redirect()->route('tenant.instructor.dashboard', ['tenant' => $tenantId]);
                            }
                            
                            return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenantId]));
                        }
                                
                        // If no staff found or password mismatch, check the credentials table
                        if ($credential && Hash::check($request->password, $credential->password)) {
                            // Check if this is a staff member (potentially an instructor)
                            if (!$staff) {
                                $staff = \App\Models\Staff\Staff::on('tenant')
                                    ->where('email', $request->email)
                                    ->first();
                            }
                                
                            if ($staff) {
                                $request->session()->regenerate();
                                Auth::guard('staff')->login($staff);
                                
                                Log::info('Staff login successful via credentials table', [
                                    'staff_id' => $staff->id,
                                    'staff_email' => $staff->email,
                                    'staff_role' => $staff->role,
                                    'tenant_id' => $tenantId
                                ]);
                                
                                // Redirect based on role
                                if ($staff->role === 'instructor') {
                                    return redirect()->route('tenant.instructor.dashboard', ['tenant' => $tenantId]);
                                }
                                
                                return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenantId]));
                            }
                            
                            // Find the corresponding admin if no staff found
                            $admin = TenantAdmin::where('email', $request->email)
                                        ->where('tenant_id', $tenantId)
                                        ->first();
                            
                            if ($admin) {
                                $request->session()->regenerate();
                                Auth::guard('admin')->login($admin);
                                
                                Log::info('Tenant admin login successful via subdomain detection', [
                                    'admin_id' => $admin->id,
                                    'admin_email' => $admin->email,
                                    'tenant_id' => $tenantId
                                ]);
                                
                                return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenantId]));
                            } else {
                                Log::warning('Credentials found but no matching admin or staff in database', [
                                    'email' => $request->email,
                                    'tenant_id' => $tenantId
                                ]);
                            }
                        } else {
                            // Add more detailed debug information
                            $hashedPassword = $staff ? $staff->password : null;
                            Log::warning('Invalid login attempt - password mismatch or no credentials', [
                                'email' => $request->email,
                                'tenant_id' => $tenantId,
                                'credential_found' => $credential ? 'yes' : 'no',
                                'staff_found' => $staff ? 'yes' : 'no',
                                'staff_id' => $staff ? $staff->id : null,
                                'staff_role' => $staff ? $staff->role : null,
                                'password_provided' => !empty($request->password) ? 'yes' : 'no',
                                'password_hash_exists' => !empty($hashedPassword) ? 'yes' : 'no',
                                'password_correct' => $staff && Hash::check($request->password, $hashedPassword) ? 'yes' : 'no'
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error checking tenant database for credentials', [
                            'tenant_id' => $tenantId,
                            'email' => $request->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    // If we get here, the tenant exists but credentials were invalid
                    return back()->withErrors([
                        'email' => 'Invalid credentials for this tenant.',
                    ])->withInput($request->except('password'));
                }
            }
        }

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
            
            // Check for staff login first - this handles instructors
            try {
                $staff = \App\Models\Staff\Staff::where('email', $request->email)->first();
                
                if ($staff) {
                    Log::info('Staff found in tenant context', [
                        'staff_id' => $staff->id,
                        'staff_email' => $staff->email,
                        'staff_role' => $staff->role
                    ]);
                    
                    // Verify password
                    if (Hash::check($request->password, $staff->password)) {
                        // Login the staff member
                        Auth::guard('staff')->login($staff);
                        $request->session()->regenerate();
                        
                        Log::info('Staff login successful in tenant context', [
                            'staff_id' => $staff->id,
                            'staff_email' => $staff->email,
                            'staff_role' => $staff->role,
                            'tenant_id' => tenant('id')
                        ]);
                        
                        // Redirect based on role
                        if ($staff->role === 'instructor') {
                            return redirect()->route('tenant.instructor.dashboard', ['tenant' => tenant('id')]);
                        }
                        
                        return redirect()->intended(route('tenant.dashboard', ['tenant' => tenant('id')]));
                    } else {
                        Log::warning('Invalid password for staff', [
                            'email' => $request->email,
                            'tenant_id' => tenant('id')
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Staff authentication error', [
                    'email' => $request->email,
                    'tenant_id' => tenant('id'),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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
            
            // If admin exists, check credentials from tenant's own database
            if ($admin) {
                try {
                    // Ensure tenant_user_credentials table exists
                    if (!Schema::connection('tenant')->hasTable('tenant_user_credentials')) {
                        Log::warning('tenant_user_credentials table doesn\'t exist in tenant database. Creating it...', [
                            'tenant_id' => tenant('id')
                        ]);
                        
                        DB::connection('tenant')->statement("
                            CREATE TABLE IF NOT EXISTS tenant_user_credentials (
                                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                email VARCHAR(255) NOT NULL,
                                password VARCHAR(255) NOT NULL, 
                                user_type ENUM('admin', 'staff', 'student') DEFAULT 'admin',
                                user_id BIGINT UNSIGNED NULL,
                                is_active TINYINT(1) DEFAULT 1,
                                remember_token VARCHAR(100) NULL,
                                created_at TIMESTAMP NULL,
                                updated_at TIMESTAMP NULL,
                                UNIQUE(email)
                            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                        ");
                    }
                    
                    // Look for credentials in the tenant's database
                    $credential = DB::connection('tenant')
                                ->table('tenant_user_credentials')
                                ->where('email', $request->email)
                                ->first();
                    
                    Log::info('Tenant credential check result', [
                        'credential_found' => $credential ? 'yes' : 'no',
                        'email' => $request->email,
                        'tenant_id' => tenant('id'),
                        'tenant_database' => DB::connection('tenant')->getDatabaseName(),
                        'password_check' => $credential ? (Hash::check($request->password, $credential->password) ? 'valid' : 'invalid') : 'no credential to check'
                    ]);
                    
                    // Check if credentials exist and password is correct
                    if ($credential && Hash::check($request->password, $credential->password)) {
                        $request->session()->regenerate();
                        Auth::guard('admin')->login($admin);
                        
                        Log::info('Tenant admin login successful', [
                            'admin_id' => $admin->id,
                            'admin_email' => $admin->email,
                            'tenant_id' => tenant('id')
                        ]);
                        
                        return redirect()->intended(route('tenant.dashboard'));
                    } else {
                        Log::warning('Invalid credentials for tenant admin', [
                            'email' => $request->email,
                            'tenant_id' => tenant('id')
                        ]);
                        return back()->withErrors([
                            'email' => 'Invalid credentials.',
                        ])->withInput($request->except('password'));
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking tenant credentials: ' . $e->getMessage(), [
                        'exception' => $e->getTraceAsString()
                    ]);
                    return back()->withErrors([
                        'email' => 'Authentication error. Please try again.',
                    ])->withInput($request->except('password'));
                }
            }
        } else {
            // For central domain
            $admin = TenantAdmin::where('email', $request->email)->first();
            
            // Check if admin exists and if they can login centrally
            if ($admin) {
                // Only allow admins with can_login_central=true or super_admin role to login centrally
                if ($admin->role !== 'super_admin' && !$admin->can_login_central) {
                    Log::warning('Central domain - Admin cannot login centrally', [
                        'email' => $request->email,
                        'role' => $admin->role,
                        'can_login_central' => $admin->can_login_central
                    ]);
                    return back()->withErrors([
                        'email' => 'You are not authorized to login through the central system. Please use your tenant subdomain.',
                    ])->withInput($request->except('password'));
                }
                
                // Check if admin's tenant is approved
                if ($admin->tenant && !$isStudentEmail && $admin->tenant->status !== 'approved') {
                    Log::warning('Central domain - Tenant not approved and not student email', [
                        'email' => $request->email,
                        'tenant_id' => $admin->tenant->id,
                        'tenant_status' => $admin->tenant->status
                    ]);
                    return back()->with('show_approval_modal', true)
                        ->withInput($request->except('password'));
                }
                
                // Find credentials and verify password
                $credential = TenantCredential::where('email', $request->email)
                                ->where('tenant_admin_id', $admin->id)
                                ->first();
                                
                if ($credential && Hash::check($request->password, $credential->password)) {
                    $request->session()->regenerate();
                    Auth::guard('admin')->login($admin);
                    
                    // For super_admin with can_login_central=true, go to central dashboard
                    if ($admin->role === 'super_admin' && $admin->can_login_central) {
                        return redirect()->route('superadmin.dashboard');
                    }
                    
                    // For other admins, redirect to their tenant
                    $domain = $admin->tenant->domains->first()->domain;
                    $port = request()->getPort();
                    $url = $domain;
                    if ($port && $port != 80) {
                        $url .= ':' . $port;
                    }
                    return redirect()->to('http://' . $url . '/dashboard');
                } else {
                    Log::warning('Invalid credentials for central login', [
                        'email' => $request->email
                    ]);
                    return back()->withErrors([
                        'email' => 'Invalid credentials.',
                    ])->withInput($request->except('password'));
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