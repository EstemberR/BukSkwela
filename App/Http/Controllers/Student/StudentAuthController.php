<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/student/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Try to set up tenant connection early
        try {
            \App\Helpers\TenantConnection::setup();
        } catch (\Exception $e) {
            \Log::warning('Failed to set up tenant connection in controller constructor: ' . $e->getMessage());
        }
        
        $this->middleware('guest:student')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('student');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if email is a student email (ends with @student.buksu.edu.ph)
        if (!Str::endsWith($request->email, '@student.buksu.edu.ph')) {
            return redirect()->back()->withErrors([
                'email' => 'This is not a valid student email. Student emails must end with @student.buksu.edu.ph'
            ]);
        }

        // Get host and extract subdomain for tenant database connection
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = null;
        
        // For localhost with port
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            if (count($parts) >= 2) {
                $subdomain = $parts[0];
            }
        } else if (count($parts) > 2) {
            $subdomain = $parts[0];
        }
        
        if ($subdomain && $subdomain != 'www') {
            // Configure database connection to use tenant database
            $tenantDb = "tenant_{$subdomain}";
            config(['database.connections.tenant.database' => $tenantDb]);
            \DB::purge('tenant');
            \DB::reconnect('tenant');
            
            // Set default connection for this request
            \DB::setDefaultConnection('tenant');
            
            // Set database connection for the auth guard
            config(['auth.guards.student.connection' => 'tenant']);
            config(['auth.providers.students.connection' => 'tenant']);
            
            // Store tenant info in session for later use
            session(['tenant_db' => $tenantDb]);
            session(['tenant_subdomain' => $subdomain]);
            
            // Store in config for access from anywhere
            config(['app.tenant_db' => $tenantDb]);
            config(['app.tenant_subdomain' => $subdomain]);
        }

        // Try direct database check
        try {
            $student = \DB::connection('tenant')
                ->table('students')
                ->where('email', $request->email)
                ->first();
                
            if (!$student) {
                return redirect()->back()->withErrors([
                    'email' => 'No student found with this email in our records.'
                ]);
            }
            
            // Check password manually using Hash facade
            if (!\Illuminate\Support\Facades\Hash::check($request->password, $student->password)) {
                return redirect()->back()->withErrors([
                    'email' => 'Invalid password. Please try again.'
                ]);
            }
            
            // Login manually
            Auth::guard('student')->loginUsingId($student->id);
            
            // Store student info in session
            session(['student_id' => $student->id]);
            session(['student_name' => $student->name]);  
            session(['student_email' => $student->email]);
            
            \Log::info('Student logged in successfully', [
                'student_id' => $student->id,
                'tenant_db' => $tenantDb ?? 'Not set'
            ]);
            
            // Make sure we're redirecting with proper settings
            return redirect()->intended($this->redirectPath())->with([
                'tenant_db' => $tenantDb ?? null,
                'tenant_subdomain' => $subdomain ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Student login error: ' . $e->getMessage(), [
                'tenant_db' => $tenantDb ?? 'Not set',
                'subdomain' => $subdomain ?? 'Not set',
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors([
                'email' => 'Error connecting to the database: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/student/login');
    }
} 