@extends('superadmin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Payment Management</h1>
        <div class="flex space-x-4">
            <div class="relative">
                <select id="exportFormat" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Export Format</option>
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <button onclick="exportPayments()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form action="{{ route('superadmin.payments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label for="plan" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                <select name="plan" id="plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Paid Subscriptions</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $paidSubscriptions }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Pending Payments</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $pendingPayments }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Overdue Payments</h3>
            <p class="text-3xl font-bold text-red-600">{{ $overduePayments }}</p>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $payment->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->subscription->plan->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('superadmin.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if($payment->status === 'pending')
                                    <form action="{{ route('superadmin.payments.mark-as-paid', $payment) }}" 
                                          method="POST" 
                                          class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900"
                                                onclick="return confirm('Are you sure you want to mark this payment as completed?')">
                                            Mark as Paid
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportPayments() {
    const format = document.getElementById('exportFormat').value;
    if (!format) {
        alert('Please select an export format');
        return;
    }
    
    window.location.href = `{{ route('superadmin.payments.export') }}?format=${format}`;
}
</script>
@endpush
@endsection 