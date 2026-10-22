<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController
{
    public function redirect(): RedirectResponse
    {
        if (! filled(config('services.google.client_id')) || ! filled(config('services.google.client_secret'))) {
            return to_route('register')->withErrors([
                'google' => __('auth.google.not_configured'),
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return to_route('register')->withErrors([
                'google' => __('auth.google.failed'),
            ]);
        }

        $googleId = trim((string) $googleUser->getId());
        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $emailIsVerified = (bool) data_get($googleUser->user, 'email_verified');

        if ($googleId === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $emailIsVerified) {
            return to_route('register')->withErrors([
                'google' => __('auth.google.email_unverified'),
            ]);
        }

        $user = User::query()->where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if ($user && $user->google_id && $user->google_id !== $googleId) {
            return to_route('register')->withErrors([
                'google' => __('auth.google.account_already_linked'),
            ]);
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => filled($googleUser->getName())
                    ? $googleUser->getName()
                    : Str::before($email, '@'),
                'email' => $email,
                'password' => Str::random(64),
                'google_id' => $googleId,
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
        } else {
            $user->forceFill([
                'google_id' => $googleId,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($user->roles()->count() === 0) {
            return redirect()->route('onboarding.index');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
