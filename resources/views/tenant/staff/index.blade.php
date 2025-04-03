@extends('tenant.layouts.app')

@section('title', 'Staff Management')

@section('content')
<div class="container">
    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Staff Members</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        Add Staff Member
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search and filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search staff..." id="searchStaff">
                                <button class="btn btn-outline-secondary" type="button">Search</button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <select class="form-select d-inline-block w-auto">
                                <option value="">All Roles</option>
                                <option value="instructor">Instructor</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>

                    <!-- Staff Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffMembers ?? [] as $staff)
                                <tr>
                                    <td>{{ $staff->staff_id }}</td>
                                    <td>{{ $staff->name }}</td>
                                    <td>{{ $staff->email }}</td>
                                    <td>{{ ucfirst($staff->role) }}</td>
                                    <td>{{ $staff->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $staff->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($staff->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $staff->id }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteStaff({{ $staff->id }})">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No staff members found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-end">
                        {{ $staffMembers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.staff.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff ID</label>
                        <input type="text" class="form-control" name="staff_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <input type="hidden" name="role" value="instructor">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="department" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
@foreach($staffMembers ?? [] as $staff)
<div class="modal fade" id="editStaffModal{{ $staff->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.staff.update', ['tenant' => tenant('id'), 'staff' => $staff->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff ID</label>
                        <input type="text" class="form-control" name="staff_id" value="{{ $staff->staff_id }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $staff->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $staff->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select Role</option>
                            <option value="instructor" {{ $staff->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}" {{ $staff->department_id === $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
    function deleteStaff(staffId) {
        if (confirm('Are you sure you want to delete this staff member?')) {
            fetch(`/tenant/{{ tenant('id') }}/admin/staff/${staffId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error deleting staff member');
                }
            });
        }
    }
</script>
@endsection 