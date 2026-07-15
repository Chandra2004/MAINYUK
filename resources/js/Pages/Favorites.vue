<template>
  <Head title="Lapangan Favorit" />
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-2xl font-extrabold text-gray-900 mb-6">
        Lapangan Favorit <span class="text-gray-400 font-normal text-lg">({{ localCourts.length }})</span>
      </h1>

      <div v-if="localCourts.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <CourtCard v-for="c in localCourts" :key="c.id" :court="c" :is-fav="true" @toggle-fav="toggleFav" />
      </div>

      <div v-else class="text-center py-24">
        <i class="fa-regular fa-heart text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Belum ada favorit</h3>
        <p class="text-gray-400 text-sm mb-4">Klik ikon ❤ pada lapangan untuk menyimpannya di sini.</p>
        <Link :href="route('dashboard')"
          class="inline-block bg-[#006241] text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-[#004d34] transition-all">
          Jelajahi Lapangan
        </Link>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CourtCard from '@/Components/CourtCard.vue'
import axios from 'axios'

const props = defineProps({ courts: Array })
const localCourts = ref([...props.courts])

const toggleFav = async (court) => {
  localCourts.value = localCourts.value.filter(c => c.id !== court.id)
  try {
    await axios.post(route('court.favorite', court.id))
  } catch {
    // Revert on error
    localCourts.value.push(court)
  }
}
</script>
