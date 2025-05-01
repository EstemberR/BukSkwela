<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\SubscriptionUpgrade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class PaymentsPdfExport
{
    protected $exportData;
    protected $mergedData;

    public function __construct($exportData = [])
    {
        $this->exportData = $exportData;
        $this->prepareData();
    }

    /**
     * Prepare and merge payments and upgrades data
     */
    protected function prepareData()
    {
        $payments = isset($this->exportData['payments']) ? $this->exportData['payments'] : collect();
        $upgrades = isset($this->exportData['upgrades']) ? $this->exportData['upgrades'] : collect();
        
        // Transform payment data
        $paymentData = $payments->map(function ($payment) {
            return [
                'id' => 'P-' . $payment->id,
                'type' => 'Payment',
                'user_name' => $payment->user->name ?? 'N/A',
                'user_email' => $payment->user->email ?? 'N/A',
                'user_phone' => $payment->user->phone ?? 'N/A',
                'plan' => isset($payment->subscription) && isset($payment->subscription->plan) 
                    ? $payment->subscription->plan->name 
                    : 'N/A',
                'amount' => $payment->amount,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method ?? 'N/A',
                'transaction_id' => $payment->transaction_id ?? 'N/A',
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ];
        });
        
        // Transform upgrade data
        $upgradeData = $upgrades->map(function ($upgrade) {
            return [
                'id' => 'U-' . $upgrade->id,
                'type' => 'Upgrade',
                'user_name' => $upgrade->tenant->name ?? 'N/A',
                'user_email' => $upgrade->tenant->email ?? 'N/A',
                'user_phone' => $upgrade->tenant->phone ?? 'N/A',
                'plan' => $upgrade->to_plan ?? 'N/A',
                'amount' => $upgrade->amount,
                'status' => $upgrade->status,
                'payment_method' => $upgrade->payment_method ?? 'N/A',
                'transaction_id' => $upgrade->receipt_number ?? 'N/A',
                'created_at' => $upgrade->created_at,
                'updated_at' => $upgrade->updated_at
            ];
        });
        
        // Merge and sort by date
        $this->mergedData = $paymentData->concat($upgradeData)->sortByDesc('created_at');
    }

    public function export()
    {
        $filters = isset($this->exportData['filters']) ? $this->exportData['filters'] : [];
        
        // Calculate summary metrics
        $totalRevenue = $this->mergedData->sum('amount');
        $totalTransactions = $this->mergedData->count();
        $completedTransactions = $this->mergedData->where('status', 'completed')->count();
        $pendingTransactions = $this->mergedData->where('status', 'pending')->count();
        $failedTransactions = $this->mergedData->where('status', 'failed')->count();
        
        $pdf = PDF::loadView('exports.payments-pdf', [
            'transactions' => $this->mergedData,
            'filters' => $filters,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'completedTransactions' => $completedTransactions,
            'pendingTransactions' => $pendingTransactions,
            'failedTransactions' => $failedTransactions,
            'reportDate' => now()->format('F d, Y h:i A'),
        ]);
        
        // Set paper to landscape for better readability of the table
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('payments-report-' . date('Y-m-d') . '.pdf');
    }
} 