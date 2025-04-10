@extends('tenant.layouts.app')

@section('title', 'Department Dashboard - Standard Layout')

@section('content')
<div class="container-fluid py-2 layout-standard">
    <div class="row">
        <!-- Main Content -->
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-primary bg-opacity-10 me-3">
                                <i class="fas fa-chalkboard-teacher fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Total Instructors</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $instructorCount ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-primary bg-opacity-10 py-3">
                            <small class="text-primary">
                                <i class="fas fa-chart-line me-1"></i>
                                Active Teaching Staff
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10 me-3">
                                <i class="fas fa-user-graduate fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Total Students</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $studentCount ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-success bg-opacity-10 py-3">
                            <small class="text-success">
                                <i class="fas fa-users me-1"></i>
                                Enrolled Learners
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-info bg-opacity-10 me-3">
                                <i class="fas fa-clipboard-list fs-4 text-info"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Pending Requirements</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $pendingRequirements ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-info bg-opacity-10 py-3">
                            <small class="text-info">
                                <i class="fas fa-clock me-1"></i>
                                Awaiting Review
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-warning bg-opacity-10 me-3">
                                <i class="fas fa-book fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Active Courses</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $activeCourses ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-warning bg-opacity-10 py-3">
                            <small class="text-warning">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Current Semester
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Row for Students and Courses -->
            <div class="d-flex flex-wrap justify-content-between mt-4">
                <!-- Recent Enrolled Students Section -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Recent Enrolled Students</p>
                    <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 200px;">
                        @forelse($students ?? [] as $student)
                            <div class="user">
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
                                        <span class="name" style="font-size: 0.85em;">{{ $student->student_id }}</span>
                                        <p class="username {{ $student->status === 'Regular' ? 'text-green-500' : ($student->status === 'Irregular' ? 'text-yellow-500' : 'text-red-500') }}" 
                                           style="font-size: 0.75em; margin: 0;">
                                            {{ $student->status }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="user">
                                <p class="text-xs text-coolGray-500 text-center">No students enrolled yet</p>
                            </div>
                        @endforelse
                    </div>
                    <a class="more" href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" style="font-size: 0.85em;">See more</a>
                </div>

                <!-- Available Courses Section -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Available Courses</p>
                    <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 200px;">
                        @forelse($courses ?? [] as $course)
                            <div class="user">
                                <div class="image">
                                    <div class="w-full h-full flex items-center justify-center course-image">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="user__content">
                                    <div class="text">
                                        <span class="name" style="font-size: 0.85em;">{{ $course->name }}</span>
                                        <div class="d-flex align-items-center gap-2" style="font-size: 0.75em; margin: 0;">
                                            <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }} rounded-pill" style="font-size: 0.65em; padding: 0.25em 0.5em;">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div style="margin-left: auto; text-align: right;">
                                        <span class="badge bg-info rounded-pill">
                                            <i class="fas fa-users fa-xs me-1"></i>{{ $course->students_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="user">
                                <p class="text-xs text-coolGray-500 text-center">No courses available</p>
                            </div>
                        @endforelse
                    </div>
                    <a class="more" href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" style="font-size: 0.85em;">See more</a>
                </div>

                <!-- Requirements Submitted Graph -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Requirements Submitted</p>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%; padding: 10px;">
                        <canvas id="requirementsChart"></canvas>
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
    console.log('Standard dashboard layout loaded');
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
            labels: ['Form 137', 'Good Moral', 'Birth Cert', 'Medical Cert', 'Photo', 'Enrollment'],
            datasets: [{
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
                borderRadius: 4,
                maxBarThickness: 25
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
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#111827',
                    bodyColor: '#111827',
                    titleFont: {
                        size: 12,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 11
                    },
                    padding: 12,
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    displayColors: true,
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
                            size: 10
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
                            size: 9
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

// Function to apply the user's card style preference
function applyCardStyle() {
    // Get card style preference from localStorage
    const cardStyle = localStorage.getItem('selectedCardStyle') || 'square';
    console.log('Applying card style:', cardStyle);
    
    // Remove all card style classes first
    document.querySelectorAll('.card, .enrolled-card').forEach(card => {
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
            // Additional glass specific styling
            document.querySelectorAll('.card-glass').forEach(card => {
                if (!card.classList.contains('enrolled-card')) {
                    card.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    card.style.backdropFilter = 'blur(10px)';
                    card.style.WebkitBackdropFilter = 'blur(10px)';
                    card.style.border = '1px solid rgba(255, 255, 255, 0.2)';
                }
            });
            
            // Dark mode specific glass styling
            if (document.body.classList.contains('dark-mode')) {
                document.querySelectorAll('.card-glass').forEach(card => {
                    if (!card.classList.contains('enrolled-card')) {
                        card.style.backgroundColor = 'rgba(30, 41, 59, 0.8)';
                        card.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                    }
                });
            }
            break;
    }
    
    console.log('Card style applied:', cardStyle);
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
    console.log('Received card style change event in standard dashboard:', e.detail.cardStyle);
    applyCardStyle();
});
</script>
@endsection

@push('styles')
<style>
    /* Standard Layout Dashboard Styles */
    .layout-standard .card {
        margin-bottom: 1.5rem;
    }
    
    /* Dashboard Cards Styles */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .card {
        overflow: hidden;
        border-radius: 1rem;
    }
    
    .card-rounded, 
    .enrolled-card.card-rounded {
        border-radius: 1rem !important;
        overflow: hidden;
    }
    
    .card-square, 
    .enrolled-card.card-square {
        border-radius: 0 !important;
        overflow: hidden;
    }
    
    .card-glass, 
    .enrolled-card.card-glass {
        border-radius: 0.5rem !important;
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    body.dark-mode .card-glass,
    body.dark-mode .enrolled-card.card-glass {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Card styles */
    .card .rounded-circle {
        transition: all 0.3s ease;
    }
    
    .card-title {
        font-size: 2rem;
        color: rgb(3, 1, 43);
    }
    
    body.dark-mode .card-title {
        color: rgb(96, 165, 250);
    }
    
    .card-subtitle {
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .card-footer {
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
    }
    
    .card-footer small {
        font-weight: 500;
    }
    
    /* Recent Enrolled Students Section Styles */
    .shadow-dashboard {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .text-coolGray-900 {
        color: rgb(17, 24, 39);
    }
    
    .text-coolGray-500 {
        color: rgb(107, 114, 128);
    }
    
    .bg-blue-50 {
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .border-coolGray-100 {
        border-color: rgb(243, 244, 246);
    }
    
    .text-green-500 {
        color: rgb(16, 185, 129);
    }
    
    .text-green-600:hover {
        color: rgb(5, 150, 105);
    }
    
    .rounded-md {
        border-radius: 0.375rem;
    }
    
    .rounded-full {
        border-radius: 9999px;
    }
    
    /* Status Colors */
    .bg-green-100 {
        background-color: rgba(16, 185, 129, 0.1);
    }
    
    .text-green-800 {
        color: rgb(6, 95, 70);
    }
    
    .bg-yellow-100 {
        background-color: rgba(245, 158, 11, 0.1);
    }
    
    .text-yellow-800 {
        color: rgb(146, 64, 14);
    }

    /* Scrollbar Styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #ffffff;
    }

    body.dark-mode .custom-scrollbar::-webkit-scrollbar-track {
        background: #1F2937;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #9CA3AF;
        border-radius: 20px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }

    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #9CA3AF #ffffff;
    }

    body.dark-mode .overflow-y-auto {
        scrollbar-color: #6B7280 #1F2937;
    }

    /* Last item in list */
    .last\:border-b-0:last-child {
        border-bottom-width: 0;
    }

    .card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .title {
        font-weight: 800;
    }

    .user {
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 8px 12px;
    }

    .user__content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-grow: 1;
    }

    .user__container {
        display: flex;
        flex-direction: column;
    }

    .name {
        font-weight: 800;
    }

    .username {
        font-size: .9em;
        color: #64696e;
    }

    body.dark-mode .username {
        color: #9CA3AF;
    }

    .image {
        width: 40px;
        height: 40px;
        background: rgb(22,19,70);
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(89,177,237,1) 100%);
        border-radius: 50%;
        margin-right: 10px;
        overflow: hidden;
    }

    .more {
        display: block;
        text-decoration: none;
        color: rgb(29, 155, 240);
        font-weight: 800;
        padding: 8px 12px;
    }

    .user:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    body.dark-mode .user:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .more:hover {
        background-color: #b3b6b6;
        border-radius: 0px 0px 15px 15px;
    }

    body.dark-mode .more:hover {
        background-color: #4B5563;
    }

    .enrolled-card {
        padding: 15px;
        display: flex;
        flex-direction: column;
        height: 280px;
        background: rgb(255, 255, 255);
        border-radius: 1rem;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px,
            rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
        transition: all ease-in-out 0.3s;
    }

    body.dark-mode .enrolled-card {
        background: #1F2937;
        box-shadow: rgba(0, 0, 0, 0.4) 0px 6px 12px -2px,
            rgba(0, 0, 0, 0.5) 0px 3px 7px -3px;
    }

    .enrolled-card:hover {
        background-color: #fdfdfd;
        box-shadow: rgba(0, 0, 0, 0.09) 0px 2px 1px, 
            rgba(0, 0, 0, 0.09) 0px 4px 2px,
            rgba(0, 0, 0, 0.09) 0px 8px 4px, 
            rgba(0, 0, 0, 0.09) 0px 16px 8px,
            rgba(0, 0, 0, 0.09) 0px 32px 16px;
    }

    body.dark-mode .enrolled-card:hover {
        background-color: #2D3748;
        box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 1px, 
            rgba(0, 0, 0, 0.2) 0px 4px 2px,
            rgba(0, 0, 0, 0.2) 0px 8px 4px, 
            rgba(0, 0, 0, 0.2) 0px 16px 8px,
            rgba(0, 0, 0, 0.2) 0px 32px 16px;
    }

    /* Course Card Styles */
    .enrolled-card .user:hover {
        background-color: rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    body.dark-mode .enrolled-card .user:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .enrolled-card .user .image {
        transition: transform 0.2s ease;
    }

    .enrolled-card .user:hover .image {
        transform: scale(1.1);
    }

    /* Course color variations */
    .course-image {
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(253,189,74,1) 100%);
    }

    .student-image {
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(89,177,237,1) 100%);
    }

    /* Chart Card Styles */
    .chart-container {
        padding: 10px;
        height: calc(100% - 40px);
        width: 100%;
    }

    /* Ensure chart container takes remaining space */
    .enrolled-card:has(#requirementsChart) {
        width: auto !important;
    }

    .enrolled-card:has(#requirementsChart) .chart-container {
        flex: 1;
        margin-top: 10px;
    }

    body.dark-mode .enrolled-card:has(#requirementsChart) {
        background: #1F2937;
    }
</style>
@endpush 