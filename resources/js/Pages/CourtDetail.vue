<template>
  <Head :title="court.name + ' - MainYuk'" />
  <DashboardLayout>
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-8">
      
      <!-- Back -->
      <Link :href="$page.props.auth?.user ? route('dashboard') : route('home')" class="back-link mb-6">
        <i class="fa-solid fa-arrow-left"></i> {{ $page.props.auth?.user ? 'Kembali ke Pencarian' : 'Kembali ke Halaman Utama' }}
      </Link>

      <!-- Gallery Carousel -->
      <div v-if="allImages.length" class="relative mb-8 rounded-2xl overflow-hidden group h-80 md:h-96">
        <img :src="allImages[currentImageIndex]" :alt="court.name"
          class="w-full h-full object-cover transition-all duration-500" />
        
        <!-- Controls -->
        <button v-if="allImages.length > 1" @click="prevImage" 
          class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-lg text-gray-800 hover:bg-white transition-all opacity-0 group-hover:opacity-100">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button v-if="allImages.length > 1" @click="nextImage" 
          class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-lg text-gray-800 hover:bg-white transition-all opacity-0 group-hover:opacity-100">
          <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Dots -->
        <div v-if="allImages.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 bg-black/30 backdrop-blur px-3 py-1.5 rounded-full">
          <button v-for="(_, index) in allImages" :key="index" @click="currentImageIndex = index"
            :class="['w-2 h-2 rounded-full transition-all', currentImageIndex === index ? 'bg-white w-4' : 'bg-white/50 hover:bg-white/80']"></button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left: Info & Reviews -->
        <div class="lg:col-span-2">
          <!-- Header -->
          <div class="flex justify-between items-start flex-wrap gap-4 mb-4">
            <div>
              <h1 class="text-3xl font-extrabold text-gray-900 mb-1">{{ court.name }}</h1>
              <p class="text-gray-500 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-[#006241]"></i>
                {{ court.address }}, {{ court.city }}
              </p>
            </div>
            
            <!-- Share & Fav Buttons -->
            <div class="flex items-center gap-2">
              <div class="flex gap-1">
                <button @click="shareWhatsApp" class="w-10 h-10 rounded-full flex items-center justify-center bg-[#25D366] text-white hover:opacity-90 transition-all">
                  <i class="fa-brands fa-whatsapp text-lg"></i>
                </button>
                <button @click="copyLink" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                  <i class="fa-solid fa-link text-sm"></i>
                </button>
              </div>
              <button @click="toggleFav"
                :class="['w-10 h-10 rounded-full flex items-center justify-center transition-all border-2', isFav ? 'bg-[#006241] border-[#006241] text-white' : 'border-gray-200 text-gray-400 hover:border-[#006241] hover:text-[#006241]']">
                <i class="fa-solid fa-heart text-sm"></i>
              </button>
            </div>
          </div>

          <!-- Rating Summary -->
          <div class="flex items-center gap-3 mb-6">
            <div class="flex gap-0.5 text-amber-400">
              <i v-for="i in 5" :key="i" :class="['fa-star text-sm', i <= Math.round(court.rating) ? 'fa-solid' : 'fa-regular text-gray-300']"></i>
            </div>
            <span class="font-extrabold text-gray-900 text-lg">{{ court.rating }}</span>
            <span class="text-gray-400 text-sm">({{ testimonials.length }} ulasan)</span>
          </div>

          <!-- Tabs -->
          <div class="flex border-b border-gray-200 mb-6 gap-6">
            <button v-for="tab in ['Fasilitas', 'Deskripsi']" :key="tab"
              @click="activeTab = tab"
              :class="['pb-3 text-sm font-bold transition-colors border-b-2', activeTab === tab ? 'text-[#006241] border-[#006241]' : 'text-gray-400 border-transparent hover:text-gray-700']">
              {{ tab }}
            </button>
          </div>

          <!-- Fasilitas -->
          <div v-if="activeTab === 'Fasilitas'" class="flex flex-wrap gap-2 mb-6">
            <span v-for="f in court.facilities" :key="f"
              class="flex items-center gap-1.5 bg-[#f0faf5] text-[#006241] px-3.5 py-1.5 rounded-full text-sm font-bold border border-[#d4e9e2]">
              <i class="fa-solid text-xs" :class="iconMap[f] || 'fa-check'"></i> {{ f }}
            </span>
          </div>

          <!-- Deskripsi -->
          <div v-if="activeTab === 'Deskripsi'" class="text-gray-600 text-sm leading-relaxed mb-6">
            {{ court.description || 'Tidak ada deskripsi tersedia.' }}
          </div>

          <!-- Jam Operasional -->
          <div class="bg-gray-50 rounded-2xl p-4 flex items-center gap-3 border border-gray-100">
            <i class="fa-regular fa-clock text-[#006241] text-xl"></i>
            <div>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Jam Operasional</p>
              <p class="text-sm font-bold text-gray-800">{{ court.open_time?.slice(0,5) }} – {{ court.close_time?.slice(0,5) }} WIB</p>
            </div>
          </div>

          <!-- Rating Breakdown (dari data real) -->
          <h3 class="font-extrabold text-gray-900 text-lg mt-8 mb-4">Ulasan Pengguna</h3>
          <div class="bg-white border border-gray-100 rounded-2xl p-6 flex flex-col md:flex-row gap-6 items-center mb-6">
            <div class="text-center md:border-r md:pr-8 md:border-gray-100 shrink-0">
              <div class="text-4xl font-extrabold text-gray-900">{{ court.rating }}</div>
              <div class="flex gap-0.5 text-amber-400 justify-center mt-1.5 mb-1">
                <i v-for="i in 5" :key="i" :class="['fa-star text-xs', i <= Math.round(court.rating) ? 'fa-solid' : 'fa-regular text-gray-200']"></i>
              </div>
              <span class="text-xs text-gray-400 font-medium">Berdasarkan {{ testimonials.length }} ulasan</span>
            </div>

            <div class="flex-1 w-full space-y-2">
              <div v-for="star in [5,4,3,2,1]" :key="star" class="flex items-center gap-3 text-xs text-gray-500 font-bold">
                <span class="w-6 text-right">{{ star }}★</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-amber-400 rounded-full" :style="{ width: testimonials.length ? `${(ratingCount[star] / testimonials.length) * 100}%` : '0%' }"></div>
                </div>
                <span class="w-6 text-left">{{ ratingCount[star] ?? 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Reviews List (data real dari DB) -->
          <div v-if="testimonials.length" class="space-y-4">
            <div v-for="t in testimonials" :key="t.id" class="border border-gray-100 rounded-2xl p-5 bg-white">
              <div class="flex items-center justify-between gap-4 mb-3">
                <div class="flex items-center gap-3">
                  <!-- Avatar: gambar jika ada, fallback ke initial -->
                  <img v-if="t.avatar" :src="t.avatar" :alt="t.name"
                    class="w-10 h-10 rounded-full object-cover shrink-0" />
                  <div v-else
                    class="w-10 h-10 rounded-full bg-[#006241]/10 text-[#006241] flex items-center justify-center font-extrabold text-sm shrink-0">
                    {{ t.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ t.name }}</h4>
                    <p class="text-[10px] text-gray-400 font-semibold">{{ t.created_at }}</p>
                  </div>
                </div>
                <div class="flex gap-0.5 text-amber-400">
                  <i v-for="i in 5" :key="i" :class="['fa-star text-[10px]', i <= t.rating ? 'fa-solid' : 'fa-regular text-gray-200']"></i>
                </div>
              </div>
              <p class="text-gray-600 text-sm leading-relaxed">{{ t.comment }}</p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-400 text-sm">
            <i class="fa-regular fa-star text-3xl mb-2 block text-gray-200"></i>
            Belum ada ulasan untuk lapangan ini.
          </div>

          <!-- Report Link -->
          <p class="mt-6 text-xs text-gray-400 flex items-center gap-1">
            Informasi tidak sesuai?
            <a href="#" @click.prevent="reportIssue" class="text-[#006241] font-bold hover:underline flex items-center gap-1">
              <i class="fa-solid fa-flag"></i> Laporkan
            </a>
          </p>
        </div>

        <!-- Right: Booking Sidebar (Inline Slot Selection) -->
        <div class="sticky top-24">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-baseline gap-1.5 mb-1">
              <span class="text-3xl font-extrabold text-[#006241]">
                {{ court.formatted_price }}
              </span>
              <span class="text-gray-400 text-sm">/ jam</span>
            </div>
            <div class="border-b border-gray-100 my-4"></div>

            <!-- Sub-lapangan Selector (BOX GRID, bukan list) -->
            <div v-if="courtsList.length" class="mb-5">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Lapangan</p>
              <div class="grid grid-cols-2 gap-2">
                <button v-for="c in courtsList" :key="c.id"
                  @click="selectedCourtDetail = c.id; selectedSlots = []"
                  :class="[
                    'flex flex-col items-start p-3 rounded-xl border-2 cursor-pointer transition-all text-left',
                    selectedCourtDetail === c.id
                      ? 'border-[#006241] bg-[#f0faf5]'
                      : 'border-gray-200 bg-gray-50 hover:border-gray-300'
                  ]">

                  <span class="text-xs font-extrabold text-gray-900 leading-tight">{{ c.name }}</span>
                  <span class="text-[10px] text-[#006241] font-bold mt-0.5">
                    Rp {{ (c.price ?? court.price_per_hour).toLocaleString('id-ID') }}/jam
                  </span>
                </button>
              </div>
            </div>

            <!-- Date Select -->
            <label class="block mb-5">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 block">Pilih Tanggal</span>
              <input v-model="selectedDate" type="date" :min="today"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15 font-semibold text-gray-700" />
            </label>

            <!-- Time Slots Grid -->
            <div class="mb-5">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Jam Tersedia</span>
              <div class="grid grid-cols-3 gap-1.5 max-h-[180px] overflow-y-auto pr-1">
                <button v-for="slot in timeSlots" :key="slot.time"
                  :disabled="slot.booked"
                  @click="toggleSlot(slot.time)"
                  :class="[
                    'py-2 rounded-lg transition-all border-2 flex flex-col items-center justify-center gap-0.5',
                    slot.booked
                      ? 'bg-red-50 text-red-300 border-red-100 cursor-not-allowed line-through'
                      : selectedSlots.includes(slot.time)
                        ? 'bg-[#006241] text-white border-[#006241]'
                        : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-[#006241] hover:text-[#006241]'
                  ]">
                  <span class="text-xs font-bold">{{ slot.time }}</span>
                  <span v-if="!slot.booked" class="text-[9px] font-semibold opacity-90">Rp {{ (slot.price / 1000).toLocaleString('id-ID') }}k</span>
                </button>
              </div>
              
              <!-- Legend -->
              <div class="flex gap-4 mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-[#006241] rounded"></span> Dipilih</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-red-100 rounded"></span> Penuh</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-gray-200 rounded"></span> Tersedia</span>
              </div>

              <!-- Total Estimasi -->
              <div v-if="selectedSlots.length" class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-[10px] font-bold text-emerald-800 uppercase">Estimasi Harga</span>
                  <span class="text-sm font-extrabold text-[#006241]">Rp {{ totalPrice.toLocaleString('id-ID') }}</span>
                </div>
                <p class="text-[10px] text-emerald-600 font-semibold">{{ selectedSlots.length }} Jam Terpilih</p>
              </div>

              <!-- Pesan error slot tidak berurutan -->
              <p v-if="slotError" class="mt-2 text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                {{ slotError }}
              </p>
            </div>

            <!-- Booking Button -->
            <button @click="bookNow"
              :disabled="selectedSlots.length === 0 || !selectedDate"
              class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer shadow-md">
              <i class="fa-regular fa-calendar-check"></i> Pesan Sekarang
            </button>

            <p class="text-center text-[10px] text-gray-400 mt-3 font-semibold">
              <i class="fa-solid fa-shield-halved text-green-500"></i>
              Pembayaran 100% aman via Midtrans
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div v-if="toast.show"
        class="fixed bottom-6 right-6 z-50 bg-white rounded-xl shadow-2xl px-5 py-4 flex items-center gap-3 min-w-[260px]">
        <i :class="['fa-solid text-xl', toast.type === 'success' ? 'fa-circle-check text-[#006241]' : 'fa-circle-exclamation text-red-600']"></i>
        <span class="text-sm font-semibold text-gray-800">{{ toast.message }}</span>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'
import { useTimeSlots } from '@/Composables/useTimeSlots.js'

const props = defineProps({
  court:        Object,
  isFav:        Boolean,
  bookedSlots:  Array,
  testimonials: { type: Array, default: () => [] },
  equipment:    { type: Array, default: () => [] },
})

const allImages = computed(() => {
  if (props.court.images?.length) return props.court.images
  return [props.court.main_image]
})

const currentImageIndex = ref(0)
const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % allImages.value.length
}
const prevImage = () => {
  currentImageIndex.value = currentImageIndex.value === 0 ? allImages.value.length - 1 : currentImageIndex.value - 1
}

const activeTab = ref('Fasilitas')
const isFav               = ref(props.isFav)

const courtsList = computed(() => {
  let list = props.court.courts_detail || [];
  if (list.length === 0) {
    list = [
      { id: 'lap-1', name: 'Lapangan 1' },
      { id: 'lap-2', name: 'Lapangan 2' },
      { id: 'lap-3', name: 'Lapangan 3' },
      { id: 'lap-4', name: 'Lapangan 4' }
    ];
  }
  return list;
});

const selectedCourtDetail = ref(courtsList.value[0]?.id ?? null)

// Date and slot selection state
const today         = new Date().toISOString().split('T')[0]
const selectedDate  = ref(today)
const selectedSlots = ref([])

const totalPrice = computed(() => {
  return timeSlots.value
    .filter(s => selectedSlots.value.includes(s.time))
    .reduce((sum, slot) => sum + slot.price, 0)
})

// ─── Hitung rating distribution dari testimonials real ────
const ratingCount = computed(() => {
  const c = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 }
  props.testimonials.forEach(t => { c[t.rating] = (c[t.rating] ?? 0) + 1 })
  return c
})

// Facility icons mapping
const iconMap = {
  'WiFi': 'fa-wifi', 'Parkir': 'fa-car', 'Toilet': 'fa-restroom',
  'AC': 'fa-snowflake', 'Kantin': 'fa-utensils', 'Ruang Ganti': 'fa-shirt',
  'Tribun': 'fa-users', 'Loker': 'fa-lock', 'Shower': 'fa-shower', 'Pro Shop': 'fa-shop'
}

// Toast notification state
const toast = ref({ show: false, message: '', type: 'success' })
const showToast = (msg, type = 'success') => {
  toast.value = { show: true, message: msg, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

// Share Functions
const shareWhatsApp = () => {
  window.open(`https://wa.me/?text=${encodeURIComponent('Cek lapangan ini di MainYuk! ' + window.location.href)}`)
}
const copyLink = () => {
  navigator.clipboard.writeText(window.location.href)
  showToast('Link berhasil disalin!', 'success')
}
const reportIssue = () => {
  showToast('Laporan berhasil dikirim. Terima kasih!', 'success')
}

// Favorite Action
const toggleFav = async () => {
  try {
    const res = await axios.post(route('court.favorite', props.court.id))
    isFav.value = res.data.isFav
    showToast(res.data.message, 'success')
  } catch {
    showToast('Gagal mengubah favorit', 'error')
  }
}

// Time Slots — menggunakan composable bersama
const { timeSlots } = useTimeSlots(selectedDate, selectedCourtDetail, props.court, props.bookedSlots)

// Toggle slot — BEBAS (tidak harus berurutan sesuai permintaan user)
const toggleSlot = (time) => {
  const idx = selectedSlots.value.indexOf(time)
  if (idx > -1) {
    selectedSlots.value = selectedSlots.value.filter(s => s !== time)
  } else {
    selectedSlots.value = [...selectedSlots.value, time].sort()
  }
}

// Booking Checkout Action
const bookNow = () => {
  if (selectedSlots.value.length === 0) return

  const sorted   = [...selectedSlots.value].sort()
  const lastSlot = timeSlots.value.find(s => s.time === sorted[sorted.length - 1])

  router.post(route('booking.add-to-cart'), {
    court_id:     props.court.id,
    date:         selectedDate.value,
    time_start:   sorted[0],
    time_end:     lastSlot?.end,
    court_detail: selectedCourtDetail.value,
    slots:        sorted,
  })
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
