<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Flash messages dari session Laravel
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'info'    => $request->session()->get('info'),
            ],
            // Data user yang login (dipakai di navbar dll)
            'auth' => [
                'user' => Auth::check() ? [
                    'id'       => Auth::id(),
                    'name'     => Auth::user()->name,
                    'username' => Auth::user()->username,
                    'email'    => Auth::user()->email,
                    'avatar'   => Auth::user()->avatar ? asset('avatars/' . Auth::user()->avatar) : null,
                    'role'     => Auth::user()->role,
                ] : null,
                'unread_notifications_count' => Auth::check() ? $request->user()->notifications()->where('is_read', false)->count() : 0,
                'recent_notifications' => Auth::check() ? $request->user()->notifications()->orderByDesc('created_at')->take(5)->get()->map(fn ($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->diffForHumans()
                ]) : [],
            ],
        ]);
    }
}
