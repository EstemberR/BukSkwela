@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Database Check: {{ $tenant->tenant_name }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Database Information:</strong>
                        <ul>
                            <li><strong>Database Name:</strong> {{ $tenant->tenantDatabase->database_name ?? 'Not configured' }}</li>
                            <li><strong>Host:</strong> {{ $tenant->tenantDatabase->database_host ?? 'Not configured' }}</li>
                            <li><strong>Port:</strong> {{ $tenant->tenantDatabase->database_port ?? 'Not configured' }}</li>
                        </ul>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Database Status</h5>
                        </div>
                        <div class="card-body">
                            <p>
                                <strong>Database Exists:</strong>
                                @if($results['database_exists'])
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </p>
                            
                            @if($results['database_exists'])
                                <div class="mt-4">
                                    <h5>Tables in Database:</h5>
                                    @if(!empty($results['tables']))
                                        <ul class="list-group">
                                            @foreach($results['tables'] as $table)
                                                <li class="list-group-item">{{ $table }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-warning">
                                            No tables found in database.
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4">
                                    <h5>Missing Tables:</h5>
                                    @if(!empty($results['missing_tables']))
                                        <div class="alert alert-warning">
                                            <ul>
                                                @foreach($results['missing_tables'] as $table)
                                                    <li>{{ $table }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        
                                        @if(isset($results['migration_output']))
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    Auto-Migration Output
                                                </div>
                                                <div class="card-body">
                                                    <pre>{{ $results['migration_output'] }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-success">
                                            All expected tables exist in the database.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">Manual Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('super-admin.tenant-data.database-action', ['tenant' => $tenant->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="migrate">
                                        <button type="submit" class="btn btn-success btn-block mb-3">
                                            <i class="fas fa-database"></i> Run Migration
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('super-admin.tenant-data.view', ['tenant' => $tenant->id]) }}" class="btn btn-info btn-block">
                                        <i class="fas fa-arrow-left"></i> Back to Tenant Data
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 