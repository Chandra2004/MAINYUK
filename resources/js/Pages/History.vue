<template>
  <Head title="Riwayat Pemesanan" />
  <DashboardLayout>
    <div class="max-w-3xl mx-auto px-4 sm:px-8 py-8">
      <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Riwayat Pemesanan</h1>

      <!-- Tabs -->
      <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-6 flex-wrap">
        <button v-for="tab in tabs" :key="tab.value"
          @click="activeTab = tab.value"
          :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-1.5',
            activeTab === tab.value ? 'bg-white text-[#006241] shadow-sm' : 'text-gray-500 hover:text-gray-700']">
          {{ tab.label }}
          <span v-if="countByStatus[tab.value]"
            class="bg-[#006241] text-white text-[10px] min-w-[16px] h-4 px-1 rounded-full inline-flex items-center justify-center">
            {{ countByStatus[tab.value] }}
          </span>
        </button>
      </div>

      <!-- Bookings List -->
      <div v-if="filteredBookings.length" class="space-y-4">
        <div v-for="b in filteredBookings" :key="b.id"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4 items-start hover:border-[#006241]/20 transition-all">
          <img :src="b.court_image" :alt="b.court_name"
            class="w-16 h-16 rounded-xl object-cover shrink-0 shadow-sm" />
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 flex-wrap mb-1">
              <h3 class="font-bold text-gray-900 truncate">{{ b.court_name }}</h3>
              <div class="flex gap-1.5 flex-wrap">
                <StatusBadge :status="b.status" />
                <PaymentBadge v-if="b.payment_status" :status="b.payment_status" />
              </div>
            </div>
            <p class="text-xs text-gray-500 font-semibold">
              <i class="fa-solid fa-hashtag text-gray-300 mr-0.5"></i>{{ b.booking_code }}
            </p>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">
              <i class="fa-regular fa-calendar text-gray-300 mr-1"></i>{{ b.date }} &nbsp;
              <i class="fa-regular fa-clock text-gray-300 mr-1"></i>{{ b.time_start }} – {{ b.time_end }}
            </p>
            <p class="text-sm font-extrabold text-[#006241] mt-1.5">
              Rp {{ b.total_price.toLocaleString('id-ID') }}
            </p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ b.created_at }}</p>
          </div>
          <div class="flex flex-col gap-2 shrink-0 w-full sm:w-auto">
            <button v-if="b.status === 'pending'" @click="repay(b)"
              class="text-[11px] bg-[#006241] text-white px-3 py-1.5 rounded-lg font-bold hover:bg-[#004d34] transition-all flex items-center justify-center gap-1">
              <i class="fa-regular fa-credit-card"></i> Bayar
            </button>
            <button v-if="b.status === 'active'" @click="openQR(b)"
              class="text-[11px] bg-[#006241] text-white px-3 py-1.5 rounded-lg font-bold hover:bg-[#004d34] transition-all">
              <i class="fa-solid fa-qrcode"></i> QR Code
            </button>
            <a v-if="(b.payment_status === 'settlement' || b.payment_status === 'capture' || b.status === 'cancelled') && b.status !== 'completed'" :href="route('booking.invoice', b.id)" target="_blank"
              class="text-[11px] bg-gray-50 border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-bold hover:bg-gray-100 transition-all text-center flex items-center justify-center gap-1">
              <i class="fa-solid fa-file-pdf text-red-400"></i> Invoice
            </a>
            <button v-if="b.can_cancel" @click="confirmCancel(b)"
              class="text-[11px] bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold hover:bg-red-100 transition-all">
              Batalkan
            </button>
            <Link v-if="b.status === 'completed' || b.status === 'cancelled'" :href="route('court.detail', b.court_id)"
              class="text-[11px] bg-amber-50 text-amber-600 px-3 py-1.5 rounded-lg font-bold hover:bg-amber-100 transition-all text-center">
              Pesan Lagi
            </Link>
            <button v-if="b.status === 'completed' && !b.is_reviewed" @click="openReview(b)"
              class="text-[11px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-bold hover:bg-blue-100 transition-all">
              Tulis Ulasan
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!filteredBookings.length" class="text-center py-20">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-regular fa-calendar-xmark text-3xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Belum ada pemesanan</h3>
        <p class="text-gray-400 text-sm mb-4">Mulai pemesanan lapangan favoritmu sekarang!</p>
        <Link :href="route('dashboard')"
          class="inline-block bg-[#006241] text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-[#004d34] transition-all">
          Cari Lapangan
        </Link>
      </div>

      <!-- Cancel Modal -->
      <div v-if="cancelModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-2xl"></i>
          </div>
          <h3 class="text-lg font-extrabold text-gray-900 mb-2">Batalkan Pemesanan?</h3>
          <p class="text-sm text-gray-500 mb-6">Anda yakin ingin membatalkan pemesanan <strong>{{ bookingToCancel?.booking_code }}</strong>? Aksi ini tidak dapat diurungkan.</p>
          <div class="flex gap-3">
            <button @click="cancelCancel" class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all">Kembali</button>
            <button @click="proceedCancel" class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all flex justify-center items-center gap-2">
              <i v-if="cancelProcessing" class="fa-solid fa-spinner fa-spin"></i> Ya, Batalkan
            </button>
          </div>
        </div>
      </div>

      <!-- Review Modal -->
      <div v-if="reviewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-gray-900">Tulis Ulasan</h3>
            <button @click="reviewModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
          </div>
          <p class="text-sm text-gray-500 mb-4">Bagaimana pengalaman Anda bermain di <strong>{{ bookingToReview?.court_name }}</strong>?</p>
          
          <form @submit.prevent="submitReview">
            <div class="flex gap-2 justify-center mb-6">
              <button type="button" v-for="star in 5" :key="star" @click="reviewForm.rating = star" class="text-3xl transition-all"
                :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-200 hover:text-yellow-200'">
                <i class="fa-solid fa-star"></i>
              </button>
            </div>
            <textarea v-model="reviewForm.comment" rows="3" placeholder="Ceritakan pengalaman Anda (opsional)..."
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] resize-none mb-6"></textarea>
            
            <button type="submit" :disabled="reviewForm.processing || !reviewForm.rating"
              class="w-full py-3 bg-[#006241] text-white rounded-xl font-bold hover:bg-[#004d34] transition-all disabled:opacity-60 flex justify-center items-center gap-2">
              <i v-if="reviewForm.processing" class="fa-solid fa-spinner fa-spin"></i> Kirim Ulasan
            </button>
          </form>
        </div>
      </div>

      <!-- QR Modal -->
      <div v-if="qrModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-gray-900">QR Code Pemesanan</h3>
            <button @click="qrModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
          </div>
          <p class="text-sm text-gray-500 mb-6">Tunjukkan QR Code ini kepada admin venue untuk check-in.</p>
          
          <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-center mb-4">
            <img :src="route('booking.qr', bookingToQR?.booking_code)" alt="QR Code" class="w-48 h-48" />
          </div>
          
          <p class="text-xs font-bold text-[#006241] uppercase tracking-widest">{{ bookingToQR?.booking_code }}</p>
        </div>
      </div>




    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ─── Booking Status Badge (status internal: active, pending, completed, cancelled)
const StatusBadge = {
  props: ['status'],
  template: `<span :class="['text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide', map[status] ?? 'bg-gray-100 text-gray-500']">{{ labels[status] ?? status }}</span>`,
  setup() {
    return {
      map: {
        active:    'bg-green-100 text-green-700',
        pending:   'bg-amber-100 text-amber-700',
        completed: 'bg-blue-100 text-blue-700',
        cancelled: 'bg-red-100 text-red-500',
      },
      labels: {
        active:    '✓ Aktif',
        pending:   '⏳ Menunggu',
        completed: '✓ Selesai',
        cancelled: '✕ Dibatalkan',
      },
    }
  }
}

// ─── Midtrans Payment Status Badge
// settlement = lunas, pending = belum bayar, expire = expired, cancel = dibatalkan midtrans
const PaymentBadge = {
  props: ['status'],
  template: `<span :class="['text-[10px] px-2 py-0.5 rounded-full font-bold tracking-wide', map[status] ?? 'bg-gray-100 text-gray-400']">{{ labels[status] ?? status }}</span>`,
  setup() {
    return {
      map: {
        settlement: 'bg-emerald-100 text-emerald-700',
        capture:    'bg-emerald-100 text-emerald-700',
        pending:    'bg-yellow-100 text-yellow-700',
        deny:       'bg-red-100 text-red-600',
        cancel:     'bg-red-100 text-red-600',
        expire:     'bg-gray-100 text-gray-500',
        refund:     'bg-purple-100 text-purple-600',
      },
      labels: {
        settlement: '💳 Lunas',
        capture:    '💳 Lunas',
        pending:    '⏳ Pending',
        deny:       '✕ Ditolak',
        cancel:     '✕ Dibatalkan',
        expire:     '⌛ Kadaluarsa',
        refund:     '↩ Dikembalikan',
      },
    }
  }
}

// ─── Props & State ────────────────────────────────────────
const props = defineProps({
  bookings: {
    type: [Array, Object],
    default: () => []
  }
})
const activeTab = ref('all')

console.log('Props Bookings:', props.bookings);

// Support both paginated (has .data) and plain array
const bookingItems = computed(() => {
  if (Array.isArray(props.bookings)) return props.bookings;
  if (props.bookings?.data) return props.bookings.data;
  if (props.bookings && typeof props.bookings === 'object') return Object.values(props.bookings);
  return [];
})

const tabs = [
  { value: 'all',       label: 'Semua' },
  { value: 'active',    label: 'Aktif' },
  { value: 'pending',   label: 'Pending' },
  { value: 'completed', label: 'Selesai' },
  { value: 'cancelled', label: 'Dibatalkan' },
]

const filteredBookings = computed(() =>
  activeTab.value === 'all'
    ? bookingItems.value
    : bookingItems.value.filter(b => b.status === activeTab.value)
)

const countByStatus = computed(() => {
  const c = {}
  bookingItems.value.forEach(b => { c[b.status] = (c[b.status] ?? 0) + 1 })
  return c
})



// ─── Cancel ───────────────────────────────────────────────
const qrModalOpen = ref(false)
const bookingToQR = ref(null)

const openQR = (booking) => {
  bookingToQR.value = booking
  qrModalOpen.value = true
}

const cancelModalOpen = ref(false)
const bookingToCancel = ref(null)
const cancelProcessing = ref(false)

const confirmCancel = (booking) => {
  bookingToCancel.value = booking
  cancelModalOpen.value = true
}

const proceedCancel = () => {
  if (bookingToCancel.value) {
    cancelProcessing.value = true
    router.post(route('booking.cancel', bookingToCancel.value.id), {}, {
      onSuccess: () => {
        cancelModalOpen.value = false
        bookingToCancel.value = null
        cancelProcessing.value = false
      },
      onError: () => {
        cancelProcessing.value = false
      }
    })
  }
}

const cancelCancel = () => {
  cancelModalOpen.value = false
  bookingToCancel.value = null
}

// ─── Review ───────────────────────────────────────────────
import { useForm } from '@inertiajs/vue3'

const reviewModalOpen = ref(false)
const bookingToReview = ref(null)
const reviewForm = useForm({
  rating: 0,
  comment: '',
})

const openReview = (booking) => {
  bookingToReview.value = booking
  reviewForm.rating = 5
  reviewForm.comment = ''
  reviewModalOpen.value = true
}

const submitReview = () => {
  if (!bookingToReview.value) return
  
  reviewForm.transform((data) => ({
    ...data,
    booking_id: bookingToReview.value.id,
  })).post(route('reviews.store'), {
    onSuccess: () => {
      reviewModalOpen.value = false
      bookingToReview.value = null
    }
  })
}


const repay = (booking) => {
  router.get(route('booking.repay', booking.booking_code))
}
</script>
