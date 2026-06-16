<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('itevcms.app_name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-shell">
<div class="d-flex">
    <aside class="sidebar d-none d-lg-flex flex-column text-white animate-on-load">
        <div class="sidebar-brand">
            <img src="{{ asset('images/transpo_enfo_orig.png') }}" alt="ITEVCMS" height="32" class="me-2">
            <div>
                <div class="brand-title text-white-50">LTEM</div>
                <h5 class="mb-0 text-white sidebar-brand-name">{{ config('itevcms.app_name') }}</h5>
                <small class="text-white-50">Land Transportation Enforcement</small>
            </div>
        </div>

        <nav class="nav flex-column flex-grow-1 sidebar-nav">
            @foreach ($navGroups ?? [] as $group)
                <div class="sidebar-group">
                    <div class="sidebar-group-label">{{ $group['label'] }}</div>
                    @foreach ($group['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) ||
                                        request()->routeIs(str_replace('.index', '.*', $item['route']));
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="nav-link {{ $isActive ? 'active' : '' }}">
                            <i class="bi bi-{{ $item['icon'] }} sidebar-nav-icon"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
    </aside>

    <div class="main-content flex-grow-1 d-flex flex-column">
        <nav class="navbar navbar-expand-lg bg-white border-bottom px-3 d-lg-none">
            <span class="navbar-brand mb-0 h6">{{ config('itevcms.app_name') }}</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @foreach ($navGroups ?? [] as $group)
                        <li class="nav-item dropdown-header text-muted small text-uppercase fw-semibold px-3 py-1 mt-2">{{ $group['label'] }}</li>
                        @foreach ($group['items'] as $item)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($item['route']) }}">
                                    <i class="bi bi-{{ $item['icon'] }} me-2"></i>{{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100 mt-2">Logout</button>
                </form>
            </div>
        </nav>

        <header class="topbar px-4 px-lg-5 py-3 border-bottom animate-on-load">
            <div>
                <div class="text-muted small">Operations console</div>
                <h2 class="h5 mb-0">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('notifications.index') }}" class="position-relative text-decoration-none text-dark topbar-icon-btn">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="font-size:0.6rem; display:none;">0</span>
                </a>

                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2 p-0 border-0" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                        <span class="status-dot text-success" style="font-size:0.6rem;">●</span>
                        <span class="fw-semibold small d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <span class="text-muted small d-none d-lg-inline">{{ auth()->user()->role->label() }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="min-width: 230px;">
                        <li>
                            <div class="dropdown-header text-wrap">
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}?tab=settings"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}?tab=password"><i class="bi bi-key me-2"></i>Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-4 p-lg-5 flex-grow-1 page-bg">
            @include('components.alerts')
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<style>
    .sidebar {
        width: var(--itevcms-sidebar-width, 240px);
        min-height: 100vh;
        background: linear-gradient(160deg, var(--itevcms-primary) 0%, #10263f 100%);
        box-shadow: 20px 0 45px rgba(15, 23, 42, 0.15);
        padding: 1rem 0;
        overflow-y: auto;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0 1rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 0.5rem;
    }

    .sidebar-brand-name {
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .sidebar-nav {
        gap: 0;
    }

    .sidebar-group {
        margin-bottom: 0.25rem;
    }

    .sidebar-group-label {
        padding: 1rem 1rem 0.35rem;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.35);
    }

    .sidebar-nav .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 1rem;
        color: rgba(255,255,255,0.75);
        border-radius: 0;
        border-left: 3px solid transparent;
        transition: all 0.15s ease;
        font-size: 0.875rem;
        margin: 0 0.5rem;
        border-radius: 0.5rem;
    }

    .sidebar-nav .nav-link:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .sidebar-nav .nav-link.active {
        background: rgba(255,255,255,0.14);
        color: #fff;
        border-left-color: #fff;
        font-weight: 600;
    }

    .sidebar-nav-icon {
        font-size: 1.1rem;
        width: 1.25rem;
        text-align: center;
        flex-shrink: 0;
    }

    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 4px;
    }

    .topbar-icon-btn {
        transition: color 0.15s ease;
    }

    .topbar-icon-btn:hover {
        color: var(--itevcms-accent) !important;
    }

    .main-content {
        min-height: 100vh;
    }

    #userDropdown:focus {
        box-shadow: none;
    }

    #userDropdown::after {
        font-size: 0.7rem;
        color: #999;
    }

    .dropdown-header {
        white-space: normal;
    }

    .topbar {
        position: relative;
        z-index: 1020;
        background: #fff;
    }

    .topbar .dropdown-menu {
        z-index: 1055;
    }

    .page-bg {
        background-color: #f8fafc;
    }

    .sticky-save {
        position: sticky;
        bottom: 0;
        z-index: 1025;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 0;
        margin-top: 2rem;
    }

    .zone-map {
        aspect-ratio: 1 / 1;
        min-height: 350px;
        max-height: 450px;
        width: 100%;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('notificationCount');
    if (!badge) return;

    function updateBadge() {
        fetch('{{ route("api.notifications.unread") }}')
            .then(r => r.json())
            .then(d => {
                if (d.count > 0) { badge.textContent = d.count; badge.style.display = 'inline'; }
                else { badge.style.display = 'none'; }
            })
            .catch(() => {});
    }

    updateBadge();
    setInterval(updateBadge, 30000);
});
</script>
</body>
</html>
