@extends('superadmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('super-admin.tenant-data.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tenant Data
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">MySQL Connection Status</h4>
                </div>
                <div class="card-body">
                    @if ($status == 'success')
                        <div class="alert alert-success">
                            <h5><i class="icon fas fa-check"></i> MySQL Connection Status</h5>
                            MySQL connection status is good. You can proceed with tenant migrations.
                        </div>
                    @elseif ($status == 'warning')
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> MySQL Connection Warning</h5>
                            There are potential issues with MySQL connections. Consider using batched migrations with small batch sizes.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h5><i class="icon fas fa-ban"></i> MySQL Connection Error</h5>
                            There was an error checking MySQL connection status.
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>MySQL Connection Details:</h5>
                        <pre class="p-3 bg-dark text-white">{{ $output }}</pre>
                    </div>

                    <div class="mt-4">
                        <h5>Migration Options:</h5>
                        <div class="list-group">
                            <a href="{{ route('super-admin.tenant-data.run-batched-migration') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Batched Migration (Recommended)</h5>
                                    <small class="text-success">Safest Option</small>
                                </div>
                                <p class="mb-1">Migrate tenant databases in small batches with delays between each batch</p>
                                <small>Ideal when MySQL connection usage is moderate to high</small>
                            </a>
                            <a href="{{ route('super-admin.tenant-data.run-migration') }}" class="list-group-item list-group-item-action {{ $status == 'warning' ? 'list-group-item-warning' : '' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Standard Migration</h5>
                                    <small class="text-warning">Faster but Riskier</small>
                                </div>
                                <p class="mb-1">Migrate all tenant databases at once</p>
                                <small>Only recommended when MySQL connection usage is low</small>
                            </a>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Shell Script (Most Reliable)</h5>
                                    <small class="text-success">Extremely Reliable</small>
                                </div>
                                <p class="mb-1">Run the shell script from command line:</p>
                                <pre class="p-2 bg-dark text-white">bash scripts/migrate_tenant_databases.sh</pre>
                                <small>This processes one tenant at a time with clean connections</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 