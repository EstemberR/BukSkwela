@extends('tenant.layouts.app')

@section('title', 'Error')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Error</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $message ?? 'An error occurred while processing your request.' }}
                    </div>
                    
                    <p>This could be due to:</p>
                    <ul>
                        <li>Database connection issues</li>
                        <li>Missing or invalid tenant configuration</li>
                        <li>Permission issues</li>
                    </ul>
                    
                    <div class="mt-4">
                        <a href="{{ route('tenant.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i> Return to Dashboard
                        </a>
                        
                        <button class="btn btn-secondary ms-2" onclick="window.location.reload();">
                            <i class="fas fa-sync-alt me-1"></i> Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 