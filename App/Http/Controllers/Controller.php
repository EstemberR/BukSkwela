<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Tenant;
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

            // Create tenant first
            $tenant = Tenant::create([
                'id' => $subdomain,
                'data' => [
                    'name' => $request->name
                ]
            ]);

            // Create domain
            $domain = $subdomain . '.' . env('CENTRAL_DOMAIN');
            $tenant->domains()->create(['domain' => $domain]);

            // Create tenant admin
            $tenantAdmin = new TenantAdmin([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id
            ]);
            $tenantAdmin->save();

            // Redirect to the tenant's subdomain login page
            return redirect()->away('http://' . $domain . '/login')
                ->with('success', 'Registration successful! Please log in to your school portal.');

        } catch(\Exception $e) {
            \Log::error('Tenant creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
