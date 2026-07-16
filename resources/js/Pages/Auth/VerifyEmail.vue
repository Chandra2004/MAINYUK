<template>
  <Head title="Verifikasi Email" />
  <div class="min-h-screen flex items-center justify-center bg-[#f2f0eb] px-4" style="font-family: 'Manrope', sans-serif;">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <Link :href="route('home')" class="text-[28px] font-extrabold tracking-[-0.03em] text-[#006241]">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>
      </div>

      <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
        <div class="w-16 h-16 bg-[#006241]/10 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-envelope-open-text text-[#006241] text-2xl"></i>
        </div>
        <h1 class="text-xl font-extrabold text-gray-900 mb-2">Verifikasi Email Anda</h1>
        <p class="text-gray-500 text-sm mb-6 leading-relaxed">
          Terima kasih telah mendaftar! Sebelum mulai, silakan verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan.
        </p>

        <div v-if="verificationLinkSent" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold mb-6 flex items-center gap-2 text-left">
          <i class="fa-solid fa-circle-check"></i> Link verifikasi baru telah dikirim ke email Anda.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <button type="submit" :disabled="form.processing"
            class="w-full bg-[#006241] text-white py-3.5 rounded-xl font-bold text-sm hover:bg-[#004d34] transition-all disabled:opacity-60 flex items-center justify-center gap-2">
            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin"></i>
            Kirim Ulang Email Verifikasi
          </button>
        </form>

        <div class="mt-6 flex justify-between items-center text-sm font-bold">
          <Link :href="route('profile')" class="text-gray-400 hover:text-gray-600 transition-colors">
            Ubah Profil
          </Link>
          <Link :href="route('logout')" method="post" as="button" class="text-red-500 hover:text-red-600 transition-colors">
            Logout
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  status: String,
})

const form = useForm({})

const submit = () => {
  form.post(route('verification.send'))
}

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')
</script>
