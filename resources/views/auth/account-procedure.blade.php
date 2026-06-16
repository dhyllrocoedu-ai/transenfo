<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('itevcms.app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 1rem;
            margin: 0;
            font-family: var(--bs-font-sans-serif);
        }
        .account-container {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 580px;
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }
        .form-panel {
            width: 50%;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .overlay-panel {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            z-index: 10;
            overflow: hidden;
            transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 1.5rem 0 0 1.5rem;
        }
        .account-container.show-register .overlay-panel {
            left: 0;
        }
        .overlay-inner {
            width: 200%;
            height: 100%;
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .account-container.show-register .overlay-inner {
            transform: translateX(-50%);
        }
        .overlay-half {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            text-align: center;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a5f 100%);
        }
        .overlay-half h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .overlay-half p {
            opacity: 0.9;
            font-size: 1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-toggle {
            border: 2px solid #fff;
            color: #fff;
            background: transparent;
            padding: 0.75rem 2.5rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-toggle:hover {
            background: #fff;
            color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #2563eb, #1e3a5f);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
        }
        @media (max-width: 768px) {
            .account-container {
                flex-direction: column;
                min-height: auto;
                max-width: 480px;
            }
            .form-panel {
                width: 100%;
                padding: 1.5rem;
            }
            .overlay-panel {
                display: none;
            }
            .mobile-toggle {
                display: block !important;
            }
        }
        .mobile-toggle {
            display: none;
        }
        .form-panel form .input-group-text {
            background: #f8fafc;
            border-right: none;
        }
        .form-panel form .form-control {
            border-left: none;
        }
        .form-panel form .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }
        .form-panel form .input-group:focus-within .input-group-text {
            border-color: #2563eb;
        }
        .form-panel form .input-group:focus-within .form-control {
            border-color: #2563eb;
        }
    </style>
</head>
<body>
    <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline-light position-fixed"
       style="top: 1rem; left: 1rem; z-index: 100; border-radius: 2rem; backdrop-filter: blur(4px); background: rgba(255,255,255,0.15);">
        <i class="bi bi-house-door-fill me-1"></i>Home
    </a>
    <div class="account-container @if($showRegister) show-register @endif" id="accountContainer">
        <!-- Login Panel -->
        <div class="form-panel">
            <div class="text-center mb-4">
                <img src="{{ asset('images/transpo_enfo_orig.png') }}" alt="ITEVCMS" height="48" class="mb-2">
                <h4 class="mb-1">{{ config('itevcms.app_name') }}</h4>
                <p class="text-muted small mb-0">Welcome back! Sign in to continue.</p>
            </div>
            @if(session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('account.procedure.store') }}">
                @csrf
                <input type="hidden" name="_action" value="login">
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Email Address</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required @if(!$showRegister) autofocus @endif>
                    </div>
                    @error('email')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small">Password</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
                <div class="text-center mt-2">
                    <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot password?</a>
                </div>
            </form>
            <div class="text-center mt-1 mobile-toggle">
                <small class="text-muted">Don't have an account? <a href="#" onclick="toggleForm('register')" class="fw-semibold">Create one</a></small>
            </div>
        </div>

        <!-- Register Panel -->
        <div class="form-panel">
            <div class="text-center mb-4">
                <img src="{{ asset('images/transpo_enfo_orig.png') }}" alt="ITEVCMS" height="48" class="mb-2">
                <h4 class="mb-1">{{ config('itevcms.app_name') }}</h4>
                <p class="text-muted small mb-0">Create your account to get started.</p>
            </div>
            <form method="POST" action="{{ route('account.procedure.store') }}">
                @csrf
                <input type="hidden" name="_action" value="register">
                <div class="mb-2">
                    <label for="reg-name" class="form-label fw-semibold small">Full Name</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="reg-name" name="name" value="{{ old('name') }}" placeholder="John Doe" required @if($showRegister) autofocus @endif>
                    </div>
                    @error('name')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="reg-email" class="form-label fw-semibold small">Email Address</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="reg-email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required>
                    </div>
                    @error('email')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="reg-phone" class="form-label fw-semibold small">Phone Number</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="reg-phone" name="phone" value="{{ old('phone') }}" placeholder="+63912345678">
                    </div>
                    @error('phone')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="reg-password" class="form-label fw-semibold small">Password</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="reg-password" name="password" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="reg-password-confirm" class="form-label fw-semibold small">Confirm Password</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-lock-check"></i></span>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="reg-password-confirm" name="password_confirmation" placeholder="••••••••" required>
                    </div>
                    @error('password_confirmation')
                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-2 form-check">
                    <input type="checkbox" class="form-check-input" id="reg-terms" name="terms" required>
                    <label class="form-check-label small" for="reg-terms">I agree to the <a href="#" class="text-decoration-none">Terms of Service</a></label>
                </div>
                @error('terms')
                    <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>
            <div class="text-center mt-3 mobile-toggle">
                <small class="text-muted">Already have an account? <a href="#" onclick="toggleForm('login')" class="fw-semibold">Sign In</a></small>
            </div>
        </div>

        <!-- Overlay -->
        <div class="overlay-panel">
            <div class="overlay-inner">
                <div class="overlay-half">
                    <h2>Hello, Friend!</h2>
                    <p>Don't have an account yet?<br>Create one and join us today.</p>
                    <button class="btn-toggle" onclick="toggleForm('register')">Create Account</button>
                </div>
                <div class="overlay-half">
                    <h2>Welcome Back!</h2>
                    <p>Already have an account?<br>Sign in to access your dashboard.</p>
                    <button class="btn-toggle" onclick="toggleForm('login')">Sign In</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm(form) {
            const container = document.getElementById('accountContainer');
            if (form === 'register') {
                container.classList.add('show-register');
            } else {
                container.classList.remove('show-register');
            }
        }
    </script>
</body>
</html>
