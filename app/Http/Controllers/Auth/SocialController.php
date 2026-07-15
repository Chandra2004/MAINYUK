<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(16)) // Random password since they login via google
                ]
            );

            Auth::login($user, true);

            // Kirim welcome notifikasi dan email hanya untuk pendaftar baru
            if ($user->wasRecentlyCreated) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type'    => 'system',
                    'title'   => 'Selamat Bergabung! 🎉',
                    'message' => 'Halo ' . $user->name . '! Akun MainYuk Anda telah berhasil dibuat via Google.',
                ]);
                \App\Jobs\SendMailJob::dispatch('welcome', [
                    'email' => $user->email,
                    'name'  => $user->name,
                ]);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }
    }
}
