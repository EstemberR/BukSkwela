@extends('tenant.layouts.app')

@section('title', 'Error')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="m-0"><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                </div>
                <div class="card-body">
                    <h4 class="mb-4">{{ $message ?? 'An error has occurred' }}</h4>
                    
                    @if(app()->environment('local', 'development') && isset($details))
                    <div class="alert alert-secondary">
                        <p class="mb-1"><strong>Details:</strong></p>
                        <code>{{ $details }}</code>
                        
                        @if(isset($code) && $code)
                        <p class="mt-2 mb-0"><strong>Code:</strong> {{ $code }}</p>
                        @endif
                    </div>
                    @endif
                    
                    <p class="mb-0">Please try again later or contact the administrator if the problem persists.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('tenant.dashboard', ['tenant' => tenant('id')]) }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 