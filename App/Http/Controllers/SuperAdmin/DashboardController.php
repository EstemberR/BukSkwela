<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalTenants' => User::where('role', 'tenant')->count(),
            'activeTenants' => User::where('role', 'tenant')->where('status', 'active')->count(),
            'activeSubscriptions' => User::where('role', 'tenant')
                ->where('status', 'active')
                ->where('subscription_status', 'active')
                ->count(),
            'pendingSubscriptions' => User::where('role', 'tenant')
                ->where('status', 'active')
                ->where('subscription_status', 'pending')
                ->count(),
            'monthlyRevenue' => Payment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'completed')
                ->sum('amount'),
            'recentTenants' => User::where('role', 'tenant')
                ->latest()
                ->take(5)
                ->get(),
            'recentPayments' => Payment::with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('SuperAdmin.Dashboard', $data);
    }
}