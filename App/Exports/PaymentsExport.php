<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\SubscriptionUpgrade;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

    public function collection()
    {
        return $this->mergedData;
    }

    public function headings(): array
    {
        return [
            'Reference ID',
            'Type',
            'Tenant/User Name',
            'Email',
            'Phone',
            'Plan',
            'Amount',
            'Status',
            'Payment Method',
            'Transaction ID',
            'Created At',
            'Updated At'
        ];
    }

    public function map($item): array
    {
        return [
            $item['id'],
            $item['type'],
            $item['user_name'],
            $item['user_email'],
            $item['user_phone'],
            $item['plan'],
            '₱' . number_format($item['amount'], 2),
            ucfirst($item['status']),
            ucfirst(str_replace('_', ' ', $item['payment_method'])),
            $item['transaction_id'],
            $item['created_at']->format('Y-m-d H:i:s'),
            $item['updated_at']->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
            'A:L' => ['borders' => ['allBorders' => ['borderStyle' => 'thin']]],
        ];
    }
} 