<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BukSkwela - Super Admin</title>
    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Dark Mode Variables */
        :root {
            /* Light mode variables */
            --bg-color: #f3f4f6;
            --text-color: #111827;
            --text-muted: #6B7280;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --navbar-bg: #ffffff;
            --primary-color: rgb(3, 1, 43);
            --primary-hover: rgb(10, 8, 70);
            --accent-color: rgb(213, 147, 4);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --input-bg: #ffffff;
            --input-border: #e5e7eb;
            --dropdown-bg: #ffffff;
            --hover-bg: rgba(243, 244, 246, 0.8);
            --logo-filter: none;
            --avatar-border: #e5e7eb;
            --modal-bg: #ffffff;
            --badge-bg: rgba(3, 1, 43, 0.1);
            --badge-color: rgb(3, 1, 43);
            --sidebar-icon-color: rgb(0, 0, 0);
            --sidebar-text-color: rgb(0, 0, 0);
            --buk-text-color: rgb(213, 147, 4);
            --buk-only-color: rgb(213, 147, 4);
        }
        
        /* Dark mode class applied to body */
        body.dark-mode {
            --bg-color: #111827;
            --text-color: #f3f4f6;
            --text-muted: #9CA3AF;
            --border-color: #374151;
            --card-bg: #1F2937;
            --sidebar-bg: #1F2937;
            --navbar-bg: #1F2937;
            --primary-color: rgb(59, 130, 246);
            --primary-hover: rgb(96, 165, 250);
            --accent-color: rgb(251, 191, 36);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --input-bg: #374151;
            --input-border: #4B5563;
            --dropdown-bg: #1F2937;
            --hover-bg: rgba(55, 65, 81, 0.8);
            --logo-filter: brightness(1.5) contrast(1.2);
            --avatar-border: #4B5563;
            --modal-bg: #1F2937;
            --badge-bg: rgba(59, 130, 246, 0.2);
            --badge-color: rgb(96, 165, 250);
            --sidebar-icon-color: #a0aec0;
            --sidebar-text-color: #e2e8f0;
            --buk-text-color: rgb(251, 191, 36);
            --buk-only-color: #ffffff;
            --dropdown-active-bg: rgb(59, 130, 246);
            --dropdown-active-text: #ffffff;
        }
        
        /* Apply variables to elements */
        body {
            background: var(--bg-color);
            color: var(--text-color);
            max-width: 100vw;
            transition: all 0.3s ease;
        }
        
        /* Specific dark mode overrides with higher specificity */
        body.dark-mode {
            background-color: var(--bg-color) !important;
            color: var(--text-color) !important;
        }
        
        body.dark-mode .sidebar {
            background-color: var(--sidebar-bg) !important;
            border-right-color: var(--border-color) !important;
        }
        
        body.dark-mode .top-navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom-color: var(--border-color) !important;
        }
        
        body.dark-mode .main-content {
            background-color: var(--bg-color) !important;
        }
        
        body.dark-mode .dropdown-menu {
            background-color: var(--dropdown-bg) !important;
            border-color: var(--border-color) !important;
        }
        
        body.dark-mode .dropdown-item {
            color: var(--text-color) !important;
        }
        
        body.dark-mode .dropdown-item:hover {
            background-color: var(--hover-bg) !important;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: fixed;
        }

        body {
            background: #f3f4f6;
            max-width: 100vw;
        }

        /* Layout wrapper */
        .layout-wrapper {
            display: flex;
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            background: #fff;
            border-right: 2px solid #e5edff;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            border-top-right-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
        }

        .sidebar-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
            width: 100%;
        }
        
        .sidebar-content::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .nav.flex-column {
            width: 100%;
            padding: 0 1rem;
        }

        .sidebar .nav-link {
            color: var(--sidebar-text-color);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            width: 100%;
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            color: var(--sidebar-icon-color);
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: #ffffff;
        }

        /* Main content wrapper */
        .content-wrapper {
            flex: 1;
            margin-left: 250px;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            max-width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        /* Top navbar styles */
        .top-navbar {
            flex-shrink: 0;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 2px solid #e5edff;
            z-index: 1050;
            height: 60px;
            display: flex;
            align-items: center;
            width: 100%;
            position: relative;
        }

        /* Main content styles */
        .main-content {
            flex: 1;
            padding: 1rem;
            background: #f3f4f6;
            overflow-y: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .main-content::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Logo container styles */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 1rem;
            gap: 0.5rem;
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
            max-height: 60px;
        }

        .logo-container h4 {
            font-size: 1rem;
            margin: 0;
            color: var(--buk-text-color);
            font-weight: 600;
        }
        
        /* Tenant name display */
        .tenant-name {
            font-size: 1.15rem !important;
            margin: 0.5rem 0 !important;
            color: var(--buk-text-color) !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 1px var(--shadow-color);
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transition: all 0.3s ease;
            text-align: center;
        }

        .tenant-buk {
            color: var(--buk-only-color) !important;
            transition: color 0.3s ease;
        }

        /* Card styles */
        .card, .settings-card, .card-body {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        /* Table styles */
        .table {
            color: var(--text-color) !important;
        }

        .table tbody tr {
            background-color: var(--card-bg) !important;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: var(--hover-bg) !important;
        }

        .table thead th {
            background-color: var(--primary-color) !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
                max-width: 100%;
            }

            .top-navbar {
                margin-left: 0;
            }
        }

        /* User avatar styles */
        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .user-avatar-container {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
        }

        .user-avatar-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Dashboard card hover effects */
        .dashboard-card {
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
        }

        body.dark-mode .dashboard-card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
        }
    </style>
    @stack('styles')
</head>
<body class="{{ session('dark_mode') ? 'dark-mode' : '' }}">
    <div x-data="{ isSidebarOpen: false }" class="layout-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'show': isSidebarOpen }">
            <div class="sidebar-content">
                <div class="logo-container">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="BukSkwela Logo">
                    <span class="tenant-name">Super<span class="tenant-buk">Admin</span></span>
                </div>
                
                <ul class="nav flex-column px-3 flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}" href="{{ route('superadmin.tenants.index') }}">
                            <i class="fas fa-users"></i> Tenants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.payments.*') ? 'active' : '' }}" href="{{ route('superadmin.payments.index') }}">
                            <i class="fas fa-money-bill-wave"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super-admin.tenant-data.*') ? 'active' : '' }}" href="{{ route('super-admin.tenant-data.index') }}">
                            <i class="fas fa-database"></i> Tenant Databases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('superadmin.account.*') ? 'active' : '' }}" href="{{ route('superadmin.account.settings') }}">
                            <i class="fas fa-user-cog"></i> Account Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="top-navbar">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <button class="btn btn-link" @click="isSidebarOpen = !isSidebarOpen" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar-container">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 100%; height: 100%;">
                                        <span style="font-size: 1.2rem; font-weight: bold;">A</span>
                                    </div>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('superadmin.account.settings') }}"><i class="fas fa-cog"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ url('/superadmin/logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle dark mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            
            // Save preference to session using fetch
            fetch('/superadmin/toggle-dark-mode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dark_mode: isDarkMode })
            });
        }
    </script>
    @stack('scripts')
</body>
</html> 