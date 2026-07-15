<template>
  <Head title="Pembayaran Berhasil" />
  <DashboardLayout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
      <div class="max-w-md w-full bg-white border border-gray-100 rounded-3xl p-8 shadow-xl text-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-green-100 shadow-sm animate-bounce">
          <i class="fa-solid fa-circle-check text-green-600 text-4xl"></i>
        </div>

        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Pembayaran Berhasil! 🎉</h1>
        <p v-if="membership" class="text-gray-500 mb-6 text-sm">Langganan MainYuk PRO Anda telah aktif. Nikmati keuntungannya!</p>
        <p v-else class="text-gray-500 mb-6 text-sm">Pemesanan Anda telah berhasil dikonfirmasi. Selamat bermain!</p>

        <!-- Booking Card -->
        <div v-if="booking" class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mb-6 text-left">
          <div class="flex items-center justify-between mb-3 border-b border-gray-200/60 pb-3">
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Detail Pemesanan</p>
            <span class="bg-[#006241] text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
              {{ booking.status === 'active' ? 'Lunas' : 'Pending' }}
            </span>
          </div>
          <p class="text-lg font-extrabold text-[#006241] mb-4 text-center tracking-wider bg-white py-2 rounded-xl border border-gray-100">{{ booking.booking_code }}</p>

          <div class="space-y-2.5 text-xs font-bold text-gray-500">
            <div class="flex justify-between">
              <span>Lapangan</span>
              <span class="text-gray-900">{{ booking.court_name }}</span>
            </div>
            <div class="flex justify-between">
              <span>Tanggal</span>
              <span class="text-gray-900">{{ booking.date }}</span>
            </div>
            <div class="flex justify-between">
              <span>Waktu</span>
              <span class="text-gray-900">{{ booking.time_start }} – {{ booking.time_end }}</span>
            </div>
            <div class="border-t border-gray-200/60 pt-3 flex justify-between font-extrabold text-sm text-gray-900">
              <span>Total Dibayar</span>
              <span class="text-[#006241]">Rp {{ booking.total_price?.toLocaleString('id-ID') }}</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <Link v-if="membership" :href="route('pro.index')"
            class="flex-1 bg-[#006241] !text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm flex items-center justify-center gap-2 cursor-pointer shadow-md">
            <i class="fa-solid fa-crown"></i> Kembali ke Membership
          </Link>
          
          <template v-else>
            <Link :href="route('history')"
              class="flex-1 bg-[#006241] !text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm flex items-center justify-center gap-2 cursor-pointer shadow-md">
              <i class="fa-solid fa-list"></i> Riwayat
            </Link>
            <Link :href="route('dashboard')"
              class="flex-1 bg-white border-2 border-gray-100 !text-gray-700 py-3.5 rounded-xl font-bold hover:border-[#006241] hover:!text-[#006241] transition-all text-sm cursor-pointer">
              Pesan Lagi
            </Link>
          </template>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

defineProps({
  booking: Object,
  membership: Boolean,
})
</script>
