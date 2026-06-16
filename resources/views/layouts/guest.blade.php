<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') — {{ config('itevcms.app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <div class="login-shell">
        <div class="login-card card w-100 animate-on-load">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/transpo_enfo_orig.png') }}" alt="ITEVCMS" height="40" class="mb-2">
                    <h4 class="mb-1">{{ config('itevcms.app_name') }}</h4>
                    <p class="text-muted small mb-0">Land Transportation Enforcement Management</p>
                </div>
                @include('components.alerts')
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
