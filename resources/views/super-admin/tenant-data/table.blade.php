@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Table: {{ $table }}</h1>
                <div>
                    <a href="{{ route('super-admin.tenant-data.view', $tenant->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Tenant Tables
                    </a>
                </div>
            </div>
            <div class="text-muted">Tenant: {{ $tenant->tenant_name }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    @foreach($columns as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        @foreach($columns as $column)
                                            <td>
                                                @if(is_object($record->$column) || is_array($record->$column))
                                                    {{ json_encode($record->$column) }}
                                                @elseif($column == 'created_at' || $column == 'updated_at')
                                                    {{ $record->$column ? date('Y-m-d H:i:s', strtotime($record->$column)) : '' }}
                                                @else
                                                    {{ $record->$column }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <a href="{{ route('super-admin.tenant-data.edit', ['tenant' => $tenant->id, 'table' => $table, 'id' => $record->id]) }}" 
                                               class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) + 1 }}" class="text-center">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 