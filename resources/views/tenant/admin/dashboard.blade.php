@extends('tenant.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <h5>Welcome to {{ tenant('id') }} Admin Dashboard</h5>
                    <div class="mt-4">
                        <a href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}" 
                           class="btn btn-primary">
                            Manage Requirements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection