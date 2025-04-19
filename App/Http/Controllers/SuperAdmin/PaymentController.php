<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\SubscriptionUpgrade;
use App\Models\Tenant;
use App\Exports\PaymentsExport;
use App\Exports\PaymentsPdfExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'subscription.plan']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('plan')) {
            $query->whereHas('subscription.plan', function ($q) use ($request) {
                $q->where('id', $request->plan);
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(10);
        $plans = Plan::all();

        // Calculate statistics
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $paidSubscriptions = Payment::where('status', 'completed')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $overduePayments = Payment::where('status', 'failed')->count();

        // Get premium upgrades
        $premiumUpgrades = SubscriptionUpgrade::with('tenant')->orderBy('created_at', 'desc')->get();
        $pendingUpgrades = SubscriptionUpgrade::where('status', 'pending')->count();

        return view('SuperAdmin.payments.index', compact(
            'payments',
            'plans',
            'totalRevenue',
            'paidSubscriptions',
            'pendingPayments',
            'overduePayments',
            'premiumUpgrades',
            'pendingUpgrades'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'subscription.plan']);
        return view('SuperAdmin.payments.show', compact('payment'));
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        $payment->user->update(['payment_status' => 'paid']);

        return redirect()->back()->with('success', 'Payment marked as completed successfully.');
    }

    /**
     * Approve a subscription upgrade request
     *
     * @param int $id The ID of the subscription upgrade to approve
     * @return \Illuminate\Http\Response
     */
    public function approveUpgrade($id)
    {
        try {
            // Find the upgrade request
            $upgrade = SubscriptionUpgrade::findOrFail($id);
            
            // Check if it's already processed
            if ($upgrade->status !== 'pending') {
                return redirect()->back()->with('info', 'This upgrade request has already been processed.');
            }
            
            // Find the tenant
            $tenant = Tenant::where('id', $upgrade->tenant_id)->first();
            
            if (!$tenant) {
                return redirect()->back()->with('error', 'Tenant not found for this upgrade request.');
            }
            
            // Get the old plan for comparison
            $oldPlan = $tenant->subscription_plan;
            
            // Update the tenant's subscription plan
            $tenant->subscription_plan = $upgrade->to_plan;
            
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
                'plan' => $upgrade->to_plan,
                'status' => 'paid',
                'amount' => $upgrade->amount,
                'payment_method' => $upgrade->payment_method,
                'receipt_number' => $upgrade->receipt_number,
                'notes' => $oldPlan === 'basic' ? 'Upgraded from Basic plan' : null
            ];
            
            // Update the tenant data
            $tenant->data = $data;
            $tenant->save();
            
            // Update the upgrade request
            $upgrade->status = 'approved';
            $upgrade->processed_at = now();
            $upgrade->processed_by = auth()->user()->name ?? 'Super Admin';
            $upgrade->notes = 'Approved and processed successfully';
            $upgrade->save();
            
            // Log the subscription upgrade
            Log::info('Tenant subscription upgraded', [
                'tenant_id' => $tenant->id,
                'upgrade_id' => $upgrade->id,
                'old_plan' => $oldPlan,
                'new_plan' => $upgrade->to_plan,
                'payment_status' => $data['payment_status'],
                'subscription_ends_at' => $data['subscription_ends_at'] ?? null
            ]);
            
            return redirect()->back()->with('success', "Subscription for {$tenant->tenant_name} has been upgraded to Premium successfully!");
            
        } catch(\Exception $e) {
            Log::error('Failed to approve subscription upgrade: ' . $e->getMessage(), [
                'upgrade_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to approve subscription: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $filters = $request->only(['status', 'plan', 'date_from', 'date_to']);
        $format = $request->get('format', 'csv');

        switch ($format) {
            case 'excel':
                return Excel::download(new PaymentsExport($filters), 'payments-report.xlsx');
            
            case 'pdf':
                $pdfExport = new PaymentsPdfExport($filters);
                return $pdfExport->export();
            
            default:
                return Excel::download(new PaymentsExport($filters), 'payments-report.csv');
        }
    }
} 