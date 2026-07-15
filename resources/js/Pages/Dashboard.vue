<template>
  <Head title="Cari Lapangan" />
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Search Bar -->
      <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
          <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="search"
            @input="debouncedSearch"
            type="text"
            placeholder="Cari lapangan, kota..."
            class="w-full pl-11 pr-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15 transition-all"
          />
        </div>
        <select v-model="filters.sport" @change="doSearch"
          class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] cursor-pointer">
          <option value="">Semua Olahraga</option>
          <option v-for="s in sports" :key="s.value" :value="s.value">{{ s.label }}</option>
        </select>
        <select v-model="filters.sort" @change="doSearch"
          class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] cursor-pointer">
          <option value="">Filter by</option>
          <option value="rating">Rating Tertinggi</option>
          <option value="price-low">Harga Terendah</option>
          <option value="price-high">Harga Tertinggi</option>
          <option value="name">Nama A-Z</option>
        </select>
      </div>

      <!-- Sport Tabs -->
      <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-hide">
        <button v-for="tab in allTabs" :key="tab.value"
          @click="filters.sport = tab.value; doSearch()"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border-2 whitespace-nowrap transition-all flex-none',
            filters.sport === tab.value
              ? 'bg-[#006241] text-white border-[#006241]'
              : 'bg-white text-gray-600 border-gray-200 hover:border-[#006241] hover:text-[#006241]'
          ]">
          <i :class="tab.icon"></i> {{ tab.label }}
        </button>
      </div>

      <!-- Results -->
      <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
          Menampilkan <span class="font-bold text-gray-800">{{ courts.length }}</span> lapangan
        </p>
      </div>

      <div v-if="courts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <CourtCard
          v-for="court in courts"
          :key="court.id"
          :court="court"
          :is-fav="localFavorites.includes(court.id)"
          @toggle-fav="toggleFav(court)"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-20">
        <i class="fa-solid fa-binoculars text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Lapangan tidak ditemukan</h3>
        <p class="text-gray-400 text-sm">Coba ubah kata kunci atau filter pencarian</p>
        <button @click="resetFilters" class="mt-4 text-[#006241] font-semibold text-sm hover:underline">Reset Filter</button>
      </div>

    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CourtCard from '@/Components/CourtCard.vue'
import axios from 'axios'

const props = defineProps({
  courts:    { type: Array, default: () => [] },
  favorites: { type: Array, default: () => [] },
  filters:   { type: Object, default: () => ({}) },
})

const search  = ref(props.filters.q ?? '')
const filters = reactive({
  sport:     props.filters.sport ?? '',
  sort:      props.filters.sort ?? '',
})

const localFavorites = ref([...props.favorites])

const sports = [
  { value: 'futsal',      label: 'Futsal',       icon: 'fa-solid fa-futbol' },
  { value: 'badminton',   label: 'Badminton',    icon: 'fa-solid fa-shuttle-space' },
  { value: 'basket',      label: 'Basket',       icon: 'fa-solid fa-basketball' },
  { value: 'tenis',       label: 'Tenis',        icon: 'fa-solid fa-table-tennis-paddle-ball' },
  { value: 'padel',       label: 'Padel',        icon: 'fa-solid fa-table-tennis-paddle-ball' },
  { value: 'mini_soccer', label: 'Mini Soccer',  icon: 'fa-solid fa-futbol' },
  { value: 'billiard',    label: 'Billiard',     icon: 'fa-solid fa-circle-play' },
]
const allTabs = [{ value: '', label: 'Semua', icon: 'fa-solid fa-grip' }, ...sports]

const doSearch = () => {
  router.get(route('dashboard'), {
    q:     search.value || undefined,
    sport: filters.sport || undefined,
    sort:  filters.sort || undefined,
  }, { preserveScroll: true, preserveState: true })
}

let timeout = null
const debouncedSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    doSearch()
  }, 500)
}

const resetFilters = () => {
  search.value      = ''
  filters.sport     = ''
  filters.sort      = ''
  filters.min_price = ''
  filters.max_price = ''
  doSearch()
}

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
</script>
