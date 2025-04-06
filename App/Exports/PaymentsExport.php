<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Payment ID',
            'Tenant Name',
            'Email',
            'Phone',
            'Subscription Plan',
            'Amount',
            'Status',
            'Payment Method',
            'Transaction ID',
            'Created At',
            'Updated At'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->user->name,
            $payment->user->email,
            $payment->user->phone ?? 'N/A',
            $payment->subscription->plan->name,
            '₱' . number_format($payment->amount, 2),
            ucfirst($payment->status),
            ucfirst($payment->payment_method),
            $payment->transaction_id ?? 'N/A',
            $payment->created_at->format('Y-m-d H:i:s'),
            $payment->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:K' => ['borders' => ['allBorders' => ['borderStyle' => 'thin']]],
        ];
    }
} 