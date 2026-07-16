<template>
  <Head title="Profil Saya" />
  <DashboardLayout>
    <div class="max-w-screen-lg mx-auto px-4 sm:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left: User Card -->
        <div>
          <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            
            <!-- Avatar Section -->
            <div class="relative w-24 h-24 mx-auto mb-4 group">
              <img v-if="avatarPreview || user.avatar" 
                :src="avatarPreview || user.avatar" 
                alt="Avatar" 
                class="w-full h-full rounded-full object-cover border-4 border-gray-50" />
              <div v-else 
                class="w-full h-full rounded-full bg-[#1E3932] text-white flex items-center justify-center text-3xl font-extrabold mx-auto">
                {{ user.name[0].toUpperCase() }}
              </div>
              
              <!-- Avatar Upload Overlay -->
              <label class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-all">
                <i class="fa-solid fa-camera text-white text-xl"></i>
                <input type="file" class="hidden" accept="image/*" @change="handleAvatarChange" />
              </label>
            </div>

            <!-- Avatar Upload Button (Shows only if file selected) -->
            <button v-if="avatarFile" @click="uploadAvatar" :disabled="avatarLoading"
              class="mb-4 bg-[#006241] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#004d34] transition-all disabled:opacity-50">
              <i v-if="avatarLoading" class="fa-solid fa-spinner fa-spin mr-1"></i>
              Simpan Foto
            </button>

            <h2 class="font-extrabold text-gray-900">{{ user.name }}</h2>
            <p class="text-gray-400 text-sm mb-1">{{ user.email }}</p>
            <p class="text-gray-300 text-xs">Bergabung {{ user.joined }}</p>

            <div class="grid grid-cols-3 gap-2 mt-5">
              <div v-for="s in statCards" :key="s.label" class="bg-gray-50 rounded-xl p-2.5">
                <p class="text-lg font-extrabold text-[#006241]">{{ s.value }}</p>
                <p class="text-[10px] text-gray-400 leading-tight">{{ s.label }}</p>
              </div>
            </div>
        </div>

        <!-- Membership Status Card -->
        <div class="mt-5">
          <div v-if="user.is_pro && new Date(user.pro_expires_at) > new Date()" class="bg-gradient-to-r from-amber-200 via-yellow-400 to-amber-500 rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-20">
              <i class="fa-solid fa-crown text-8xl text-white"></i>
            </div>
            <div class="relative z-10 text-amber-900">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-lg flex items-center gap-2">
                  <i class="fa-solid fa-crown"></i> MainYuk PRO
                </h3>
                <span class="bg-amber-900/10 px-3 py-1 rounded-full text-xs font-bold">Aktif</span>
              </div>
              <p class="text-sm font-medium mb-1">Nikmati bebas biaya layanan dan ekstra diskon 10% di setiap pemesanan!</p>
              <div class="flex items-center justify-between mt-4">
                <p class="text-xs opacity-80 font-semibold">Berlaku sampai {{ new Date(user.pro_expires_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) }}</p>
                <button @click="showUnsubscribeModal = true"
                  class="bg-white/20 hover:bg-white/30 transition-all text-white px-3 py-1.5 rounded-lg text-xs font-bold border border-white/30 backdrop-blur-sm cursor-pointer">
                  Berhenti Langganan
                </button>
              </div>
            </div>
          </div>
          
          <div v-else class="bg-white rounded-2xl shadow-sm p-6 border-2 border-amber-200">
            <div class="flex flex-col gap-4">
              <div>
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-1">
                  <i class="fa-solid fa-crown text-amber-500"></i> MainYuk PRO
                </h3>
                <p class="text-xs text-gray-500 font-medium">Bebas biaya layanan & ekstra diskon 10% setiap booking!</p>
              </div>
              <button @click="subscribePro" :disabled="subscribeLoading"
                class="w-full bg-amber-500 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-amber-600 transition-all disabled:opacity-50 text-center">
                <i v-if="subscribeLoading" class="fa-solid fa-spinner fa-spin mr-1"></i>
                <span v-else>Langganan Rp 50.000 / bln</span>
              </button>
            </div>
            <p v-if="subscribeError" class="text-red-500 text-xs mt-3 font-semibold">{{ subscribeError }}</p>
          </div>
        </div>

      </div>

      <!-- Right: Edit Forms -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Flash Messages -->
          <div v-if="$page.props.flash?.success"
            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ $page.props.flash.success }}
          </div>
          <div v-if="$page.props.flash?.error"
            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $page.props.flash.error }}
          </div>

          <!-- Profile Form -->
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-5">Informasi Profil</h3>
            <form @submit.prevent="updateProfile" class="space-y-4">
              <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Nama Lengkap</label>
                <input v-model="profileForm.name" type="text" required
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
                <p v-if="$page.props.errors.name" class="text-red-500 text-xs mt-1 font-semibold">{{ $page.props.errors.name }}</p>
              </div>
              <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Nomor Telepon</label>
                <input v-model="profileForm.phone" type="tel"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
                <p v-if="$page.props.errors.phone" class="text-red-500 text-xs mt-1 font-semibold">{{ $page.props.errors.phone }}</p>
              </div>
              <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Username</label>
                <input v-model="profileForm.username" type="text"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
                <p v-if="$page.props.errors.username" class="text-red-500 text-xs mt-1 font-semibold">{{ $page.props.errors.username }}</p>
              </div>
              <button type="submit" :disabled="profileLoading"
                class="w-full bg-[#006241] text-white py-3 rounded-xl font-bold hover:bg-[#004d34] transition-all text-sm disabled:opacity-60 flex items-center justify-center gap-2">
                <i v-if="profileLoading" class="fa-solid fa-spinner fa-spin"></i>
                Simpan Perubahan
              </button>
            </form>
          </div>

          <!-- Password Form -->
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-5">Ubah Kata Sandi</h3>
            <form @submit.prevent="updatePassword" class="space-y-4">
              <div class="relative">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Kata Sandi Lama</label>
                <input v-model="passForm.current_password" :type="showPass ? 'text' : 'password'"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241]" />
                <p v-if="$page.props.errors.current_password" class="text-red-500 text-xs mt-1 font-semibold">{{ $page.props.errors.current_password }}</p>
              </div>
              <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Kata Sandi Baru</label>
                <input v-model="passForm.password" :type="showPass ? 'text' : 'password'"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241]" />
                <p v-if="$page.props.errors.password" class="text-red-500 text-xs mt-1 font-semibold">{{ $page.props.errors.password }}</p>
              </div>
              <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1.5 block">Konfirmasi Sandi Baru</label>
                <input v-model="passForm.password_confirmation" :type="showPass ? 'text' : 'password'"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241]" />
              </div>
              <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                <input type="checkbox" v-model="showPass" class="accent-[#006241]" /> Tampilkan kata sandi
              </label>
              <button type="submit" :disabled="passLoading"
                class="w-full bg-gray-800 text-white py-3 rounded-xl font-bold hover:bg-gray-700 transition-all text-sm disabled:opacity-60 flex items-center justify-center gap-2">
                <i v-if="passLoading" class="fa-solid fa-spinner fa-spin"></i>
                Ubah Kata Sandi
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Unsubscribe Modal -->
    <div v-if="showUnsubscribeModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="showUnsubscribeModal = false"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl animate-in fade-in zoom-in duration-200">
        <!-- Close Button -->
        <button @click="showUnsubscribeModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
          <i class="fa-solid fa-times"></i>
        </button>

        <div class="text-center mb-6">
          <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-face-frown-open text-amber-500 text-4xl"></i>
          </div>
          <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Yakin Ingin Berhenti?</h3>
          <p class="text-gray-500 text-sm leading-relaxed">
            Wah, sayang sekali! Jika Anda berhenti berlangganan MainYuk PRO, Anda akan kehilangan <strong>Bebas Biaya Layanan</strong> dan <strong>Ekstra Diskon 10%</strong> untuk setiap pemesanan.
          </p>
        </div>

        <div class="flex flex-col gap-3">
          <button @click="showUnsubscribeModal = false" class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold hover:bg-[#004d34] transition-all">
            Tetap Berlangganan (Direkomendasikan)
          </button>
          <Link :href="route('pro.unsubscribe')" method="delete" as="button" @click="showUnsubscribeModal = false" class="w-full bg-red-50 text-red-600 py-3.5 rounded-xl font-bold hover:bg-red-100 transition-all">
            Ya, Berhenti Langganan
          </Link>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

import axios from 'axios'

const props = defineProps({ user: Object, stats: Object })

// Form States
const profileForm   = ref({ name: props.user.name, phone: props.user.phone, username: props.user.username })
const passForm      = ref({ current_password: '', password: '', password_confirmation: '' })

// Modal States
const showUnsubscribeModal = ref(false)

// Loading States
const profileLoading = ref(false)
const passLoading   = ref(false)
const showPass      = ref(false)

const subscribeLoading = ref(false)
const subscribeError   = ref('')

const subscribePro = () => {
  subscribeLoading.value = true
  router.post(route('pro.subscribe'), {}, {
    onError: () => {
      subscribeLoading.value = false
      subscribeError.value = 'Gagal memproses pembayaran. Silakan coba lagi.'
    },
    onFinish: () => {
      subscribeLoading.value = false
    }
  })
}

// Avatar Upload States
const avatarFile    = ref(null)
const avatarPreview = ref(null)
const avatarLoading = ref(false)

const statistics = computed(() => [
  { label: 'Total Pemesanan', value: props.stats.total_bookings },
  { label: 'Aktif',         value: props.stats.active_bookings },
  { label: 'Selesai',       value: props.stats.completed },
])

const updateProfile = () => {
  profileLoading.value = true
  router.put(route('profile.update'), profileForm.value, {
    onFinish: () => { profileLoading.value = false }
  })
}

const updatePassword = () => {
  passLoading.value = true
  router.put(route('profile.password'), passForm.value, {
    onSuccess: () => { passForm.value = { current_password: '', password: '', password_confirmation: '' } },
    onFinish: () => { passLoading.value = false },
  })
}

// Handle Avatar selection
const handleAvatarChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  avatarFile.value = file
  
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Upload Avatar to backend
const uploadAvatar = () => {
  if (!avatarFile.value) return

  avatarLoading.value = true
  const formData = new FormData()
  formData.append('avatar', avatarFile.value)

  router.post(route('profile.avatar'), formData, {
    onSuccess: () => {
      avatarFile.value = null
      avatarPreview.value = null
    },
    onFinish: () => { avatarLoading.value = false },
  })
}
</script>
