<template>
  <Head title="Notifikasi" />
  <DashboardLayout>
    <div class="max-w-screen-md mx-auto px-4 sm:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Notifikasi</h1>
        <button v-if="hasUnread" @click="markAll"
          class="text-sm text-[#006241] font-semibold hover:underline">
          Tandai semua dibaca
        </button>
      </div>

      <!-- List -->
      <div v-if="notifications.length" class="space-y-3">
        <div v-for="n in notifications" :key="n.id"
          @click="markOne(n)"
          :class="['bg-white rounded-2xl p-4 flex gap-4 cursor-pointer transition-all border-2', !n.is_read ? 'border-[#006241]/20 bg-[#f0faf5]' : 'border-transparent hover:border-gray-100']">
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0', iconBg[n.type]]">
            <i :class="[iconMap[n.type], 'text-sm']"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <p class="font-bold text-gray-900 text-sm">{{ n.title }}</p>
              <span v-if="!n.is_read" class="w-2 h-2 bg-[#006241] rounded-full flex-shrink-0 mt-1.5"></span>
            </div>
            <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">{{ n.message }}</p>
            <p class="text-xs text-gray-300 mt-2">{{ n.created_at }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-20">
        <i class="fa-regular fa-bell text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700">Tidak ada notifikasi</h3>
      </div>

      <!-- Modal Box -->
      <div v-if="selectedNotification" class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4" @click.self="selectedNotification = null">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
          <button @click="selectedNotification = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-lg">
            <i class="fa-solid fa-xmark"></i>
          </button>
          
          <div class="flex items-center gap-3 mb-4">
            <div :class="['w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0', iconBg[selectedNotification.type]]">
              <i :class="[iconMap[selectedNotification.type], 'text-lg']"></i>
            </div>
            <div>
              <p class="font-extrabold text-gray-900 text-lg leading-tight">{{ selectedNotification.title }}</p>
              <p class="text-xs text-gray-300 mt-1">{{ selectedNotification.created_at }}</p>
            </div>
          </div>
          
          <div class="border-t border-gray-100 pt-4 mb-6">
            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ selectedNotification.message }}</p>
          </div>
          
          <button @click="selectedNotification = null" class="w-full bg-[#006241] text-white py-3 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all cursor-pointer">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'

const props = defineProps({
  notifications: Array,
  unread_count: Number,
})

const notifications = ref(props.notifications)
const selectedNotification = ref(null)

const iconMap = {
  booking: 'fa-solid fa-calendar-check text-[#006241]',
  payment: 'fa-solid fa-credit-card text-blue-600',
  promo:   'fa-solid fa-tag text-amber-600',
  system:  'fa-solid fa-bell text-gray-600',
}
const iconBg = {
  booking: 'bg-[#f0faf5]',
  payment: 'bg-blue-50',
  promo:   'bg-amber-50',
  system:  'bg-gray-100',
}

const hasUnread = computed(() => notifications.value.some(n => !n.is_read))

const markOne = async (n) => {
  selectedNotification.value = n
  if (n.is_read) return
  n.is_read = true
  await axios.post(route('notifications.read'), { id: n.id })
}

const markAll = async () => {
  notifications.value.forEach(n => n.is_read = true)
  await axios.post(route('notifications.read'))
}
</script>
