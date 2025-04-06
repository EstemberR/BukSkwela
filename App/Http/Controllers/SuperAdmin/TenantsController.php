<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function index()
    {
        $tenants = Tenant::orderBy('created_at', 'desc')->paginate(10);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function approve($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            // Set status directly on the model
            $tenant->status = 'approved';
            
            // Set default subscription if not set
            if (!$tenant->subscription_plan) {
                $tenant->subscription_plan = 'basic';
            }
            
            // Ensure tenant name is set
            if (!$tenant->tenant_name && isset($tenant->data['name'])) {
                $tenant->tenant_name = $tenant->data['name'];
            } else if (!$tenant->tenant_name) {
                $tenant->tenant_name = $tenant->id;
            }
            
            // Ensure tenant email is set
            if (!$tenant->tenant_email && isset($tenant->data['admin_email'])) {
                $tenant->tenant_email = $tenant->data['admin_email'];
            } else if (!$tenant->tenant_email && isset($tenant->data['email'])) {
                $tenant->tenant_email = $tenant->data['email'];
            }
            
            // Save the tenant
            $tenant->save();
            
            // Force refresh from database to ensure data is updated
            $tenant->refresh();
            
            // Log the status change
            \Log::info('Tenant approved: ' . $id, ['tenant_id' => $id, 'status' => $tenant->status]);
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant approved successfully.')
                ->with('tab', 'active');
        } catch(\Exception $e) {
            \Log::error('Failed to approve tenant: ' . $e->getMessage(), ['tenant_id' => $id]);
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to approve tenant: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->status = 'rejected';
            $tenant->save();
            $tenant->refresh();
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant rejected successfully.')
                ->with('tab', 'rejected');
        } catch(\Exception $e) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to reject tenant: ' . $e->getMessage());
        }
    }

    public function enable($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->status = 'approved';
            $tenant->save();
            $tenant->refresh();
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant enabled successfully.')
                ->with('tab', 'active');
        } catch(\Exception $e) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to enable tenant: ' . $e->getMessage());
        }
    }

    public function disable($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->status = 'disabled';
            $tenant->save();
            $tenant->refresh();
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant disabled successfully.')
                ->with('tab', 'rejected');
        } catch(\Exception $e) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to disable tenant: ' . $e->getMessage());
        }
    }

    public function deny($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->status = 'denied';
            $tenant->save();
            $tenant->refresh();
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant denied successfully.')
                ->with('tab', 'rejected');
        } catch(\Exception $e) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to deny tenant: ' . $e->getMessage());
        }
    }

    public function downgradePlan($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            // Downgrade to basic plan if not already basic
            if ($tenant->subscription_plan !== 'basic') {
                // Store the old plan for reporting
                $oldPlan = $tenant->subscription_plan;
                
                // Set the plan to basic
                $tenant->subscription_plan = 'basic';
                
                // Update payment status in data
                $data = $tenant->data;
                if (isset($data['payment_status'])) {
                    $data['payment_status'] = 'downgraded';
                }
                
                // Add to payment history if it exists
                if (isset($data['payment_history'])) {
                    $data['payment_history'][] = [
                        'date' => now()->format('Y-m-d H:i:s'),
                        'plan' => 'basic',
                        'status' => 'downgraded',
                        'amount' => 0
                    ];
                }
                
                $tenant->data = $data;
                $tenant->save();
                $tenant->refresh();
                
                return redirect()->route('superadmin.tenants.index')
                    ->with('success', 'Tenant plan downgraded to basic successfully.')
                    ->with('tab', 'all');
            }
            
            return redirect()->route('superadmin.tenants.index')
                ->with('info', 'Tenant is already on the basic plan.')
                ->with('tab', 'all');
        } catch(\Exception $e) {
            return redirect()->route('superadmin.tenants.index')
                ->with('error', 'Failed to downgrade tenant plan: ' . $e->getMessage());
        }
    }

    public function updateSubscription($id, Request $request)
    {
        try {
            $request->validate([
                'subscription_plan' => 'required|in:basic,premium,enterprise'
            ]);
            
            $tenant = Tenant::findOrFail($id);
            
            // Get the old plan for comparison
            $oldPlan = $tenant->subscription_plan;
            
            // Update subscription plan
            $tenant->subscription_plan = $request->subscription_plan;
            
            // Add payment status change if upgrading from basic
            if ($oldPlan === 'basic' && in_array($request->subscription_plan, ['premium', 'enterprise'])) {
                // Update the data array for payment status
                $data = $tenant->data;
                $data['payment_status'] = 'pending';
                
                // Add to payment history
                if (!isset($data['payment_history'])) {
                    $data['payment_history'] = [];
                }
                
                $data['payment_history'][] = [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'plan' => $request->subscription_plan,
                    'status' => 'pending',
                    'amount' => $request->subscription_plan === 'premium' ? 29.99 : 49.99
                ];
                
                $tenant->data = $data;
            }
            
            // Save the tenant
            $tenant->save();
            
            // Force refresh from database to ensure data is updated
            $tenant->refresh();
            
            // Log the subscription change
            \Log::info('Tenant subscription updated: ' . $id, [
                'tenant_id' => $id, 
                'old_plan' => $oldPlan,
                'new_plan' => $request->subscription_plan
            ]);
            
            return redirect()->route('superadmin.tenants.show', $tenant->id)
                ->with('success', 'Tenant subscription updated to ' . ucfirst($request->subscription_plan) . ' successfully.');
        } catch(\Exception $e) {
            \Log::error('Failed to update tenant subscription: ' . $e->getMessage(), ['tenant_id' => $id]);
            return redirect()->route('superadmin.tenants.show', $id)
                ->with('error', 'Failed to update tenant subscription: ' . $e->getMessage());
        }
    }
} 