

<?php $__env->startSection('title', 'Department Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-12">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Instructors</h5>
                            <h2 class="card-text"><?php echo e($instructorCount ?? 0); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="card-text"><?php echo e($studentCount ?? 0); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Requirements</h5>
                            <h2 class="card-text"><?php echo e($pendingRequirements ?? 0); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Courses</h5>
                            <h2 class="card-text"><?php echo e($activeCourses ?? 0); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Requirements Tables -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#regular">Regular Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#irregular">Irregular Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#probation">Probation Students</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <?php $__currentLoopData = ['Regular', 'Irregular', 'Probation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="tab-pane fade <?php echo e($status === 'Regular' ? 'show active' : ''); ?>" id="<?php echo e(strtolower($status)); ?>">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Requirements Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $studentsByStatus[$status] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($student->student_id); ?></td>
                                            <td><?php echo e($student->name); ?></td>
                                            <td>
                                                <?php
                                                    $total = $student->requirements->count();
                                                    $approved = $student->requirements->where('pivot.status', 'approved')->count();
                                                ?>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?php echo e($total ? ($approved/$total*100) : 0); ?>%">
                                                        <?php echo e($approved); ?>/<?php echo e($total); ?>

                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm" onclick="viewRequirements(<?php echo e($student->id); ?>)">
                                                    View Requirements
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No <?php echo e(strtolower($status)); ?> students found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Instructor Management -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Instructors</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                        Add Instructor
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Courses</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $instructors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($instructor->name); ?></td>
                                <td><?php echo e($instructor->email); ?></td>
                                <td><?php echo e($instructor->courses_count); ?></td>
                                <td><?php echo e($instructor->students_count); ?></td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info">Edit</button>
                                    <button class="btn btn-sm btn-danger">Deactivate</button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">No instructors found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Instructor Modal -->
<div class="modal fade" id="addInstructorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('tenant.instructor.store', ['tenant' => tenant('id')])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Requirements Modal -->
<div class="modal fade" id="viewRequirementsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Requirements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="requirementsList"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Requirement Modal -->
<div class="modal fade" id="addRequirementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Requirement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('tenant.requirements.store', ['tenant' => tenant('id')])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Categories</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Regular" id="regularCheck" checked>
                            <label class="form-check-label" for="regularCheck">
                                Regular Students
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Irregular" id="irregularCheck">
                            <label class="form-check-label" for="irregularCheck">
                                Irregular Students
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Probation" id="probationCheck">
                            <label class="form-check-label" for="probationCheck">
                                Probation Students
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php $__currentLoopData = $requirementCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Type</label>
                        <select class="form-select" name="file_type" required>
                            <option value="pdf">PDF</option>
                            <option value="doc">Word Document</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_required" id="isRequired" checked>
                            <label class="form-check-label" for="isRequired">
                                Required
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function viewRequirements(studentId) {
    fetch(`/tenant/<?php echo e(tenant('id')); ?>/admin/students/${studentId}/requirements`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="list-group">';
            data.requirements.forEach(req => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1">${req.name}</h6>
                            <span class="badge bg-${req.pivot.status === 'approved' ? 'success' : (req.pivot.status === 'rejected' ? 'danger' : 'warning')}">
                                ${req.pivot.status}
                            </span>
                        </div>
                        ${req.pivot.file_path ? `
                            <div class="mt-2">
                                <a href="/storage/${req.pivot.file_path}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                                <button class="btn btn-sm btn-success" onclick="updateStatus(${req.pivot.id}, 'approved')">Approve</button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus(${req.pivot.id}, 'rejected')">Reject</button>
                            </div>
                        ` : `
                            <p class="text-muted mb-0">No file uploaded yet</p>
                        `}
                        ${req.pivot.remarks ? `<p class="text-muted mt-2 mb-0">Remarks: ${req.pivot.remarks}</p>` : ''}
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('requirementsList').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewRequirementsModal')).show();
        });
}

function updateStatus(studentRequirementId, status) {
    const remarks = status === 'rejected' ? prompt('Please enter rejection remarks:') : null;
    if (status === 'rejected' && !remarks) return;

    fetch(`/tenant/<?php echo e(tenant('id')); ?>/admin/student-requirements/${studentRequirementId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ status, remarks })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('tenant.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\BukSkwela\resources\views/tenant/dashboard.blade.php ENDPATH**/ ?>