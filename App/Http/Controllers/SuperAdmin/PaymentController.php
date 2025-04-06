<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Exports\PaymentsExport;
use App\Exports\PaymentsPdfExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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

        return view('SuperAdmin.payments.index', compact(
            'payments',
            'plans',
            'totalRevenue',
            'paidSubscriptions',
            'pendingPayments',
            'overduePayments'
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