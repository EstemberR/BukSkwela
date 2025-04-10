@extends('tenant.layouts.app')

@section('title', 'Department Dashboard')

@section('content')
<div class="container-fluid p-5">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h3>Loading your preferred dashboard layout...</h3>
        <p class="text-muted">Redirecting to your customized dashboard...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check user's layout preference
    function checkLayoutPreference() {
        // First try localStorage for immediate response
        const selectedLayout = localStorage.getItem('selectedDashboardLayout');
        
        console.log('Checking layout preference, localStorage value:', selectedLayout);
        
        if (selectedLayout) {
            console.log('Using layout from localStorage:', selectedLayout);
            redirectToLayout(selectedLayout);
            return;
        }
        
        // If not in localStorage, try to fetch from server
        const tenantId = "{{ tenant('id') }}";
        console.log('Tenant ID from blade:', tenantId);
        
        // Ensure we use the correct URL format that matches the Laravel route
        let getLayoutUrl;
        if (window.location.pathname.startsWith(`/${tenantId}`)) {
            // When accessing via tenant path, use tenant prefix
            getLayoutUrl = `/${tenantId}/settings/get-layout`;
        } else {
            // When accessing via subdomain
            getLayoutUrl = `/settings/get-layout`;
        }
        
        console.log('Fetching layout from URL:', getLayoutUrl);
        
        fetch(getLayoutUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache',
                'X-Tenant-ID': tenantId
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Layout fetch response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Layout data received:', data);
            if (data.success && data.dashboard_layout) {
                // Save to localStorage for future quick access
                localStorage.setItem('selectedDashboardLayout', data.dashboard_layout);
                console.log('Saved layout to localStorage and redirecting to:', data.dashboard_layout);
                redirectToLayout(data.dashboard_layout);
            } else if (data.success && !data.dashboard_layout) {
                console.log('Success but no dashboard_layout in response, using standard');
                redirectToLayout('standard');
            } else {
                // Default to standard if no preference is set
                console.log('No success in response, using standard layout');
                redirectToLayout('standard');
            }
        })
        .catch(error => {
            console.error('Error fetching layout preference:', error);
            // Default to standard on error
            redirectToLayout('standard');
        });
    }
    
    function redirectToLayout(layout) {
        // Check if layout is one of the supported layouts
        const supportedLayouts = ['standard', 'compact', 'modern'];
        console.log('Redirecting to layout:', layout, 'Supported:', supportedLayouts.includes(layout));
        
        if (!supportedLayouts.includes(layout)) {
            console.log('Unsupported layout, defaulting to standard');
            layout = 'standard'; // Default to standard for unsupported layout names
        }
        
        const tenantId = "{{ tenant('id') }}";
        let redirectUrl;
        
        // Build the redirect URL with appropriate tenant path
        if (window.location.pathname.startsWith(`/${tenantId}`)) {
            // When accessing via tenant path
            redirectUrl = `/${tenantId}/dashboard-${layout}`;
        } else {
            // When accessing via subdomain
            redirectUrl = `/dashboard-${layout}`;
        }
        
        console.log('Final redirect URL:', redirectUrl);
        
        // Redirect to the appropriate dashboard layout
        window.location.href = redirectUrl;
    }
    
    // Initialize the layout check
    console.log('Starting dashboard layout detection');
    checkLayoutPreference();
});

// Function to apply the user's card style preference
function applyCardStyle() {
    // Get card style preference from localStorage
    let cardStyle = localStorage.getItem('selectedCardStyle');
    
    // If not found in localStorage, check from backend (by reading body data attributes if set)
    if (!cardStyle && document.body.dataset.cardStyle) {
        cardStyle = document.body.dataset.cardStyle;
        // Store in localStorage for future use
        localStorage.setItem('selectedCardStyle', cardStyle);
    }
    
    // Default to 'square' if not set
    if (!cardStyle) {
        cardStyle = 'square';
        localStorage.setItem('selectedCardStyle', cardStyle);
    }
    
    console.log('Applying card style:', cardStyle);
    
    // Target all card types across all layouts
    const cardSelectors = '.card, .enrolled-card, .stat-card, .compact-content-card, .modern-stat-card, .modern-card';
    
    // Remove all card style classes first
    document.querySelectorAll(cardSelectors).forEach(card => {
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
            document.querySelectorAll(`${cardSelectors}.card-glass`).forEach(card => {
                card.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                card.style.backdropFilter = 'blur(10px)';
                card.style.WebkitBackdropFilter = 'blur(10px)';
                card.style.border = '1px solid rgba(255, 255, 255, 0.2)';
            });
            
            // Dark mode specific glass styling
            if (document.body.classList.contains('dark-mode')) {
                document.querySelectorAll(`${cardSelectors}.card-glass`).forEach(card => {
                    card.style.backgroundColor = 'rgba(30, 41, 59, 0.8)';
                    card.style.border = '1px solid rgba(255, 255, 255, 0.1)';
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

// Listen for custom event from settings page
document.addEventListener('cardStyleChanged', function(e) {
    console.log('Card style changed via custom event:', e.detail.cardStyle);
    applyCardStyle();
});

// Apply card style when DOM content is loaded
document.addEventListener('DOMContentLoaded', function() {
    applyCardStyle();
});
</script>
@endsection

@push('styles')
<style>
    /* Dashboard CSS Variables */
    :root {
        --card-border-radius: 1rem;
        --card-background-color: white;
        --card-border-color: rgba(0, 0, 0, 0.125);
        --card-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --card-hover-transform: translateY(-5px);
        --card-hover-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    }
    
    /* Dark mode card variables */
    body.dark-mode {
        --card-background-color: #1F2937;
        --card-border-color: rgba(255, 255, 255, 0.125);
        --card-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
        --card-hover-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
    }
    
    /* Dashboard Cards Styles */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: var(--card-hover-transform);
        box-shadow: var(--card-hover-shadow) !important;
    }
    
    /* General card styles that apply to all card types across all layouts */
    .card, .enrolled-card, .stat-card, .compact-content-card, .modern-stat-card, .modern-card {
        overflow: hidden;
        background-color: var(--card-background-color);
        border-color: var(--card-border-color);
        box-shadow: var(--card-box-shadow);
        transition: all 0.3s ease;
    }
    
    /* Card style variations */
    .card-rounded, 
    .enrolled-card.card-rounded, 
    .stat-card.card-rounded, 
    .compact-content-card.card-rounded, 
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
    .enrolled-card.card-square, 
    .stat-card.card-square, 
    .compact-content-card.card-square, 
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
    .enrolled-card.card-glass, 
    .stat-card.card-glass, 
    .compact-content-card.card-glass, 
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
    body.dark-mode .enrolled-card.card-glass,
    body.dark-mode .stat-card.card-glass,
    body.dark-mode .compact-content-card.card-glass,
    body.dark-mode .modern-stat-card.card-glass,
    body.dark-mode .modern-card.card-glass {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Sidebar positioning */
    .sidebar-right .row {
        flex-direction: row-reverse;
    }
    
    /* Layout structure classes */
    .layout-standard .card {
        margin-bottom: 1.5rem;
    }
    
    .layout-compact .card {
        margin-bottom: 1rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .layout-compact .col-md-3 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .layout-modern .dashboard-page-header {
        padding-bottom: 1rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    body.dark-mode .layout-modern .dashboard-page-header {
        border-color: rgba(255,255,255,0.1);
    }
    
    .layout-modern .card {
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    }
    
    .layout-modern .card-body {
        padding: 1.5rem;
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
    
    /* Existing modal styles */
    .modal-dialog {
        max-width: 80%;
    }
    
    .nav-tabs .nav-link {
        color: #4b5563;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link.active {
        color: rgb(3, 1, 43);
        border-bottom: 2px solid rgb(3, 1, 43);
        font-weight: 600;
    }
    
    body.dark-mode .nav-tabs .nav-link {
        color: #e2e8f0;
    }
    
    body.dark-mode .nav-tabs .nav-link.active {
        color: rgb(96, 165, 250);
        border-bottom: 2px solid rgb(96, 165, 250);
    }
    
    .list-group-item {
        border: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 0.5rem;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(3, 1, 43, 0.05);
        border-color: rgb(3, 1, 43);
    }
    
    body.dark-mode .list-group-item:hover {
        background-color: rgba(96, 165, 250, 0.05);
        border-color: rgb(96, 165, 250);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 1em;
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
    
    /* Layout notification styles */
    .layout-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 18px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        font-size: 14px;
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .layout-notification-info {
        background-color: #3b82f6;
        color: white;
        border-left: 4px solid #1d4ed8;
    }
    
    .layout-notification-warning {
        background-color: #f59e0b;
        color: white;
        border-left: 4px solid #b45309;
    }
    
    .layout-notification-error {
        background-color: #ef4444;
        color: white;
        border-left: 4px solid #b91c1c;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    body.dark-mode .layout-notification {
        box-shadow: 0 4px 12px rgba(0,0,0,0.35);
    }
</style>
@endpush
