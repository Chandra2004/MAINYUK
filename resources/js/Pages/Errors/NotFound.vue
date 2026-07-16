<template>
  <Head title="Halaman Tidak Ditemukan" />
  <div class="min-h-screen bg-[#f2f0eb] flex flex-col items-center justify-center px-4 text-center" style="font-family: 'Manrope', sans-serif;">
    <div class="text-9xl font-extrabold text-[#1E3932] leading-none mb-4" style="letter-spacing: -0.05em;">404</div>
    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Halaman Tidak Ditemukan</h1>
    <p class="text-gray-400 max-w-sm mb-8">Sepertinya lapangan yang kamu cari sudah penuh atau tidak ada. Coba kembali ke beranda!</p>
    <div class="flex gap-3">
      <Link :href="route('home')"
        class="bg-[#006241] text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-[#004d34] transition-all flex items-center gap-2">
        <i class="fa-solid fa-house"></i> Ke Beranda
      </Link>
      <Link :href="route('dashboard')"
        class="border-2 border-[#006241] text-[#006241] px-6 py-3 rounded-full font-bold text-sm hover:bg-[#006241] hover:text-white transition-all">
        Cari Lapangan
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>
