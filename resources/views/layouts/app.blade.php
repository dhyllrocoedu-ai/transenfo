<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('itevcms.app_name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="d-flex">
    <aside class="sidebar d-none d-lg-flex flex-column p-3 text-white">
        <div class="mb-4">
            <div class="brand-title text-white-50">LTEM</div>
            <h5 class="mb-0 text-white">{{ config('itevcms.app_name') }}</h5>
            <small class="text-white-50">Land Transportation Enforcement</small>
        </div>
        <nav class="nav flex-column flex-grow-1">
            @foreach ($navItems ?? [] as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'active' : '' }}">
                    <i class="bi bi-{{ $item['icon'] }} me-2"></i>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="border-top border-white border-opacity-25 pt-3 mt-3">
            <div class="small text-white-50">{{ auth()->user()->role->label() }}</div>
            <div class="text-white">{{ auth()->user()->name }}</div>
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

        <main class="p-4">
            @include('components.alerts')
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
