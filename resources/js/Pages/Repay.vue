<template>
  <Head :title="`Bayar Pemesanan - ${booking.court_name}`" />

  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 py-12">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8 text-center bg-white border-b border-gray-100">
          <div class="w-16 h-16 bg-[#006241] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fa-solid fa-credit-card text-white text-2xl"></i>
          </div>
          <h1 class="text-2xl font-extrabold text-gray-900">Selesaikan Pembayaran</h1>
          <p class="text-gray-500 text-sm mt-1">Satu langkah lagi untuk pemesanan Anda dikonfirmasi</p>
        </div>

        <div class="p-6">
          <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Detail Pemesanan</h3>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-[#006241]/10 rounded-xl flex items-center justify-center text-[#006241]">
                <i class="fa-solid fa-hashtag text-lg"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 font-semibold mb-0.5">Kode Pemesanan</p>
                <p class="font-extrabold text-gray-900">{{ booking.booking_code }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-[#006241]/10 rounded-xl flex items-center justify-center text-[#006241]">
                <i class="fa-solid fa-location-dot text-lg"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 font-semibold mb-0.5">Lapangan</p>
                <p class="font-bold text-gray-900">{{ booking.court_name }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-[#006241]/10 rounded-xl flex items-center justify-center text-[#006241]">
                <i class="fa-regular fa-calendar-check text-lg"></i>
              </div>
              <div>
                <p class="text-xs text-gray-500 font-semibold mb-0.5">Jadwal</p>
                <p class="font-bold text-gray-900">{{ booking.date }} | {{ booking.time_start }} - {{ booking.time_end }}</p>
              </div>
            </div>
          </div>

          <div class="bg-[#006241] rounded-2xl p-6 text-white relative overflow-hidden shadow-lg mb-6">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-black/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex justify-between items-end">
              <div>
                <p class="text-white/80 text-sm font-semibold mb-1">Total Tagihan</p>
                <p class="text-3xl font-extrabold tracking-tight">Rp {{ booking.total_price.toLocaleString('id-ID') }}</p>
              </div>
              <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-wallet text-xl"></i>
              </div>
            </div>
          </div>

          <button @click="pay" :disabled="loading"
            class="w-full py-4 bg-[#006241] text-white rounded-2xl font-bold text-lg hover:bg-[#004d34] transition-all disabled:opacity-70 flex justify-center items-center gap-2 shadow-lg shadow-[#006241]/30">
            <i v-if="loading" class="fa-solid fa-circle-notch fa-spin"></i>
            <span v-else>Bayar Sekarang</span>
          </button>
          
          <Link :href="route('history')" class="block text-center text-gray-400 hover:text-gray-600 text-sm font-bold mt-4">
            Kembali ke Riwayat
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  booking: Object,
  snap_token: String,
  clientKey: String,
})

const loading = ref(false)

onMounted(() => {
  const script = document.createElement('script')
  script.src = 'https://app.sandbox.midtrans.com/snap/snap.js'
  script.setAttribute('data-client-key', props.clientKey)
  document.head.appendChild(script)
})

const pay = () => {
  if (!props.snap_token) {
    alert('Token pembayaran tidak ditemukan!')
    return
  }

  loading.value = true
  
  window.snap.pay(props.snap_token, {
    onSuccess: (result) => { 
      window.location.href = route('payment.success') + `?order_id=${result.order_id}`
    },
    onPending: (result) => { 
      window.location.href = route('payment.success') + `?order_id=${result.order_id}`
    },
    onError: () => { 
      window.location.href = route('payment.failed')
    },
    onClose: () => { 
      loading.value = false 
      window.location.href = route('payment.failed')
    },
  })
}
</script>
