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
            color: var(--sidebar-icon-color);
            transition: all 0.3s ease;
        }

        .sidebar .dropdown-item:hover i {
            color: #ffffff;
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

        /* Add these styles for the Free Trial indicator */
        .free-trial-indicator .badge {
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            background-color: rgba(3, 1, 43, 0.1) !important;
            color: rgb(3, 1, 43) !important;
            border: 1px solid rgba(3, 1, 43, 0.2);
        }

        .free-trial-indicator .badge i {
            font-size: 0.75rem;
        }

        /* Modal Styles */
        .subscription-modal {
            display: none !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .subscription-modal.show {
            display: flex !important;
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
            cursor: pointer;
            position: relative;
            padding: 10px 24px;
            font-size: 16px;
            color: rgb(193, 163, 98);
            border: 2px solid rgb(193, 163, 98);
            border-radius: 34px;
            background-color: transparent;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            overflow: hidden;
            margin: 0.5rem;
            width: calc(100% - 1rem);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
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
            cursor: pointer;
            position: relative;
            padding: 8px 16px;
            font-size: 14px;
            color: rgb(193, 163, 98);
            border: 2px solid rgb(193, 163, 98);
            border-radius: 34px;
            background-color: transparent;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 6px;
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
            background-color: var(--toast-bg, #252525);
            color: var(--text-color, #e0e0e0);
            border-color: var(--border-color, #444);
        }

        .dark-mode .toast-header {
            background-color: var(--toast-header-bg, #2c2c2c);
            color: var(--text-color, #e0e0e0);
            border-bottom-color: var(--border-color, #444);
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
            background-color: var(--modal-bg, #252525);
            border-color: var(--border-color, #444);
        }

        .dark-mode .modal-header,
        .dark-mode .modal-footer {
            background-color: var(--modal-header-bg, #2c2c2c);
            border-color: var(--border-color, #444);
        }

        .dark-mode .modal-title {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .close {
            color: var(--text-color, #e0e0e0);
        }

        .dark-mode .close:hover {
            color: var(--close-hover-color, #fff);
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
                    <h4 class="tenant-name">
                        <span class="tenant-buk">{{ substr(tenant('id'), 0, 3) }}</span>{{ substr(tenant('id'), 3) }}
                    </h4>
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
                    @php
                        // Get the tenant's subscription plan
                        $tenantData = tenant();
                        $isPremium = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
                        
                        // Also check session
                        $isPremiumFromSession = session('is_premium') === true;
                        
                        // Check localStorage via JavaScript
                        $isPremiumJsCheck = true;
                        
                        // Use either source
                        $isPremium = $isPremium || $isPremiumFromSession;
                    @endphp
                    
                    @if(!$isPremium)
                    <button class="upgrade-btn w-100" onclick="openSubscriptionModal()">
                        <i class="fas fa-crown me-2"></i> Upgrade to Pro
                    </button>
                    @else
                    <div class="premium-indicator">
                        <i class="fas fa-crown"></i> Premium Account
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
                        <!-- Free Trial Indicator -->
                        <div class="free-trial-indicator me-3">
                            @php
                                // Get the tenant's subscription plan
                                $tenantData = tenant();
                                $isPremium = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
                                
                                // Also check session
                                $isPremiumFromSession = session('is_premium') === true;
                                
                                // Use either source
                                $isPremium = $isPremium || $isPremiumFromSession;
                            @endphp
                            
                            @if($isPremium)
                            <div class="navbar-premium-indicator">
                                <i class="fas fa-crown"></i> Premium
                            </div>
                            @else
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                <i class="fas fa-star me-1"></i>
                                Free Trial
                            </span>
                            @endif
                        </div>

                        <!-- User menu -->
                        <div class="dropdown ms-2">
                            <button class="btn p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar-container">
                                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" 
                                         alt="User" class="user-avatar">
                                </div>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="userDropdown">
                                <div class="dropdown-header">
                                    <strong>{{ Auth::guard('admin')->user()->name ?? 'User' }}</strong>
                                    <p class="mb-0 text-muted small">{{ Auth::guard('admin')->user()->email ?? 'No email' }}</p>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('profile.index', ['tenant' => tenant('id')]) }}">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('tenant.settings', ['tenant' => tenant('id')]) }}">
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
            // Initialize dark mode from localStorage
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'enabled') {
                document.body.classList.add('dark-mode');
            }
            
            // Listen for dark mode toggle events
            document.addEventListener('darkModeToggled', function(e) {
                if (e.detail.isDarkMode) {
                    document.body.classList.add('dark-mode');
                } else {
                    document.body.classList.remove('dark-mode');
                }
            });
            
            // Check premium status from multiple sources
            function checkPremiumStatus() {
                const isPremiumCookie = document.cookie.split('; ').find(row => row.startsWith('is_premium='));
                const isPremiumLocal = localStorage.getItem('isPremium') === 'true';
                // PHP session value passed to JavaScript
                const isPremiumSession = {{ session('is_premium') ? 'true' : 'false' }};
                
                return isPremiumCookie || isPremiumLocal || isPremiumSession;
            }
            
            // Set premium status if true from any source
            const isPremium = checkPremiumStatus();
            if (isPremium) {
                // Store the premium status in both localStorage and cookie for persistence
                localStorage.setItem('isPremium', 'true');
                document.cookie = "is_premium=true; path=/; max-age=86400";
                
                // Update UI to reflect premium status
                const freeTrialIndicators = document.querySelectorAll('.free-trial-indicator');
                freeTrialIndicators.forEach(indicator => {
                    // Remove the free trial badge
                    const freeBadge = indicator.querySelector('.badge');
                    if (freeBadge) {
                        // Create and add premium indicator
                        const premiumIndicator = document.createElement('div');
                        premiumIndicator.className = 'navbar-premium-indicator';
                        premiumIndicator.innerHTML = '<i class="fas fa-crown"></i> Premium';
                        indicator.replaceChild(premiumIndicator, freeBadge);
                    }
                });
                
                // Hide upgrade buttons
                const upgradeButtons = document.querySelectorAll('.upgrade-btn');
                upgradeButtons.forEach(button => {
                    button.style.display = 'none';
                    // Add premium badge in place of the button
                    const premiumBadge = document.createElement('div');
                    premiumBadge.className = 'premium-indicator';
                    premiumBadge.innerHTML = `
                        <i class="fas fa-crown"></i> Premium Account
                    `;
                    button.parentNode.insertBefore(premiumBadge, button);
                });
            }
            
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

            // Apply current settings on page load
            $(function() {
                // Apply dark mode if enabled
                if($('#darkModeToggle').is(':checked')) {
                    $('body').addClass('dark-mode');
                }
                
                // Ensure tenant name is visible in sidebar
                const tenantNameEl = document.querySelector('.tenant-name');
                if (tenantNameEl) {
                    // Force visibility of tenant name, especially on the settings page
                    tenantNameEl.style.display = 'block';
                    tenantNameEl.style.visibility = 'visible';
                    tenantNameEl.style.opacity = '1';
                    
                    // Add special highlight when on settings page
                    if (window.location.href.includes('/settings')) {
                        tenantNameEl.style.color = '#FF9900';
                        tenantNameEl.style.fontSize = '1.25rem';
                    }
                }
            });
        });
    </script>
    @stack('scripts')

    <!-- Subscription Modal -->
    <div id="subscriptionModal" class="subscription-modal">
        <div class="subscription-overlay"></div>
        <div class="subscription-content">
            <button class="close-modal" onclick="closeSubscriptionModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Premium User Content - Initially Hidden -->
            <div id="premiumContent" class="plan-card" style="display: none;">
                <h2>Premium Plan<span>You're already on our premium plan</span></h2>
                <div class="etiquet-price" style="background: rgb(193, 163, 98);">
                    <p style="color: #212121;">Active</p>
                    <div></div>
                </div>
                <div class="benefits-list text-center mt-4">
                    <p>Thank you for being a premium subscriber!</p>
                    <p>You have access to all premium features.</p>
                </div>
                <div class="button-get-plan">
                    <a href="#" onclick="closeSubscriptionModal(); return false;" style="background-color: rgb(193, 163, 98); color: #212121;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>CONTINUE</span>
                    </a>
                </div>
            </div>
            
            <!-- Regular Plan Card - For Non-Premium Users -->
            <div id="regularContent" class="plan-card">
                <h2>Premium<span>Unlock all features for your department</span></h2>
                <div class="etiquet-price">
                    <p>5,000</p>
                    <div></div>
                </div>
                <div class="benefits-list">
                    <ul>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Instructor Management</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Student Management</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>View Student Submission Status</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Probationary Status Management</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Custom Enrollment Requirements</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Submission Reports</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                            </svg>
                            <span>Branding Customization</span>
                        </li>
                    </ul>
                </div>
                <div class="button-get-plan">
                    <a href="{{ route('profile.index', ['tenant' => tenant('id')]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-rocket">
                            <path d="M156.6 384.9L125.7 353.1C117.2 345.5 114.2 333.1 117.1 321.8C120.1 312.9 124.1 301.3 129.8 288H24C15.38 288 7.414 283.4 3.146 275.9C-1.123 268.4-1.042 259.2 3.357 251.8L55.83 163.3C68.79 141.4 92.33 127.1 117.8 127.1H200C202.4 124 204.8 120.3 207.2 116.7C289.1-4.07 411.1-8.142 483.9 5.275C495.6 7.414 504.6 16.43 506.7 28.06C520.1 100.9 516.1 222.9 395.3 304.8C391.8 307.2 387.1 309.6 384 311.1V394.2C384 419.7 370.6 443.2 348.7 456.2L260.2 508.6C252.8 513 243.6 513.1 236.1 508.9C228.6 504.6 224 496.6 224 488V380.8C209.9 385.6 197.6 389.7 188.3 392.7C177.1 396.3 164.9 393.2 156.6 384.9V384.9zM384 167.1C406.1 167.1 424 150.1 424 127.1C424 105.9 406.1 87.1 384 87.1C361.9 87.1 344 105.9 344 127.1C344 150.1 361.9 167.1 384 167.1z"></path>
                        </svg>
                        <span>UPGRADE NOW</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openSubscriptionModal() {
        // Check premium status from multiple sources
        const isPremiumCookie = document.cookie.split('; ').find(row => row.startsWith('is_premium='));
        const isPremiumLocal = localStorage.getItem('isPremium') === 'true';
        const isPremium = isPremiumCookie || isPremiumLocal || {{ session('is_premium') ? 'true' : 'false' }};
        
        const modal = document.getElementById('subscriptionModal');
        
        // Show the appropriate content based on subscription status
        if (isPremium) {
            document.getElementById('premiumContent').style.display = 'block';
            document.getElementById('regularContent').style.display = 'none';
        } else {
            document.getElementById('premiumContent').style.display = 'none';
            document.getElementById('regularContent').style.display = 'block';
        }
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSubscriptionModal() {
        const modal = document.getElementById('subscriptionModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside the plan card
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('subscriptionModal');
        const overlay = document.querySelector('.subscription-overlay');
        
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeSubscriptionModal();
            }
        });
    });
    </script>

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
</body>
</html>