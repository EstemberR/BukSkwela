@extends('tenant.layouts.app')

@section('title', 'Department Dashboard - Compact Layout')

@section('content')
<div class="container-fluid py-3 layout-compact" data-page="dashboard">
    <div class="row">
        <!-- Main Content -->
        <div class="col-12">
            <!-- Statistics Cards - 2x2 Grid -->
            <div class="row g-3">
                <div class="col-md-6 col-lg-3 compact-card">
                    <div class="card h-100 border-0 shadow-sm stat-card dashboard-card">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stat-icon-container bg-primary-soft me-3">
                                <i class="fas fa-chalkboard-teacher text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted fs-sm">Instructors</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $instructorCount ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 compact-card">
                    <div class="card h-100 border-0 shadow-sm stat-card dashboard-card">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stat-icon-container bg-success-soft me-3">
                                <i class="fas fa-user-graduate text-success"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted fs-sm">Students</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $studentCount ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 compact-card">
                    <div class="card h-100 border-0 shadow-sm stat-card dashboard-card">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stat-icon-container bg-info-soft me-3">
                                <i class="fas fa-clipboard-list text-info"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted fs-sm">Requirements</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $pendingRequirements ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 compact-card">
                    <div class="card h-100 border-0 shadow-sm stat-card dashboard-card">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stat-icon-container bg-warning-soft me-3">
                                <i class="fas fa-book text-warning"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted fs-sm">Courses</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $activeCourses ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Cards Row -->
            <div class="row mt-3 g-3">
                <!-- Recent Enrolled Students Section -->
                <div class="col-md-12 col-lg-4">
                    <div class="card compact-content-card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                            <h6 class="mb-0 fw-semibold">Recent Students</h6>
                            <a href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" class="btn btn-sm btn-link text-primary p-0">
                                <i class="fas fa-external-link-alt me-1"></i> View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush custom-scrollbar" style="max-height: 280px; overflow-y: auto;">
                                @forelse($students ?? [] as $student)
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="compact-avatar">
                                                @if($student->avatar)
                                                    <img src="{{ asset('storage/' . $student->avatar) }}" 
                                                        alt="{{ $student->student_id }}" 
                                                        class="rounded-circle">
                                                @else
                                                    <div class="avatar-placeholder student-image">
                                                        <i class="fas fa-user-graduate text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1 fw-semibold">{{ $student->student_id }}</h6>
                                                <span class="badge bg-{{ $student->status === 'Regular' ? 'success' : ($student->status === 'Irregular' ? 'warning' : 'danger') }} rounded-pill">
                                                    {{ $student->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-4">
                                        <i class="fas fa-user-plus text-muted mb-2" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-0">No students enrolled yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Courses Section -->
                <div class="col-md-12 col-lg-4">
                    <div class="card compact-content-card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                            <h6 class="mb-0 fw-semibold">Active Courses</h6>
                            <a href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" class="btn btn-sm btn-link text-primary p-0">
                                <i class="fas fa-external-link-alt me-1"></i> View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush custom-scrollbar" style="max-height: 280px; overflow-y: auto;">
                                @forelse($courses ?? [] as $course)
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="compact-avatar">
                                                    <div class="avatar-placeholder course-image">
                                                        <i class="fas fa-book text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-1 fw-semibold">{{ $course->name }}</h6>
                                                    <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                                        {{ ucfirst($course->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="badge bg-info rounded-pill">
                                                <i class="fas fa-users fa-xs me-1"></i>{{ $course->students_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-4">
                                        <i class="fas fa-book-open text-muted mb-2" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-0">No courses available</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements Chart Section -->
                <div class="col-md-12 col-lg-4">
                    <div class="card compact-content-card h-100 border-0 shadow-sm dashboard-card">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0 fw-semibold">Requirements Progress</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 280px;">
                                <canvas id="requirementsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Include modals and other components -->
@include('tenant.dashboard-components.modals')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Compact dashboard layout loaded');
    
    // Enable compact sidebar mode for this layout
    document.body.classList.add('layout-compact');
    document.body.classList.add('compact-sidebar');
    console.log('Compact sidebar mode enabled for compact layout');
    
    // Setup dropdown hover functionality
    setupDropdownHover();
    
    // Initialize charts and other components
    setupRequirementsChart();
    
    // Add quick actions panel if it doesn't exist
    if (!document.querySelector('.quick-actions-panel')) {
        setupQuickActionsPanel();
    }
    
    // Apply user's card style preference
    applyCardStyle();
});

// Function to handle dropdown hover in compact mode
function setupDropdownHover() {
    const dropdownItems = document.querySelectorAll('.sidebar .nav-item.dropdown');
    
    dropdownItems.forEach(item => {
        const menu = item.querySelector('.dropdown-menu');
        const toggleBtn = item.querySelector('.dropdown-toggle');
        
        // Add hover behavior
        item.addEventListener('mouseenter', function() {
            if (document.body.classList.contains('compact-sidebar')) {
                if (menu) {
                    menu.classList.add('show');
                    console.log('Showing dropdown menu on hover');
                }
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (document.body.classList.contains('compact-sidebar')) {
                if (menu) {
                    menu.classList.remove('show');
                    console.log('Hiding dropdown menu on leave');
                }
            }
        });
        
        // Prevent click from toggling in compact mode
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                if (document.body.classList.contains('compact-sidebar')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        }
    });
}

// Requirements Chart
const setupRequirementsChart = () => {
    const ctx = document.getElementById('requirementsChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Form 137', 'Good Moral', 'Birth Cert', 'Medical', 'Photo', 'Enrollment'],
            datasets: [{
                data: [85, 92, 78, 65, 95, 70],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderWidth: 0,
                borderRadius: 4,
                maxBarThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#111827',
                    bodyColor: '#111827',
                    titleFont: {
                        size: 13,
                        weight: 'bold',
                        family: "'Inter', sans-serif"
                    },
                    bodyFont: {
                        size: 12,
                        family: "'Inter', sans-serif"
                    },
                    padding: 12,
                    boxPadding: 6,
                    displayColors: false,
                    borderColor: 'rgba(230, 230, 230, 1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return `Completion Rate: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            size: 11,
                            family: "'Inter', sans-serif"
                        },
                        color: '#6B7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10,
                            family: "'Inter', sans-serif"
                        },
                        color: '#6B7280'
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });
};

// Setup Quick Actions Panel
const setupQuickActionsPanel = () => {
    const container = document.querySelector('.container-fluid');
    if (!container) return;
    
    const panel = document.createElement('div');
    panel.className = 'card quick-actions-panel';
    panel.innerHTML = `
        <div class="card-header">
            <strong>Quick Actions</strong>
        </div>
        <div class="card-body p-2">
            <div class="d-grid gap-2">
                <a href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" class="btn quick-action-btn">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
                <a href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" class="btn quick-action-btn">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('tenant.staff.index', ['tenant' => tenant('id')]) }}" class="btn quick-action-btn">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Staff</span>
                </a>
                <a href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}" class="btn quick-action-btn">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Requirements</span>
                </a>
            </div>
        </div>
    `;
    
    document.body.appendChild(panel);
};

// Function to apply the user's card style preference
function applyCardStyle() {
    // Get card style preference from localStorage
    const cardStyle = localStorage.getItem('selectedCardStyle') || 'square';
    console.log('Applying card style in compact dashboard:', cardStyle);
    
    // Target all card elements in compact layout
    const cardSelectors = [
        '.card', 
        '.stat-card',
        '.compact-content-card',
        '.quick-actions-panel'
    ];
    
    // Remove all card style classes first from all cards
    document.querySelectorAll(cardSelectors.join(', ')).forEach(card => {
        card.classList.remove('card-rounded', 'card-square', 'card-glass');
        // Add the selected style class
        card.classList.add(`card-${cardStyle}`);
    });
    
    // Apply specific styles based on the card type
    switch(cardStyle) {
        case 'rounded':
            document.documentElement.style.setProperty('--card-border-radius', '1rem');
            document.querySelectorAll('.stat-card, .compact-content-card, .quick-actions-panel').forEach(card => {
                card.style.borderRadius = '16px';
            });
            document.querySelectorAll('.compact-avatar, .avatar-placeholder').forEach(avatar => {
                avatar.style.borderRadius = '12px';
            });
            break;
        case 'square':
            document.documentElement.style.setProperty('--card-border-radius', '0');
            document.querySelectorAll('.stat-card, .compact-content-card, .quick-actions-panel').forEach(card => {
                card.style.borderRadius = '0';
            });
            document.querySelectorAll('.compact-avatar, .avatar-placeholder').forEach(avatar => {
                avatar.style.borderRadius = '0';
            });
            break;
        case 'glass':
            document.documentElement.style.setProperty('--card-border-radius', '0.5rem');
            // Glass specific styling for compact layout
            document.querySelectorAll('.card-glass').forEach(card => {
                if (card.classList.contains('stat-card') || 
                    card.classList.contains('compact-content-card') || 
                    card.classList.contains('quick-actions-panel')) {
                    card.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    card.style.backdropFilter = 'blur(10px)';
                    card.style.WebkitBackdropFilter = 'blur(10px)';
                    card.style.border = '1px solid rgba(255, 255, 255, 0.2)';
                    card.style.borderRadius = '8px';
                }
            });
            
            // Dark mode specific glass styling
            if (document.body.classList.contains('dark-mode')) {
                document.querySelectorAll('.card-glass').forEach(card => {
                    if (card.classList.contains('stat-card') || 
                        card.classList.contains('compact-content-card') || 
                        card.classList.contains('quick-actions-panel')) {
                        card.style.backgroundColor = 'rgba(30, 41, 59, 0.8)';
                        card.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                    }
                });
            }
            
            // Restore rounded avatars for glass style
            document.querySelectorAll('.compact-avatar, .avatar-placeholder').forEach(avatar => {
                avatar.style.borderRadius = '8px';
            });
            break;
    }
    
    console.log('Compact dashboard card style applied:', cardStyle);
}

// Listen for storage events to update card style if changed in another tab
window.addEventListener('storage', function(e) {
    if (e.key === 'selectedCardStyle') {
        console.log('Card style changed in another tab:', e.newValue);
        applyCardStyle();
    }
});

// Listen for direct custom events from the settings page
document.addEventListener('cardStyleChanged', function(e) {
    console.log('Received card style change event in compact dashboard:', e.detail.cardStyle);
    applyCardStyle();
});
</script>
@endsection

@push('styles')
<style>
    /* Import Inter font for more professional look */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* CSS Variables for card styling */
    :root {
        --card-border-radius: 1rem;
        --card-background-color: white;
        --card-border-color: rgba(0, 0, 0, 0.125);
        --card-box-shadow: 5px 5px 15px rgba(0,0,0,0.05), -5px -5px 15px rgba(255,255,255,0.6);
        --card-hover-transform: translateY(-4px);
        --card-hover-shadow: 8px 8px 20px rgba(0,0,0,0.1), -8px -8px 20px rgba(255,255,255,0.8);
        --avatar-border-radius: 12px;
    }
    
    /* Dark mode card variables */
    body.dark-mode {
        --card-background-color: #1e293b;
        --card-border-color: rgba(255, 255, 255, 0.125);
        --card-box-shadow: 5px 5px 15px rgba(0,0,0,0.3), -5px -5px 15px rgba(30,41,59,0.5);
        --card-hover-shadow: 8px 8px 20px rgba(0,0,0,0.4), -8px -8px 20px rgba(30,41,59,0.6);
    }
    
    /* Compact Layout Dashboard Styles */
    .layout-compact {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        padding: 1.25rem;
    }
    
    body.dark-mode .layout-compact {
        background-color: #0f172a;
    }
    
    /* Card style variations */
    .card-rounded {
        border-radius: 16px !important;
        overflow: hidden;
    }
    
    .card-square {
        border-radius: 0 !important;
        overflow: hidden;
    }
    
    .card-glass {
        border-radius: 8px !important;
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    body.dark-mode .card-glass {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Additional styles for sidebar compact mode */
    body.compact-sidebar .sidebar {
        overflow-y: visible !important; /* Override any other styles */
    }
    
    body.compact-sidebar .sidebar-content {
        overflow-y: visible !important; /* Override any other styles */
    }
    
    /* Fix vertical alignment of icons */
    body.compact-sidebar .sidebar .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        height: 48px; /* Fixed height for all nav items */
    }
    
    body.compact-sidebar .sidebar:hover .nav-link {
        justify-content: flex-start;
        padding: 0.75rem 1rem;
    }
    
    body.compact-sidebar .sidebar .nav-link i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin: 0;
        font-size: 1.25rem;
    }
    
    body.compact-sidebar .sidebar:hover .nav-link i {
        margin-right: 0.75rem;
    }
    
    body.compact-sidebar .sidebar .dropdown-menu {
        position: absolute !important;
        top: 0 !important;
        left: 70px !important;
        transform: none !important;
        margin-top: 0 !important;
        min-width: 200px !important;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
        z-index: 1060 !important;
    }
    
    body.compact-sidebar .sidebar:hover .dropdown-menu {
        left: 250px !important;
    }
    
    body.compact-sidebar .sidebar .nav-link.dropdown-toggle .dropdown-icon {
        display: none !important;
    }
    
    body.compact-sidebar .sidebar:hover .nav-link.dropdown-toggle .dropdown-icon {
        display: inline-block !important;
    }
    
    body.compact-sidebar .sidebar .nav-link.dropdown-toggle .nav-content {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    body.compact-sidebar .sidebar:hover .nav-link.dropdown-toggle .nav-content {
        justify-content: flex-start !important;
    }
    
    /* Stat Cards Enhancement */
    .stat-card {
        border-radius: var(--card-border-radius);
        transition: all 0.3s ease;
        overflow: hidden;
        border: none;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        box-shadow: var(--card-box-shadow);
    }
    
    body.dark-mode .stat-card {
        background: linear-gradient(145deg, var(--card-background-color), #0f172a);
        box-shadow: var(--card-box-shadow);
    }
    
    .stat-card:hover {
        transform: var(--card-hover-transform);
        box-shadow: var(--card-hover-shadow);
    }
    
    body.dark-mode .stat-card:hover {
        box-shadow: var(--card-hover-shadow);
    }
    
    .stat-icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: var(--avatar-border-radius);
        font-size: 1.25rem;
    }
    
    .bg-primary-soft {
        background: linear-gradient(135deg, #93c5fd, #3b82f6);
        color: white;
    }
    
    .bg-success-soft {
        background: linear-gradient(135deg, #86efac, #10b981);
        color: white;
    }
    
    .bg-info-soft {
        background: linear-gradient(135deg, #7dd3fc, #0ea5e9);
        color: white;
    }
    
    .bg-warning-soft {
        background: linear-gradient(135deg, #fed7aa, #f97316);
        color: white;
    }
    
    /* Content Cards Enhancement */
    .compact-content-card {
        border-radius: var(--card-border-radius);
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        box-shadow: var(--card-box-shadow);
    }
    
    body.dark-mode .compact-content-card {
        background: linear-gradient(145deg, var(--card-background-color), #0f172a);
        box-shadow: var(--card-box-shadow);
    }
    
    .compact-content-card:hover {
        transform: var(--card-hover-transform);
        box-shadow: var(--card-hover-shadow);
    }
    
    body.dark-mode .compact-content-card:hover {
        box-shadow: var(--card-hover-shadow);
    }
    
    .compact-content-card .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background: transparent;
    }
    
    body.dark-mode .compact-content-card .card-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    /* Avatar and List Item Styles */
    .compact-avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--avatar-border-radius);
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .compact-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--avatar-border-radius);
        color: white;
    }
    
    .student-image {
        background: linear-gradient(135deg, #93c5fd, #3b82f6);
    }
    
    .course-image {
        background: linear-gradient(135deg, #fed7aa, #f97316);
    }
    
    .list-group-item {
        transition: all 0.2s ease;
        border: none;
        margin-bottom: 5px;
        border-radius: 10px !important;
    }
    
    .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.03);
        transform: translateX(5px);
    }
    
    body.dark-mode .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    /* Quick Actions Panel Enhancement */
    .quick-actions-panel {
        position: fixed;
        right: 20px;
        top: 100px;
        width: 180px;
        z-index: 1000;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        box-shadow: 5px 5px 15px rgba(0,0,0,0.05), -5px -5px 15px rgba(255,255,255,0.6);
    }
    
    body.dark-mode .quick-actions-panel {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        box-shadow: 5px 5px 15px rgba(0,0,0,0.3), -5px -5px 15px rgba(30,41,59,0.5);
    }
    
    .quick-actions-panel:hover {
        box-shadow: 8px 8px 20px rgba(0,0,0,0.1), -8px -8px 20px rgba(255,255,255,0.8);
    }
    
    body.dark-mode .quick-actions-panel:hover {
        box-shadow: 8px 8px 20px rgba(0,0,0,0.4), -8px -8px 20px rgba(30,41,59,0.6);
    }
    
    .quick-actions-panel .card-header {
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 1rem 1.25rem;
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    body.dark-mode .quick-actions-panel .card-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .quick-action-btn {
        border-radius: 10px;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        font-weight: 500;
        border: none;
        background: linear-gradient(145deg, #f0f0f0, #ffffff);
        box-shadow: 3px 3px 7px rgba(0,0,0,0.05), -3px -3px 7px rgba(255,255,255,0.6);
    }
    
    body.dark-mode .quick-action-btn {
        background: linear-gradient(145deg, #0f172a, #1e293b);
        box-shadow: 3px 3px 7px rgba(0,0,0,0.2), -3px -3px 7px rgba(30,41,59,0.3);
        color: #e2e8f0;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 5px 5px 10px rgba(0,0,0,0.1), -5px -5px 10px rgba(255,255,255,0.8);
    }
    
    body.dark-mode .quick-action-btn:hover {
        box-shadow: 5px 5px 10px rgba(0,0,0,0.3), -5px -5px 10px rgba(30,41,59,0.5);
    }
    
    /* Scrollbar Styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    body.dark-mode .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    body.dark-mode .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    
    /* Misc Enhancements */
    .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .card-body {
        padding: 1rem 1.25rem;
    }
    
    .fw-semibold {
        font-weight: 600;
    }
    
    .fs-sm {
        font-size: 0.875rem;
    }
    
    .btn-link {
        text-decoration: none;
    }
    
    .btn-outline-primary {
        border-color: rgba(59, 130, 246, 0.5);
    }
    
    .btn-outline-primary:hover {
        background-color: rgba(59, 130, 246, 0.08);
        color: #3b82f6;
    }
    
    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        border-radius: 8px;
    }
    
    /* Dark Mode Adjustments */
    body.dark-mode .card {
        background-color: #1e293b;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    
    body.dark-mode .card-title {
        color: #f1f5f9;
    }
    
    body.dark-mode .card-subtitle {
        color: #94a3b8;
    }
    
    body.dark-mode .list-group-item {
        background-color: #1e293b;
        border-color: rgba(255, 255, 255, 0.05);
    }
    
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    
    body.dark-mode .btn-outline-primary {
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.5);
    }
    
    body.dark-mode .btn-outline-primary:hover {
        background-color: rgba(96, 165, 250, 0.1);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.7);
    }
    
    body.dark-mode .quick-actions-panel {
        background-color: #1e293b;
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .quick-actions-panel {
            display: none;
        }
    }
    
    @media (max-width: 767.98px) {
        .layout-compact {
            padding: 0.75rem;
        }
        
        .stat-icon-container {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .card-title {
            font-size: 1.25rem;
        }
    }
    
    /* Fix dropdown and special navigation items */
    body.compact-sidebar .sidebar .nav-item.dropdown .nav-link.dropdown-toggle {
        height: 48px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-link.dropdown-toggle {
        padding: 0.75rem 1rem;
        justify-content: space-between;
    }
    
    body.compact-sidebar .sidebar .upgrade-btn {
        height: 48px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    body.compact-sidebar .sidebar:hover .upgrade-btn {
        padding: 0.5rem 1rem;
        justify-content: flex-start;
    }
    
    body.compact-sidebar .sidebar .upgrade-btn i {
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }
    
    body.compact-sidebar .sidebar:hover .upgrade-btn i {
        margin-right: 0.75rem;
    }
    
    body.compact-sidebar .sidebar .premium-indicator {
        height: 48px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    body.compact-sidebar .sidebar:hover .premium-indicator {
        padding: 0.5rem 1rem;
        justify-content: flex-start;
    }
    
    body.dark-mode .premium-indicator {
        height: 48px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    body.compact-sidebar .sidebar:hover .premium-indicator {
        padding: 0.5rem 1rem;
        justify-content: flex-start;
    }
    
    /* Card style variations for compact layout */
    .card-rounded, 
    .stat-card.card-rounded, 
    .compact-content-card.card-rounded,
    .quick-actions-panel.card-rounded {
        border-radius: 1rem !important;
        overflow: hidden;
    }
    
    .card-rounded .card-header,
    .card-rounded .card-footer {
        border-radius: 1rem 1rem 0 0 !important;
    }
    
    .card-square, 
    .stat-card.card-square, 
    .compact-content-card.card-square,
    .quick-actions-panel.card-square {
        border-radius: 0 !important;
        overflow: hidden;
    }
    
    .card-square .card-header,
    .card-square .card-footer {
        border-radius: 0 !important;
    }
    
    .card-glass, 
    .stat-card.card-glass, 
    .compact-content-card.card-glass,
    .quick-actions-panel.card-glass {
        border-radius: 0.5rem !important;
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    body.dark-mode .card-glass,
    body.dark-mode .stat-card.card-glass,
    body.dark-mode .compact-content-card.card-glass,
    body.dark-mode .quick-actions-panel.card-glass {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush 