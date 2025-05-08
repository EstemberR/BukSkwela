@extends('tenant.layouts.app')

@section('title', 'Pending Approval')

@section('body-class', 'hold-transition login-page')

@section('content')
<div class="pending-approval-box">
    <div class="text-center mb-4">
        <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
        <h2 class="mt-3">Account Pending Approval</h2>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning">
                <h5>{{ $message ?? 'Your tenant account is currently pending approval.' }}</h5>
            </div>
            
            <p class="lead">Your account has been registered successfully but is waiting for approval from our administrators.</p>
            
            <div class="approval-steps mt-4">
                <h5>Approval Process:</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Account Registration</span>
                        <span class="badge badge-success rounded-pill">Completed</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Admin Review</span>
                        <span class="badge badge-warning rounded-pill">In Progress</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Account Activation</span>
                        <span class="badge badge-secondary rounded-pill">Pending</span>
                    </li>
                </ul>
            </div>
            
            <div class="mt-4">
                <p>This process usually takes <strong>24-48 hours</strong>. If your account has not been approved after this time, please contact our support team.</p>
                
                <p>Tenant ID: <strong>{{ tenant('id') }}</strong></p>
                
                @if(isset($tenant) && $tenant)
                <p>Registration Date: <strong>{{ $tenant->created_at->format('F j, Y, g:i a') }}</strong></p>
                @endif
            </div>
            
            <div class="text-center mt-4">
                <a href="mailto:support@bukskwela.com" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
                <a href="{{ route('tenant.login') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.pending-approval-box {
    max-width: 500px;
    margin: 7% auto;
}
</style>
@endsection 