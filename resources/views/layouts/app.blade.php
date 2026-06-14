<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('itevcms.app_name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-shell">
<div class="d-flex">
    <aside class="sidebar d-none d-lg-flex flex-column p-3 text-white animate-on-load">
        <div class="sidebar-brand mb-4">
            <div class="brand-badge">LT</div>
            <div>
                <div class="brand-title text-white-50">LTEM</div>
                <h5 class="mb-0 text-white">{{ config('itevcms.app_name') }}</h5>
                <small class="text-white-50">Land Transportation Enforcement</small>
            </div>
        </div>
        <nav class="nav flex-column flex-grow-1 gap-1">
            @foreach ($navItems ?? [] as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'active' : '' }}">
                    <i class="bi bi-{{ $item['icon'] }} me-2"></i>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="sidebar-footer mt-3">
            <div class="small text-white-50">{{ auth()->user()->role->label() }}</div>
            <div class="text-white fw-semibold">{{ auth()->user()->name }}</div>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light w-100">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main-content flex-grow-1">
        <nav class="navbar navbar-expand-lg bg-white border-bottom px-3 d-lg-none">
            <span class="navbar-brand mb-0 h6">{{ config('itevcms.app_name') }}</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @foreach ($navItems ?? [] as $item)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </nav>

        <header class="topbar px-4 px-lg-5 py-3 border-bottom animate-on-load">
            <div>
                <div class="text-muted small">Operations console</div>
                <h2 class="h5 mb-0">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('notifications.index') }}" class="position-relative text-decoration-none text-dark">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="font-size:0.6rem; display:none;">0</span>
                </a>
                <span class="status-pill">Online</span>
                <span class="text-muted small">{{ auth()->user()->role->label() }}</span>
            </div>
        </header>

        <main class="p-4 p-lg-5">
            @include('components.alerts')
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
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
