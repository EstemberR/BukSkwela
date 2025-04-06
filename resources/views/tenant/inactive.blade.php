@extends('layouts.app')

@section('title', 'Tenant Inactive')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Account Pending Approval</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <div class="alert alert-danger">
                        <h5>{{ $message ?? 'Your tenant account is currently pending approval.' }}</h5>
                    </div>
                    
                    <p class="lead">Your account has been registered but is waiting for approval from our administrators before you can access it.</p>
                    
                    <p>This process usually takes 24-48 hours. If your account has not been approved after this time, please contact our support team.</p>
                    
                    <div class="text-center mt-4">
                        <a href="mailto:support@bukskwela.com" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> Contact Support
                        </a>
                        <a href="{{ env('APP_URL') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-home"></i> Return to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 