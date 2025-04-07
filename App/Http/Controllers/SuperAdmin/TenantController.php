<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'tenant');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->has('subscription_plan')) {
            $query->where('subscription_plan', $request->subscription_plan);
        }

        $tenants = $query->latest()->get();

        return view('SuperAdmin.tenants.index', compact('tenants'));
    }

    public function show(User $tenant)
    {
        return view('SuperAdmin.tenants.show', compact('tenant'));
    }

    public function approve(User $tenant)
    {
        $tenant->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Tenant approved successfully.');
    }

    public function reject(User $tenant)
    {
        $tenant->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Tenant rejected successfully.');
    }

    public function disable(User $tenant)
    {
        $tenant->update(['status' => 'disabled']);
        return redirect()->back()->with('success', 'Tenant disabled successfully.');
    }

    public function enable(User $tenant)
    {
        $tenant->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Tenant enabled successfully.');
    }

    public function downgrade(User $tenant)
    {
        $tenant->update(['subscription_plan' => 'basic']);
        return redirect()->back()->with('success', 'Tenant plan downgraded successfully.');
    }
} 