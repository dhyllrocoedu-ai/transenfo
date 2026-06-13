<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SupabaseAuthService
{
    public function attempt(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if ($user && ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if ($user && $user->password) {
            $localAttempt = $this->attemptViaLocal($email, $password, $user);

            if ($localAttempt) {
                return $localAttempt;
            }
        }

        if (config('supabase.url')) {
            return $this->attemptViaSupabase($email, $password, $user);
        }

        return $this->attemptViaLocal($email, $password, $user);
    }

    protected function attemptViaSupabase(string $email, string $password, ?User $user): User
    {
        $response = Http::withHeaders([
            'apikey' => config('supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(config('supabase.url').'/auth/v1/token?grant_type=password', [
            'email' => $email,
            'password' => $password,
        ]);

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $data = $response->json();
        $supabaseId = $data['user']['id'] ?? null;

        if (! $user) {
            $user = User::create([
                'supabase_id' => $supabaseId,
                'name' => $data['user']['user_metadata']['name'] ?? $email,
                'email' => $email,
                'role' => 'vehicle_owner',
                'is_active' => true,
            ]);
        } else {
            $user->update(['supabase_id' => $supabaseId]);
        }

        return $user;
    }

    protected function attemptViaLocal(string $email, string $password, ?User $user): ?User
    {
        if (! $user || ! $user->password || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function login(User $user, bool $remember = false): void
    {
        Auth::login($user, $remember);
        request()->session()->regenerate();
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
