<template>
  <Head title="Pembayaran Gagal" />
  <DashboardLayout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
      <div class="max-w-md w-full bg-white border border-gray-100 rounded-3xl p-8 shadow-xl text-center">
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-red-100 shadow-sm animate-pulse">
          <i class="fa-solid fa-circle-xmark text-red-500 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Pembayaran Gagal</h1>
        <p v-if="membership" class="text-gray-500 mb-6 text-sm">Pembayaran langganan Anda tidak berhasil. Silakan coba lagi atau gunakan metode lain.</p>
        <p v-else class="text-gray-500 mb-6 text-sm">Transaksi Anda tidak berhasil. Silakan coba beberapa saat lagi atau hubungi pihak bank Anda.</p>

        <div v-if="booking" class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mb-6 text-left">
          <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-2">Kode Pemesanan yang Gagal</p>
          <p class="text-lg font-extrabold text-red-600 mb-1 font-mono tracking-wider bg-white py-2 px-3 rounded-xl border border-gray-100 text-center">{{ booking.booking_code }}</p>
          <p class="text-gray-600 text-xs font-bold text-center mt-2">{{ booking.court_name }}</p>
        </div>

        <div v-if="membership" class="flex gap-3">
          <Link :href="route('pro.index')"
            class="flex-1 bg-[#006241] !text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm cursor-pointer shadow-md text-center flex items-center justify-center gap-2">
            Kembali ke Membership
          </Link>
        </div>
        <div v-else class="flex gap-3">
          <Link :href="route('dashboard')"
            class="flex-1 bg-[#006241] !text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm cursor-pointer shadow-md text-center flex items-center justify-center gap-2">
            Pemesanan Baru
          </Link>
          <Link :href="route('history')"
            class="flex-1 bg-white border-2 border-gray-100 !text-gray-700 py-3.5 rounded-xl font-bold hover:border-[#006241] hover:!text-[#006241] transition-all text-sm cursor-pointer text-center">
            Riwayat
          </Link>
        </div>

        <p class="text-xs text-gray-400 mt-6 font-semibold">
          Butuh bantuan? <a href="mailto:support@mainyuk.id" class="text-[#006241] font-bold hover:underline">Hubungi Support</a>
        </p>
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
