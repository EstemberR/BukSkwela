@extends('tenant.layouts.app')

@section('title', 'Student Enrollment')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.student.dashboard', ['tenant' => tenant('id')]) }}">DASHBOARD</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Enrollment Overview</li>
                    </ol>
                </nav>
            </div>
            <h2 class="mt-2">Enrollment Overview</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-2">
                <a href="#" class="btn btn-outline-secondary">Application History</a>
                <a href="#" class="btn btn-success">Apply Enrollment</a>
            </div>
        </div>
    </div>

    <!-- Empty State / No enrollments yet -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-5 text-center">
            <div class="py-5">
                <h3 class="fw-bold mb-3">No Enrollment Applications Yet</h3>
                <p class="text-muted mb-4">You haven't applied for any enrollments yet. Start by clicking the button below to apply.</p>
                <a href="#" class="btn btn-success px-4 py-2">
                    <i class="fas fa-plus me-2"></i> Apply Enrollment
                </a>
            </div>
        </div>
    </div>

    <!-- Enrollment Applications Table (initially hidden until they have applications) -->
    <div class="card shadow-sm mb-4 d-none">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Your Enrollment Applications</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Application ID</th>
                            <th scope="col">Program/Course</th>
                            <th scope="col">Submitted Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ENR-2023-001</td>
                            <td>Bachelor of Science in Information Technology</td>
                            <td>June 15, 2023</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ENR-2023-002</td>
                            <td>Bachelor of Science in Computer Science</td>
                            <td>June 16, 2023</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.85rem;
    }
    
    .breadcrumb-item.active {
        font-size: 0.85rem;
    }
    
    .badge {
        font-weight: 500;
    }
</style>
@endpush 