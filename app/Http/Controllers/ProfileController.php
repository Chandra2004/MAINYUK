<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Profile', [
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'username' => $user->username,
                'avatar'         => $user->avatar ? Storage::disk('public')->url($user->avatar) : null,
                'role'           => $user->role,
                'joined'         => $user->created_at->format('M Y'),
                'is_pro'         => $user->is_pro,
                'pro_expires_at' => $user->pro_expires_at,
            ],
            'stats' => [
                'total_bookings'  => $user->bookings()->count(),
                'active_bookings' => $user->bookings()->active()->count(),
                'completed'       => $user->bookings()->completed()->count(),
                'total_spent'     => $user->bookings()->where('status', '!=', 'cancelled')->sum('total_price'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Upload avatar ke storage public.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // maks 2MB
        ]);

        $user = Auth::user();

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Simpan avatar baru ke storage public/avatars/
        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with([
            'success'    => 'Foto profil berhasil diperbarui.',
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama tidak sesuai.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
