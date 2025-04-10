@extends('tenant.layouts.app')

@section('title', 'Department Dashboard - Modern Layout')

@section('content')

    <!-- Modern Layout Grid -->
    <div class="modern-layout-grid row">
        <!-- Main Content Column -->
        <div class="col-md-9 modern-main-content pe-md-4">
            <!-- Content Cards in 2-column layout -->
            <div class="d-flex flex-wrap justify-content-between">
                <!-- Recent Enrolled Students Section -->
                <div class="enrolled-card modern-card mb-4" style="width: 48%;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Recent Enrolled Students</h6>
                        <a href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-external-link-alt me-1"></i> View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 220px;">
                            @forelse($students ?? [] as $student)
                                <div class="user modern-user">
                                    <div class="image">
                                        @if($student->avatar)
                                            <img src="{{ asset('storage/' . $student->avatar) }}" 
                                                alt="{{ $student->student_id }}" 
                                                class="w-full h-full rounded-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center student-image">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="user__content">
                                        <div class="text">
                                            <span class="name">{{ $student->student_id }}</span>
                                            <p class="username {{ $student->status === 'Regular' ? 'text-green-500' : ($student->status === 'Irregular' ? 'text-yellow-500' : 'text-red-500') }}">
                                                {{ $student->status }}
                                            </p>
                                        </div>
                                        <button class="btn btn-sm btn-light rounded-pill">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center">
                                    <p class="text-muted">No students enrolled yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Available Courses Section -->
                <div class="enrolled-card modern-card mb-4" style="width: 48%;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Available Courses</h6>
                        <a href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-external-link-alt me-1"></i> View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 220px;">
                            @forelse($courses ?? [] as $course)
                                <div class="user modern-user">
                                    <div class="image">
                                        <div class="w-full h-full flex items-center justify-center course-image">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="user__content">
                                        <div class="text">
                                            <span class="name">{{ $course->name }}</span>
                                            <div class="d-flex align-items-center gap-2">
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
                                <div class="p-4 text-center">
                                    <p class="text-muted">No courses available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Requirements Submitted Graph -->
                <div class="enrolled-card modern-card w-100 mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">Requirements Submission Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                            <canvas id="requirementsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Stat Cards in Column -->
        <div class="col-md-3 modern-sidebar">
            <div class="statistics-sidebar">
                <div class="mb-3">
                    <div class="card h-100 border-0 shadow-sm modern-stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3">
                                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                                </div>
                                <h6 class="card-subtitle mb-0 text-muted">Instructors</h6>
                            </div>
                            <h2 class="card-title mb-0 fw-bold">{{ $instructorCount ?? 0 }}</h2>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card h-100 border-0 shadow-sm modern-stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-success bg-opacity-10 me-3">
                                    <i class="fas fa-user-graduate text-success"></i>
                                </div>
                                <h6 class="card-subtitle mb-0 text-muted">Students</h6>
                            </div>
                            <h2 class="card-title mb-0 fw-bold">{{ $studentCount ?? 0 }}</h2>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card h-100 border-0 shadow-sm modern-stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-info bg-opacity-10 me-3">
                                    <i class="fas fa-clipboard-list text-info"></i>
                                </div>
                                <h6 class="card-subtitle mb-0 text-muted">Requirements</h6>
                            </div>
                            <h2 class="card-title mb-0 fw-bold">{{ $pendingRequirements ?? 0 }}</h2>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card h-100 border-0 shadow-sm modern-stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-warning bg-opacity-10 me-3">
                                    <i class="fas fa-book text-warning"></i>
                                </div>
                                <h6 class="card-subtitle mb-0 text-muted">Courses</h6>
                            </div>
                            <h2 class="card-title mb-0 fw-bold">{{ $activeCourses ?? 0 }}</h2>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"></div>
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
    console.log('Modern dashboard layout loaded');
    setupRequirementsChart();
    
    // Apply user's card style preference
    applyCardStyle();
});

// Requirements Chart
const setupRequirementsChart = () => {
    const ctx = document.getElementById('requirementsChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Form 137', 'Good Moral', 'Birth Certificate', 'Medical Certificate', 'ID Photo', 'Enrollment'],
            datasets: [{
                label: 'Completion Rate',
                data: [85, 92, 78, 65, 95, 70],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(46, 204, 113, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(46, 204, 113, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                maxBarThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#111827',
                    bodyColor: '#111827',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 15,
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            return `Submitted: ${context.parsed.y}%`;
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
                            size: 12
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
                            size: 12
                        },
                        color: '#6B7280'
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
};

// Apply user's card style preference
function applyCardStyle() {
    // Get card style preference from localStorage
    const cardStyle = localStorage.getItem('selectedCardStyle') || 'square';
    console.log('Applying card style in modern dashboard:', cardStyle);
    
    // Target all card elements in modern layout
    const cardSelectors = [
        '.modern-card', 
        '.modern-stat-card',
        '.card'
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
            break;
        case 'square':
            document.documentElement.style.setProperty('--card-border-radius', '0');
            break;
        case 'glass':
            document.documentElement.style.setProperty('--card-border-radius', '0.5rem');
            document.querySelectorAll('.card-glass').forEach(card => {
                card.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                card.style.backdropFilter = 'blur(10px)';
                card.style.WebkitBackdropFilter = 'blur(10px)';
                card.style.border = '1px solid rgba(255, 255, 255, 0.2)';
            });
            
            // Dark mode specific glass styling
            if (document.body.classList.contains('dark-mode')) {
                document.querySelectorAll('.card-glass').forEach(card => {
                    card.style.backgroundColor = 'rgba(30, 41, 59, 0.8)';
                    card.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                });
            }
            break;
    }
    
    console.log('Modern dashboard card style applied:', cardStyle);
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
    console.log('Received card style change event in modern dashboard:', e.detail.cardStyle);
    applyCardStyle();
});
</script>
@endsection

@push('styles')
<style>
    /* CSS Variables for card styling */
    :root {
        --card-border-radius: 1rem;
        --card-background-color: white;
        --card-border-color: rgba(0, 0, 0, 0.125);
        --card-box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --card-hover-transform: translateY(-5px);
        --card-hover-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    }
    
    /* Dark mode card variables */
    body.dark-mode {
        --card-background-color: #1F2937;
        --card-border-color: rgba(255, 255, 255, 0.1);
        --card-box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3), 0 4px 6px -2px rgba(0,0,0,0.2);
        --card-hover-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
    }
    
    /* Modern Layout Dashboard Styles */
    .layout-modern {
        background-color: #f8f9fa;
    }
    
    body.dark-mode .layout-modern {
        background-color: #1a202c;
    }
    
    .layout-modern .dashboard-page-header {
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    body.dark-mode .layout-modern .dashboard-page-header {
        border-color: rgba(255,255,255,0.1);
    }
    
    .layout-modern .card {
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-box-shadow);
        border: none;
        overflow: hidden;
    }
    
    .modern-stat-card {
        padding: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .modern-stat-card:hover {
        transform: var(--card-hover-transform);
        box-shadow: var(--card-hover-shadow) !important;
    }
    
    .modern-card {
        background: var(--card-background-color);
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-box-shadow);
        overflow: hidden;
    }
    
    body.dark-mode .modern-card {
        background: var(--card-background-color);
    }
    
    /* Card style variations */
    .card-rounded {
        border-radius: 1rem !important;
        overflow: hidden;
    }
    
    .card-square {
        border-radius: 0 !important;
        overflow: hidden;
    }
    
    .card-glass {
        border-radius: 0.5rem !important;
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
    
    .modern-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }
    
    body.dark-mode .modern-card .card-header {
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .modern-user {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    body.dark-mode .modern-user {
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .modern-user:last-child {
        border-bottom: none;
    }
    
    .modern-user .name {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .modern-user .username {
        font-size: 0.8rem;
    }
    
    /* Modern Main Content and Sidebar */
    .modern-main-content {
        padding-right: 2rem;
    }
    
    .modern-sidebar {
        padding-left: 0.5rem;
    }
    
    /* Sidebar Stats Cards */
    .statistics-sidebar .card-title {
        font-size: 1.75rem;
    }
    
    .statistics-sidebar .card-subtitle {
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    /* Chart styles for modern layout */
    .chart-container {
        padding: 1rem;
    }
    
    /* Quick links */
    .list-group-item-action {
        padding: 0.75rem 1.25rem;
        transition: all 0.2s ease;
    }
    
    .list-group-item-action:hover {
        background-color: rgba(59, 130, 246, 0.05);
        color: #3b82f6;
    }
    
    body.dark-mode .list-group-item-action:hover {
        background-color: rgba(59, 130, 246, 0.1);
        color: #60a5fa;
    }
    
    @media (max-width: 768px) {
        .modern-layout-grid {
            flex-direction: column-reverse;
        }
        
        .modern-main-content {
            padding-right: 1rem;
            margin-top: 2rem;
        }
        
        .enrolled-card {
            width: 100% !important;
        }
    }
    
    /* Modern Dashboard Layout Styles */
    .modern-layout-grid {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    
    .modern-main-content {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }
    
    .modern-sidebar {
        padding: 1.5rem;
    }
    
    .modern-card {
        background-color: var(--card-background-color, white);
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }
    
    .modern-stat-card {
        padding: 1rem;
        border: none;
        background-color: var(--card-background-color, white);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .modern-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
    }
    
    .modern-user {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem;
    }
    
    .modern-user:last-child {
        border-bottom: none;
    }
    
    body.dark-mode .modern-user {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .modern-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem;
        font-weight: 600;
    }
    
    body.dark-mode .modern-card .card-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    /* Card style variations for modern layout */
    .card-rounded, 
    .modern-stat-card.card-rounded, 
    .modern-card.card-rounded {
        border-radius: 1rem !important;
        overflow: hidden;
    }
    
    .card-rounded .card-header,
    .card-rounded .card-footer {
        border-radius: 1rem 1rem 0 0 !important;
    }
    
    .card-square, 
    .modern-stat-card.card-square, 
    .modern-card.card-square {
        border-radius: 0 !important;
        overflow: hidden;
    }
    
    .card-square .card-header,
    .card-square .card-footer {
        border-radius: 0 !important;
    }
    
    .card-glass, 
    .modern-stat-card.card-glass, 
    .modern-card.card-glass {
        border-radius: 0.5rem !important;
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    body.dark-mode .card-glass,
    body.dark-mode .modern-stat-card.card-glass,
    body.dark-mode .modern-card.card-glass {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush 