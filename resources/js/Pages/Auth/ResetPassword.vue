<template>
  <Head title="Reset Kata Sandi" />
  <div class="min-h-screen flex items-center justify-center bg-[#f2f0eb] px-4" style="font-family: 'Manrope', sans-serif;">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <Link :href="route('home')" class="text-[28px] font-extrabold tracking-[-0.03em] text-[#006241]">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-extrabold text-gray-900 mb-1">Reset Kata Sandi</h1>
        <p class="text-gray-400 text-sm mb-6">Masukkan kata sandi baru untuk akun Anda.</p>

        <div v-if="$page.props.errors.email" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4">
          {{ $page.props.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Email (Readonly) -->
          <div class="relative">
            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.email" type="email" readonly
              class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 focus:outline-none cursor-not-allowed" />
          </div>

          <!-- Password Baru -->
          <div>
            <div class="relative">
              <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Kata Sandi Baru (Min. 8 karakter)" required
                class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                <i :class="['fa-solid text-sm', showPassword ? 'fa-eye-slash' : 'fa-eye']"></i>
              </button>
            </div>
            <div v-if="$page.props.errors.password" class="text-red-500 text-xs mt-1">{{ $page.props.errors.password }}</div>
          </div>

          <!-- Konfirmasi Password -->
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" placeholder="Ulangi Kata Sandi Baru" required
              class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
          </div>

          <button type="submit" :disabled="form.processing"
            class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all disabled:opacity-60 flex items-center justify-center gap-2 mt-6">
            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin"></i>
            Simpan Kata Sandi Baru
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  email: String,
  token: String,
})

const showPassword = ref(false)

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>
