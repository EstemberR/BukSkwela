@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>{{ $tenant_name }} Dashboard</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        Welcome back, {{ $admin->name ?? $admin->email }}!
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5 class="card-title">Students</h5>
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-primary">Manage Students</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                    <h5 class="card-title">Staff</h5>
                                    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-primary">Manage Staff</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-book fa-3x mb-3"></i>
                                    <h5 class="card-title">Courses</h5>
                                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary">Manage Courses</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <h5 class="card-title">Requirements</h5>
                                    <a href="{{ route('admin.requirements.index') }}" class="btn btn-sm btn-primary">Manage Requirements</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="card">
                            <div class="card-header">Tenant Information</div>
                            <div class="card-body">
                                <p><strong>Tenant ID:</strong> {{ $tenant_id }}</p>
                                <p><strong>Tenant Name:</strong> {{ $tenant_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 