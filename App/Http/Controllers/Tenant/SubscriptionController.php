<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionUpgrade;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Handle tenant subscription upgrade request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function upgrade(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'payment_method' => 'required|in:bank_transfer,gcash,paymaya',
                'reference_number' => 'required|string|max:255',
                'plan' => 'nullable|in:premium,ultimate'
            ]);

            // Get current tenant
            $tenantId = tenant('id');
            $tenant = Tenant::where('id', $tenantId)->first();

            // Check if tenant exists
            if (!$tenant) {
                Log::error('Tenant not found during upgrade process', [
                    'tenant_id' => $tenantId
                ]);
                return back()->with('error', 'Unable to process your request. Please contact support.');
            }

            // Get requested plan or default to premium
            $requestedPlan = $request->plan ?? 'premium';

            // Check if tenant is already on the requested plan or higher
            if ($tenant->subscription_plan === $requestedPlan) {
                return back()->with('info', 'Your account is already on the ' . ucfirst($requestedPlan) . ' plan.');
            }
            
            // If requesting premium but already on ultimate, inform them
            if ($requestedPlan === 'premium' && $tenant->subscription_plan === 'ultimate') {
                return back()->with('info', 'Your account is already on the Ultimate plan, which includes all Premium features.');
            }
            
            // Get the old plan for comparison
            $oldPlan = $tenant->subscription_plan;
            
            // Set amount based on the requested plan
            $amount = $requestedPlan === 'premium' ? 999.00 : 1999.00;
            
            // Create subscription upgrade record with approved status
            $upgrade = new SubscriptionUpgrade();
            $upgrade->tenant_id = $tenantId;
            $upgrade->from_plan = $tenant->subscription_plan;
            $upgrade->to_plan = $requestedPlan;
            $upgrade->payment_method = $request->payment_method;
            $upgrade->receipt_number = $request->reference_number;
            $upgrade->reference_number = $request->reference_number;
            $upgrade->amount = $amount; // Set based on plan
            $upgrade->status = 'approved'; // Auto-approve
            $upgrade->processed_at = now(); // Set process time
            $upgrade->processed_by = 'Auto-System';
            $upgrade->notes = 'Automatically approved on submission';
            $upgrade->save();

            // UPDATE THE TENANT'S SUBSCRIPTION PLAN IMMEDIATELY
            // Update the tenant's subscription plan
            $tenant->subscription_plan = $requestedPlan;
            
            // Update tenant data with subscription details
            $data = $tenant->data ?? [];
            
            // Initialize payment history if it doesn't exist
            if (!isset($data['payment_history'])) {
                $data['payment_history'] = [];
            }
            
            // Set subscription details
            $data['payment_status'] = 'paid';
            $data['subscription_starts_at'] = now()->format('Y-m-d H:i:s');
            $data['subscription_ends_at'] = now()->addYear()->format('Y-m-d H:i:s');
            $data['last_payment_date'] = now()->format('Y-m-d H:i:s');
            $data['payment_amount'] = $upgrade->amount;
            
            // Add payment record to history
            $data['payment_history'][] = [
                'date' => now()->format('Y-m-d H:i:s'),
                'plan' => ucfirst($requestedPlan),
                'status' => 'paid',
                'amount' => $upgrade->amount,
                'payment_method' => $request->payment_method,
                'receipt_number' => $request->reference_number,
                'notes' => $oldPlan === 'basic' ? 'Auto-upgraded from Basic plan' : null
            ];
            
            // Update the tenant data
            $tenant->data = $data;
            $tenant->save();

            // Log the auto-upgrade
            Log::info('Tenant subscription auto-upgraded', [
                'tenant_id' => $tenantId,
                'upgrade_id' => $upgrade->id,
                'payment_method' => $request->payment_method,
                'receipt_number' => $request->reference_number,
                'old_plan' => $oldPlan,
                'new_plan' => ucfirst($requestedPlan),
                'subscription_ends_at' => $data['subscription_ends_at']
            ]);

            return redirect()->route('profile.index', ['tenant' => $tenantId])
                ->with('success', 'Your account has been successfully upgraded to ' . ucfirst($requestedPlan) . '! All ' . ucfirst($requestedPlan) . ' features are now available.');

        } catch (\Exception $e) {
            Log::error('Error processing subscription upgrade', [
                'tenant_id' => tenant('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'An error occurred while processing your request: ' . $e->getMessage());
        }
    }
} 