<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Transactions Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #333;
        }
        .header p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        .stats {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .stat-box {
            width: 19%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            box-sizing: border-box;
        }
        .stat-box h3 {
            font-size: 12px;
            margin: 0 0 5px 0;
            color: #666;
        }
        .stat-box p {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }
        .stat-total {
            color: #2980b9;
        }
        .stat-completed {
            color: #27ae60;
        }
        .stat-pending {
            color: #f39c12;
        }
        .stat-failed {
            color: #e74c3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
        }
        .status {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #e6f4ea;
            color: #1e7e34;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .type-payment {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .type-upgrade {
            background-color: #e8f5e9;
            color: #1b5e20;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Transactions Report</h1>
        <p>Generated on {{ $reportDate }}</p>
    </div>

    @if(!empty($filters))
        <div style="margin-bottom: 15px;">
            <h3 style="margin: 0 0 8px 0; font-size: 14px;">Filters Applied:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                @if(!empty($filters['status']))
                    <li>Status: {{ ucfirst($filters['status']) }}</li>
                @endif
                @if(!empty($filters['plan']))
                    <li>Plan: {{ $filters['plan'] }}</li>
                @endif
                @if(!empty($filters['date_from']))
                    <li>Date From: {{ \Carbon\Carbon::parse($filters['date_from'])->format('F d, Y') }}</li>
                @endif
                @if(!empty($filters['date_to']))
                    <li>Date To: {{ \Carbon\Carbon::parse($filters['date_to'])->format('F d, Y') }}</li>
                @endif
            </ul>
        </div>
    @endif

    <div class="stats">
        <div class="stat-box">
            <h3>Total Revenue</h3>
            <p class="stat-total">₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="stat-box">
            <h3>Total Transactions</h3>
            <p>{{ $totalTransactions }}</p>
        </div>
        <div class="stat-box">
            <h3>Completed</h3>
            <p class="stat-completed">{{ $completedTransactions }}</p>
        </div>
        <div class="stat-box">
            <h3>Pending</h3>
            <p class="stat-pending">{{ $pendingTransactions }}</p>
        </div>
        <div class="stat-box">
            <h3>Failed</h3>
            <p class="stat-failed">{{ $failedTransactions }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ref ID</th>
                <th>Type</th>
                <th>Tenant/User</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction['id'] }}</td>
                    <td>
                        <span class="type-{{ strtolower($transaction['type']) }}">
                            {{ $transaction['type'] }}
                        </span>
                    </td>
                    <td>{{ $transaction['user_name'] }}</td>
                    <td>{{ $transaction['user_email'] }}</td>
                    <td>{{ $transaction['plan'] }}</td>
                    <td>₱{{ number_format($transaction['amount'], 2) }}</td>
                    <td>
                        <span class="status status-{{ $transaction['status'] }}">
                            {{ ucfirst($transaction['status']) }}
                        </span>
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction['payment_method'])) }}</td>
                    <td>{{ $transaction['created_at'] instanceof \DateTime ? $transaction['created_at']->format('M d, Y') : (is_string($transaction['created_at']) ? $transaction['created_at'] : 'N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No transaction records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated by BukSkwela Payment Management System | Page {PAGE_NUM} of {PAGE_COUNT}</p>
    </div>
</body>
</html> 