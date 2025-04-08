<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantAdmin;
use App\Mail\TenantRegistrationPending;
use Illuminate\Support\Facades\Mail;
use App\Helpers\PasswordGenerator;

class Controller extends BaseController
{
    public function register()
    {
        return view('register');
    }

    public function registerSave(Request $request)
    {
        try {
            $subdomain = strtolower($request->subdomain);
            if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $subdomain)) {
                throw new \Exception('Invalid subdomain format');
            }

            // Generate a secure password
            $password = PasswordGenerator::generate(random_int(10, 15));

            // Create tenant first with pending status
            $tenant = new Tenant();
            $tenant->id = $subdomain;
            $tenant->tenant_name = $request->name;
            $tenant->tenant_email = $request->admin_email;
            $tenant->status = 'pending';
            $tenant->subscription_plan = $request->subscription_plan ?? 'basic';
            
            // Set the JSON data for backward compatibility and additional fields
            $tenant->data = [
                'name' => $request->name,
                'status' => 'pending',
                'subscription_plan' => $request->subscription_plan ?? 'basic',
                'created_at' => now(),
                'admin_name' => $request->admin_name,
                'admin_email' => $request->admin_email,
                'payment_status' => 'unpaid'
            ];
            
            $tenant->save();
            
            // Log successful tenant creation
            \Log::info("Tenant registered", [
                'tenant_id' => $tenant->id,
                'name' => $tenant->tenant_name,
                'admin_email' => $tenant->tenant_email
            ]);

            // Create domain
            $domain = $subdomain . '.' . env('CENTRAL_DOMAIN');
            $tenant->domains()->create(['domain' => $domain]);

            // The tenant database will be created automatically by the tenancy system
            // using our custom database manager with separate credentials
            // We'll still create a record in tenant_databases
            $databaseName = 'tenant_' . $subdomain;
            $tenantDatabase = new TenantDatabase([
                'tenant_id' => $tenant->id,
                'database_name' => $databaseName,
                'database_host' => env('DB_HOST', 'localhost'),
                'database_port' => env('DB_PORT', '3306')
                // Username and password will be set by the database manager
            ]);
            $tenantDatabase->save();

            // Create tenant admin with generated password
            $tenantAdmin = new TenantAdmin([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($password),
                'tenant_id' => $tenant->id
            ]);
            $tenantAdmin->save();

            // Send registration pending email with credentials
            try {
                Mail::to($request->admin_email)->send(new TenantRegistrationPending($tenant, $password));
            } catch (\Exception $e) {
                \Log::error('Failed to send registration email: ' . $e->getMessage());
                // Continue with registration even if email fails
            }

            // Automatically set up database for this tenant
            try {
                \Log::info("Auto-setting up database for new tenant: {$tenant->id}");
                \Artisan::call('db:setup-tenant', [
                    'tenant' => $tenant->id
                ]);
                \Log::info("Database setup response: " . \Artisan::output());
            } catch (\Exception $e) {
                \Log::error("Error auto-setting up database: " . $e->getMessage());
                // We continue even if database setup fails
                // The admin can manually set up the database later
            }

            // Return to registration success page
            return redirect()->route('register.success');

        } catch(\Exception $e) {
            \Log::error('Tenant creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function registerSuccess()
    {
        return view('register-success');
    }

    public function testEmail()
    {
        try {
            $testEmail = env('MAIL_FROM_ADDRESS');
            
            // Create a test tenant
            $tenant = new Tenant();
            $tenant->id = 'test-dept';
            $tenant->tenant_name = 'Test Department';
            $tenant->tenant_email = $testEmail;
            $tenant->status = 'pending';
            $tenant->data = [
                'name' => 'Test Department',
                'admin_name' => 'Test Admin',
                'admin_email' => $testEmail
            ];

            // Generate a test password
            $password = PasswordGenerator::generate(12);

            // Send test email
            Mail::to($testEmail)->send(new TenantRegistrationPending($tenant, $password));

            return 'Test email sent successfully! Check your inbox at ' . $testEmail;
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage());
            return 'Test email failed: ' . $e->getMessage();
        }
    }
}
