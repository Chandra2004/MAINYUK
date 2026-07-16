<template>
  <Head title="Konfirmasi Pemesanan - MainYuk" />
  <DashboardLayout>
    <div class="max-w-xl mx-auto px-4 sm:px-6 py-8">

      <!-- Page Header -->
      <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">
          Konfirmasi <span class="text-[#006241]">Pemesanan</span>
        </h1>
        <p class="text-gray-500 text-sm">Periksa detail dan selesaikan pembayaran.</p>
      </div>

      <!-- Session Info Bar -->
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between mb-6 shadow-sm">
        <div class="flex items-center gap-2 text-sm font-semibold text-amber-700">
          <i class="fa-solid fa-circle-info text-amber-500"></i>
          <span>Selesaikan pembayaran sebelum slot habis dipesan orang lain.</span>
        </div>
      </div>

      <!-- Expired Overlay -->
      <div v-if="isExpired" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-clock text-red-500 text-3xl"></i>
          </div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Sesi Habis</h3>
          <p class="text-sm text-gray-500 mb-6">Waktu memilih jadwal telah habis. Silakan pilih ulang jadwal Anda.</p>
          <Link :href="route('dashboard')" class="block w-full bg-[#006241] text-white py-3 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm">
            Pilih Lapangan Baru
          </Link>
        </div>
      </div>

      <div class="space-y-5">
        <!-- Order Detail Summary -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm space-y-3.5">
          <div class="flex gap-4 items-center border-b border-gray-100 pb-3.5">
            <img :src="court.main_image" :alt="court.name" class="w-16 h-16 rounded-xl object-cover shrink-0" />
            <div>
              <h3 class="font-extrabold text-gray-900 leading-snug">{{ court.name }}</h3>
              <p class="text-xs text-gray-400 font-bold flex items-center gap-1 mt-0.5">
                <i class="fa-solid fa-location-dot text-[#006241]"></i> {{ court.city }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 text-xs text-gray-600 font-semibold">
            <div class="flex flex-col gap-0.5">
              <span class="text-[10px] text-gray-400 uppercase tracking-wider">Tanggal</span>
              <span class="text-gray-900 font-bold">{{ cart.date }}</span>
            </div>
            <div class="flex flex-col gap-0.5">
              <span class="text-[10px] text-gray-400 uppercase tracking-wider">Jam</span>
              <span class="text-gray-900 font-bold">{{ cart.time_start }} – {{ cart.time_end }}</span>
            </div>

          </div>
        </div>

        <!-- Equipment Add-ons (dari DB, dengan quantity counter) -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 mb-3.5">
            <i class="fa-solid fa-plus-circle text-[#006241]"></i> Sewa Peralatan (opsional)
          </h3>

          <div v-if="equipment.length === 0" class="text-xs text-gray-400 italic text-center py-3">
            Tidak ada peralatan sewa untuk lapangan ini.
          </div>

          <div v-else class="space-y-3">
            <div v-for="item in equipment" :key="item.id"
              class="flex items-center justify-between border border-gray-100 rounded-xl p-3 hover:border-gray-200 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#f0faf5] flex items-center justify-center text-[#006241] shrink-0">
                  <i :class="item.icon" class="text-sm"></i>
                </div>
                <div>
                  <h4 class="font-bold text-gray-900 text-xs leading-none">{{ item.name }}</h4>
                  <p class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ item.description }}</p>
                  <p class="text-[10px] text-[#006241] font-bold mt-0.5">Rp {{ item.price_per_unit.toLocaleString('id-ID') }}/unit</p>
                </div>
              </div>
              <!-- Quantity Counter -->
              <div class="flex items-center gap-2">
                <button @click="decreaseQty(item)"
                  class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 font-bold text-sm flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer"
                  :disabled="(addonQty[item.id] ?? 0) === 0">
                  −
                </button>
                <span class="w-6 text-center text-sm font-extrabold text-gray-900">
                  {{ addonQty[item.id] ?? 0 }}
                </span>
                <button @click="increaseQty(item)"
                  class="w-7 h-7 rounded-full bg-[#006241] text-white font-bold text-sm flex items-center justify-center hover:bg-[#004d34] transition-all cursor-pointer">
                  +
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Promo Code -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 mb-3">
            <i class="fa-solid fa-tag text-[#006241]"></i> Promo & Diskon
          </h3>
          
          <div v-if="promoSuccess" class="flex items-center justify-between bg-[#f0faf5] p-3 rounded-xl border border-[#006241]/20">
            <div>
              <p class="text-xs font-bold text-[#006241]">{{ promoCode }}</p>
              <p class="text-[10px] text-green-600">{{ promoMsg }}</p>
            </div>
            <button @click="removePromo" class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1 bg-red-50 rounded-lg">
              Hapus
            </button>
          </div>
          
          <button v-else @click="promoListModalOpen = true" class="w-full py-3 bg-gray-50 border border-gray-200 text-gray-700 font-bold rounded-xl flex items-center justify-between hover:bg-gray-100 transition-all px-4 text-sm">
            <span class="flex items-center gap-2"><i class="fa-solid fa-ticket text-[#006241]"></i> Pilih Promo</span>
            <i class="fa-solid fa-chevron-right text-gray-400"></i>
          </button>
        </div>



        <!-- Price Breakdown -->
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm space-y-3 text-xs font-bold text-gray-500">
          <div class="flex justify-between">
            <span>Tarif Dasar ({{ summary.hours }} jam)</span>
            <span class="text-gray-900">Rp {{ baseRate.toLocaleString('id-ID') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="flex items-center gap-1">
              Biaya Layanan (5%)
              <span v-if="isPro" class="bg-[#cba258] text-white px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wider">PRO Gratis</span>
            </span>
            <span :class="isPro ? 'line-through text-gray-300' : 'text-gray-900'">Rp {{ Math.round(baseRate * 0.05).toLocaleString('id-ID') }}</span>
          </div>
          <div v-if="addonTotal > 0" class="flex justify-between">
            <span>Sewa Peralatan</span>
            <span class="text-gray-900">Rp {{ addonTotal.toLocaleString('id-ID') }}</span>
          </div>
          <div v-if="discountAmount > 0" class="flex justify-between text-green-600">
            <span class="flex items-center gap-1">
              Diskon {{ isPro ? '(PRO 10% + Promo)' : '(Promo)' }} 
              <span v-if="isPro" class="bg-[#cba258] text-white px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wider">PRO</span>
            </span>
            <span>– Rp {{ discountAmount.toLocaleString('id-ID') }}</span>
          </div>
          <div class="border-t border-gray-100 pt-3 flex justify-between font-extrabold text-base text-gray-900">
            <span>Total Bayar</span>
            <span class="text-[#006241]">Rp {{ totalPay.toLocaleString('id-ID') }}</span>
          </div>
        </div>

        <!-- Cancellation Policy -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex gap-3 text-xs text-gray-600 font-semibold leading-relaxed">
          <i class="fa-solid fa-circle-info text-[#006241] text-sm mt-0.5"></i>
          <div>
            <strong class="text-gray-900 block mb-0.5">Kebijakan Pembatalan:</strong>
            H-1 gratis. Hari H dikenakan potongan 50%. Non-refundable setelah jam mulai.
          </div>
        </div>

        <!-- Checkout Buttons -->
        <div>
          <button @click="proceedToPayment" :disabled="isProceeding"
            class="w-full text-center bg-[#006241] text-white py-4 rounded-xl font-bold hover:bg-[#004d34] transition-all mb-3 text-base shadow-md cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <i v-if="isProceeding" class="fa-solid fa-spinner fa-spin"></i>
            {{ isProceeding ? 'Memproses...' : 'Bayar Sekarang' }}
          </button>
          <Link :href="route('dashboard')" class="block w-full text-center text-gray-400 hover:text-gray-600 text-sm font-bold py-2">
            Batal
          </Link>
        </div>

        <!-- Security Badges -->
        <div class="flex justify-center items-center gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-2">
          <span class="flex items-center gap-1"><i class="fa-solid fa-lock text-green-500"></i> SSL Encrypted</span>
          <span class="flex items-center gap-1"><i class="fa-solid fa-shield-halved text-green-500"></i> Verified Payment</span>
          <span class="flex items-center gap-1"><i class="fa-solid fa-clock text-green-500"></i> Konfirmasi Instan</span>
        </div>
      </div>
    </div>

    <!-- Modal List Promo -->
    <Transition name="fade">
      <div v-if="promoListModalOpen" class="fixed inset-0 z-[100] flex flex-col justify-end md:items-center md:justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full md:max-w-md rounded-t-3xl md:rounded-3xl overflow-hidden shadow-2xl relative">
          <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-extrabold text-gray-900">Pilih Promo</h3>
            <button @click="promoListModalOpen = false" class="text-gray-400 hover:text-gray-600">
              <i class="fa-solid fa-xmark text-xl"></i>
            </button>
          </div>
          
          <div class="p-6 max-h-[60vh] overflow-y-auto bg-gray-50">
            <div v-if="!available_promos?.length" class="text-center py-8">
              <i class="fa-solid fa-ticket text-4xl text-gray-300 mb-3"></i>
              <p class="text-sm font-bold text-gray-500">Tidak ada promo tersedia saat ini.</p>
            </div>
            
            <div class="space-y-3">
              <div v-for="p in available_promos" :key="p.id" 
                class="bg-white border border-gray-200 rounded-2xl p-4 hover:border-[#006241] transition-all relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-16 h-16 bg-[#006241]/5 rounded-full blur-xl"></div>
                <div class="flex justify-between items-start mb-2 relative z-10">
                  <div>
                    <h4 class="font-extrabold text-lg text-gray-900">{{ p.code }}</h4>
                    <span class="inline-block mt-1 text-[10px] bg-[#006241] text-white px-2 py-0.5 rounded-full font-bold shadow-sm">Diskon {{ p.discount_percent }}%</span>
                  </div>
                  <button @click="usePromo(p.code)" class="bg-[#006241] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#004d34] transition-all">
                    Pakai
                  </button>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 relative z-10">
                  <p class="text-xs text-gray-500 leading-relaxed">{{ p.description || 'Syarat dan ketentuan berlaku.' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'

const props = defineProps({
  cart:      Object,
  court:     Object,
  summary:   Object,
  equipment: { type: Array, default: () => [] },
  available_promos: { type: Array, default: () => [] },
})

const promoListModalOpen = ref(false)

const usePromo = (code) => {
  promoCode.value = code
  promoListModalOpen.value = false
  applyPromo()
}

const isExpired   = ref(false)
const timeLeft    = ref(30 * 60) // 30 menit: waktu cukup untuk review & pilih addon
let timerInterval = null

const formattedTime = computed(() => {
  const m = Math.floor(timeLeft.value / 60)
  const s = timeLeft.value % 60
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

onMounted(() => {
  timerInterval = setInterval(() => {
    timeLeft.value--
    if (timeLeft.value <= 0) {
      clearInterval(timerInterval)
      isExpired.value = true  // tampilkan overlay expired, BUKAN silent redirect
    }
  }, 1000)
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

// ─── Equipment Quantity Counter ───────────────────────────
const addonQty = ref({})   // { equipmentId: qty }

const increaseQty = (item) => {
  const current = addonQty.value[item.id] ?? 0
  if (current < item.stock) {
    addonQty.value = { ...addonQty.value, [item.id]: current + 1 }
  }
}

const decreaseQty = (item) => {
  const current = addonQty.value[item.id] ?? 0
  if (current > 0) {
    addonQty.value = { ...addonQty.value, [item.id]: current - 1 }
  }
}

const addonTotal = computed(() => {
  return props.equipment.reduce((sum, item) => {
    return sum + (item.price_per_unit * (addonQty.value[item.id] ?? 0))
  }, 0)
})

const addonItems = computed(() =>
  props.equipment
    .filter(e => (addonQty.value[e.id] ?? 0) > 0)
    .map(e => ({ id: e.id, qty: addonQty.value[e.id] }))
)

// ─── Promo Code (AJAX ke backend) ────────────────────────
const promoCode    = ref('')
const promoMsg     = ref('')
const promoSuccess = ref(false)
const promoLoading = ref(false)
const discountPct  = ref(0)

const applyPromo = async () => {
  const code = promoCode.value.trim().toUpperCase()
  if (!code) return

  promoLoading.value = true
  try {
    const res = await axios.post(route('booking.validate-promo'), {
      code,
      court_id: props.court.id,
    })
    if (res.data.valid) {
      discountPct.value  = res.data.discount_percent
      promoMsg.value     = res.data.message
      promoSuccess.value = true
    } else {
      discountPct.value  = 0
      promoMsg.value     = res.data.message
      promoSuccess.value = false
    }
  } catch {
    promoMsg.value     = '❌ Gagal memvalidasi kode promo, coba lagi.'
    promoSuccess.value = false
  } finally {
    promoLoading.value = false
  }
}

const removePromo = () => {
  promoCode.value    = ''
  promoMsg.value     = ''
  promoSuccess.value = false
  discountPct.value  = 0
}

// ─── Price Calculations ───────────────────────────────────
import { usePage } from '@inertiajs/vue3'
const page = usePage()

const isPro = computed(() => {
  const user = page.props.auth?.user
  if (!user || !user.is_pro || !user.pro_expires_at) return false
  return new Date(user.pro_expires_at) > new Date()
})

const baseRate       = computed(() => props.summary.subtotal)
const serviceFee     = computed(() => isPro.value ? 0 : Math.round(baseRate.value * 0.05))
const finalDiscountPct = computed(() => discountPct.value + (isPro.value ? 10 : 0))
const discountAmount = computed(() => Math.round(baseRate.value * finalDiscountPct.value / 100))
const totalPay       = computed(() => Math.max(0, baseRate.value + serviceFee.value + addonTotal.value - discountAmount.value))

const isProceeding = ref(false)



const proceedToPayment = () => {
  isProceeding.value = true
  router.visit(route('booking.payment'), {
    method: 'get',
    data: {
      promo_code:  promoCode.value.trim().toUpperCase(),
      addon_total: addonTotal.value,
      addon_items: addonItems.value,

    },
    onError: () => { isProceeding.value = false },
  })
}
</script>
