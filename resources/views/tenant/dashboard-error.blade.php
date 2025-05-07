@extends('tenant.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-danger">Database Connection Error</h5>
                    <div class="alert alert-danger">
                        <h4>We're having trouble connecting to your database</h4>
                        <p>There was an error connecting to your tenant database. This might be due to the following reasons:</p>
                        <ul>
                            <li>The database hasn't been created yet</li>
                            <li>There's a configuration issue with the database connection</li>
                            <li>The database server might be temporarily unavailable</li>
                        </ul>
                        <p>Technical details: {{ $error }}</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ request()->getSchemeAndHttpHost() }}/dashboard" class="btn btn-primary">Try Again</a>
                        <a href="http://127.0.0.1:8000/login" class="btn btn-outline-secondary ml-2">Back to Main Site</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 