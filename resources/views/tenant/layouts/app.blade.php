<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        
        /* Regular styles with CSS variables */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            background: var(--sidebar-bg);
            border-right: 2px solid var(--border-color);
            box-shadow: 4px 0 10px var(--shadow-color);
            border-top-right-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        /* Compact mode sidebar */
        body.compact-sidebar .sidebar {
            width: 70px;
            overflow: visible;
            z-index: 1060;
        }
        
        body.compact-sidebar .sidebar:hover {
            width: 250px;
            z-index: 1060;
        }
        
        body.compact-sidebar .content-wrapper {
            margin-left: 70px;
            max-width: calc(100% - 70px);
        }
        
        /* Fix icon alignment in compact mode */
        body.compact-sidebar .sidebar .nav-item {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 46px; /* Consistent height for all nav items */
            position: relative;
        }
        
        body.compact-sidebar .sidebar .nav-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0.75rem;
            width: 100%;
            height: 100%;
            position: relative;
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
            line-height: 1;
            position: absolute;
            left: 23px; /* Fixed position for icons */
            transform: translateX(-50%); /* Center the icon */
        }
        
        body.compact-sidebar .sidebar:hover .nav-link i {
            margin-right: 0;
            position: absolute;
            left: 20px; /* Fixed position when hovered */
            transform: translateX(0); /* No centering needed when hovered */
        }
        
        body.compact-sidebar .sidebar .nav-link span,
        body.compact-sidebar .sidebar .tenant-name,
        body.compact-sidebar .sidebar .upgrade-btn span {
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-block;
            line-height: 1;
            margin-left: 35px; /* Fixed distance from the left edge */
        }
        
        body.compact-sidebar .sidebar:hover .nav-link span,
        body.compact-sidebar .sidebar:hover .tenant-name,
        body.compact-sidebar .sidebar:hover .upgrade-btn span {
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Adjust dropdown items */
        body.compact-sidebar .sidebar .nav-item.dropdown {
            height: 46px;
        }
        
        body.compact-sidebar .sidebar .nav-item.dropdown .nav-link.dropdown-toggle {
            justify-content: center;
            padding: 0;
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-link.dropdown-toggle {
            justify-content: space-between;
            padding: 0.75rem 1rem;
        }
        
        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            width: 100%;
            position: relative;
        }
        
        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content i {
            position: absolute;
            left: 23px;
            transform: translateX(-50%);
        }
        
        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content i {
            position: absolute;
            left: 20px;
            transform: translateX(0);
        }
        
        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content span {
            margin-left: 35px;
        }
        
        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content {
            justify-content: flex-start;
        }
        
        body.compact-sidebar .sidebar .dropdown-icon {
            display: none;
            position: absolute;
            right: 15px;
        }
        
        body.compact-sidebar .sidebar:hover .dropdown-icon {
            display: inline-block;
        }
        
        /* Upgrade button and premium indicator */
        body.compact-sidebar .sidebar .upgrade-btn,
        body.compact-sidebar .sidebar .premium-indicator {
            display: none;
        }
        
        body.compact-sidebar .sidebar:hover .upgrade-btn,
        body.compact-sidebar .sidebar:hover .premium-indicator {
            display: none;
        }
        
        body.compact-sidebar .sidebar .upgrade-btn i,
        body.compact-sidebar .sidebar .premium-indicator i {
            display: none;
        }
        
        body.compact-sidebar .sidebar:hover .upgrade-btn i,
        body.compact-sidebar .sidebar:hover .premium-indicator i {
            display: none;
        }
        
        body.compact-sidebar .sidebar .upgrade-btn span,
        body.compact-sidebar .sidebar .premium-indicator span {
            display: none;
        }
        
        body.compact-sidebar .sidebar .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 1rem;
            gap: 0.5rem;
        }
        
        body.compact-sidebar .sidebar .logo-container img {
            max-width: 40px;
            max-height: 40px;
            transition: all 0.3s ease;
        }
        
        body.compact-sidebar .sidebar:hover .logo-container img {
            max-width: 100%;
            max-height: 60px;
        }
        
        body.compact-sidebar .sidebar .nav-link {
            padding: 0.75rem;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        body.compact-sidebar .sidebar:hover .nav-link {
            padding: 0.75rem 1rem;
            justify-content: flex-start;
        }
        
        body.compact-sidebar .sidebar .nav-link i {
            margin-right: 0;
            width: 20px;
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }
        
        body.compact-sidebar .sidebar:hover .nav-link i {
            margin-right: 0.75rem;
        }
        
        body.compact-sidebar .sidebar .dropdown-menu {
            left: 70px;
            top: 0;
        }
        
        body.compact-sidebar .sidebar:hover .dropdown-menu {
            left: 100%;
        }
        
        /* Fix for dropdown in compact mode */
        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        
        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content {
            justify-content: flex-start;
        }
        
        body.compact-sidebar .sidebar .dropdown-icon {
            display: none;
        }
        
        body.compact-sidebar .sidebar:hover .dropdown-icon {
            display: inline-block;
        }
        
        /* Auto-compact for layout-compact */
        body.layout-compact {
            /* This will be toggled by JavaScript */
        }
        
        .sidebar .nav-link {
            color: var(--sidebar-text-color);
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: var(--primary-color);
            color: #ffffff;
        }
        
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: #ffffff;
        }
        
        .top-navbar {
            background: var(--navbar-bg);
            box-shadow: 0 2px 10px var(--shadow-color);
            border-bottom: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .main-content {
            background: var(--bg-color);
            transition: all 0.3s ease;
        }
        
        .card, .settings-card, .card-body {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .dropdown-menu {
            background-color: var(--dropdown-bg) !important;
            border-color: var(--border-color) !important;
            transition: all 0.3s ease;
        }
        
        .dropdown-item {
            color: var(--text-color) !important;
        }
        
        .dropdown-item:hover {
            background-color: var(--hover-bg) !important;
        }
        
        /* Table styles for dark mode */
        .table {
            color: var(--text-color) !important;
        }
        
        .table tbody tr {
            background-color: var(--card-bg) !important;
        }
        
        .table tbody tr:nth-of-type(odd) {
            background-color: var(--hover-bg) !important;
        }
        
        /* Form controls */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border-color: var(--input-border) !important;
            color: var(--text-color) !important;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color), 0.25) !important;
        }
        
        /* Text colors */
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Enhance tenant name display */
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
        }

        .tenant-buk {
            color: var(--buk-only-color) !important;
            transition: color 0.3s ease;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 1rem 0;
            gap: 0.25rem;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            min-width: 2.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination li.active span {
            background-color: rgb(3, 1, 43);
            color: #fff;
            border-color: rgb(3, 1, 43);
        }

        .pagination li a:hover {
            background-color: #f3f4f6;
            border-color: #e5e7eb;
        }

        .pagination li.disabled span {
            background-color: #f9fafb;
            color: #9ca3af;
            cursor: not-allowed;
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
            color: var(--sidebar-icon-color);
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: #ffffff;
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
            display: none;
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
            color: var(--sidebar-icon-color);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover i {
            color: #ffffff;
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
            display: none;
            left: 100%;
            position: absolute;
            top: 0;
            margin-top: 0;
            margin-left: 0;
            border-radius: 0.5rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            z-index: 1100;
            transition: none;
            min-width: 220px;
        }
        
        .sidebar .dropdown-menu.show {
            display: block;
        }
        
        .sidebar .nav-item.dropdown {
            position: relative;
        }
        
        .sidebar .nav-link.dropdown-toggle::after {
            display: none;
        }
        
        .sidebar .dropdown-item {
            padding: 0.75rem 1rem;
            color: #4b5563;
            display: flex;
            align-items: center;
        }
        
        .sidebar .dropdown-item:hover {
            background-color: var(--primary-color);
            color: #ffffff;
        }
        
        .sidebar .dropdown-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            color: var(--sidebar-icon-color);
            transition: all 0.3s ease;
        }
        
        .sidebar .dropdown-item:hover i {
            color: #ffffff;
        }
        
        /* Update the nav-link styles for the Reports dropdown */
        .sidebar .nav-link.dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-right: 2rem;
        }
        
        .sidebar .nav-link.dropdown-toggle .nav-content {
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link.dropdown-toggle:focus {
            box-shadow: none;
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

        /* Add these styles for the Free Trial indicator */
        .free-trial-indicator .badge {
            display: none;
        }

        .free-trial-indicator .badge i {
            font-size: 0.75rem;
        }

        /* Modal Styles */
        .subscription-modal {
            display: none !important;
        }

        .subscription-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }

        .subscription-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2001;
        }

        /* From Uiverse.io by abrahamcalsin */ 
        .plan-card {
            background: #fff;
            width: 25rem;
            padding-left: 2.5rem;
            padding-right: 2.5rem;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            border-radius: 10px;
            border-bottom: 4px solid #000446;
            box-shadow: 0 6px 30px rgba(207, 212, 222, 0.3);
            font-family: "Poppins", sans-serif;
            position: relative;
        }

        .plan-card h2 {
            margin-bottom: 1rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .plan-card h2 span {
            display: block;
            margin-top: 0.2rem;
            color: #4d4d4d;
            font-size: 1rem;
            font-weight: 400;
        }

        .etiquet-price {
            position: relative;
            background: #fdbd4a;
            width: calc(100% + 5rem);
            margin-left: -2.5rem;
            padding: 0.5rem 2.5rem;
            border-radius: 5px 0 0 5px;
        }

        .etiquet-price p {
            margin: 0;
            padding-top: 0.4rem;
            display: flex;
            font-size: 2.5rem;
            font-weight: 500;
        }

        .etiquet-price p:before {
            content: "₱";
            margin-right: 8px;
            font-size: 1.5rem;
            font-weight: 300;
            align-self: flex-start;
            margin-top: 0.2rem;
        }

        .etiquet-price div {
            position: absolute;
            bottom: -23px;
            right: 0px;
            width: 0;
            height: 0;
            border-top: 13px solid #c58102;
            border-bottom: 10px solid transparent;
            border-right: 13px solid transparent;
            z-index: -6;
        }

        .benefits-list {
            margin-top: 2rem;
        }

        .benefits-list ul {
            padding: 0;
            font-size: 1rem;
        }

        .benefits-list ul li {
            color: #4d4d4d;
            list-style: none;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .benefits-list ul li svg {
            width: 1.2rem;
            fill: #000446;
        }

        .benefits-list ul li span {
            font-weight: 400;
        }

        .button-get-plan {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .button-get-plan a {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000446;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            letter-spacing: 0.05rem;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .button-get-plan a:hover {
            transform: translateY(-3%);
            box-shadow: 0 3px 10px rgba(207, 212, 222, 0.9);
            background: #000660;
        }

        .button-get-plan .svg-rocket {
            margin-right: 12px;
            width: 1.2rem;
            fill: currentColor;
        }

        .close-modal {
            position: absolute;
            top: -15px;
            right: -15px;
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            z-index: 2002;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: rotate(90deg);
        }

        /* Table Styles */
        .card {
            background-color: #ffffff;
            width: 100%;
            max-width: 100%;
            max-height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        .table-concept {
            width: 100%;
            height: 100%;
            max-height: 100%;
            overflow: auto;
            box-sizing: border-box;
        }

        .table-concept .table-radio {
            display: none;
        }

        .table-concept .table-radio:checked + .table-display {
            display: block;
        }

        .table-concept .table-radio:checked + .table-display + table {
            width: 100%;
            display: table;
        }

        .table-concept .table-radio:checked + .table-display + table + .pagination {
            display: flex;
        }

        .table-concept .table-display {
            background-color: #e2e2e2;
            text-align: right;
            padding: 10px;
            display: none;
            position: sticky;
            left: 0;
        }

        .table-concept table {
            background-color: #ffffff;
            font-size: 16px;
            border-collapse: collapse;
            display: none;
        }

        .table-concept table tr:last-child td {
            border-bottom: 0;
        }

        .table-concept table th,
        .table-concept table td {
            text-align: left;
            padding: 15px;
            box-sizing: border-box;
        }

        .table-concept table th {
            color: #ffffff;
            font-weight: bold;
            background-color: rgb(3, 1, 43);
            border-bottom: solid 2px #d8d8d8;
            position: sticky;
            top: 0;
        }

        .table-concept table td {
            border: solid 1px #d8d8d8;
            border-left: 0;
            border-right: 0;
            white-space: nowrap;
        }

        .table-concept table tbody tr {
            transition: background-color 150ms ease-out;
        }

        .table-concept table tbody tr:nth-child(2n) {
            background-color: #f5f5f5;
        }

        .table-concept table tbody tr:hover {
            background-color: #ebebeb;
        }

        .table-concept .pagination {
            background-color: #8f8f8f;
            width: 100%;
            display: none;
            position: sticky;
            bottom: 0;
            left: 0;
            padding: 0;
            margin: 0;
            justify-content: center;
            gap: 5px;
        }

        .table-concept .pagination > label {
            color: #ffffff;
            padding: 8px 16px;
            cursor: pointer;
            background-color: #8f8f8f;
            border: none;
            transition: all 0.3s ease;
            user-select: none;
        }

        .table-concept .pagination > label:not(.disabled):not(.active):hover {
            background-color: #767676;
        }

        .table-concept .pagination > label.active {
            background-color: #2f2f2f;
            color: #ffffff;
            font-weight: bold;
        }

        .table-concept .pagination > label.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #a8a8a8;
        }

        /* Table Display Info */
        .table-display {
            background-color: #f8f9fa;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 12px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Hide all tables and pagination by default */
        .table-concept table,
        .table-concept .pagination {
            display: none;
        }

        /* Show active table and its pagination */
        .table-concept .table-radio:checked + .table-display,
        .table-concept .table-radio:checked + .table-display + table,
        .table-concept .table-radio:checked + .table-display + table + .pagination {
            display: block;
        }

        .table-concept .table-radio:checked + .table-display + table + .pagination {
            display: flex;
        }

        /* Ensure table header stays on top */
        .table-concept table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: rgb(3, 1, 43);
            color: #ffffff;
            font-weight: bold;
        }

        /* Additional style for all table headers */
        .table thead th {
            background-color: rgb(3, 1, 43) !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }

        /* Style for Bootstrap table headers */
        .table > :not(caption) > * > th {
            background-color: rgb(3, 1, 43) !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }

        .table-title {
            color: #ffffff;
            background-color: #2f2f2f;
            padding: 15px;
        }

        .table-title h2 {
            margin: 0;
            padding: 0;
        }

        .button-container {
            width: 100%;
            box-sizing: border-box;
            display: flex;
            justify-content: flex-end;
            padding: 10px;
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .button-container span {
            color: #8f8f8f;
            text-align: right;
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            margin-right: 10px;
        }

        .button-container button {
            font-family: inherit;
            font-size: inherit;
            color: #ffffff;
            padding: 10px 15px;
            border: 0;
            margin: 0;
            outline: 0;
            border-radius: 0;
            transition: background-color 225ms ease-out;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .button-container button.primary {
            background-color: #147eff;
        }

        .button-container button.primary:hover {
            background-color: #2e8fff;
        }

        .button-container button.primary:active {
            background-color: #0066e6;
        }

        .button-container button.danger {
            background-color: #d11800;
        }

        .button-container button.danger:hover {
            background-color: #f01c00;
        }

        .button-container button.danger:active {
            background-color: #b81600;
        }

        .button-container button svg {
            fill: #ffffff;
            vertical-align: middle;
            padding: 0;
            margin: 0;
        }

        /* Ensure table scrolls horizontally on mobile */
        @media (max-width: 768px) {
            .table-concept {
                overflow-x: auto;
            }
            
            .table-concept table {
                min-width: 800px;
            }
        }

        /* Additional styles for premium badge */
        .premium-indicator {
            display: none;
        }

        .premium-indicator::before {
            content: '';
            position: absolute;
            inset: 0;
            margin: auto;
            width: 50px;
            height: 50px;
            border-radius: inherit;
            scale: 0;
            z-index: -1;
            background-color: rgb(193, 163, 98);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .premium-indicator:hover::before {
            scale: 3;
        }

        .premium-indicator:hover {
            color: #212121;
            scale: 1.05;
            box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4);
        }

        .premium-indicator:active {
            scale: 1;
        }

        .premium-indicator i {
            font-size: 14px;
        }

        /* Navbar premium indicator */
        .navbar-premium-indicator {
            display: none;
        }

        .navbar-premium-indicator::before {
            content: '';
            position: absolute;
            inset: 0;
            margin: auto;
            width: 50px;
            height: 50px;
            border-radius: inherit;
            scale: 0;
            z-index: -1;
            background-color: rgb(193, 163, 98);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .navbar-premium-indicator:hover::before {
            scale: 3;
        }

        .navbar-premium-indicator:hover {
            color: #212121;
            scale: 1.05;
            box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4);
        }

        .navbar-premium-indicator:active {
            scale: 1;
        }

        .navbar-premium-indicator i {
            font-size: 12px;
        }

        /* Navbar dark mode toggle styles */
        .navbar-dark-mode-toggle {
            margin-right: 15px;
            display: flex;
            align-items: center;
        }

        .theme-switch {
            display: inline-block;
            position: relative;
            width: 50px;
            height: 24px;
            margin-bottom: 0;
        }

        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5px;
        }

        .theme-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            z-index: 1;
        }

        .theme-slider-icon {
            font-size: 12px;
            color: white;
            z-index: 0;
        }

        .light-icon {
            margin-right: auto;
            color: #FFB300;
        }

        .dark-icon {
            margin-left: auto;
            color: #5C6BC0;
        }

        input:checked + .theme-slider {
            background-color: #375A7F;
        }

        input:focus + .theme-slider {
            box-shadow: 0 0 1px #375A7F;
        }

        input:checked + .theme-slider:before {
            transform: translateX(26px);
        }

        /* More specific dark mode element styling */
        body.dark-mode .logo-container img {
            filter: var(--logo-filter);
        }
        
        body.dark-mode .tenant-name {
            color: var(--buk-text-color) !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
        
        body.dark-mode .tenant-buk {
            color: var(--buk-only-color) !important;
        }
        
        body.dark-mode .user-avatar-container {
            border: 1px solid var(--avatar-border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        
        body.dark-mode .badge {
            background-color: var(--badge-bg) !important;
            color: var(--badge-color) !important;
        }
        
        body.dark-mode .subscription-modal .subscription-content {
            background-color: var(--modal-bg);
        }
        
        body.dark-mode .plan-card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        
        body.dark-mode .dropdown-header {
            border-bottom-color: var(--border-color);
            color: var(--text-muted);
        }
        
        body.dark-mode .dropdown-divider {
            border-top-color: var(--border-color);
        }
        
        body.dark-mode .card-footer {
            background-color: rgba(255, 255, 255, 0.05);
            border-top-color: var(--border-color);
        }
        
        /* Sidebar dark mode styles */
        body.dark-mode .sidebar .nav-link {
            color: #e2e8f0;
        }
        
        body.dark-mode .sidebar .nav-link i {
            color: #a0aec0;
        }
        
        body.dark-mode .sidebar .nav-link:hover i,
        body.dark-mode .sidebar .nav-link.active i {
            color: #ffffff;
        }
        
        body.dark-mode .sidebar .dropdown-item {
            color: #e2e8f0;
        }
        
        body.dark-mode .sidebar .dropdown-item i {
            color: #a0aec0;
        }
        
        body.dark-mode .sidebar .dropdown-item:hover i {
            color: #ffffff;
        }
        
        body.dark-mode .sidebar .tenant-name {
            color: #ffffff !important;
        }
        
        body.dark-mode .sidebar .dropdown-menu {
            background-color: #1a202c;
            border-color: #2d3748;
        }
        
        body.dark-mode .sidebar .nav-link.dropdown-toggle .dropdown-icon {
            color: #a0aec0;
        }
        
        body.dark-mode .sidebar .nav-link.dropdown-toggle:hover .dropdown-icon,
        body.dark-mode .sidebar .nav-link.dropdown-toggle.show .dropdown-icon {
            color: #ffffff;
        }
        
        /* Logo container styles */
        .dark-mode .logo-container {
            filter: var(--logo-filter, brightness(1.2) contrast(1.2));
        }
        .dark-mode .tenant-name {
            color: var(--accent-color, #f0f0f0);
        }
        .dark-mode .user-avatar {
            border: var(--avatar-border, 2px solid #444);
            filter: brightness(0.9);
        }
        .dark-mode .badge {
            background-color: var(--badge-bg, #2c2c2c);
            color: var(--accent-color, #f0f0f0);
        }
        .dark-mode .subscription-modal,
        .dark-mode .plan-card {
            background-color: var(--modal-bg, #1e1e1e);
            border: 1px solid var(--border-color, #444);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
        .dark-mode .dropdown-header,
        .dark-mode .dropdown-divider {
            border-color: var(--border-color, #444);
        }
        .dark-mode .card-footer {
            background-color: var(--card-bg, #2c2c2c);
            border-color: var(--border-color, #444);
        }

        /* Navigation Dark Mode Styles */
        .dark-mode .side-navbar {
            background-color: var(--nav-bg, #1a1a1a);
            border-right: 1px solid var(--border-color, #444);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .side-navbar li a,
        .dark-mode .side-navbar .dropdown-menu {
            color: var(--text-color, #e0e0e0);
            background-color: var(--nav-bg, #1a1a1a);
        }

        .dark-mode .side-navbar li a:hover {
            background-color: var(--hover-bg, #2c2c2c);
        }

        .dark-mode .side-navbar li.active > a {
            background-color: var(--active-bg, #333);
            color: var(--active-text, #ffffff);
            border-left: 3px solid var(--active-border, #d4af37);
        }

        .dark-mode .side-navbar .dropdown-menu {
            border: 1px solid var(--border-color, #444);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .dark-mode .side-navbar .dropdown-item {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .side-navbar .dropdown-item:hover {
            background-color: var(--hover-bg, #2c2c2c);
            color: var(--hover-text, #ffffff);
        }

        .dark-mode .side-navbar .dropdown-toggle::after {
            color: var(--accent-color, #d4af37);
        }

        .dark-mode .nav-icon {
            color: var(--icon-color, #d4af37);
            filter: brightness(1.2);
        }

        .dark-mode .menu-button {
            background-color: var(--button-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .menu-button:hover {
            background-color: var(--button-hover-bg, #333);
        }

        /* Tables, Content and Form Elements Dark Mode */
        .dark-mode .table {
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .table th,
        .dark-mode .table td {
            border-color: var(--border-color, #444);
        }

        .dark-mode .table thead th {
            background-color: var(--table-header-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-bottom: 2px solid var(--border-color, #444);
        }

        .dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--table-stripe-bg, #252525);
        }

        .dark-mode .table-hover tbody tr:hover {
            background-color: var(--table-hover-bg, #333);
        }

        .dark-mode .content-section,
        .dark-mode .card,
        .dark-mode .card-header,
        .dark-mode .card-body {
            background-color: var(--card-bg, #1e1e1e);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .card-header {
            background-color: var(--card-header-bg, #2c2c2c);
            border-bottom: 1px solid var(--border-color, #444);
        }

        .dark-mode .form-control,
        .dark-mode .form-select,
        .dark-mode .input-group-text {
            background-color: var(--input-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            background-color: var(--input-focus-bg, #333);
            color: var(--text-color, #e0e0e0);
            border-color: var(--input-focus-border, #d4af37);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }

        .dark-mode .form-control::placeholder {
            color: var(--placeholder-color, #888);
        }

        .dark-mode .btn-primary {
            background-color: var(--primary-btn-bg, #3a3a3a);
            border-color: var(--primary-btn-border, #444);
            color: var(--primary-btn-text, #e0e0e0);
        }

        .dark-mode .btn-primary:hover,
        .dark-mode .btn-primary:focus {
            background-color: var(--primary-btn-hover-bg, #444);
            border-color: var(--primary-btn-hover-border, #555);
            color: var(--primary-btn-hover-text, #ffffff);
        }

        .dark-mode .btn-secondary {
            background-color: var(--secondary-btn-bg, #2c2c2c);
            border-color: var(--secondary-btn-border, #444);
            color: var(--secondary-btn-text, #e0e0e0);
        }

        .dark-mode .btn-secondary:hover,
        .dark-mode .btn-secondary:focus {
            background-color: var(--secondary-btn-hover-bg, #3a3a3a);
            border-color: var(--secondary-btn-hover-border, #555);
        }

        .dark-mode .pagination .page-item .page-link {
            background-color: var(--pagination-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .pagination .page-item.active .page-link {
            background-color: var(--pagination-active-bg, #d4af37);
            color: var(--pagination-active-text, #333);
            border-color: var(--pagination-active-border, #d4af37);
        }

        .dark-mode .pagination .page-item .page-link:hover {
            background-color: var(--pagination-hover-bg, #3a3a3a);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #555);
        }

        /* Alerts and Notifications Dark Mode */
        .dark-mode .alert {
            background-color: var(--alert-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #98c379;
            border-color: rgba(40, 167, 69, 0.3);
        }

        .dark-mode .alert-info {
            background-color: rgba(23, 162, 184, 0.2);
            color: #61afef;
            border-color: rgba(23, 162, 184, 0.3);
        }

        .dark-mode .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            color: #e5c07b;
            border-color: rgba(255, 193, 7, 0.3);
        }

        .dark-mode .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: #e06c75;
            border-color: rgba(220, 53, 69, 0.3);
        }

        .dark-mode .toast {
            background-color: var(--modal-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .toast-header {
            background-color: var(--card-header-bg);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }

        .dark-mode .toast-body {
            background-color: var(--modal-bg);
            color: var(--text-color);
        }

        .dark-mode .toast .close {
            color: var(--text-color);
            text-shadow: none;
        }
        
        .dark-mode .toast .btn-close-white {
            opacity: 0.8;
        }
        
        .dark-mode .toast .btn-close-white:hover {
            opacity: 1;
        }
        
        .dark-mode .toast-container {
            z-index: 1060;
        }
        
        .dark-mode #themeToast {
            background-color: var(--accent-color);
        }
        
        .dark-mode #themeToast .toast-body {
            background-color: transparent;
            color: #212529;
        }

        /* Settings modal styles for dark mode */
        .dark-mode .settings-modal .modal-content {
            background-color: var(--modal-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .dark-mode .settings-modal .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .dark-mode .settings-modal .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .dark-mode .settings-list .settings-item {
            border-bottom: 1px solid var(--border-color);
        }

        .dark-mode .settings-list .settings-item:last-child {
            border-bottom: none;
        }

        .dark-mode .settings-list .settings-label {
            color: var(--text-color);
        }

        .dark-mode .settings-list .settings-description {
            color: var(--text-secondary);
        }

        /* Switch toggle for dark mode */
        .dark-mode .switch-toggle {
            background-color: var(--border-color);
        }

        .dark-mode .switch-toggle.checked {
            background-color: var(--accent-color);
        }

        .dark-mode .switch-toggle .toggle-handle {
            background-color: white;
        }

        .dark-mode .tooltip .tooltip-inner {
            background-color: var(--tooltip-bg, #333);
            color: var(--tooltip-text, #e0e0e0);
        }

        .dark-mode .tooltip .arrow::before {
            border-top-color: var(--tooltip-bg, #333);
        }

        .dark-mode .popover {
            background-color: var(--popover-bg, #252525);
            border-color: var(--border-color, #444);
        }

        .dark-mode .popover-header {
            background-color: var(--popover-header-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-bottom-color: var(--border-color, #444);
        }

        .dark-mode .popover-body {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .modal-content {
            background-color: var(--buk-dark-bg-secondary);
            color: var(--buk-light-text-color);
            border: 1px solid var(--buk-dark-border-color);
        }

        .dark-mode .modal-header {
            border-bottom: 1px solid var(--buk-dark-border-color);
        }

        .dark-mode .modal-footer {
            border-top: 1px solid var(--buk-dark-border-color);
        }

        .dark-mode .close {
            color: var(--buk-light-text-color);
        }

        .dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Progress bars and loaders */
        .dark-mode .progress {
            background-color: var(--progress-bg, #2c2c2c);
        }

        .dark-mode .progress-bar {
            background-color: var(--progress-bar-bg, #d4af37);
        }

        /* Lists dark mode styles */
        .dark-mode .list-group {
            background-color: transparent;
        }

        .dark-mode .list-group-item {
            background-color: var(--list-item-bg, #252525);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .list-group-item-action:hover,
        .dark-mode .list-group-item-action:focus {
            background-color: var(--list-item-hover-bg, #333);
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .list-group-item.active {
            background-color: var(--accent-color, #d4af37);
            color: #212529;
            border-color: var(--accent-color, #d4af37);
        }

        /* Tabs and pills dark mode styles */
        .dark-mode .nav-tabs,
        .dark-mode .nav-pills {
            border-color: var(--border-color, #444);
        }

        .dark-mode .nav-tabs .nav-link,
        .dark-mode .nav-pills .nav-link {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .nav-tabs .nav-link:hover,
        .dark-mode .nav-pills .nav-link:hover {
            border-color: var(--border-color, #444);
            background-color: var(--tab-hover-bg, #2c2c2c);
        }

        .dark-mode .nav-tabs .nav-link.active,
        .dark-mode .nav-pills .nav-link.active {
            background-color: var(--tab-active-bg, #d4af37);
            color: #212529;
            border-color: var(--border-color, #444);
        }

        .dark-mode .tab-content {
            background-color: var(--tab-content-bg, #252525);
            border-color: var(--border-color, #444);
        }

        /* Breadcrumbs dark mode */
        .dark-mode .breadcrumb {
            background-color: var(--breadcrumb-bg, #2c2c2c);
        }

        .dark-mode .breadcrumb-item {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .breadcrumb-item.active {
            color: var(--breadcrumb-active, #d4af37);
        }

        .dark-mode .breadcrumb-item + .breadcrumb-item::before {
            color: var(--breadcrumb-divider, #6c757d);
        }

        /* Miscellaneous elements */
        .dark-mode code,
        .dark-mode pre {
            background-color: var(--code-bg, #2d2d2d);
            color: var(--code-color, #e6e6e6);
            border-color: var(--border-color, #444);
        }

        .dark-mode hr {
            border-top-color: var(--border-color, #444);
        }

        .dark-mode .dropdown-item.active,
        .dark-mode .dropdown-item:active {
            background-color: var(--accent-color, #d4af37);
            color: #212529;
        }

        /* Premium elements in dark mode */
        .dark-mode .premium-button,
        .premium-button {
          cursor: pointer;
          position: relative;
          padding: 6px 16px;
          font-size: 14px;
          color: rgb(193, 163, 98) !important;
          border: 2px solid rgb(193, 163, 98) !important;
          border-radius: 25px;
          background-color: transparent !important;
          font-weight: 600;
          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
          overflow: hidden;
          margin: 0.3rem;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          text-decoration: none;
        }

        .dark-mode .premium-button::before,
        .premium-button::before {
          content: '';
          position: absolute;
          inset: 0;
          margin: auto;
          width: 40px;
          height: 40px;
          border-radius: inherit;
          scale: 0;
          z-index: -1;
          background-color: rgb(193, 163, 98) !important;
          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .dark-mode .premium-button:hover::before,
        .premium-button:hover::before {
          scale: 3;
        }

        .dark-mode .premium-button:hover,
        .premium-button:hover {
          color: #212121 !important;
          scale: 1.1;
          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
          background-color: transparent !important;
          text-decoration: none;
        }

        .dark-mode .premium-button:active,
        .premium-button:active {
          scale: 1;
        }

        .dark-mode .premium-button i,
        .premium-button i {
          margin-right: 8px;
          color: rgb(193, 163, 98) !important;
          transition: all 0.3s ease;
        }

        .dark-mode .premium-button:hover i,
        .premium-button:hover i {
          color: #212121 !important;
        }

        /* Premium badge styles */
        .dark-mode .premium-badge,
        .premium-badge {
          display: inline-flex;
          align-items: center;
          color: rgb(193, 163, 98) !important;
          border: 1px solid rgb(193, 163, 98) !important;
          border-radius: 25px;
          background-color: transparent !important;
          padding: 4px 10px;
          font-weight: 600;
          position: relative;
          overflow: hidden;
          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
          font-size: 0.75rem;
        }

        .dark-mode .premium-badge::before,
        .premium-badge::before {
          content: '';
          position: absolute;
          inset: 0;
          margin: auto;
          width: 30px;
          height: 30px;
          border-radius: inherit;
          scale: 0;
          z-index: -1;
          background-color: rgb(193, 163, 98) !important;
          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .dark-mode .premium-badge:hover::before,
        .premium-badge:hover::before {
          scale: 3;
        }

        .dark-mode .premium-badge:hover,
        .premium-badge:hover {
          color: #212121 !important;
          scale: 1.1;
          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
        }

        .dark-mode .premium-badge:active,
        .premium-badge:active {
          scale: 1;
        }

        .dark-mode .premium-badge i,
        .premium-badge i {
          margin-right: 0.4rem;
          font-size: 0.875rem;
          position: relative;
          z-index: 1;
          color: rgb(193, 163, 98) !important;
        }

        .dark-mode .premium-badge span,
        .premium-badge span {
          position: relative;
          z-index: 1;
          color: rgb(193, 163, 98) !important;
        }

        .dark-mode .premium-badge:hover i,
        .premium-badge:hover i,
        .dark-mode .premium-badge:hover span,
        .premium-badge:hover span {
          color: #212121 !important;
        }

        /* Button Get Plan and Etiquet Price updated styles */
        .dark-mode .button-get-plan a,
        .button-get-plan a {
          cursor: pointer;
          position: relative;
          padding: 10px 24px;
          font-size: 16px;
          color: rgb(193, 163, 98) !important;
          border: 2px solid rgb(193, 163, 98) !important;
          border-radius: 34px;
          background-color: transparent !important;
          font-weight: 600;
          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
          overflow: hidden;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          text-decoration: none;
        }

        .dark-mode .button-get-plan a::before,
        .button-get-plan a::before {
          content: '';
          position: absolute;
          inset: 0;
          margin: auto;
          width: 50px;
          height: 50px;
          border-radius: inherit;
          scale: 0;
          z-index: -1;
          background-color: rgb(193, 163, 98) !important;
          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .dark-mode .button-get-plan a:hover::before,
        .button-get-plan a:hover::before {
          scale: 3;
        }

        .dark-mode .button-get-plan a:hover,
        .button-get-plan a:hover {
          color: #212121 !important;
          scale: 1.1;
          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
          text-decoration: none;
        }

        .dark-mode .button-get-plan .svg-rocket,
        .button-get-plan .svg-rocket {
          margin-right: 10px;
          width: .9rem;
          fill: rgb(193, 163, 98) !important;
          transition: all 0.3s ease;
        }

        .dark-mode .button-get-plan a:hover .svg-rocket,
        .button-get-plan a:hover .svg-rocket {
          fill: #212121 !important;
        }

        .dark-mode .etiquet-price,
        .etiquet-price {
          position: relative;
          background-color: transparent !important;
          border: 2px solid rgb(193, 163, 98) !important;
          color: rgb(193, 163, 98) !important;
          width: 14.46rem;
          margin-left: -0.65rem;
          padding: .2rem 1.2rem;
          border-radius: 5px 0 0 5px;
          transition: all 0.3s ease;
        }

        .dark-mode .etiquet-price p,
        .dark-mode .etiquet-price p:before,
        .dark-mode .etiquet-price p:after,
        .etiquet-price p,
        .etiquet-price p:before,
        .etiquet-price p:after {
          color: rgb(193, 163, 98) !important;
        }

        /* Dark mode styles for logo, avatar, badges, and modals */
        .dark-mode .logo-container img {
            filter: var(--logo-filter);
        }
        
        .dark-mode .tenant-name {
            color: var(--buk-text-color);
        }
        
        .dark-mode .user-avatar {
            border: 2px solid var(--avatar-border);
        }
        
        .dark-mode .badge {
            background-color: var(--badge-bg);
            color: var(--badge-color);
        }
        
        .dark-mode .subscription-modal .modal-content {
            background-color: var(--modal-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        
        .dark-mode .plan-card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }
        
        .dark-mode .dropdown-header {
            color: var(--text-muted);
            border-bottom-color: var(--border-color);
        }
        
        .dark-mode .dropdown-divider {
            border-color: var(--border-color);
        }
        
        .dark-mode .card-footer {
            background-color: rgba(31, 41, 55, 0.5);
            border-top-color: var(--border-color);
        }
        
        /* Dark mode styles for tables, content sections, buttons, and form elements */
        .dark-mode .table {
            color: var(--text-color);
        }
        
        .dark-mode .table thead th {
            background-color: rgba(31, 41, 55, 0.7);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        
        .dark-mode .table td, 
        .dark-mode .table th {
            border-color: var(--border-color);
        }
        
        .dark-mode .content-section {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }
        
        .dark-mode .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .dark-mode .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .dark-mode .btn-secondary {
            background-color: #4B5563;
            border-color: #4B5563;
            color: #ffffff;
        }
        
        .dark-mode .btn-secondary:hover {
            background-color: #374151;
            border-color: #374151;
        }
        
        .dark-mode .form-control,
        .dark-mode .form-select {
            background-color: var(--input-bg);
            border-color: var(--input-border);
            color: var(--text-color);
        }
        
        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        
        .dark-mode .pagination .page-link {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .dark-mode .pagination .page-link:hover {
            background-color: var(--hover-bg);
            border-color: var(--border-color);
        }
        
        .dark-mode .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }
        
        /* Card examples - preserved light appearance in dark mode */
        .dark-mode .card-examples-wrapper {
            background-color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
            border: 1px solid #e5e7eb !important;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        
        .dark-mode .card-example {
            background-color: #ffffff !important;
            color: #111827 !important;
            border-color: #e5e7eb !important;
        }
        
        .dark-mode .card-example i,
        .dark-mode .card-example h5,
        .dark-mode .card-example .card-title {
            color: #111827 !important;
        }
        
        .dark-mode .card-example-glass {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }

        /* List group styles for dark mode */
        .dark-mode .list-group-item {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .dark-mode .list-group-item-action:hover {
            background-color: var(--hover-bg);
        }

        .dark-mode .list-group-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Nav pills and tabs for dark mode */
        .dark-mode .nav-pills .nav-link {
            color: var(--text-color);
        }

        .dark-mode .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .dark-mode .nav-tabs {
            border-color: var(--border-color);
        }

        .dark-mode .nav-tabs .nav-link {
            color: var(--text-color);
            border-color: transparent;
        }

        .dark-mode .nav-tabs .nav-link:hover {
            border-color: var(--border-color);
            background-color: var(--hover-bg);
        }

        .dark-mode .nav-tabs .nav-link.active {
            color: var(--text-color);
            background-color: var(--card-bg);
            border-color: var(--border-color);
            border-bottom-color: var(--card-bg);
        }

        /* Tooltip styles for dark mode */
        .dark-mode .tooltip .tooltip-inner {
            background-color: var(--dropdown-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .dark-mode .tooltip .tooltip-arrow::before {
            border-top-color: var(--dropdown-bg);
        }

        /* Progress bar styles for dark mode */
        .dark-mode .progress {
            background-color: var(--input-bg);
        }

        .dark-mode .progress-bar {
            background-color: var(--primary-color);
        }

        /* Card special styles for dark mode */
        .dark-mode .card-header {
            background-color: rgba(31, 41, 55, 0.6);
            border-bottom-color: var(--border-color);
        }

        /* Breadcrumb styles for dark mode */
        .dark-mode .breadcrumb {
            background-color: var(--card-bg);
        }

        .dark-mode .breadcrumb-item {
            color: var(--text-muted);
        }

        .dark-mode .breadcrumb-item.active {
            color: var(--text-color);
        }

        .dark-mode .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted);
        }

        /* Code and pre blocks for dark mode */
        .dark-mode code, .dark-mode pre {
            background-color: #2d3748;
            color: #e2e8f0;
            border-color: var(--border-color);
        }

        /* Adjustments for specific tenant UI elements */
        .dark-mode .tenant-logo img {
            filter: var(--logo-filter);
        }

        .dark-mode .tenant-info {
            color: var(--text-color);
        }

        .dark-mode .stats-card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            box-shadow: 0 4px 6px var(--shadow-color);
        }

        .dark-mode .stats-card .stats-icon {
            color: var(--primary-color);
        }

        .dark-mode .stats-card .stats-value {
            color: var(--text-color);
        }

        .dark-mode .stats-card .stats-label {
            color: var(--text-muted);
        }

        .dark-mode .subscription-modal .subscription-content {
            background-color: var(--modal-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .dark-mode .subscription-modal .close-modal {
            color: var(--text-color);
            background-color: var(--btn-bg);
        }

        .dark-mode .subscription-modal .close-modal:hover {
            background-color: var(--btn-hover);
        }

        .dark-mode .plan-card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .dark-mode .plan-card h2 {
            color: var(--accent-color);
        }

        .dark-mode .plan-card h2 span {
            color: var(--text-secondary);
        }

        .dark-mode .benefits-list ul li {
            color: var(--text-color);
        }

        .dark-mode .benefits-list ul li svg {
            fill: var(--accent-color);
        }

        .dark-mode .button-get-plan a {
            background-color: var(--btn-primary);
            color: white;
        }

        .dark-mode .button-get-plan a:hover {
            background-color: var(--btn-primary-hover);
        }

        /* Pagination styles for dark mode */
        .dark-mode .pagination {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .dark-mode .pagination label {
            color: var(--text-color);
        }

        .dark-mode .pagination label:hover:not(.disabled):not(.active) {
            background-color: var(--btn-hover);
        }

        .dark-mode .pagination label.active {
            background-color: var(--btn-primary);
            color: white;
        }

        .dark-mode .pagination label.disabled {
            color: var(--text-secondary);
        }

        .dark-mode .table-display {
            color: var(--text-secondary);
        }

        /* Tooltip styles for dark mode */
        .dark-mode .tooltip .tooltip-inner {
            background-color: var(--dropdown-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        /* Toast notification styles for dark mode */
        .dark-mode .toast {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--border-color);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .toast-header {
            background-color: rgba(31, 41, 55, 0.7);
            color: var(--text-color);
            border-bottom-color: var(--border-color);
        }
        
        .dark-mode .toast-body {
            background-color: var(--card-bg);
            color: var(--text-color);
        }
        
        .dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        .dark-mode #themeToast .toast-body {
            background-color: var(--card-bg);
        }
        
        .dark-mode .toast-container {
            z-index: 1090;
        }

        /* Dark Mode for Subscription Modal */
        .dark-mode .subscription-modal {
            background-color: rgba(0, 0, 0, 0.85);
        }

        .dark-mode .subscription-content {
            background-color: var(--buk-card-bg);
            color: var(--buk-text-color);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .dark-mode .close-modal {
            color: var(--buk-text-color);
            background-color: rgba(40, 40, 40, 0.7);
        }

        .dark-mode .close-modal:hover {
            background-color: rgba(60, 60, 60, 0.9);
        }

        .dark-mode .plan-card {
            background-color: var(--buk-card-bg);
            color: var(--buk-text-color);
            border: 1px solid var(--buk-border-color);
        }

        .dark-mode .plan-card h2 {
            color: var(--buk-text-color);
        }

        .dark-mode .plan-card h2 span {
            color: var(--buk-text-color-secondary);
        }

        .dark-mode .benefits-list {
            color: var(--buk-text-color);
        }

        .dark-mode .benefits-list ul li {
            color: var(--buk-text-color);
            border-bottom: 1px solid var(--buk-border-color);
        }

        .dark-mode .benefits-list ul li svg {
            fill: var(--accent-color);
        }

        .dark-mode .button-get-plan a {
            background-color: var(--accent-color);
            color: var(--buk-card-bg);
        }

        .dark-mode .button-get-plan a:hover {
            background-color: var(--accent-color-hover);
        }

        .dark-mode #toastMessage {
            color: inherit;
        }

        /* Common Toast Styling for Dark Mode */
        .dark-mode .toast {
            background-color: var(--buk-card-bg);
            color: var(--buk-text-color);
            border: 1px solid var(--buk-border-color);
        }

        .dark-mode .toast-header {
            background-color: rgba(30, 30, 30, 0.9);
            color: var(--buk-text-color);
            border-bottom: 1px solid var(--buk-border-color);
        }

        .dark-mode .toast .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .dark-mode #themeToast {
            border: 1px solid var(--buk-border-color);
        }

        .dark-mode .toast-container {
            z-index: 9999;
        }

        /* Font style success modal specific styles */
        .dark-mode #fontStyleSuccessModal .modal-body {
            background-color: var(--buk-dark-bg-secondary);
        }

        .dark-mode #fontStyleSuccessModal .font-preview {
            background-color: var(--buk-dark-bg-primary);
            border-color: var(--buk-dark-border-color);
            color: var(--buk-light-text-color);
        }

        .dark-mode #fontStyleSuccessModal .modal-footer .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
        }

        .dark-mode #fontStyleSuccessModal .modal-footer .btn-primary:hover {
            background-color: var(--accent-hover-color, #0056b3);
            border-color: var(--accent-hover-color, #0056b3);
        }

        /* Glass card style for dashboard components */
        .card-style-glass .card, 
        .card-style-glass .settings-card,
        .card-style-glass .stat-card,
        .card-style-glass .content-card {
            display: block;
            position: relative;
            background-color: #f2f8f9;
            border-radius: 4px;
            padding: 32px 24px;
            margin: 12px;
            text-decoration: none;
            z-index: 0;
            overflow: hidden;
            border: 1px solid #f2f8f9;
            transition: all 0.3s ease;
        }

        .card-style-glass .card p,
        .card-style-glass .settings-card p,
        .card-style-glass .stat-card p,
        .card-style-glass .content-card p {
            font-size: 17px;
            font-weight: 400;
            line-height: 20px;
            color: #666;
            transition: all 0.3s ease-out;
        }

        .card-style-glass .card p.small,
        .card-style-glass .settings-card p.small,
        .card-style-glass .stat-card p.small,
        .card-style-glass .content-card p.small {
            font-size: 14px;
        }

        .card-style-glass .card:before,
        .card-style-glass .settings-card:before,
        .card-style-glass .stat-card:before,
        .card-style-glass .content-card:before {
            content: "";
            position: absolute;
            z-index: -1;
            top: -16px;
            right: -16px;
            background: #00838d;
            height: 32px;
            width: 32px;
            border-radius: 32px;
            transform: scale(1);
            transform-origin: 50% 50%;
            transition: transform 0.25s ease-out;
        }

        .card-style-glass .card:hover:before,
        .card-style-glass .settings-card:hover:before,
        .card-style-glass .stat-card:hover:before,
        .card-style-glass .content-card:hover:before {
            transform: scale(21);
        }

        .card-style-glass .card:hover,
        .card-style-glass .settings-card:hover,
        .card-style-glass .stat-card:hover,
        .card-style-glass .content-card:hover {
            border: 1px solid #00838d;
            box-shadow: 0px 0px 999px 999px rgba(255, 255, 255, 0.5);
            z-index: 500;
        }

        .card-style-glass .card:hover p,
        .card-style-glass .settings-card:hover p,
        .card-style-glass .stat-card:hover p,
        .card-style-glass .content-card:hover p {
            color: rgba(255, 255, 255, 0.8);
        }

        .card-style-glass .card:hover h3,
        .card-style-glass .settings-card:hover h3,
        .card-style-glass .stat-card:hover h3,
        .card-style-glass .content-card:hover h3 {
            color: #fff;
            transition: all 0.3s ease-out;
        }

        .card-style-glass .go-corner {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            width: 32px;
            height: 32px;
            overflow: hidden;
            top: 0;
            right: 0;
            background-color: #00838d;
            border-radius: 0 4px 0 32px;
            opacity: 0.7;
            transition: opacity 0.3s linear;
        }

        .card-style-glass .card:hover .go-corner,
        .card-style-glass .settings-card:hover .go-corner,
        .card-style-glass .stat-card:hover .go-corner,
        .card-style-glass .content-card:hover .go-corner {
            opacity: 1;
        }

        .card-style-glass .go-arrow {
            margin-top: -4px;
            margin-right: -4px;
            color: white;
            font-family: courier, sans;
        }

        /* Dark mode specific styles for glass cards */
        body.dark-mode .card-style-glass .card,
        body.dark-mode .card-style-glass .settings-card,
        body.dark-mode .card-style-glass .stat-card,
        body.dark-mode .card-style-glass .content-card {
            background-color: #1a1a1a;
            border-color: #2d2d2d;
        }

        body.dark-mode .card-style-glass .card p,
        body.dark-mode .card-style-glass .settings-card p,
        body.dark-mode .card-style-glass .stat-card p,
        body.dark-mode .card-style-glass .content-card p {
            color: #a0aec0;
        }

        body.dark-mode .card-style-glass .card:hover,
        body.dark-mode .card-style-glass .settings-card:hover,
        body.dark-mode .card-style-glass .stat-card:hover,
        body.dark-mode .card-style-glass .content-card:hover {
            border-color: #00838d;
            box-shadow: 0px 0px 999px 999px rgba(0, 0, 0, 0.5);
        }

        /* Glass card style for dashboard components */
        body[data-card-style="glass"] .dashboard-card,
        .card-style-glass .dashboard-card {
            display: block;
            position: relative;
            background-color: #f2f8f9;
            border-radius: 4px;
            padding: 32px 24px;
            margin: 12px;
            text-decoration: none;
            z-index: 0;
            overflow: hidden;
            border: 1px solid #f2f8f9;
            transition: all 0.3s ease;
        }

        body[data-card-style="glass"] .dashboard-card p,
        .card-style-glass .dashboard-card p {
            font-size: 17px;
            font-weight: 400;
            line-height: 20px;
            color: #666;
            transition: all 0.3s ease-out;
        }

        body[data-card-style="glass"] .dashboard-card p.small,
        .card-style-glass .dashboard-card p.small {
            font-size: 14px;
        }

        body[data-card-style="glass"] .dashboard-card:before,
        .card-style-glass .dashboard-card:before {
            content: "";
            position: absolute;
            z-index: -1;
            top: -16px;
            right: -16px;
            background: #00838d;
            height: 32px;
            width: 32px;
            border-radius: 32px;
            transform: scale(1);
            transform-origin: 50% 50%;
            transition: transform 0.25s ease-out;
        }

        body[data-card-style="glass"] .dashboard-card:hover:before,
        .card-style-glass .dashboard-card:hover:before {
            transform: scale(21);
        }

        body[data-card-style="glass"] .dashboard-card:hover,
        .card-style-glass .dashboard-card:hover {
            border: 1px solid #00838d;
            box-shadow: 0px 0px 999px 999px rgba(255, 255, 255, 0.5);
            z-index: 500;
        }

        body[data-card-style="glass"] .dashboard-card:hover p,
        .card-style-glass .dashboard-card:hover p {
            color: rgba(255, 255, 255, 0.8);
        }

        body[data-card-style="glass"] .dashboard-card:hover h3,
        .card-style-glass .dashboard-card:hover h3 {
            color: #fff;
            transition: all 0.3s ease-out;
        }

        body[data-card-style="glass"] .dashboard-card .go-corner,
        .card-style-glass .dashboard-card .go-corner {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            width: 32px;
            height: 32px;
            overflow: hidden;
            top: 0;
            right: 0;
            background-color: #00838d;
            border-radius: 0 4px 0 32px;
            opacity: 0.7;
            transition: opacity 0.3s linear;
        }

        body[data-card-style="glass"] .dashboard-card:hover .go-corner,
        .card-style-glass .dashboard-card:hover .go-corner {
            opacity: 1;
        }

        body[data-card-style="glass"] .dashboard-card .go-arrow,
        .card-style-glass .dashboard-card .go-arrow {
            margin-top: -4px;
            margin-right: -4px;
            color: white;
            font-family: courier, sans;
        }

        /* Dark mode specific styles for glass dashboard cards */
        body.dark-mode[data-card-style="glass"] .dashboard-card,
        body.dark-mode .card-style-glass .dashboard-card {
            background-color: #1a1a1a;
            border-color: #2d2d2d;
        }

        body.dark-mode[data-card-style="glass"] .dashboard-card p,
        body.dark-mode .card-style-glass .dashboard-card p {
            color: #a0aec0;
        }

        body.dark-mode[data-card-style="glass"] .dashboard-card:hover,
        body.dark-mode .card-style-glass .dashboard-card:hover {
            border-color: #00838d;
            box-shadow: 0px 0px 999px 999px rgba(0, 0, 0, 0.5);
        }

        /* Regular card styles (non-dashboard) */
        .card-style-glass .card:not(.dashboard-card),
        .card-style-glass .settings-card:not(.dashboard-card),
        .card-style-glass .stat-card:not(.dashboard-card),
        .card-style-glass .content-card:not(.dashboard-card) {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
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

        /* Card style variations - without hover effects */
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
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        body.dark-mode .card-glass {
            background-color: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Dashboard-specific hover effects */
        body[data-page="dashboard"] .card {
            transition: all 0.3s ease;
        }

        body[data-page="dashboard"] .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
        }

        body.dark-mode[data-page="dashboard"] .card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
        }

        /* Card style variations - without hover effects */
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
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="{{ isset($settings) && $settings->dark_mode ? 'dark-mode' : '' }}" 
      data-card-style="{{ isset($settings) && $settings->card_style ? $settings->card_style : 'square' }}">
    <div x-data="{ isSidebarOpen: false }" class="layout-wrapper">
    <!-- Sidebar -->
        <div class="sidebar" :class="{ 'show': isSidebarOpen }">
            <div class="sidebar-content">
                <div class="logo-container">
                    <img src="{{ asset('assets/images/logo.png') }}" 
                         alt="BukSkwela Logo">
                </div>
                
                <ul class="nav flex-column px-3 flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" 
                           href="{{ route('tenant.dashboard', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.students.*') ? 'active' : '' }}" 
                           href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-users"></i> <span>Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.staff.*') ? 'active' : '' }}" 
                           href="{{ route('tenant.staff.index', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-chalkboard-teacher"></i> <span>Staff</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.courses.*') ? 'active' : '' }}" 
                           href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-book"></i> <span>Courses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.admin.requirements.*') ? 'active' : '' }}" 
                           href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-clipboard-list"></i> <span>Requirements</span>
                        </a>
                    </li>
                   
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}" 
                           href="#"
                           data-bs-toggle="dropdown" 
                           aria-expanded="{{ request()->routeIs('tenant.reports.*') ? 'true' : 'false' }}">
                            <div class="nav-content">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </div>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <div class="dropdown-menu {{ request()->routeIs('tenant.reports.*') ? 'show' : '' }}">
                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.students') || request()->routeIs('tenant.reports.students.*') ? 'active' : '' }}" 
                               href="{{ route('tenant.reports.students', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-user-graduate"></i>
                                <span>Student Reports</span>
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.staff') || request()->routeIs('tenant.reports.staff.*') ? 'active' : '' }}" 
                               href="{{ route('tenant.reports.staff', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-user-tie"></i>
                                <span>Staff Reports</span>
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.courses') || request()->routeIs('tenant.reports.courses.*') ? 'active' : '' }}" 
                               href="{{ route('tenant.reports.courses', ['tenant' => tenant('id')]) }}">
                                <i class="fas fa-book-open"></i>
                                <span>Course Reports</span>
                            </a>
                        </div>
                    </li>
                </ul>

                <!-- Upgrade to Pro Button -->
                <div class="mt-auto">
                    @php
                        $currentTenant = \App\Models\Tenant::where('id', tenant('id'))->first();
                        $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
                    @endphp
                    
                    @if(!$isPremium)
                        <a href="#" class="btn btn-sm btn-outline-warning w-100 mb-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#sidebarPremiumModal">
                            <i class="fas fa-crown me-1"></i>
                            <small>Upgrade to Premium</small>
                        </a>
                    @else
                        <div class="premium-badge w-100 mb-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-crown text-warning me-1"></i>
                            <small>Premium Account</small>
                        </div>
                    @endif
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
                        <!-- Premium Indicator -->
                        @php
                            // Get current URL to extract tenant ID
                            $url = request()->url();
                            preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
                            $tenantDomain = $matches[1] ?? null;
                            
                            // Get tenant from domain or tenant helper
                            if ($tenantDomain) {
                                $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
                            } else {
                                $tenantId = tenant('id') ?? null;
                                $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
                            }
                            
                            $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
                        @endphp

                        @if($isPremium)
                            <div class="premium-badge me-3">
                                <i class="fas fa-crown"></i>
                                <span>Premium</span>
                            </div>
                        @endif

                        <!-- Dark Mode Toggle -->
                        <div class="navbar-dark-mode-toggle me-3">
                            <label class="theme-switch" title="Toggle Dark Mode">
                                <input type="checkbox" id="navbarDarkModeToggle">
                                <span class="theme-slider">
                                    <i class="fas fa-sun theme-slider-icon light-icon"></i>
                                    <i class="fas fa-moon theme-slider-icon dark-icon"></i>
                                </span>
                            </label>
                        </div>

                        <!-- Admin Avatar with Dropdown -->
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar-container">
                                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" 
                                         alt="User" 
                                         class="user-avatar"
                                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                </div>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <div class="dropdown-header">
                                    <strong>{{ Auth::guard('admin')->user()->name ?? 'User' }}</strong>
                                    <p class="mb-0 text-muted small">{{ Auth::guard('admin')->user()->email ?? 'No email' }}</p>
                                    @if($isPremium)
                                        <span class="badge bg-warning text-dark mt-1">
                                            <i class="fas fa-crown"></i> Premium
                                        </span>
                                    @endif
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ $currentTenant ? route('profile.index', ['tenant' => $currentTenant->id]) : '#' }}">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                                <a class="dropdown-item" href="{{ $currentTenant ? route('tenant.settings', ['tenant' => $currentTenant->id]) : '#' }}">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" onclick="logoutToCentralDomain()" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

    <!-- Add this right after the nav to debug tenant info -->
    @if(config('app.debug'))
        <div class="d-none">
            @php
                dump([
                    'url' => $url ?? null,
                    'tenant_domain_from_url' => $tenantDomain ?? null,
                    'tenant_id_from_helper' => tenant('id') ?? null,
                    'current_tenant' => $currentTenant ?? null,
                    'is_premium' => $isPremium ?? false,
                    'subscription_plan' => $currentTenant->subscription_plan ?? null
                ]);
            @endphp
        </div>
    @endif

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
            // Handle payment method change in sidebar modal
            document.getElementById('sidebar_payment_method')?.addEventListener('change', function() {
                // Hide all payment details
                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
                    el.classList.add('d-none');
                });
                
                // Show selected payment method details
                const method = this.value;
                if (method) {
                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
                }
            });
            
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

    <!-- Subscription Modal -->
    <div id="subscriptionModal" class="subscription-modal" style="display: none !important;">
        <!-- Subscription modal content removed -->
    </div>

    <!-- Add before closing body tag -->
    <script>
        // Pagination functionality
        function setupPagination(tableId, itemsPerPage, totalItems) {
            const pagesCount = Math.ceil(totalItems / itemsPerPage);
            const tableContainer = document.getElementById(tableId);
            
            if (!tableContainer) return;

            // Create radio buttons for each page
            for (let i = 0; i < pagesCount; i++) {
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = `${tableId}_radio`;
                radio.id = `${tableId}_radio_${i}`;
                radio.className = 'table-radio';
                if (i === 0) radio.checked = true;
                tableContainer.appendChild(radio);

                // Create display info
                const display = document.createElement('div');
                display.className = 'table-display';
                const start = (i * itemsPerPage) + 1;
                const end = Math.min((i + 1) * itemsPerPage, totalItems);
                display.textContent = `Showing ${start} to ${end} of ${totalItems} items`;
                tableContainer.appendChild(display);

                // Your table goes here (you'll need to create this dynamically or have multiple tables)
                // Create pagination
                const pagination = document.createElement('div');
                pagination.className = 'pagination';

                // Previous button
                const prevLabel = document.createElement('label');
                prevLabel.htmlFor = i > 0 ? `${tableId}_radio_${i - 1}` : '';
                prevLabel.className = i === 0 ? 'disabled' : '';
                prevLabel.textContent = '« Previous';
                pagination.appendChild(prevLabel);

                // Page numbers
                for (let j = 0; j < pagesCount; j++) {
                    const pageLabel = document.createElement('label');
                    pageLabel.htmlFor = `${tableId}_radio_${j}`;
                    pageLabel.className = i === j ? 'active' : '';
                    pageLabel.textContent = j + 1;
                    pagination.appendChild(pageLabel);
                }

                // Next button
                const nextLabel = document.createElement('label');
                nextLabel.htmlFor = i < pagesCount - 1 ? `${tableId}_radio_${i + 1}` : '';
                nextLabel.className = i === pagesCount - 1 ? 'disabled' : '';
                nextLabel.textContent = 'Next »';
                pagination.appendChild(nextLabel);

                tableContainer.appendChild(pagination);
            }
        }

        // Initialize pagination for tables
        document.addEventListener('DOMContentLoaded', function() {
            // Example usage:
            // setupPagination('myTable', 20, 95); // 20 items per page, 95 total items
        });
    </script>

    <!-- Add this at the bottom of your layout file, before the closing </body> tag -->
    
    <script>
        // Global function to redirect to central domain
        function logoutToCentralDomain() {
            // Use the tenant_logout.html page to perform a client-side redirect
            window.location.href = '/tenant_logout.html';
            return false; // Prevent default link behavior
        }
    </script>
    
    <!-- Toast container for notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="themeToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="toastMessage">Theme preference saved</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    
    <!-- Sidebar compact mode script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we should apply compact sidebar mode (for compact layout)
        function checkCompactMode() {
            // If we're in a compact layout page, auto-enable compact sidebar
            if (document.querySelector('.layout-compact')) {
                document.body.classList.add('compact-sidebar');
                console.log('Compact layout detected, enabling compact sidebar');
            }
        }
        
        // Add toggle button to navbar if it doesn't exist
        const navbar = document.querySelector('.top-navbar .container-fluid');
        if (navbar && !document.getElementById('toggleSidebar')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'toggleSidebar';
            toggleBtn.className = 'btn btn-sm btn-outline-secondary me-2';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.title = 'Toggle Sidebar';
            
            // Insert at the beginning of navbar
            navbar.insertBefore(toggleBtn, navbar.firstChild);
            
            // Add event listener
            toggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('compact-sidebar');
                
                // Save preference to localStorage
                if (document.body.classList.contains('compact-sidebar')) {
                    localStorage.setItem('sidebarMode', 'compact');
                    showToast('Compact sidebar enabled');
                } else {
                    localStorage.setItem('sidebarMode', 'expanded');
                    showToast('Expanded sidebar enabled');
                }
            });
        }
        
        // Function to show toast notification
        function showToast(message) {
            const toastEl = document.getElementById('themeToast');
            if (toastEl) {
                const toastMessage = document.getElementById('toastMessage');
                if (toastMessage) {
                    toastMessage.textContent = message;
                }
                const bsToast = new bootstrap.Toast(toastEl);
                bsToast.show();
            }
        }
        
        // Apply saved preference from localStorage
        const savedSidebarMode = localStorage.getItem('sidebarMode');
        if (savedSidebarMode === 'compact') {
            document.body.classList.add('compact-sidebar');
        }
        
        // Apply compact mode for compact layout
        checkCompactMode();
        
        // Also check when the window is loaded completely
        window.addEventListener('load', checkCompactMode);
    });
    </script>

    <!-- Global card style application -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply card style from localStorage on all pages
        const cardStyle = localStorage.getItem('selectedCardStyle') || 'square';
        
        const applyGlobalCardStyle = () => {
            console.log('Applying global card style:', cardStyle);
            
            // Target all card types across all layouts
            const cardSelectors = '.card, .enrolled-card, .stat-card, .compact-content-card, .modern-stat-card, .modern-card';
            
            // Remove all card style classes first
            document.querySelectorAll(cardSelectors).forEach(card => {
                card.classList.remove('card-rounded', 'card-square', 'card-glass');
                // Add the selected style class
                card.classList.add(`card-${cardStyle}`);
            });
            
            // If we're on a dashboard page that has its own applyCardStyle function,
            // it will apply more specific styles later
        };
        
        // Apply global styles
        applyGlobalCardStyle();
        
        // Listen for changes
        window.addEventListener('storage', function(e) {
            if (e.key === 'selectedCardStyle') {
                // Reapply global style
                applyGlobalCardStyle();
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply card style from localStorage if available
        try {
            const savedCardStyle = localStorage.getItem('selectedCardStyle');
            if (savedCardStyle) {
                applyCardStyle(savedCardStyle);
                console.log('Applied card style from localStorage:', savedCardStyle);
            }
        } catch (e) {
            console.error('Error applying card style from localStorage:', e);
        }
        
        // Listen for card style changes
        document.addEventListener('cardStyleChanged', function(e) {
            const cardStyle = e.detail.cardStyle;
            applyCardStyle(cardStyle);
            console.log('Applied card style from event:', cardStyle);
        });
        
        // Listen for storage events (changes from other tabs)
        window.addEventListener('storage', function(e) {
            if (e.key === 'selectedCardStyle') {
                applyCardStyle(e.newValue);
                console.log('Applied card style from storage event:', e.newValue);
            }
        });
        
        /**
         * Apply card style to all dashboard elements
         */
        function applyCardStyle(style) {
            // Remove all style classes first
            document.body.classList.remove('card-style-square', 'card-style-rounded', 'card-style-glass');
            
            // Add the selected style class
            if (style) {
                document.body.classList.add('card-style-' + style);
            }
        }
    });
    </script>

    <!-- Include the Tenant Approval Modal -->
    @include('Modals.TenantApproval')
    
    <!-- Scripts to handle tenant approval modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if approval modal session flag is set
            @if(session('show_approval_modal'))
                const loginEmail = document.querySelector('input[name="email"]')?.value || '';
                console.log('Email for modal check:', loginEmail);
                
                // Only show approval modal if not a student email
                if (!loginEmail.includes('@student.buksu.edu.ph')) {
                    console.log('Not a student email, showing approval modal');
                    
                    // Check if modal should be prevented (student email was entered)
                    if (sessionStorage.getItem('preventApprovalModal') !== 'true') {
                        // Use Bootstrap 5 Modal API
                        const approvalModal = document.getElementById('tenantApprovalModal');
                        if (approvalModal) {
                            const modal = new bootstrap.Modal(approvalModal);
                            modal.show();
                        }
                    } else {
                        console.log('Modal showing prevented by session storage flag');
                    }
                } else {
                    console.log('Student email detected, not showing approval modal');
                    // Remove the session flag
                    @php
                    if (session()->has('show_approval_modal')) {
                        session()->forget('show_approval_modal');
                    }
                    @endphp
                }
            @endif
            
            // Special check for student emails when page loads
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                const checkStudentEmail = function() {
                    const email = emailInput.value || '';
                    if (email.includes('@student.buksu.edu.ph')) {
                        console.log('Student email detected in input');
                        // Hide modal if it's currently shown
                        const modalElement = document.getElementById('tenantApprovalModal');
                        if (modalElement) {
                            const bsModal = bootstrap.Modal.getInstance(modalElement);
                            if (bsModal) {
                                bsModal.hide();
                                console.log('Hiding modal for student email');
                            }
                        }
                    }
                };
                
                // Check on input change
                emailInput.addEventListener('input', checkStudentEmail);
                
                // Check on page load
                checkStudentEmail();
            }
        });
    </script>

    <!-- Initialize all dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle payment method change in sidebar modal
            document.getElementById('sidebar_payment_method')?.addEventListener('change', function() {
                // Hide all payment details
                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
                    el.classList.add('d-none');
                });
                
                // Show selected payment method details
                const method = this.value;
                if (method) {
                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
                }
            });

            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>

    <!-- Sidebar Premium Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle payment method change in sidebar modal
            document.getElementById('sidebar_payment_method')?.addEventListener('change', function() {
                // Hide all payment details
                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
                    el.classList.add('d-none');
                });
                
                // Show selected payment method details
                const method = this.value;
                if (method) {
                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
                }
            });

            // Form validation and submission
            const sidebarUpgradeForm = document.getElementById('sidebarUpgradeForm');
            if (sidebarUpgradeForm) {
                sidebarUpgradeForm.addEventListener('submit', function(e) {
                    // Get form values
                    const paymentMethod = document.getElementById('sidebar_payment_method').value;
                    const referenceNumber = document.getElementById('sidebar_reference_number').value;
                    
                    // Basic validation
                    if (!paymentMethod) {
                        e.preventDefault();
                        alert('Please select a payment method');
                        return false;
                    }
                    
                    if (!referenceNumber) {
                        e.preventDefault();
                        alert('Please enter your payment reference number');
                        return false;
                    }
                    
                    // Disable the button and show loading state
                    const submitButton = document.getElementById('sidebarUpgradeButton');
                    if (submitButton) {
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                        submitButton.disabled = true;
                    }
                    
                    // Let the form submit (the controller will handle the upgrade process)
                    return true;
                });
            }
        });
    </script>

    <!-- Sidebar Premium Modal -->
    <div class="modal fade" id="sidebarPremiumModal" tabindex="-1" aria-labelledby="sidebarPremiumModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="sidebarPremiumModalLabel">
                        <i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Display session messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="text-center mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-crown text-warning fs-1"></i>
                        </div>
                        <h4>Unlock Premium Features</h4>
                        <p class="text-muted">Upgrade your account to access premium features and enhance your school management capabilities.</p>
                    </div>
                    
                    <div class="card border-warning mb-4">
                        <div class="card-header bg-warning bg-opacity-10 border-warning">
                            <h5 class="mb-0 text-warning"><i class="fas fa-star me-2"></i>Premium Benefits</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center border-0 px-0">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Profile customization</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center border-0 px-0">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Advanced reporting and analytics</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center border-0 px-0">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Unlimited staff accounts</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center border-0 px-0">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Priority customer support</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary bg-opacity-10 border-primary">
                            <h5 class="mb-0 text-primary"><i class="fas fa-money-bill-wave me-2"></i>Subscription Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">Premium Plan:</span>
                                <span class="badge bg-primary rounded-pill px-3 py-2">Monthly</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Price:</span>
                                <span class="fw-bold fs-4">₱999.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Billing:</span>
                                <span>Monthly, auto-renews</span>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('tenant.subscription.upgrade', ['tenant' => tenant('id')]) }}" method="POST" id="sidebarUpgradeForm">
                        @csrf
                        <div class="mb-3">
                            <label for="sidebar_payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="sidebar_payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                            </select>
                        </div>
                        
                        <div id="sidebar_bankTransferDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Bank Transfer Instructions</h6>
                                <p class="mb-0">Please transfer ₱999.00 to the following account:</p>
                                <hr>
                                <p class="mb-1"><strong>Bank:</strong> BDO</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-1"><strong>Account Number:</strong> 1234-5678-9012</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div id="sidebar_gcashDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>GCash Instructions</h6>
                                <p class="mb-0">Please send ₱999.00 to the following GCash number:</p>
                                <hr>
                                <p class="mb-1"><strong>GCash Number:</strong> 0917-123-4567</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div id="sidebar_paymayaDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>PayMaya Instructions</h6>
                                <p class="mb-0">Please send ₱999.00 to the following PayMaya number:</p>
                                <hr>
                                <p class="mb-1"><strong>PayMaya Number:</strong> 0918-765-4321</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sidebar_reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="sidebar_reference_number" name="reference_number" placeholder="Enter your payment reference number" required>
                            <div class="form-text">Please enter the reference number from your payment transaction.</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning" id="sidebarUpgradeButton">
                                <i class="fas fa-crown me-2"></i>Upgrade Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>