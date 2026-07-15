<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'MainYuk') }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="MainYuk! - Platform pemesanan lapangan olahraga terbaik, tercepat, dan terlengkap di kota Anda. Booking lapangan futsal, badminton, basket, dan tenis dengan mudah.">
    <meta name="keywords" content="booking lapangan, futsal, badminton, basket, tenis, olahraga, mainyuk">
    <meta name="author" content="MainYuk Team">
    <meta property="og:title" content="MainYuk! - Booking Lapangan Olahraga">
    <meta property="og:description" content="Sistem pemesanan lapangan olahraga terbaik dan tercepat. Gabung Mabar, bayar patungan, dan nikmati fitur lengkap lainnya.">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#006241">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Midtrans Snap -->
    @if(config('services.midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    <!-- Ziggy Routes (make route() available globally) -->
    @routes

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>