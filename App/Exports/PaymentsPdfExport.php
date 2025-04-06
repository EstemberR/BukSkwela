<?php

namespace App\Exports;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PaymentsPdfExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function export()
    {
        $query = Payment::with(['user', 'subscription.plan']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['plan'])) {
            $query->whereHas('subscription.plan', function ($q) {
                $q->where('id', $this->filters['plan']);
            });
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $payments = $query->get();

        $pdf = PDF::loadView('exports.payments-pdf', [
            'payments' => $payments,
            'filters' => $this->filters,
            'totalRevenue' => $payments->sum('amount'),
            'paidSubscriptions' => $payments->where('status', 'completed')->count(),
            'pendingPayments' => $payments->where('status', 'pending')->count(),
            'overduePayments' => $payments->where('status', 'failed')->count(),
        ]);

        return $pdf->download('payments-report.pdf');
    }
} 