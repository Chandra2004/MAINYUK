<template>
  <Head title="Pembayaran - MainYuk" />
  <DashboardLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-[#f0faf5] py-8 px-4">
      <div class="max-w-md mx-auto">

        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-[#006241] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fa-solid fa-credit-card text-white text-2xl"></i>
          </div>
          <h2 class="text-xl font-extrabold text-gray-900 mt-4">Selesaikan Pemesanan</h2>
          <p class="text-gray-500 text-sm mt-1">Satu langkah lagi untuk pemesanan Anda dikonfirmasi</p>
        </div>

        <!-- Court Summary Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
          <div class="flex items-center gap-4">
            <img :src="court.main_image" :alt="court.name"
              class="w-16 h-16 rounded-xl object-cover shrink-0 shadow-sm" />
            <div class="flex-1 min-w-0">
              <h2 class="font-extrabold text-gray-900 text-base truncate">{{ court.name }}</h2>
              <p class="text-xs text-gray-400 font-semibold flex items-center gap-1 mt-0.5">
                <i class="fa-solid fa-location-dot text-[#006241]"></i> {{ court.city }}
              </p>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-2 gap-2">
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Tanggal</p>
              <p class="text-sm font-extrabold text-gray-900">{{ cart.date }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Jam</p>
              <p class="text-sm font-extrabold text-gray-900">{{ cart.time_start }} – {{ cart.time_end }}</p>
            </div>
          </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
          <h3 class="text-sm font-extrabold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-[#006241]"></i>
            Rincian Pembayaran
          </h3>

          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between text-gray-500 font-semibold">
              <span>Sewa lapangan ({{ summary.hours }} jam)</span>
              <span class="text-gray-900">Rp {{ summary.subtotal.toLocaleString('id-ID') }}</span>
            </div>
            <div class="flex justify-between text-gray-500 font-semibold">
              <span>Biaya layanan (5%)</span>
              <span class="text-gray-900">Rp {{ summary.service_fee.toLocaleString('id-ID') }}</span>
            </div>
            <div v-if="summary.addon_total > 0" class="flex justify-between text-gray-500 font-semibold">
              <span>Sewa peralatan</span>
              <span class="text-gray-900">Rp {{ summary.addon_total.toLocaleString('id-ID') }}</span>
            </div>
            <div v-if="summary.discount > 0" class="flex justify-between text-green-600 font-semibold">
              <span>Diskon promo</span>
              <span>– Rp {{ summary.discount.toLocaleString('id-ID') }}</span>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-400 font-semibold">Total Pembayaran</p>
              <p class="text-2xl font-extrabold text-[#006241]">
                Rp {{ summary.amount_due.toLocaleString('id-ID') }}
              </p>
            </div>
            <div class="bg-[#f0faf5] border border-[#006241]/20 rounded-xl px-3 py-2 text-center">
              <p class="text-[10px] text-[#006241] font-bold uppercase tracking-wider">Metode</p>
              <p class="text-xs font-extrabold text-[#006241]">LUNAS</p>
            </div>
          </div>
        </div>

        <!-- Midtrans handles payment methods -->

        <!-- Pay Button -->
        <button @click="pay" :disabled="loading"
          class="w-full py-4 bg-[#006241] text-white font-extrabold text-base rounded-2xl shadow-lg hover:bg-[#004d34] transition-all flex items-center justify-center gap-3 cursor-pointer disabled:opacity-70">
          <span v-if="loading">
            <i class="fa-solid fa-spinner fa-spin"></i> Memproses...
          </span>
          <span v-else>
            <i class="fa-solid fa-lock mr-2"></i>
            Bayar Rp {{ summary.amount_due.toLocaleString('id-ID') }}
          </span>
        </button>

        <!-- Error Message -->
        <div v-if="errorMsg" class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
          <p class="text-sm text-red-600 font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ errorMsg }}
          </p>
        </div>

        <!-- Security Notice -->
        <div class="flex justify-center gap-6 mt-6 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
          <span class="flex items-center gap-1"><i class="fa-solid fa-lock text-green-500"></i> SSL Aman</span>
          <span class="flex items-center gap-1"><i class="fa-brands fa-cc-visa text-blue-500"></i> PCI DSS</span>
          <span class="flex items-center gap-1"><i class="fa-solid fa-shield-halved text-green-500"></i> Midtrans</span>
        </div>

        <Link :href="route('booking.cart')" class="block text-center text-gray-400 hover:text-gray-600 text-sm font-bold mt-4">
          ← Kembali ke Keranjang
        </Link>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'

const props = defineProps({
  cart:      Object,
  court:     Object,
  summary:   Object,
  clientKey: String,
})

const loading  = ref(false)
const errorMsg = ref('')

const payMethods = [
  { name: 'GoPay',      icon: 'fa-solid fa-g' },
  { name: 'QRIS',       icon: 'fa-solid fa-qrcode' },
  { name: 'Transfer',   icon: 'fa-solid fa-building-columns' },
  { name: 'BCA / BRI',  icon: 'fa-solid fa-credit-card' },
  { name: 'OVO',        icon: 'fa-solid fa-o' },
  { name: 'Indomaret',  icon: 'fa-solid fa-store' },
]

const pay = async () => {
  loading.value  = true
  errorMsg.value = ''

  try {
    const res = await axios.post(route('booking.process'), {
      promo_code:  props.cart?.promo_code ?? '',
      addon_total: props.summary.addon_total ?? 0,
      addon_items: props.cart?.addon_items ?? [],
    })

    const { snap_token, order_id } = res.data

    // Buka Midtrans Snap — order_id WAJIB diteruskan ke callback URL
    window.snap.pay(snap_token, {
      onSuccess: (result) => {
        const id = result.order_id || order_id
        window.location.href = route('payment.success') + (id ? `?order_id=${id}` : '')
      },
      onPending: (result) => {
        window.location.href = route('history')
      },
      onError: (result) => {
        window.location.href = route('history')
      },
      onClose: () => { 
        loading.value = false 
        window.location.href = route('payment.failed') + (order_id ? `?order_id=${order_id}` : '')
      },
    })
  } catch (e) {
    loading.value = false
    errorMsg.value = e.response?.data?.error ?? 'Terjadi kesalahan. Silakan coba lagi.'
  }
}
</script>
