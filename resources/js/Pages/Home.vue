<template>
  <Head title="MainYuk — Sewa Lapangan Olahraga Premium" />
  <MainLayout>

    <!-- ─── HERO ─── -->
    <section id="beranda" class="relative h-screen min-h-[600px] flex items-center">
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=2070&auto=format&fit=crop"
          alt="Arena Olahraga Premium"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-[#1E3932]/70"></div>
      </div>
      <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-10 w-full">
        <h1 class="text-5xl md:text-6xl font-extrabold text-white leading-tight tracking-[-0.02em] mb-4">
          Sewa Lapangan Olahraga<br>
          <span class="text-[#cba258]">Premium & Instan</span>
        </h1>
        <p class="text-white/80 text-lg max-w-xl mb-8 leading-relaxed">
          Temukan fasilitas olahraga terbaik dengan standar tinggi. Lakukan pemesanan jadwal favoritmu dalam hitungan detik, tanpa ribet.
        </p>
        <div class="flex gap-4 flex-wrap">
          <Link :href="route('register')" class="flex items-center gap-2 bg-[#00754A] text-white px-7 py-3.5 rounded-full font-bold text-base hover:bg-[#005a38] transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
            Mulai Main Sekarang
          </Link>
          <button @click="scrollTo('#kategori')" class="border-2 border-white/60 text-white px-7 py-3.5 rounded-full font-bold text-base hover:bg-white/10 transition-all bg-transparent cursor-pointer">
            Jelajahi Lapangan
          </button>
        </div>
      </div>
    </section>

    <!-- ─── PROMO CAROUSEL ─── -->
    <section class="max-w-[1200px] mx-auto px-4 md:px-10 mt-20">
      <div class="relative overflow-hidden rounded-2xl">
        <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${promoIdx * 100}%)` }">
          <div v-for="(promo, i) in promos" :key="i"
            class="flex-shrink-0 w-full flex items-center justify-between gap-4 px-8 py-6 rounded-2xl"
            :style="{ background: promo.bg }"
          >
            <div>
              <h3 class="text-white font-bold text-xl mb-1">{{ promo.title }}</h3>
              <p class="text-white/80 text-sm mb-3">{{ promo.desc }}</p>
              <span class="bg-white/20 text-white text-sm font-bold px-4 py-1.5 rounded-full tracking-widest">{{ promo.code }}</span>
            </div>
            <i :class="promo.icon" class="text-white/20 text-6xl hidden md:block"></i>
          </div>
        </div>
        <!-- Dots -->
        <div class="flex gap-2 justify-center mt-4 pb-1">
          <button v-for="(_, i) in promos" :key="i"
            @click="promoIdx = i"
            :class="['w-2 h-2 rounded-full transition-all', promoIdx === i ? 'bg-[#006241] w-5' : 'bg-gray-300']"
          ></button>
        </div>
      </div>
    </section>

    <!-- ─── KATEGORI ─── -->
    <section id="kategori" class="max-w-[1200px] mx-auto px-4 md:px-10 mt-20">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight mb-2">Kategori <span class="text-[#006241]">Populer</span></h2>
        <p class="text-gray-500">Pilih olahraga favoritmu dan temukan lapangan terbaik.</p>
      </div>

      <!-- Sport Filter Tabs -->
      <div class="flex gap-2.5 flex-wrap mb-8 justify-center">
        <button
          v-for="tab in sportTabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="[
            'flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold border-2 transition-all',
            activeTab === tab.value
              ? 'bg-[#006241] text-white border-[#006241]'
              : 'bg-white text-gray-600 border-gray-200 hover:border-[#006241] hover:text-[#006241]'
          ]"
        >
          <i :class="tab.icon"></i> {{ tab.label }}
        </button>
      </div>

      <!-- Court Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <CourtCard
          v-for="court in filteredCourts"
          :key="court.id"
          :court="court"
          :is-fav="localFavorites.includes(court.id)"
          @toggle-fav="toggleFav(court)"
        />
      </div>
    </section>

    <!-- ─── CARA BOOKING ─── -->
    <section id="cara-booking" class="bg-[#1E3932] mt-20 py-16">
      <div class="max-w-[1200px] mx-auto px-4 md:px-10">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-extrabold text-white mb-2">Cara <span class="text-[#cba258]">Pemesanan</span></h2>
          <p class="text-white/60">Tiga langkah mudah untuk mulai berolahraga.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="step in steps" :key="step.title" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-[#1E3932] rounded-xl flex items-center justify-center shrink-0">
              <i :class="step.icon" class="text-[#cba258] text-2xl"></i>
            </div>
            <div>
              <h3 class="text-white font-bold text-lg mb-2">{{ step.title }}</h3>
              <p class="text-white/60 text-sm leading-relaxed">{{ step.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── REKOMENDASI ─── -->
    <section id="lapangan" class="max-w-[1200px] mx-auto px-4 md:px-10 mt-20 mb-20">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight mb-2">Rekomendasi <span class="text-[#006241]">Lapangan</span></h2>
        <p class="text-gray-500">Pilihan terbaik dengan rating tertinggi dari pengguna kami.</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <CourtCard
          v-for="court in topCourts"
          :key="court.id"
          :court="court"
          :is-fav="localFavorites.includes(court.id)"
          @toggle-fav="toggleFav(court)"
        />
      </div>
    </section>

    <!-- ─── KONTAK ─── -->
    <section class="bg-[#1E3932] py-16">
      <div class="max-w-[1200px] mx-auto px-4 md:px-10">
        <div class="text-center mb-10">
          <h2 class="text-3xl font-extrabold text-white mb-2">Hubungi <span class="text-[#cba258]">Kami</span></h2>
          <p class="text-white/60">Ada pertanyaan? Tim kami siap membantu 24/7.</p>
        </div>
        <form @submit.prevent="submitContact" class="max-w-lg mx-auto flex flex-col gap-4">
          <input v-model="contactForm.name" type="text" placeholder="Nama Lengkap" required
            class="w-full px-5 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:border-[#cba258] transition-colors text-sm">
          <input v-model="contactForm.email" type="email" placeholder="Alamat Email" required
            class="w-full px-5 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:border-[#cba258] transition-colors text-sm">
          <textarea v-model="contactForm.message" placeholder="Pesan Anda" rows="4" required
            class="w-full px-5 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:border-[#cba258] transition-colors text-sm resize-none"></textarea>
          <button type="submit"
            class="bg-[#00754A] text-white py-3.5 rounded-full font-bold text-sm hover:bg-[#005a38] transition-all">
            Kirim Pesan
          </button>
        </form>
      </div>
    </section>

    <!-- Toast -->
    <Transition name="toast">
      <div v-if="toast.show"
        class="fixed bottom-6 right-6 z-50 bg-white rounded-xl shadow-2xl px-5 py-4 flex items-center gap-3 min-w-[260px]">
        <i :class="['fa-solid fa-circle-check text-[#00754A] text-xl', toast.type === 'error' ? '!text-red-600' : '']"></i>
        <span class="text-sm font-semibold text-gray-800">{{ toast.message }}</span>
      </div>
    </Transition>

  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import CourtCard from '@/Components/CourtCard.vue'
import axios from 'axios'

const props = defineProps({
  courts:    { type: Array, default: () => [] },
  favorites: { type: Array, default: () => [] },
  dbPromos:  { type: Array, default: () => [] },
})

const localFavorites = ref([...props.favorites])

const toggleFav = async (court) => {
  const isFav = localFavorites.value.includes(court.id)
  if (isFav) {
    localFavorites.value = localFavorites.value.filter(id => id !== court.id)
  } else {
    localFavorites.value.push(court.id)
  }

  try {
    await axios.post(route('court.favorite', court.id))
  } catch {
    // Revert on error
    if (isFav) localFavorites.value.push(court.id)
    else localFavorites.value = localFavorites.value.filter(id => id !== court.id)
  }
}

const bgs = ['#006241', '#2C5282', '#744210', '#10B981', '#4C1D95']
const icons = ['fa-solid fa-percent', 'fa-solid fa-tags', 'fa-solid fa-ticket', 'fa-solid fa-gift', 'fa-solid fa-star']

const promos = props.dbPromos ? props.dbPromos.map((p, i) => ({
  title: `Promo ${p.code}!`,
  desc: p.description,
  code: p.code,
  bg: bgs[i % bgs.length],
  icon: icons[i % icons.length]
})) : []
const promoIdx = ref(0)
let promoInterval = null

onMounted(() => {
  promoInterval = setInterval(() => { promoIdx.value = (promoIdx.value + 1) % promos.length }, 4000)
})

onUnmounted(() => {
  if (promoInterval) clearInterval(promoInterval)
})

// ─── Sport Tabs ───
const sportTabs = [
  { value: 'all',         label: 'Semua',       icon: 'fa-solid fa-grip' },
  { value: 'futsal',      label: 'Futsal',       icon: 'fa-solid fa-futbol' },
  { value: 'badminton',   label: 'Badminton',    icon: 'fa-solid fa-shuttle-space' },
  { value: 'basket',      label: 'Basket',       icon: 'fa-solid fa-basketball' },
  { value: 'tenis',       label: 'Tenis',        icon: 'fa-solid fa-table-tennis-paddle-ball' },
  { value: 'padel',       label: 'Padel',        icon: 'fa-solid fa-table-tennis-paddle-ball' },
  { value: 'mini_soccer', label: 'Mini Soccer',  icon: 'fa-solid fa-futbol' },
  { value: 'billiard',    label: 'Billiard',     icon: 'fa-solid fa-circle-play' },
]
const activeTab = ref('all')

const filteredCourts = computed(() =>
  activeTab.value === 'all' ? props.courts : props.courts.filter(c => c.sport_type === activeTab.value)
)
const topCourts = computed(() => props.courts.filter(c => c.rating >= 4.8).slice(0, 4))

// ─── Steps ───
const steps = [
  { icon: 'fa-solid fa-magnifying-glass', title: 'Cari Lapangan',  desc: 'Temukan lapangan sesuai lokasi dan jenis olahraga favoritmu.' },
  { icon: 'fa-regular fa-calendar-check', title: 'Pilih Jadwal',   desc: 'Tentukan tanggal, jam, dan durasi bermain sesuai kebutuhanmu.' },
  { icon: 'fa-solid fa-credit-card',       title: 'Bayar & Main!', desc: 'Lakukan pembayaran online aman dan siap bermain.' },
]

// ─── Contact Form ───
const contactForm = useForm({ name: '', email: '', message: '' })
const toast = ref({ show: false, message: '', type: 'success' })

const showToast = (msg, type = 'success') => {
  toast.value = { show: true, message: msg, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

const submitContact = () => {
  contactForm.post(route('contact.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Pesan berhasil terkirim!')
      contactForm.reset()
    },
    onError: () => {
      showToast('Gagal mengirim pesan. Silakan coba lagi.', 'error')
    }
  })
}

// ─── Scroll helper ───
const scrollTo = (selector) => {
  document.querySelector(selector)?.scrollIntoView({ behavior: 'smooth' })
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
