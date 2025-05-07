<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        // Get all tenants with payment information
        $tenants = Tenant::whereNotNull('data->last_payment_date')
            ->orderBy('data->last_payment_date', 'desc')
            ->paginate(10);
            
        return view('superadmin.payments.index', compact('tenants'));
    }
    
    public function show($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('superadmin.payments.show', compact('tenant'));
    }
    
    public function subscriptionLogs()
    {
        // Get all tenants with subscription plan changes
        $tenants = Tenant::whereNotNull('data->subscription_plan')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
            
        return view('superadmin.payments.subscription-logs', compact('tenants'));
    }
    
    public function paymentStatus()
    {
        // Get tenants with different payment statuses
        $paidTenants = Tenant::where('data->payment_status', 'paid')->count();
        $pendingTenants = Tenant::where('data->payment_status', 'pending')->count();
        $overdueTenants = Tenant::where('data->payment_status', 'overdue')->count();
        
        // Get recent payments
        $recentPayments = Tenant::whereNotNull('data->last_payment_date')
            ->orderBy('data->last_payment_date', 'desc')
            ->take(10)
            ->get();
            
        return view('superadmin.payments.status', compact(
            'paidTenants',
            'pendingTenants',
            'overdueTenants',
            'recentPayments'
        ));
    }
} 