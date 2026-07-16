<template>
  <Head title="Lupa Kata Sandi" />
  <div class="min-h-screen flex items-center justify-center bg-[#f2f0eb] px-4" style="font-family: 'Manrope', sans-serif;">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <Link :href="route('home')" class="text-[28px] font-extrabold tracking-[-0.03em] text-[#006241]">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-8">
        <div v-if="!sent">
          <h1 class="text-xl font-extrabold text-gray-900 mb-1">Lupa Kata Sandi?</h1>
          <p class="text-gray-400 text-sm mb-6">Masukkan email Anda dan kami akan mengirimkan link reset.</p>

          <div v-if="$page.props.errors.email" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4">
            {{ $page.props.errors.email }}
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div class="relative">
              <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
              <input v-model="form.email" type="email" placeholder="Alamat Email" required
                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#006241] focus:ring-2 focus:ring-[#006241]/15" />
            </div>
            <button type="submit" :disabled="form.processing"
              class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all disabled:opacity-60 flex items-center justify-center gap-2">
              <i v-if="form.processing" class="fa-solid fa-spinner fa-spin"></i>
              Kirim Link Reset
            </button>
          </form>
        </div>

        <div v-else class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-envelope-circle-check text-green-600 text-2xl"></i>
          </div>
          <h2 class="font-bold text-gray-900 mb-2">Email Terkirim!</h2>
          <p class="text-gray-400 text-sm">Link reset password telah dikirim ke <strong>{{ form.email }}</strong>. Cek inbox Anda.</p>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
          <Link :href="route('login')" class="text-link">
            ← Kembali ke Login
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const form = useForm({ email: '' })
const sent = ref(false)
const page = usePage()

watch(() => page.props.flash?.status, (v) => { if (v) sent.value = true })

const submit = () => {
  form.post(route('password.email'), {
    onSuccess: () => { sent.value = true }
  })
}
</script>
