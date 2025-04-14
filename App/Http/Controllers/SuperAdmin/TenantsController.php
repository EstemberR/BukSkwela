<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\TenantApproved;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantsController extends Controller
{
    public function index()
    {
        $tenants = Tenant::orderBy('created_at', 'desc')->paginate(10);
        
        // Synchronize payment status with subscription plan for all tenants
        foreach ($tenants as $tenant) {
            $this->syncPaymentStatus($tenant);
        }
        
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show($id)
    {
        $tenant = Tenant::findOrFail($id);
        
        // Synchronize payment status with subscription plan
        $tenant = $this->syncPaymentStatus($tenant);
        
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
            
            // Automatically set up and migrate the tenant database
            try {
                Log::info("Auto-setting up database for newly approved tenant: {$tenant->id}");
                
                // Use the dedicated setup-tenant command to create a separate database
                \Artisan::call('db:setup-tenant', [
                    'tenant' => $tenant->id
                ]);
                $setupOutput = \Artisan::output();
                Log::info("Database setup output: " . $setupOutput);
                
                // No need to run migrations separately as db:setup-tenant does this
                
                // Fix the staff table structure to ensure it's correct
                \Artisan::call('tenant:fix-staff', [
                    'tenant' => $tenant->id,
                    '--force' => true
                ]);
                Log::info("Staff table fix response: " . \Artisan::output());
                
                // Verify the database exists
                $databaseName = 'tenant_' . $tenant->id;
                $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
                
                if (!empty($dbExists)) {
                    Log::info("Verified database {$databaseName} exists for tenant {$tenant->id}");
                } else {
                    Log::error("Failed to create database {$databaseName} for tenant {$tenant->id}");
                }
                
                Log::info("Tenant database auto-setup complete for tenant: {$tenant->id}");
            } catch (\Exception $e) {
                Log::error("Error auto-setting up database for approved tenant: " . $e->getMessage());
                // We continue even if database setup fails
                // The admin can manually set up the database later
            }
            
            // Send approval email to tenant
            try {
                if ($tenant->tenant_email) {
                    Mail::to($tenant->tenant_email)->send(new TenantApproved($tenant));
                    Log::info("Approval email sent to tenant: {$tenant->id} at {$tenant->tenant_email}");
                } else {
                    Log::warning("Could not send approval email to tenant {$tenant->id} - no email address available");
                }
            } catch (\Exception $e) {
                Log::error("Failed to send tenant approval email: " . $e->getMessage(), [
                    'tenant_id' => $tenant->id,
                    'email' => $tenant->tenant_email
                ]);
                // Continue even if email sending fails
            }
            
            // Log the status change
            Log::info('Tenant approved: ' . $id, ['tenant_id' => $id, 'status' => $tenant->status]);
            
            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant approved successfully. Database was automatically set up and approval email was sent.')
                ->with('tab', 'active');
        } catch(\Exception $e) {
            Log::error('Failed to approve tenant: ' . $e->getMessage(), ['tenant_id' => $id]);
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
                'subscription_plan' => 'required|in:basic,premium'
            ]);
            
            $tenant = Tenant::findOrFail($id);
            
            // Get the old plan for comparison
            $oldPlan = $tenant->subscription_plan;
            
            // Skip if current plan is the same as requested plan
            if ($oldPlan === $request->subscription_plan) {
                return redirect()->back()
                    ->with('info', 'Tenant is already on the ' . ucfirst($request->subscription_plan) . ' plan.');
            }
            
            // Update subscription plan
            $tenant->subscription_plan = $request->subscription_plan;
            
            // Set default data array if it doesn't exist
            $data = $tenant->data ?? [];
            
            // Handle enterprise plan users
            if ($oldPlan === 'enterprise') {
                if ($request->subscription_plan === 'premium') {
                    $data['payment_status'] = 'paid';
                    $data['subscription_ends_at'] = now()->addYear()->format('Y-m-d H:i:s');
                } else {
                    // Downgrading to basic
                    $data['payment_status'] = 'downgraded';
                }
                
                // Add to payment history
                if (!isset($data['payment_history'])) {
                    $data['payment_history'] = [];
                }
                
                $data['payment_history'][] = [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'plan' => $request->subscription_plan,
                    'status' => $request->subscription_plan === 'premium' ? 'paid' : 'downgraded',
                    'amount' => $request->subscription_plan === 'premium' ? 29.99 : 0,
                    'notes' => 'Downgraded from Enterprise plan'
                ];
            }
            // Always set premium plan to paid status
            else if ($request->subscription_plan === 'premium') {
                // Set payment status to paid
                $data['payment_status'] = 'paid';
                $data['subscription_ends_at'] = now()->addYear()->format('Y-m-d H:i:s');
                
                // Add to payment history
                if (!isset($data['payment_history'])) {
                    $data['payment_history'] = [];
                }
                
                $data['payment_history'][] = [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'plan' => 'premium',
                    'status' => 'paid',
                    'amount' => 29.99,
                    'notes' => $oldPlan === 'basic' ? 'Upgraded from Basic plan' : null
                ];
            } 
            // Set downgrade status if downgrading from premium/enterprise to basic
            else if ($request->subscription_plan === 'basic' && in_array($oldPlan, ['premium', 'enterprise'])) {
                $data['payment_status'] = 'downgraded';
                
                // Add to payment history
                if (!isset($data['payment_history'])) {
                    $data['payment_history'] = [];
                }
                
                $data['payment_history'][] = [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'plan' => 'basic',
                    'status' => 'downgraded',
                    'amount' => 0,
                    'notes' => 'Downgraded from ' . ucfirst($oldPlan) . ' plan'
                ];
            }
            
            // Update the tenant data
            $tenant->data = $data;
            
            // Save the tenant
            $tenant->save();
            
            // Force refresh from database to ensure data is updated
            $tenant->refresh();
            
            // Log the subscription change
            Log::info('Tenant subscription updated: ' . $id, [
                'tenant_id' => $id, 
                'old_plan' => $oldPlan,
                'new_plan' => $request->subscription_plan,
                'payment_status' => $data['payment_status'] ?? 'unknown'
            ]);
            
            // Success message with more detail
            $successMessage = "Subscription for <strong>{$tenant->tenant_name}</strong> updated from <span class='badge bg-secondary'>" . 
                              ucfirst($oldPlan) . "</span> to <span class='badge bg-" . 
                              ($request->subscription_plan === 'premium' ? 'warning' : 'info') . 
                              "'>" . ucfirst($request->subscription_plan) . "</span> successfully.";
            
            // Add payment status to success message
            $paymentStatus = $data['payment_status'] ?? 'unknown';
            $paymentStatusClass = 'secondary';
            
            if ($paymentStatus === 'paid') {
                $paymentStatusClass = 'success';
            } else if ($paymentStatus === 'downgraded') {
                $paymentStatusClass = 'secondary';
            }
            
            $successMessage .= " Payment status: <span class='badge bg-{$paymentStatusClass}'>" . ucfirst($paymentStatus) . "</span>";
            
            // Determine if the request came from the tenant index page
            $referer = request()->headers->get('referer');
            if (strpos($referer, 'tenants') !== false && strpos($referer, 'show') === false) {
                return redirect()->route('superadmin.tenants.index')
                    ->with('success', $successMessage);
            }
            
            return redirect()->back()
                ->with('success', $successMessage);
        } catch(\Exception $e) {
            Log::error('Failed to update tenant subscription: ' . $e->getMessage(), ['tenant_id' => $id]);
            return redirect()->back()
                ->with('error', 'Failed to update tenant subscription: ' . $e->getMessage());
        }
    }

    /**
     * Synchronize payment status with subscription plan
     * This ensures the payment status is consistent with the subscription plan
     *
     * @param Tenant $tenant The tenant to synchronize
     * @return Tenant The updated tenant
     */
    protected function syncPaymentStatus(Tenant $tenant)
    {
        // Skip if tenant doesn't have a data array yet
        if (!$tenant->data) {
            return $tenant;
        }
        
        $data = $tenant->data;
        $changed = false;
        
        // Make sure payment_status exists in data
        if (!isset($data['payment_status'])) {
            $data['payment_status'] = 'unpaid';
            $changed = true;
        }
        
        // If plan is premium but payment status is not paid, fix it
        if ($tenant->subscription_plan === 'premium' && $data['payment_status'] !== 'paid') {
            $data['payment_status'] = 'paid';
            $data['subscription_ends_at'] = $data['subscription_ends_at'] ?? now()->addYear()->format('Y-m-d H:i:s');
            $changed = true;
        }
        
        // If plan is basic but was downgraded from premium, ensure status is downgraded
        if ($tenant->subscription_plan === 'basic' && 
            isset($data['payment_history']) && 
            is_array($data['payment_history']) && 
            count($data['payment_history']) > 0) {
            
            // Check if there was a premium plan before
            $hadPremium = false;
            foreach ($data['payment_history'] as $payment) {
                if (isset($payment['plan']) && $payment['plan'] === 'premium') {
                    $hadPremium = true;
                    break;
                }
            }
            
            if ($hadPremium && $data['payment_status'] !== 'downgraded') {
                $data['payment_status'] = 'downgraded';
                $changed = true;
            }
        }
        
        // If there were changes, save them
        if ($changed) {
            $tenant->data = $data;
            $tenant->save();
            $tenant->refresh();
        }
        
        return $tenant;
    }
} 