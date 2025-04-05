<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ tenant('id') }}</title>
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
            width: 250px;
        }

        .nav.flex-column {
            width: 100%;
            padding: 0 1rem;
        }

        .sidebar .nav-link {
            color: rgb(0, 0, 0);
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
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link.dropdown-toggle::after {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
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

        .container-fluid {
            max-width: 100%;
            padding-left: 15px;
            padding-right: 15px;
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

        /* User avatar styles */
        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Upgrade button styles */
        .upgrade-btn {
            background: linear-gradient(135deg,rgb(13, 10, 71) 0%,rgb(16, 46, 199) 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
            width: calc(100% - 1rem);
        }

        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            z-index: 1100;
            margin-top: 0;
            min-width: 220px;
            background: #fff;
            position: absolute;
            left: 100%;
            top: 0;
            transform: translateX(10px);
            margin-left: 0;
        }

        .nav-item.dropdown {
            position: relative;
        }

        .nav-item.dropdown .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            padding: 0.75rem 1rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            color: #4b5563;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
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

        /* Logo container styles */
        .logo-container {
            padding: 1rem;
            text-align: center;
            width: 100%;
        }

        .logo-container img {
            width: 200px;
            height: auto;
            margin-bottom: 0.75rem;
            object-fit: contain;
            max-width: 100%;
        }

        .logo-container h4 {
            font-size: 1rem;
            margin: 0;
            color:rgb(213, 147, 4);
            font-weight: 600;
        }

        /* Navbar dropdown styles */
        .top-navbar .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            top: 100%;
            margin-top: 0.5rem;
            min-width: 200px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            z-index: 1060;
            background: #fff;
            transform: none !important;
        }

        .top-navbar .dropdown {
            position: relative;
        }

        .top-navbar .btn-link {
            color: #4b5563;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .top-navbar .btn-link:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .top-navbar .dropdown-item {
            padding: 0.75rem 1rem;
            color: #4b5563;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .top-navbar .dropdown-item:hover {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .top-navbar .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Override Bootstrap's dropdown styles */
        .dropdown-menu[data-bs-popper] {
            top: 100%;
            left: auto;
            right: 0;
            margin-top: 0.5rem;
        }

        .top-navbar .dropdown-menu-end {
            --bs-position: end;
        }

        /* Ensure dropdowns are above other elements */
        .top-navbar .dropdown {
            z-index: 1060;
        }

        .top-navbar .dropdown-menu.show {
            display: block !important;
        }

        /* User avatar container */
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

        /* Notification badge positioning */
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }

        .navbar-nav .dropdown-menu {
            position: absolute;
        }

        /* Sidebar dropdown styles */
        .sidebar .dropdown-menu {
            position: absolute !important;
            left: 0 !important;
            top: 100% !important;
            transform: none !important;
            margin-top: 0 !important;
            width: calc(100% - 2rem);
            min-width: unset;
            background: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            z-index: 1100;
            display: none;
        }

        .sidebar .dropdown-menu.show {
            display: block;
        }

        .sidebar .nav-item.dropdown {
            position: relative !important;
        }

        .sidebar .nav-link.dropdown-toggle::after {
            display: none;
        }

        .sidebar .dropdown-item {
            padding: 0.75rem 1rem;
            color: rgb(0, 0, 0);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .dropdown-item:hover {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .sidebar .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Update the nav-link styles for the Reports dropdown */
        .sidebar .nav-link.dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 0.75rem 1rem;
        }

        .sidebar .nav-link.dropdown-toggle:focus {
            outline: none;
        }

        .sidebar .nav-link.dropdown-toggle.show {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .sidebar .nav-link .nav-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link .dropdown-icon {
            font-size: 0.75rem;
            transition: transform 0.2s;
        }

        .sidebar .nav-link.show .dropdown-icon {
            transform: rotate(180deg);
        }

        /* Navbar specific dropdown styles */
        .navbar .dropdown-menu {
            position: absolute !important;
            inset: auto !important;
            right: 0 !important;
            left: auto !important;
            top: 100% !important;
            transform: none !important;
            margin-top: 0.5rem !important;
            min-width: 200px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            z-index: 1100;
            background: #fff;
        }

        .navbar .dropdown {
            position: relative !important;
        }

        /* Navbar buttons and controls */
        .navbar .btn-link {
            padding: 0.5rem;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .navbar .btn-link:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .navbar .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(30%, -30%);
        }

        /* User avatar styles */
        .navbar .user-avatar-container {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .navbar .user-avatar-container:hover {
            opacity: 0.8;
        }

        .navbar .user-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Navbar dropdown items */
        .navbar .dropdown-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            color: #4b5563;
            transition: all 0.2s ease;
        }

        .navbar .dropdown-item:hover {
            background: rgb(3, 1, 43);
            color: #fff;
        }

        .navbar .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
        }

        /* Navbar dropdown header */
        .navbar .dropdown-header {
            padding: 0.75rem 1rem;
            color: #4b5563;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Override Bootstrap's dropdown display */
        .navbar .dropdown-menu.show {
            display: block !important;
        }

        .navbar .dropdown-toggle::after {
            display: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div x-data="{ isSidebarOpen: false }" class="layout-wrapper">
    <!-- Sidebar -->
        <div class="sidebar" :class="{ 'show': isSidebarOpen }">
            <div class="sidebar-content">
                <div class="logo-container">
                    <img src="{{ asset('assets/images/logo.png') }}" 
                         alt="BukSkwela Logo">
                    <h4>{{ tenant('id') }}</h4>
        </div>
                <ul class="nav flex-column px-3 flex-grow-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('tenant.admin.dashboard', ['tenant' => tenant('id')]) }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.students.*') ? 'active' : '' }}" 
                   href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}">
                    <i class="fas fa-users"></i> Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.staff.*') ? 'active' : '' }}" 
                   href="{{ route('tenant.staff.index', ['tenant' => tenant('id')]) }}">
                    <i class="fas fa-chalkboard-teacher"></i> Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.courses.*') ? 'active' : '' }}" 
                   href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}">
                    <i class="fas fa-book"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.admin.requirements.*') ? 'active' : '' }}" 
                   href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}">
                    <i class="fas fa-clipboard-list"></i> Requirements
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}" 
                           href="#"
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <div class="nav-content">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </div>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" 
                               href="{{ route('tenant.reports.students', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-user-graduate"></i>
                                <span>Student Reports</span>
                            </a>
                            <a class="dropdown-item" 
                               href="{{ route('tenant.reports.staff', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-user-tie"></i>
                                <span>Staff Reports</span>
                            </a>
                            <a class="dropdown-item" 
                               href="{{ route('tenant.reports.courses', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-book-open"></i>
                                <span>Course Reports</span>
                            </a>
                            <a class="dropdown-item" 
                               href="{{ route('tenant.reports.requirements', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-tasks"></i>
                                <span>Requirements Reports</span>
                            </a>
                        </div>
                    </li>
                </ul>

                <!-- Upgrade to Pro Button -->
                <div class="mt-auto">
                    <button class="upgrade-btn w-100">
                        <i class="fas fa-crown me-2"></i> Upgrade to Pro
                    </button>
                </div>
            </div>
    </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
    <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light top-navbar">
        <div class="container-fluid">
                    <!-- Mobile menu button -->
                    <button @click="isSidebarOpen = !isSidebarOpen" 
                            class="btn btn-link d-lg-none">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <!-- Notifications -->
                        <div class="dropdown">
                            <button class="btn btn-link" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg"></i>
                                <span class="notification-badge badge rounded-pill bg-danger">3</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notificationsDropdown">
                                <div class="dropdown-header">
                                    Notifications
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-envelope"></i>
                                    <span>New message received</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-bell"></i>
                                    <span>System update available</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Important alert</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="#">
                                    View all
                                </a>
                            </div>
                        </div>

                        <!-- User menu -->
                        <div class="dropdown ms-3">
                            <button class="btn p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar-container">
                                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" 
                                         alt="User" class="user-avatar">
                                </div>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="userDropdown">
                                <div class="dropdown-header">
                                    <strong>Admin User</strong>
                                    <p class="mb-0 text-muted small">Administrator</p>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST" class="px-3 py-2">
                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                    </button>
                </form>
                            </div>
                        </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
            <main class="main-content">
        @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Bootstrap components -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize all popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>