<template>
  <Head title="Masuk — MainYuk" />

  <div class="min-h-screen flex" style="font-family: 'Manrope', sans-serif;">
    <!-- LEFT — Brand Panel -->
    <div class="hidden lg:flex flex-col justify-center px-14 w-[45%] bg-[#1E3932]">
      <Link :href="route('home')" class="text-[32px] font-extrabold tracking-[-0.03em] text-white! mb-8 block">
        Main<span class="text-[#cba258]">Yuk</span>
      </Link>
      <h2 class="text-3xl font-extrabold text-white mb-3 leading-tight">Selamat datang kembali, atlet!</h2>
      <p class="text-white/60 text-sm leading-relaxed mb-10">
        Masuk dan lanjutkan perjalanan olahragamu bersama ribuan pengguna MainYuk.
      </p>
      <div class="flex flex-col gap-4">
        <div v-for="feat in features" :key="feat.text"
          class="flex items-center gap-3 text-white/80 text-sm font-medium">
          <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
            <i :class="feat.icon" class="text-[#cba258]"></i>
          </div>
          {{ feat.text }}
        </div>
      </div>
    </div>

    <!-- RIGHT — Form Panel -->
    <div class="flex-1 flex items-center justify-center bg-[#f2f0eb] px-6">
      <div class="w-full max-w-[420px]">
        <!-- Mobile Logo -->
        <Link :href="route('home')" class="lg:hidden text-[28px] font-extrabold tracking-[-0.03em] text-[#006241]! mb-8 block text-center">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>

        <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Masuk</h1>
        <p class="text-gray-500 text-sm mb-6">Masukkan email dan kata sandi Anda.</p>

        <!-- Social Buttons -->
        <div class="mb-5">
          <a :href="route('auth.google')"
            class="w-full flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all cursor-pointer">
            <i class="fa-brands fa-google text-red-500"></i> Masuk dengan Google
          </a>
        </div>

        <!-- Divider -->
        <div class="relative flex items-center gap-3 mb-5">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400 font-medium">atau masuk dengan email</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Flash Success (dari register) -->
        <div v-if="$page.props.flash?.success"
          class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-4 text-sm text-green-700 font-semibold flex items-center gap-2">
          <i class="fa-solid fa-circle-check text-green-500"></i>
          {{ $page.props.flash.success }}
        </div>

        <!-- Error Message -->
        <div v-if="form.errors.email || form.errors.password" class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4 text-sm text-red-600">
          {{ form.errors.email || form.errors.password }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <div class="relative">
            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              v-model="form.email"
              type="email"
              placeholder="Alamat Email"
              required
              autocomplete="email"
              class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all"
            />
          </div>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              v-model="form.password"
              :type="showPass ? 'text' : 'password'"
              placeholder="Kata Sandi"
              required
              autocomplete="current-password"
              class="w-full pl-11 pr-11 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all"
            />
            <button type="button" @click="showPass = !showPass"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer">
              <i :class="showPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
            </button>
          </div>

          <div class="flex justify-between items-center">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
              <input v-model="form.remember" type="checkbox" class="accent-[#006241]"> Ingat saya
            </label>
            <Link :href="route('password.request')" class="text-xs text-link">Lupa kata sandi?</Link>
          </div>

          <button type="submit"
            :disabled="form.processing"
            class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all disabled:opacity-60 disabled:cursor-not-allowed mt-1 flex items-center justify-center gap-2">
            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin"></i>
            {{ form.processing ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-5">
          Belum punya akun?
          <Link :href="route('register')" class="text-link">Daftar gratis</Link>
        </p>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <Transition name="toast">
    <div v-if="toast.show"
      class="fixed bottom-6 right-6 z-50 bg-white rounded-xl shadow-2xl px-5 py-4 flex items-center gap-3 min-w-[260px]">
      <i class="fa-solid fa-circle-info text-[#006241] text-xl"></i>
      <span class="text-sm font-semibold text-gray-800">{{ toast.message }}</span>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const showPass = ref(false)
const toast = ref({ show: false, message: '' })

const features = [
  { icon: 'fa-solid fa-map-location-dot', text: 'Lebih dari 500 lapangan tersedia' },
  { icon: 'fa-solid fa-bolt',             text: 'Pemesanan instan, konfirmasi real-time' },
  { icon: 'fa-solid fa-shield-halved',    text: 'Pembayaran aman & terjamin' },
]

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}

const socialToast = (provider) => {
  toast.value = { show: true, message: `Login ${provider} segera hadir!` }
  setTimeout(() => { toast.value.show = false }, 3000)
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
