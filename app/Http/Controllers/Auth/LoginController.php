<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request, SupabaseAuthService $authService): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = $authService->attempt($credentials['email'], $credentials['password']);
        $authService->login($user, $request->boolean('remember'));

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(SupabaseAuthService $authService): RedirectResponse
    {
        $authService->logout();

        return redirect()->route('login');
    }
}
