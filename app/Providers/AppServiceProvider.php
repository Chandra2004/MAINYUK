<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS jika menggunakan Ngrok (APP_URL https)
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share auth user + unread notification count with every Inertia page
        Inertia::share([
            'auth' => fn () => [
                'user' => Auth::check() ? [
                    'id'       => Auth::user()->id,
                    'name'     => Auth::user()->name,
                    'email'    => Auth::user()->email,
                    'username' => Auth::user()->username,
                    'avatar'   => Auth::user()->avatar,
                    'role'     => Auth::user()->role,
                ] : null,
            ],
            'unreadNotifications' => fn () => Auth::check()
                ? Notification::where('user_id', Auth::id())->where('is_read', false)->count()
                : 0,
            'flash' => fn () => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
        ]);
    }
}
