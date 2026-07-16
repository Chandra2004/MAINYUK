<template>
  <Head title="MainYuk! PRO" />
  <DashboardLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      
      <!-- Hero Section -->
      <div class="bg-gradient-to-br from-[#1E3932] to-[#006241] rounded-3xl p-8 md:p-12 text-white relative overflow-hidden shadow-2xl mb-8">
        <div class="absolute top-0 right-0 opacity-10">
          <i class="fa-solid fa-crown text-[200px] -mt-10 -mr-10"></i>
        </div>
        
        <div class="relative z-10 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#cba258] text-[#1E3932] text-xs font-bold rounded-full mb-6 uppercase tracking-wider">
            <i class="fa-solid fa-star"></i> Membership Eksklusif
          </div>
          <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
            MainYuk! <span class="text-[#cba258]">PRO</span>
          </h1>
          <p class="text-lg text-white/80 font-medium mb-8">
            Tingkatkan pengalaman olahragamu. Dapatkan diskon 10% setiap booking dan bebas biaya layanan tanpa batas.
          </p>

          <div v-if="is_pro" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full flex items-center justify-between">
              <div>
                <p class="text-sm text-white/70 font-semibold mb-1">Status Langganan</p>
                <p class="text-xl font-bold text-[#cba258] flex items-center gap-2">
                  <i class="fa-solid fa-check-circle"></i> Aktif
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm text-white/70 font-semibold mb-1">Berlaku Hingga</p>
                <p class="text-lg font-bold text-white">{{ pro_expires_at }}</p>
              </div>
            </div>
            <button @click="showUnsubscribeModal = true"
              class="w-full sm:w-auto bg-white/10 hover:bg-white/20 transition-all text-white px-4 py-2.5 rounded-xl text-sm font-bold border border-white/20 cursor-pointer">
              Berhenti Langganan
            </button>
          </div>

          <form v-else @submit.prevent="subscribe">
            <button type="submit" :disabled="processing"
              class="bg-[#cba258] hover:bg-[#b08d4b] text-[#1E3932] px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-[0_4px_14px_rgba(203,162,88,0.39)] flex items-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
              <i v-if="processing" class="fa-solid fa-spinner fa-spin"></i>
              <span v-else>Berlangganan Rp 50.000 / Bulan</span>
              <i v-if="!processing" class="fa-solid fa-arrow-right"></i>
            </button>
            <p class="text-xs text-white/60 mt-3 font-medium">Bisa dibatalkan kapan saja. Pembayaran aman terenkripsi.</p>
          </form>
        </div>
      </div>

      <!-- Benefits -->
      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
          <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 text-xl mb-4">
            <i class="fa-solid fa-percent"></i>
          </div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Diskon 10%</h3>
          <p class="text-gray-500 text-sm leading-relaxed">
            Dapatkan potongan harga 10% untuk setiap booking lapangan, setiap saat, tanpa batas kuota.
          </p>
        </div>
        
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
          <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 text-xl mb-4">
            <i class="fa-solid fa-ban"></i>
          </div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Bebas Biaya Layanan</h3>
          <p class="text-gray-500 text-sm leading-relaxed">
            Tidak perlu lagi membayar biaya layanan 5% di setiap transaksi. Semua bebas potongan.
          </p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
          <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 text-xl mb-4">
            <i class="fa-solid fa-headset"></i>
          </div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Prioritas Support</h3>
          <p class="text-gray-500 text-sm leading-relaxed">
            Dapatkan layanan Customer Service VIP. Keluhan Anda akan diselesaikan dalam hitungan menit.
          </p>
        </div>
      </div>

    </div>

    <!-- Unsubscribe Modal -->
    <div v-if="showUnsubscribeModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="showUnsubscribeModal = false"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl animate-in fade-in zoom-in duration-200">
        <!-- Close Button -->
        <button @click="showUnsubscribeModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
          <i class="fa-solid fa-times"></i>
        </button>

        <div class="text-center mb-6">
          <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-face-frown-open text-amber-500 text-4xl"></i>
          </div>
          <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Yakin Ingin Berhenti?</h3>
          <p class="text-gray-500 text-sm leading-relaxed">
            Wah, sayang sekali! Jika Anda berhenti berlangganan MainYuk PRO, Anda akan kehilangan <strong>Bebas Biaya Layanan</strong> dan <strong>Ekstra Diskon 10%</strong> untuk setiap pemesanan.
          </p>
        </div>

        <div class="flex flex-col gap-3">
          <button @click="showUnsubscribeModal = false" class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all">
            Tetap Berlangganan (Direkomendasikan)
          </button>
          <Link :href="route('pro.unsubscribe')" method="delete" as="button" @click="showUnsubscribeModal = false" class="w-full bg-red-50 text-red-600 py-3.5 rounded-xl font-bold hover:bg-red-100 transition-all">
            Ya, Berhenti Langganan
          </Link>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  is_pro: Boolean,
  pro_expires_at: String,
})

const processing = ref(false)
const showUnsubscribeModal = ref(false)

const subscribe = () => {
  processing.value = true
  router.post(route('pro.subscribe'), {}, {
    onFinish: () => { processing.value = false }
  })
}
</script>
