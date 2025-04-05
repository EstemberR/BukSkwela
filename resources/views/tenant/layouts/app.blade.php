<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --maroon: #800000;
            --maroon-dark: #600000;
            --maroon-light: #aa0000;
        }

        .bg-maroon {
            background-color: var(--maroon) !important;
        }

        .text-maroon {
            color: var(--maroon) !important;
        }

        .btn-maroon {
            background-color: var(--maroon);
            border-color: var(--maroon);
            color: white;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-dark);
            border-color: var(--maroon-dark);
            color: white;
        }

        .navbar-dark.bg-maroon {
            background-color: var(--maroon) !important;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .card-header {
            border-bottom: none;
            border-radius: 8px 8px 0 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-maroon">
        <div class="container">
            <a class="navbar-brand" href="{{ route('tenant.admin.dashboard', ['tenant' => tenant('id')]) }}">
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('tenant.admin.dashboard', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tenant.admin.requirements.*') ? 'active' : '' }}" 
                           href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}">
                            <i class="fas fa-folder"></i> Requirements
                        </a>
                    </li>
                    <!-- Add more navigation items as needed -->
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit', ['tenant' => tenant('id')]) }}">
                                        <i class="fas fa-user-cog"></i> Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout', ['tenant' => tenant('id')]) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login', ['tenant' => tenant('id')]) }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>