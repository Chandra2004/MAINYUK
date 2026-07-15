<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return Inertia::render('Notifications', [
            'notifications' => $notifications,
            'unread_count'  => $notifications->where('is_read', false)->count(),
        ]);
    }

    public function markRead(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:notifications,id',
        ]);

        $query = Notification::where('user_id', Auth::id());

        if (isset($validated['id'])) {
            $query->where('id', $validated['id']);
        }

        $query->update(['is_read' => true]);

        return back();
    }
}
