<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantAdmin;

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

            // Create tenant admin
            $tenantAdmin = new TenantAdmin([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id
            ]);
            $tenantAdmin->save();

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
}
