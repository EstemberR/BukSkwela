@extends('tenant.layouts.app')

@section('title', 'Enrollment Approvals')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.instructor.dashboard', ['tenant' => tenant('id')]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Enrollment Approvals</li>
                    </ol>
                </nav>
            </div>
            <h2 class="mt-2">Pending Enrollment Applications</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-2">
                <a href="#" class="btn btn-outline-secondary refresh-applications">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages Section -->
    <div id="alertMessages">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <!-- Applications Filter -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Filter Applications</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFilters">
                        <i class="fas fa-filter me-1"></i> Toggle Filters
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" id="filtersContainer">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="all">All Statuses</option>
                        <option value="pending" selected>Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="programFilter" class="form-label">Program/Course</label>
                    <select id="programFilter" class="form-select">
                        <option value="all">All Programs</option>
                        <!-- Programs will be loaded dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="yearLevelFilter" class="form-label">Year Level</label>
                    <select id="yearLevelFilter" class="form-select">
                        <option value="all">All Years</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary" id="applyFilters">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications List -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Enrollment Applications</h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary" id="applicationCount">0</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student</th>
                            <th scope="col">Program</th>
                            <th scope="col">Year Level</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date Applied</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applicationsTableBody">
                        <!-- Applications will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div id="pagination" class="d-flex justify-content-center">
                <!-- Pagination controls will be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Application Details Modal -->
<div class="modal fade" id="applicationDetailsModal" tabindex="-1" aria-labelledby="applicationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationDetailsModalLabel">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="applicationDetailsContent">
                    <!-- Application details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveApplicationBtn">
                    <i class="fas fa-check me-1"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" id="rejectApplicationBtn">
                    <i class="fas fa-times me-1"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Provide Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm">
                    <input type="hidden" id="feedbackApplicationId">
                    <input type="hidden" id="feedbackAction">
                    
                    <div class="mb-3">
                        <label for="feedbackNotes" class="form-label">Notes/Feedback (Optional)</label>
                        <textarea class="form-control" id="feedbackNotes" rows="4" placeholder="Enter any notes or feedback for the student..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitFeedbackBtn">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Status badges */
    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 30px;
        font-weight: 500;
        font-size: 0.8rem;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 5px;
        width: fit-content;
    }
    
    .status-badge.pending {
        background-color: #fff8dd;
        color: #ffc107;
    }
    
    .status-badge.approved {
        background-color: #e3fcef;
        color: #28a745;
    }
    
    .status-badge.rejected {
        background-color: #feecee;
        color: #dc3545;
    }
    
    /* Action buttons */
    .action-btn {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
    }
    
    /* Application details sections */
    .details-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .details-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }
    
    .details-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.3rem;
    }
    
    .details-value {
        color: #212529;
    }
    
    /* Document item styling */
    .document-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #fafafa;
        border-radius: 0.5rem;
        margin-bottom: 0.8rem;
        transition: all 0.2s ease;
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.01);
    }
    
    .document-item:hover {
        background: #f8f8f8;
        transform: translateY(-1px);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    .document-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        width: 40px;
        text-align: center;
    }
    
    .document-info {
        flex: 1;
    }
    
    .document-name {
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    
    .document-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .document-action {
        margin-left: 1rem;
    }
    
    /* Student info card */
    .student-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #fafafa;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.01);
    }
    
    .student-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: #6c757d;
    }
    
    .student-info {
        flex: 1;
    }
    
    .student-name {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.2rem;
    }
    
    .student-email {
        color: #6c757d;
        margin-bottom: 0.2rem;
    }
    
    /* Card styles */
    .card {
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.01);
    }
    
    .card:hover {
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    /* Loading spinners */
    .loading-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .loading-text {
        margin-left: 0.8rem;
        font-weight: 500;
        color: #6c757d;
    }
    
    /* No data message */
    .no-data-message {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #6c757d;
    }
    
    .no-data-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Pagination styling */
    .pagination-item {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 0.2rem;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .pagination-item:hover {
        background-color: #e9ecef;
    }
    
    .pagination-item.active {
        background-color: #007bff;
        color: white;
    }
    
    .pagination-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Filter container toggle */
    #filtersContainer {
        display: block;
    }
</style>
@endpush

@push('scripts')
<script>
// Add global error handler at the very top
window.onerror = function(message, source, lineno, colno, error) {
    console.error('JavaScript Error:', message);
    console.error('Source:', source);
    console.error('Line:', lineno);
    console.error('Column:', colno);
    console.error('Error Object:', error);
    
    // Display error to user if in enrollment approval page
    if (document.getElementById('alertMessages')) {
        document.getElementById('alertMessages').innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> JavaScript Error: ${message} at line ${lineno}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }
    
    return false; // Let default error handler run as well
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Enrollment Approval script loaded at:', new Date().toISOString());
    
    // Current page and filters state
    let currentPage = 1;
    let filters = {
        status: 'pending',
        program: 'all',
        yearLevel: 'all'
    };
    
    // Local data cache
    let applications = [];
    let programs = [];
    
    // DOM elements
    const applicationsTableBody = document.getElementById('applicationsTableBody');
    const applicationCount = document.getElementById('applicationCount');
    const pagination = document.getElementById('pagination');
    const filtersContainer = document.getElementById('filtersContainer');
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const statusFilter = document.getElementById('statusFilter');
    const programFilter = document.getElementById('programFilter');
    const yearLevelFilter = document.getElementById('yearLevelFilter');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const refreshApplicationsBtn = document.querySelector('.refresh-applications');
    
    // Modal elements
    const applicationDetailsModal = document.getElementById('applicationDetailsModal');
    const applicationDetailsContent = document.getElementById('applicationDetailsContent');
    const approveApplicationBtn = document.getElementById('approveApplicationBtn');
    const rejectApplicationBtn = document.getElementById('rejectApplicationBtn');
    
    // Feedback modal elements
    const feedbackModal = document.getElementById('feedbackModal');
    const feedbackApplicationId = document.getElementById('feedbackApplicationId');
    const feedbackAction = document.getElementById('feedbackAction');
    const feedbackNotes = document.getElementById('feedbackNotes');
    const submitFeedbackBtn = document.getElementById('submitFeedbackBtn');
    
    // Initialize Bootstrap modal instances
    const applicationDetailsModalInstance = new bootstrap.Modal(applicationDetailsModal);
    const feedbackModalInstance = new bootstrap.Modal(feedbackModal);
    
    // Initialize the page
    init();
    
    // ===== Functions =====
    
    /**
     * Initialize the page by loading applications and setting up event listeners
     */
    function init() {
        // Load applications with default filter (pending)
        loadApplications();
        
        // Load available programs for the filter dropdown
        loadPrograms();
        
        // Set up event listeners
        setupEventListeners();
    }
    
    /**
     * Set up all event listeners
     */
    function setupEventListeners() {
        // Toggle filters container visibility
        toggleFiltersBtn.addEventListener('click', function() {
            const isVisible = filtersContainer.style.display !== 'none';
            filtersContainer.style.display = isVisible ? 'none' : 'block';
            toggleFiltersBtn.innerHTML = isVisible ? 
                '<i class="fas fa-filter me-1"></i> Show Filters' : 
                '<i class="fas fa-filter me-1"></i> Hide Filters';
        });
        
        // Apply filters button click
        applyFiltersBtn.addEventListener('click', function() {
            filters.status = statusFilter.value;
            filters.program = programFilter.value;
            filters.yearLevel = yearLevelFilter.value;
            currentPage = 1;
            loadApplications();
        });
        
        // Reset filters button click
        resetFiltersBtn.addEventListener('click', function() {
            statusFilter.value = 'pending';
            programFilter.value = 'all';
            yearLevelFilter.value = 'all';
            filters = {
                status: 'pending',
                program: 'all',
                yearLevel: 'all'
            };
            currentPage = 1;
            loadApplications();
        });
        
        // Refresh applications button click
        refreshApplicationsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadApplications();
        });
        
        // Application action buttons (approve/reject) in modal
        approveApplicationBtn.addEventListener('click', function() {
            const applicationId = applicationDetailsModal.dataset.applicationId;
            if (applicationId) {
                feedbackApplicationId.value = applicationId;
                feedbackAction.value = 'approve';
                feedbackNotes.value = '';
                feedbackModalInstance.show();
            }
        });
        
        rejectApplicationBtn.addEventListener('click', function() {
            const applicationId = applicationDetailsModal.dataset.applicationId;
            if (applicationId) {
                feedbackApplicationId.value = applicationId;
                feedbackAction.value = 'reject';
                feedbackNotes.value = '';
                feedbackModalInstance.show();
            }
        });
        
        // Submit feedback button click
        submitFeedbackBtn.addEventListener('click', function() {
            const applicationId = feedbackApplicationId.value;
            const action = feedbackAction.value;
            const notes = feedbackNotes.value;
            
            if (applicationId && action) {
                updateApplicationStatus(applicationId, action, notes);
                feedbackModalInstance.hide();
            }
        });
    }
    
    /**
     * Load applications based on current filters and page
     */
    function loadApplications() {
        // Show loading indicator
        showLoading(applicationsTableBody);
        
        // Build API URL with filters
        const url = `{{ route('tenant.instructor.enrollment.applications', ['tenant' => tenant('id')]) }}?` + 
            `page=${currentPage}&status=${filters.status}&program=${filters.program}&year=${filters.yearLevel}`;
        
        // Fetch applications from server
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    applications = data.applications.data || [];
                    renderApplications(applications);
                    renderPagination(data.applications);
                    applicationCount.textContent = data.applications.total || 0;
                } else {
                    showError(applicationsTableBody, data.message || 'Failed to load applications');
                }
            })
            .catch(error => {
                console.error('Error loading applications:', error);
                showError(applicationsTableBody, 'Error loading applications. Please try again.');
            });
    }
    
    /**
     * Load available programs for the filter dropdown
     */
    function loadPrograms() {
        fetch(`{{ route('tenant.instructor.programs', ['tenant' => tenant('id')]) }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    programs = data.programs || [];
                    renderProgramsDropdown();
                }
            })
            .catch(error => {
                console.error('Error loading programs:', error);
            });
    }
    
    /**
     * Render programs dropdown options
     */
    function renderProgramsDropdown() {
        const options = programs.map(program => 
            `<option value="${program.id}">${program.name}</option>`
        );
        
        programFilter.innerHTML = '<option value="all">All Programs</option>' + options.join('');
    }
    
    /**
     * Render applications table body
     */
    function renderApplications(applications) {
        if (!applications || applications.length === 0) {
            applicationsTableBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="no-data-message">
                            <div class="no-data-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <p class="mb-0">No applications found matching your filters.</p>
                            <button class="btn btn-outline-primary btn-sm mt-3" id="resetFiltersBtn">
                                <i class="fas fa-undo me-1"></i> Reset Filters
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            // Attach event listener to the reset button
            const resetBtn = document.getElementById('resetFiltersBtn');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    resetFiltersBtn.click();
                });
            }
            return;
        }
        
        let html = '';
        
        applications.forEach((app, index) => {
            const student = app.student || {};
            const program = app.program || {};
            
            // Create status badge HTML based on status
            let statusBadge = '';
            if (app.status === 'pending') {
                statusBadge = `
                    <div class="status-badge pending">
                        <i class="fas fa-clock"></i>
                        <span>Pending</span>
                    </div>
                `;
            } else if (app.status === 'approved') {
                statusBadge = `
                    <div class="status-badge approved">
                        <i class="fas fa-check-circle"></i>
                        <span>Approved</span>
                    </div>
                `;
            } else if (app.status === 'rejected') {
                statusBadge = `
                    <div class="status-badge rejected">
                        <i class="fas fa-times-circle"></i>
                        <span>Rejected</span>
                    </div>
                `;
            }
            
            // Format date
            const dateApplied = new Date(app.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
            // Create actions HTML based on status
            let actionsHtml = '';
            if (app.status === 'pending') {
                actionsHtml = `
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary action-btn view-details" data-id="${app.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success action-btn approve-application" data-id="${app.id}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger action-btn reject-application" data-id="${app.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            } else {
                actionsHtml = `
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary action-btn view-details" data-id="${app.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                `;
            }
            
            html += `
                <tr>
                    <td>${(currentPage - 1) * 10 + index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2" style="width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-secondary"></i>
                            </div>
                            <div>
                                <div class="fw-medium">${student.name || 'Unknown'}</div>
                                <div class="small text-muted">${student.email || 'No email'}</div>
                            </div>
                        </div>
                    </td>
                    <td>${program.name || 'Unknown program'}</td>
                    <td>${app.year_level ? app.year_level + ' Year' : 'N/A'}</td>
                    <td>${statusBadge}</td>
                    <td>${dateApplied}</td>
                    <td>${actionsHtml}</td>
                </tr>
            `;
        });
        
        applicationsTableBody.innerHTML = html;
        
        // Add event listeners to action buttons
        const viewDetailsButtons = applicationsTableBody.querySelectorAll('.view-details');
        const approveButtons = applicationsTableBody.querySelectorAll('.approve-application');
        const rejectButtons = applicationsTableBody.querySelectorAll('.reject-application');
        
        viewDetailsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.dataset.id;
                showApplicationDetails(applicationId);
            });
        });
        
        approveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.dataset.id;
                feedbackApplicationId.value = applicationId;
                feedbackAction.value = 'approve';
                feedbackNotes.value = '';
                feedbackModalInstance.show();
            });
        });
        
        rejectButtons.forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.dataset.id;
                feedbackApplicationId.value = applicationId;
                feedbackAction.value = 'reject';
                feedbackNotes.value = '';
                feedbackModalInstance.show();
            });
        });
    }
    
    /**
     * Render pagination controls
     */
    function renderPagination(paginationData) {
        if (!paginationData || !paginationData.last_page) {
            pagination.innerHTML = '';
            return;
        }
        
        const totalPages = paginationData.last_page;
        currentPage = paginationData.current_page;
        
        let html = '';
        
        // Previous button
        html += `
            <div class="pagination-item ${currentPage === 1 ? 'disabled' : ''}" 
                ${currentPage !== 1 ? 'data-page="' + (currentPage - 1) + '"' : ''}>
                <i class="fas fa-chevron-left"></i>
            </div>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages <= 5 || 
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `
                    <div class="pagination-item ${i === currentPage ? 'active' : ''}" data-page="${i}">
                        ${i}
                    </div>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<div class="px-1">...</div>`;
            }
        }
        
        // Next button
        html += `
            <div class="pagination-item ${currentPage === totalPages ? 'disabled' : ''}"
                ${currentPage !== totalPages ? 'data-page="' + (currentPage + 1) + '"' : ''}>
                <i class="fas fa-chevron-right"></i>
            </div>
        `;
        
        pagination.innerHTML = html;
        
        // Add event listeners to pagination items
        const paginationItems = pagination.querySelectorAll('.pagination-item:not(.disabled)');
        paginationItems.forEach(item => {
            item.addEventListener('click', function() {
                const page = this.dataset.page;
                if (page) {
                    currentPage = parseInt(page);
                    loadApplications();
                }
            });
        });
    }
    
    /**
     * Show application details in modal
     */
    function showApplicationDetails(applicationId) {
        // Show loading state in modal
        applicationDetailsContent.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="loading-text">Loading application details...</span>
            </div>
        `;
        
        // Show modal
        applicationDetailsModal.dataset.applicationId = applicationId;
        applicationDetailsModalInstance.show();
        
        // Load application details
        fetch(`{{ route('tenant.instructor.enrollment.application', ['tenant' => tenant('id'), 'id' => '__ID__']) }}`.replace('__ID__', applicationId))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderApplicationDetails(data.application);
                    
                    // Show/hide action buttons based on status
                    if (data.application.status === 'pending') {
                        approveApplicationBtn.style.display = 'block';
                        rejectApplicationBtn.style.display = 'block';
                    } else {
                        approveApplicationBtn.style.display = 'none';
                        rejectApplicationBtn.style.display = 'none';
                    }
                } else {
                    applicationDetailsContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> ${data.message || 'Failed to load application details'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading application details:', error);
                applicationDetailsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> Error loading application details. Please try again.
                    </div>
                `;
            });
    }
    
    /**
     * Render application details in modal
     */
    function renderApplicationDetails(application) {
        const student = application.student || {};
        const program = application.program || {};
        const documents = application.documents || [];
        
        // Format date
        const dateApplied = new Date(application.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Create status badge
        let statusBadge = '';
        if (application.status === 'pending') {
            statusBadge = `<div class="status-badge pending"><i class="fas fa-clock"></i> Pending</div>`;
        } else if (application.status === 'approved') {
            statusBadge = `<div class="status-badge approved"><i class="fas fa-check-circle"></i> Approved</div>`;
        } else if (application.status === 'rejected') {
            statusBadge = `<div class="status-badge rejected"><i class="fas fa-times-circle"></i> Rejected</div>`;
        }
        
        // Build documents HTML
        let documentsHtml = '';
        if (documents && documents.length > 0) {
            documentsHtml = documents.map(doc => {
                // Determine document icon
                let iconClass = 'fa-file';
                let fileType = '';
                
                if (doc.file_type) {
                    fileType = doc.file_type.toLowerCase();
                } else if (doc.mime_type) {
                    fileType = doc.mime_type.toLowerCase();
                } else if (doc.file_name) {
                    const extension = doc.file_name.split('.').pop().toLowerCase();
                    if (['pdf'].includes(extension)) {
                        fileType = 'pdf';
                    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        fileType = 'image';
                    } else if (['doc', 'docx'].includes(extension)) {
                        fileType = 'word';
                    } else if (['xls', 'xlsx'].includes(extension)) {
                        fileType = 'excel';
                    }
                }
                
                if (fileType.includes('pdf')) {
                    iconClass = 'fa-file-pdf';
                } else if (fileType.includes('image')) {
                    iconClass = 'fa-file-image';
                } else if (fileType.includes('word')) {
                    iconClass = 'fa-file-word';
                } else if (fileType.includes('excel')) {
                    iconClass = 'fa-file-excel';
                }
                
                // Format upload date if available
                let uploadDate = 'Unknown date';
                if (doc.created_at) {
                    uploadDate = new Date(doc.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                } else if (doc.uploaded_at) {
                    uploadDate = new Date(doc.uploaded_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
                
                const documentName = doc.folder_name || doc.field_name || doc.name || 'Document';
                const fileName = doc.file_name || doc.filename || 'Unnamed file';
                
                return `
                    <div class="document-item">
                        <div class="document-icon">
                            <i class="fas ${iconClass} text-primary"></i>
                        </div>
                        <div class="document-info">
                            <div class="document-name">${documentName}</div>
                            <div class="document-meta">${fileName} - Uploaded on ${uploadDate}</div>
                        </div>
                        ${doc.file_path || doc.url ? `
                            <div class="document-action">
                                <a href="${doc.file_path || doc.url}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> View
                                </a>
                            </div>
                        ` : ''}
                    </div>
                `;
            }).join('');
        } else {
            documentsHtml = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No documents attached to this application.
                </div>
            `;
        }
        
        // Build HTML for application details
        const html = `
            <div class="student-card">
                <div class="student-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="student-info">
                    <div class="student-name">${student.name || 'Unknown Student'}</div>
                    <div class="student-email">${student.email || 'No email'}</div>
                    <div class="mt-1">${statusBadge}</div>
                </div>
            </div>
            
            <div class="details-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">Application ID</div>
                            <div class="details-value">#${application.id}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">Submitted Date</div>
                            <div class="details-value">${dateApplied}</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">Program/Course</div>
                            <div class="details-value">${program.name || 'Unknown Program'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">Year Level</div>
                            <div class="details-value">${application.year_level ? application.year_level + ' Year' : 'N/A'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">Student Status</div>
                            <div class="details-value">${application.student_status || 'Regular'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="details-label">School Year</div>
                            <div class="details-value">${application.school_year_start || ''} - ${application.school_year_end || ''}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="details-label">Student Notes</div>
                    <div class="details-value p-2 border rounded bg-light">
                        ${application.notes || 'No notes provided'}
                    </div>
                </div>
            </div>
            
            <div class="details-section">
                <div class="details-label mb-3">Submitted Documents</div>
                ${documentsHtml}
            </div>
            
            ${application.admin_notes ? `
                <div class="details-section">
                    <div class="details-label mb-2">Feedback/Notes from Administrator</div>
                    <div class="p-3 border rounded bg-light">
                        ${application.admin_notes}
                    </div>
                </div>
            ` : ''}
        `;
        
        applicationDetailsContent.innerHTML = html;
    }
    
    /**
     * Update application status (approve/reject)
     */
    function updateApplicationStatus(applicationId, action, notes) {
        // Show loading state in button
        submitFeedbackBtn.disabled = true;
        submitFeedbackBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Processing...
        `;
        
        const url = `{{ route('tenant.instructor.enrollment.update-status', ['tenant' => tenant('id'), 'id' => '__ID__']) }}`
            .replace('__ID__', applicationId);
        
        const data = {
            action: action,
            notes: notes,
            _token: '{{ csrf_token() }}'
        };
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            submitFeedbackBtn.disabled = false;
            submitFeedbackBtn.innerHTML = 'Submit';
            
            if (data.success) {
                // Close application details modal
                applicationDetailsModalInstance.hide();
                
                // Show success message
                showAlert('success', data.message || 'Application status updated successfully');
                
                // Reload applications
                loadApplications();
            } else {
                // Show error message
                showAlert('error', data.message || 'Failed to update application status');
            }
        })
        .catch(error => {
            console.error('Error updating application status:', error);
            
            submitFeedbackBtn.disabled = false;
            submitFeedbackBtn.innerHTML = 'Submit';
            
            // Show error message
            showAlert('error', 'Error updating application status. Please try again.');
        });
    }
    
    /**
     * Show loading indicator in container
     */
    function showLoading(container) {
        container.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="loading-text">Loading applications...</span>
                    </div>
                </td>
            </tr>
        `;
    }
    
    /**
     * Show error message in container
     */
    function showError(container, message) {
        container.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle me-2"></i> ${message}
                    </div>
                </td>
            </tr>
        `;
    }
    
    /**
     * Show alert message at the top of the page
     */
    function showAlert(type, message) {
        const alertsContainer = document.getElementById('alertMessages');
        
        const alertHtml = `
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        alertsContainer.innerHTML = alertHtml;
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = alertsContainer.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
});
</script>
@endpush
