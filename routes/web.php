<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Public Routes ───────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', fn() => Inertia::render('Faq'))->name('faq');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

// ─── Auth Routes (Guest Only) ────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/forgot-password',  [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');

    Route::get('/reset-password/{token}',  [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',         [AuthController::class, 'resetPassword'])->name('password.update');

    // Social Login (Google)
    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'callback']);
});

// ─── Logout ──────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Courts (Public) ─────────────────────────────────────
Route::get('/courts/{court}', [CourtController::class, 'show'])->name('court.detail');


// ─── Authenticated Routes ────────────────────────────────
Route::middleware('auth')->group(function () {

    // Email Verification Routes
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.send');

    // Dashboard (search courts)
    Route::get('/dashboard', [CourtController::class, 'index'])->name('dashboard');

    // Membership
    Route::post('/membership/subscribe', [\App\Http\Controllers\MembershipController::class, 'subscribe'])->name('membership.subscribe');
    Route::get('/membership/success', [\App\Http\Controllers\MembershipController::class, 'success'])->name('membership.success');
    Route::get('/membership/failed', [\App\Http\Controllers\MembershipController::class, 'failed'])->name('membership.failed');

    // Favorites
    Route::get('/favorites',              [CourtController::class, 'favorites'])->name('favorites');
    Route::post('/courts/{court}/favorite', [CourtController::class, 'toggleFavorite'])->name('court.favorite')->middleware('throttle:60,1');

    // Profile
    Route::get('/profile',          [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar',  [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Notifications
    Route::get('/notifications',         [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read',   [NotificationController::class, 'markRead'])->name('notifications.read');

    // PRO Subscription Routes
    Route::get('/pro', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('pro.index');
    Route::post('/pro/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('pro.subscribe');
    Route::delete('/pro/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribe'])->name('pro.unsubscribe');

    // History
    Route::get('/history', [BookingController::class, 'history'])->name('history');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('throttle:10,1');

    // Booking flow
    Route::get('/booking/{court}/schedule', [BookingController::class, 'schedule'])->name('booking.schedule');
    Route::get('/booking/{court}/weather',  [BookingController::class, 'weather'])->name('booking.weather');
    Route::post('/booking/cart',            [BookingController::class, 'addToCart'])->name('booking.add-to-cart');
    Route::get('/booking/cart',             [BookingController::class, 'cart'])->name('booking.cart');
    Route::get('/booking/payment',          [BookingController::class, 'payment'])->name('booking.payment');
    Route::get('/booking/{booking:booking_code}/repay', [BookingController::class, 'repay'])->name('booking.repay');
    Route::post('/booking/{booking:booking_code}/repay-balance', [BookingController::class, 'repayBalance'])->name('booking.repay-balance');
    Route::get('/booking/{booking_code}/qr', [BookingController::class, 'qr'])->name('booking.qr');
    Route::post('/booking/payment',         [BookingController::class, 'processPayment'])->name('booking.process');
    Route::post('/booking/validate-promo',  [BookingController::class, 'validatePromo'])->name('booking.validate-promo')->middleware('throttle:30,1');

    Route::get('/admin/bookings/{booking_code}/check-in', function ($booking_code) {
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'super_admin'])) {
            abort(403);
        }
        $booking = \App\Models\Booking::where('booking_code', $booking_code)->firstOrFail();
        $booking->update(['check_in_time' => now()]);
        return redirect()->to('/admin/bookings')->with('success', 'Berhasil Check-in');
    })->name('admin.bookings.check-in');

    // Payment result pages
    Route::get('/payment/success', [BookingController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed',  [BookingController::class, 'paymentFailed'])->name('payment.failed');

    // Cancel, Reschedule & Invoice
    Route::post('/booking/{booking}/cancel',  [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::get('/booking/{booking}/invoice',  [BookingController::class, 'downloadInvoice'])->name('booking.invoice');
});

// ─── Midtrans Webhook (No CSRF) ──────────────────────────
Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ─── 404 ─────────────────────────────────────────────────
Route::fallback(fn() => Inertia::render('Errors/NotFound', [], 404));
