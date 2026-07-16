<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{

    // ─── Login ───────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        $request->session()->regenerate();

        Notification::create([
            'user_id' => Auth::id(),
            'type'    => 'system',
            'title'   => 'Login Berhasil',
            'message' => 'Anda berhasil masuk pada ' . now()->format('d M Y H:i'),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    // ─── Register ────────────────────────────────────────
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
        ]);

        // Welcome notification
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'system',
            'title'   => 'Selamat Bergabung! 🎉',
            'message' => 'Halo ' . $user->name . '! Akun MainYuk Anda telah berhasil dibuat. Silakan verifikasi email Anda.',
        ]);

        \App\Jobs\SendMailJob::dispatch('welcome', [
            'email' => $user->email,
            'name'  => $user->name,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat bergabung di MainYuk.');
    }

    // ─── Email Verification ──────────────────────────────

    public function showVerifyEmail(Request $request)
    {
        return redirect()->route('dashboard');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }

    // ─── Logout ──────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // ─── Forgot Password ─────────────────────────────────
    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }

        throw ValidationException::withMessages(['email' => __($status)]);
    }

    // ─── Reset Password ──────────────────────────────────
    public function showResetPassword(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages(['email' => __($status)]);
    }
}
