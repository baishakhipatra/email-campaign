<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Email Campaign - @yield('title')</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        :root { --sidebar-width: 260px; --bg-dark: #1a1c23; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Nunito', sans-serif; overflow-x: hidden; }
        
        /* Sidebar Styling */
        .sidebar { width: var(--sidebar-width); background: var(--bg-dark); height: 100vh; position: fixed; color: #fff; padding-top: 20px; transition: all 0.3s; z-index: 1000; }
        .sidebar .nav-link { color: #a0aec0; padding: 12px 25px; display: flex; align-items: center; gap: 10px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); border-left: 4px solid #3182ce; }
        .sidebar .nav-link i { font-size: 1.2rem; }
        .sidebar-heading { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #718096; padding: 20px 25px 10px; }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.3s; }
        .header { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        
        /* Stats Cards */
        .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
</head>
<body>
    @auth
    <div class="sidebar">
        <div class="px-4 mb-4">
            <h4 class="text-white fw-bold"><i class="bx bxs-envelope text-info"></i> MailPulse</h4>
            <small class="text">Campaign Manager</small>
        </div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        <a href="{{ route('campaigns.index') }}" class="nav-link {{ request()->is('campaigns*') ? 'active' : '' }}">
            <i class='bx bx-paper-plane'></i> Campaigns
        </a>
        <a href="{{ route('templates.index') }}" class="nav-link {{ request()->is('templates*') ? 'active' : '' }}">
            <i class='bx bx-layout'></i> Templates
        </a>
        <a href="{{ route('subscribers.index') }}" class="nav-link">
            <i class='bx bx-group'></i> Subscribers
        </a>
        <a href="{{ route('lists.index') }}" class="nav-link">
            <i class='bx bx-list-ul'></i> Lists
        </a>
        <a href="{{ route('settings.smtp.index') }}" class="nav-link">
            <i class='bx bx-cog'></i> SMTP Settings
        </a>
    </div>
    @endauth

    <div class="{{ Auth::check() ? 'main-content' : '' }}">
        @auth
        <header class="header">
            <button class="btn border-0 d-md-none"><i class="bx bx-menu"></i></button>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-muted">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </header>
        @endauth

        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    {{-- Select2 JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#lists').select2({
                placeholder: "Select subscriber lists",
                allowClear: true,
                width: '100%'
            });
        });

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
        };
    </script>
</body>
</html>
