@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Edit Record: {{ $table }}</h1>
                <div>
                    <a href="{{ route('super-admin.tenant-data.table', ['tenant' => $tenant->id, 'table' => $table]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Table Data
                    </a>
                </div>
            </div>
            <div class="text-muted">Tenant: {{ $tenant->tenant_name }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Record</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('super-admin.tenant-data.update', ['tenant' => $tenant->id, 'table' => $table, 'id' => $record->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @foreach($columns as $column)
                            @if($column != 'id' && $column != 'created_at' && $column != 'updated_at')
                                <div class="mb-3">
                                    <label for="{{ $column }}" class="form-label">{{ ucwords(str_replace('_', ' ', $column)) }}</label>
                                    
                                    @if(is_object($record->$column) || is_array($record->$column))
                                        <textarea class="form-control" id="{{ $column }}" name="{{ $column }}" rows="5">{{ json_encode($record->$column, JSON_PRETTY_PRINT) }}</textarea>
                                    @else
                                        <input type="text" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $record->$column }}">
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Record</button>
                            <a href="{{ route('super-admin.tenant-data.table', ['tenant' => $tenant->id, 'table' => $table]) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 