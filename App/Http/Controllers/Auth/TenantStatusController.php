<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantStatusController extends Controller
{
    /**
     * Check tenant status and show appropriate page
     */
    public function checkStatus()
    {
        if (tenant()) {
            $tenant = Tenant::find(tenant('id'));
            
            if (!$tenant) {
                return response()->view('tenant.inactive', [
                    'message' => 'Tenant not found.'
                ], 404);
            }
            
            // If tenant is pending approval
            if ($tenant->status === 'pending') {
                return response()->view('tenant.pending-approval', [
                    'tenant' => $tenant,
                    'message' => 'Your tenant account is currently pending approval.'
                ]);
            }
            
            // If tenant is rejected or disabled
            if (in_array($tenant->status, ['rejected', 'disabled', 'denied'])) {
                return response()->view('tenant.inactive', [
                    'message' => 'Your tenant account has been ' . $tenant->status . '. Please contact the administrator.'
                ], 403);
            }
            
            // If tenant is approved but user is trying to access status page
            if ($tenant->status === 'approved') {
                return redirect()->route('tenant.login')
                    ->with('success', 'Your tenant account is active. You can now log in.');
            }
        }
        
        // If not a tenant subdomain, redirect to main site
        return redirect(env('APP_URL', 'http://localhost'));
    }
} 