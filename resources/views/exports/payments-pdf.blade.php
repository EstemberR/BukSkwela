<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payments Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .stats {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        .stat-box {
            width: 23%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        .stat-box h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            color: #666;
        }
        .stat-box p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
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
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payments Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    @if(!empty($filters))
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0;">Filters Applied:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                @if(!empty($filters['status']))
                    <li>Status: {{ ucfirst($filters['status']) }}</li>
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
            <p>₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="stat-box">
            <h3>Paid Subscriptions</h3>
            <p>{{ $paidSubscriptions }}</p>
        </div>
        <div class="stat-box">
            <h3>Pending Payments</h3>
            <p>{{ $pendingPayments }}</p>
        </div>
        <div class="stat-box">
            <h3>Overdue Payments</h3>
            <p>{{ $overduePayments }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Tenant</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>#{{ $payment->id }}</td>
                    <td>{{ $payment->user->name }}</td>
                    <td>{{ $payment->subscription->plan->name }}</td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>
                        <span class="status status-{{ $payment->status }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Page {PAGE_NUM} of {PAGE_COUNT}</p>
    </div>
</body>
</html> 