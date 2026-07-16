<template>
  <Head title="Pilih Jadwal" />
  <DashboardLayout>
    <div class="max-w-screen-lg mx-auto px-4 sm:px-8 py-8">

      <Link :href="route('court.detail', court.id)" class="back-link mb-6">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail
      </Link>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Schedule Picker -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-sm p-6 mb-5">
            <h1 class="text-xl font-extrabold text-gray-900 mb-1">Pilih Jadwal</h1>
            <p class="text-gray-400 text-sm mb-5">{{ court.name }} · {{ court.city }}</p>

            <!-- Date -->
            <label class="block mb-4">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">Tanggal</span>
              <input v-model="selectedDate" type="date" :min="today"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15"
                @change="updateAvailability" />
            </label>

            <!-- Weather Banner -->
            <div v-if="weatherInfo" class="mb-5 p-4 rounded-xl border flex items-center gap-3 transition-all"
              :class="weatherInfo.is_raining ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200'">
              <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                :class="weatherInfo.is_raining ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-500'">
                <i class="fa-solid text-lg" :class="weatherInfo.is_raining ? 'fa-cloud-showers-heavy' : 'fa-sun'"></i>
              </div>
              <div>
                <p class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
                  Prakiraan Cuaca: {{ weatherInfo.condition }}
                  <span class="text-[10px] bg-white px-2 py-0.5 rounded-full font-bold shadow-sm"
                    :class="weatherInfo.is_raining ? 'text-blue-600' : 'text-orange-500'">
                    {{ weatherInfo.rain_probability }}% Hujan
                  </span>
                </p>
                <p class="text-xs font-semibold mt-0.5" :class="weatherInfo.is_raining ? 'text-blue-700' : 'text-orange-700'">
                  {{ weatherInfo.is_raining 
                    ? '🌧️ Berpotensi hujan. Fitur Rain-Check aktif! Anda dapat reschedule gratis maksimal H-1 jika lapangan tidak bisa digunakan.' 
                    : '☀️ Cuaca cerah, waktu yang tepat untuk berolahraga!' }}
                </p>
              </div>
            </div>

            <!-- Dynamic Pricing Info -->
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-tags text-sm"></i>
              </div>
              <div>
                <p class="text-sm font-extrabold text-emerald-900">Informasi Tarif Dinamis</p>
                <p class="text-xs font-semibold text-emerald-700 mt-0.5 leading-relaxed">
                  Terdapat <span class="font-bold">tambahan biaya 20%</span> dari harga normal untuk jam sibuk (Senin-Jumat, 17:00-23:00) dan akhir pekan (Sabtu-Minggu sepanjang hari).
                </p>
              </div>
            </div>

            <!-- Sub-lapangan -->
            <div v-if="court.courts_detail?.length" class="mb-4">
              <span class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">Lapangan</span>
              <div class="flex flex-wrap gap-2">
                <button v-for="c in court.courts_detail" :key="c.id"
                  @click="selectedCourtDetail = c.id"
                  :class="['px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all', selectedCourtDetail === c.id ? 'bg-[#006241] text-white border-[#006241]' : 'border-gray-200 text-gray-600 hover:border-[#006241]']">
                  {{ c.name }}
                </button>
              </div>
            </div>

            <!-- Time Slots -->
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2 block">Pilih Sesi (klik untuk memilih)</span>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
              <button v-for="slot in timeSlots" :key="slot.time"
                :disabled="slot.booked"
                @click="toggleSlot(slot)"
                :class="[
                  'py-2 rounded-xl transition-all border-2 flex flex-col items-center justify-center gap-0.5',
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
            <div class="flex gap-4 mt-4 text-xs text-gray-400">
              <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-[#006241] rounded"></span> Dipilih</span>
              <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-200 rounded"></span> Sudah Dipesan</span>
              <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-gray-200 rounded"></span> Tersedia</span>
            </div>

            <!-- Total Estimasi Mobile/Desktop -->
            <div v-if="selectedSlots.length" class="mt-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-bold text-emerald-800 uppercase">Estimasi Harga</span>
                <span class="text-base font-extrabold text-[#006241]">Rp {{ totalPrice.toLocaleString('id-ID') }}</span>
              </div>
              <p class="text-[11px] text-emerald-600 font-semibold">{{ selectedSlots.length }} Jam Terpilih ({{ timeRange }})</p>
            </div>

            <!-- Pesan error slot tidak berurutan -->
            <p v-if="slotError" class="mt-3 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
              {{ slotError }}
            </p>
          </div>
        </div>

        <!-- Right: Summary -->
        <div>
          <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
            <h3 class="font-bold text-gray-900 mb-4">Ringkasan</h3>
            <div class="flex items-center gap-3 mb-4">
              <img :src="court.main_image" :alt="court.name" class="w-14 h-14 rounded-xl object-cover" />
              <div>
                <p class="text-sm font-bold text-gray-900">{{ court.name }}</p>
                <p class="text-xs text-gray-400">{{ court.city }}</p>
              </div>
            </div>
            <div class="text-sm text-gray-600 space-y-2 mb-5">
              <div class="flex justify-between">
                <span>Tanggal</span>
                <span class="font-semibold text-gray-900">{{ selectedDate || '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span>Waktu</span>
                <span class="font-semibold text-gray-900">{{ timeRange || '—' }}</span>
              </div>

              <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                <span>Total</span>
                <span class="text-[#006241]">Rp {{ totalPrice.toLocaleString('id-ID') }}</span>
              </div>
            </div>

            <button @click="addToCart"
              :disabled="selectedSlots.length === 0 || !selectedDate || isLoading"
              class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
              <i v-if="isLoading" class="fa-solid fa-spinner fa-spin"></i>
              <i v-else class="fa-solid fa-cart-plus"></i>
              {{ isLoading ? 'Memproses...' : 'Lanjut ke Keranjang' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useTimeSlots, areConsecutive } from '@/Composables/useTimeSlots.js'

const props = defineProps({
  court:       Object,
  bookedSlots: Array,
})

const today               = new Date().toISOString().split('T')[0]
const selectedDate        = ref(today)
const selectedSlots       = ref([])
const selectedCourtDetail = ref(props.court.courts_detail?.[0]?.id ?? null)
const slotError           = ref('')

// Gunakan composable bersama (tidak duplikasi lagi)
const { timeSlots } = useTimeSlots(selectedDate, selectedCourtDetail, props.court, props.bookedSlots)

const toggleSlot = (slot) => {
  if (slot.booked) return

  const idx = selectedSlots.value.indexOf(slot.time)
  let newSlots

  if (idx > -1) {
    newSlots = selectedSlots.value.filter(s => s !== slot.time)
  } else {
    newSlots = [...selectedSlots.value, slot.time]
  }

  // Validasi slot harus berurutan sebelum ditambahkan
  if (newSlots.length > 1 && !areConsecutive(newSlots)) {
    slotError.value = '⚠️ Slot jam harus berurutan. Pilih jam yang berdampingan.'
    return
  }

  slotError.value = ''
  selectedSlots.value = newSlots
}

const timeRange = computed(() => {
  if (!selectedSlots.value.length) return null
  const sorted = [...selectedSlots.value].sort()
  const last = timeSlots.value.find(s => s.time === sorted[sorted.length - 1])
  return `${sorted[0]} – ${last?.end}`
})

const totalPrice = computed(() => {
  let total = 0
  for (const time of selectedSlots.value) {
    const slotObj = timeSlots.value.find(s => s.time === time)
    if (slotObj) {
      total += slotObj.price
    }
  }
  return total
})

const updateAvailability = () => {
  selectedSlots.value = []
  slotError.value = ''
  fetchWeather()
}

// Weather fetching
import axios from 'axios'
import { onMounted } from 'vue'

const weatherInfo = ref(null)

const fetchWeather = async () => {
  if (!props.court.latitude || !props.court.longitude) return
  
  try {
    const res = await axios.get(route('booking.weather', props.court.id), {
      params: { date: selectedDate.value }
    })
    weatherInfo.value = res.data
  } catch {
    weatherInfo.value = null
  }
}

onMounted(() => {
  fetchWeather()
})

const isLoading = ref(false)

const addToCart = () => {
  if (!areConsecutive(selectedSlots.value)) {
    slotError.value = '⚠️ Slot jam harus berurutan.'
    return
  }
  const sorted = [...selectedSlots.value].sort()
  const last   = timeSlots.value.find(s => s.time === sorted[sorted.length - 1])

  isLoading.value = true
  router.post(route('booking.add-to-cart'), {
    court_id:     props.court.id,
    date:         selectedDate.value,
    time_start:   sorted[0],
    time_end:     last?.end,
    court_detail: selectedCourtDetail.value,
    slots:        sorted,
  }, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>
