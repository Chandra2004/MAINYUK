<template>
  <Head title="Daftar Gratis — MainYuk" />

  <div class="min-h-screen flex" style="font-family: 'Manrope', sans-serif;">
    <!-- LEFT — Brand Panel -->
    <div class="hidden lg:flex flex-col justify-center px-14 w-[45%] bg-[#1E3932]">
      <Link :href="route('home')" class="text-[32px] font-extrabold tracking-[-0.03em] text-white! mb-8 block">
        Main<span class="text-[#cba258]">Yuk</span>
      </Link>
      <h2 class="text-3xl font-extrabold text-white mb-3 leading-tight">Mulai perjalanan olahragamu hari ini.</h2>
      <p class="text-white/60 text-sm leading-relaxed mb-10">
        Bergabung gratis dan nikmati kemudahan booking lapangan olahraga premium kapan saja.
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
    <div class="flex-1 flex items-center justify-center bg-[#f2f0eb] px-6 py-10">
      <div class="w-full max-w-[420px]">
        <!-- Mobile Logo -->
        <Link :href="route('home')" class="lg:hidden text-[28px] font-extrabold tracking-[-0.03em] text-[#006241]! mb-8 block text-center">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>

        <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Buat Akun</h1>
        <p class="text-gray-500 text-sm mb-6">Isi data di bawah ini untuk mulai bergabung.</p>

        <!-- Social Buttons -->
        <div class="mb-5">
          <a :href="route('auth.google')"
            class="w-full flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all cursor-pointer">
            <i class="fa-brands fa-google text-red-500"></i> Daftar dengan Google
          </a>
        </div>

        <div class="relative flex items-center gap-3 mb-5">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400 font-medium">atau daftar dengan email</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Errors -->
        <div v-if="Object.keys(form.errors).length" class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4 text-sm text-red-600">
          {{ Object.values(form.errors)[0] }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-3.5">
          <!-- Name -->
          <div class="relative">
            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.name" type="text" placeholder="Nama Lengkap" required autocomplete="name"
              class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all" />
          </div>

          <!-- Email -->
          <div class="relative">
            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.email" type="email" placeholder="Alamat Email" required autocomplete="email"
              class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all" />
          </div>

          <!-- Password -->
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.password" :type="showPass ? 'text' : 'password'" placeholder="Kata Sandi (min. 8 karakter)" required autocomplete="new-password"
              class="w-full pl-11 pr-11 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all" />
            <button type="button" @click="showPass = !showPass"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer">
              <i :class="showPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
            </button>
          </div>

          <!-- Password Strength -->
          <div v-if="form.password" class="flex gap-1 -mt-1">
            <div v-for="i in 4" :key="i"
              :class="['h-1 flex-1 rounded-full transition-all', i <= pwStrength ? pwStrengthColor : 'bg-gray-200']"></div>
          </div>

          <!-- Confirm Password -->
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.password_confirmation" :type="showPass ? 'text' : 'password'" placeholder="Konfirmasi Kata Sandi" required autocomplete="new-password"
              class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/20 transition-all" />
          </div>

          <!-- Terms -->
          <label class="flex items-start gap-2.5 text-sm text-gray-600 cursor-pointer">
            <input v-model="form.terms" type="checkbox" required class="accent-[#006241] mt-0.5">
            Saya menyetujui <a href="#" class="text-link">Syarat & Ketentuan</a>
          </label>

          <button type="submit"
            :disabled="form.processing"
            class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-1">
            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin"></i>
            {{ form.processing ? 'Memproses...' : 'Daftar Gratis' }}
          </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-5">
          Sudah punya akun?
          <Link :href="route('login')" class="text-link">Masuk di sini</Link>
        </p>
      </div>
    </div>
  </div>

  <Transition name="toast">
    <div v-if="toast.show" class="fixed bottom-6 right-6 z-50 bg-white rounded-xl shadow-2xl px-5 py-4 flex items-center gap-3 min-w-[260px]">
      <i class="fa-solid fa-circle-info text-[#006241] text-xl"></i>
      <span class="text-sm font-semibold text-gray-800">{{ toast.message }}</span>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const showPass = ref(false)
const toast = ref({ show: false, message: '' })

const features = [
  { icon: 'fa-solid fa-tag',    text: 'Diskon 20% untuk booking pertama' },
  { icon: 'fa-solid fa-star',   text: 'Akses ke 500+ lapangan pilihan' },
  { icon: 'fa-solid fa-bell',   text: 'Notifikasi jadwal & promo eksklusif' },
]

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}

const pwStrength = computed(() => {
  const p = form.password
  if (!p) return 0
  let s = 0
  if (p.length >= 8) s++
  if (/[A-Z]/.test(p)) s++
  if (/[0-9]/.test(p)) s++
  if (/[^A-Za-z0-9]/.test(p)) s++
  return s
})

const pwStrengthColor = computed(() => {
  const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500']
  return colors[pwStrength.value - 1] || 'bg-red-400'
})

const socialToast = (provider) => {
  toast.value = { show: true, message: `Daftar via ${provider} segera hadir!` }
  setTimeout(() => { toast.value.show = false }, 3000)
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
