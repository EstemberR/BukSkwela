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
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route('tenant.student.dashboard');
    }

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
        return view('tenant.students.auth.login');
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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
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

        // Log the login attempt
        \Log::info('Student login attempt', [
            'email' => $request->email,
            'host' => $request->getHost(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent()
        ]);

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
        
        // Log the detected subdomain
        \Log::info('Subdomain detection', [
            'host' => $host,
            'parts' => $parts,
            'detected_subdomain' => $subdomain
        ]);
        
        if ($subdomain && $subdomain != 'www') {
            // Configure database connection to use tenant database
            $tenantDb = "tenant_{$subdomain}";
            
            \Log::info('Setting up tenant connection', [
                'tenant_db' => $tenantDb,
                'subdomain' => $subdomain
            ]);
            
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
            \Log::info('Checking student credentials in database', [
                'connection' => \DB::getDefaultConnection(),
                'tenant_db' => config('database.connections.tenant.database'),
                'email' => $request->email
            ]);
            
            $student = \DB::connection('tenant')
                ->table('students')
                ->where('email', $request->email)
                ->first();
            
            \Log::info('Student lookup result', [
                'email' => $request->email,
                'found' => $student ? 'yes' : 'no',
                'student_id' => $student->id ?? 'N/A',
                'status' => $student->status ?? 'N/A'
            ]);
                
            if (!$student) {
                return redirect()->back()->withErrors([
                    'email' => 'No student found with this email in our records.'
                ]);
            }
            
            // Check password manually using Hash facade
            if (!\Illuminate\Support\Facades\Hash::check($request->password, $student->password)) {
                \Log::warning('Invalid password for student', [
                    'email' => $request->email,
                    'student_id' => $student->id
                ]);
                
                return redirect()->back()->withErrors([
                    'email' => 'Invalid password. Please try again.'
                ]);
            }
            
            // Check status (allow active, regular, probation, irregular)
            $activeStatuses = ['active', 'regular', 'probation', 'irregular'];
            if (!in_array($student->status, $activeStatuses)) {
                \Log::warning('Inactive student attempted login', [
                    'email' => $request->email,
                    'student_id' => $student->id,
                    'status' => $student->status
                ]);
                
                return redirect()->back()->withErrors([
                    'email' => 'Your account is inactive. Please contact the administrator.'
                ]);
            }
            
            // Login manually
            Auth::guard('student')->loginUsingId($student->id);
            
            if (Auth::guard('student')->check()) {
                \Log::info('Student successfully authenticated', [
                    'student_id' => $student->id,
                    'auth_id' => Auth::guard('student')->id()
                ]);
            } else {
                \Log::error('Failed to authenticate student', [
                    'student_id' => $student->id
                ]);
                
                return redirect()->back()->withErrors([
                    'email' => 'Authentication failed. Please try again.'
                ]);
            }
            
            // Store student info in session
            session(['student_id' => $student->id]);
            session(['student_name' => $student->name]);  
            session(['student_email' => $student->email]);
            
            \Log::info('Student logged in successfully', [
                'student_id' => $student->id,
                'tenant_db' => $tenantDb ?? 'Not set',
                'redirecting_to' => $this->redirectPath()
            ]);
            
            // Make sure we're redirecting with proper settings
            return redirect()->intended($this->redirectPath())->with([
                'success' => 'Login successful!',
                'tenant_db' => $tenantDb ?? null,
                'tenant_subdomain' => $subdomain ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Student login error: ' . $e->getMessage(), [
                'tenant_db' => $tenantDb ?? 'Not set',
                'subdomain' => $subdomain ?? 'Not set',
                'email' => $request->email,
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
        // Get the tenant ID before logout
        $tenantId = tenant('id');
        
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->has('redirect')) {
            // Use custom redirect URL if provided
            return redirect($request->input('redirect'));
        } else {
            // Default redirect to tenant login page
            return redirect('http://' . $tenantId . '.localhost:8000/login');
        }
    }
} 