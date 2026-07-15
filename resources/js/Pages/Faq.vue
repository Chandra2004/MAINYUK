<template>
  <Head title="FAQ" />
  <MainLayout>
    <!-- Hero -->
    <div class="bg-[#1E3932] pt-32 pb-16 text-center px-4">
      <h1 class="text-4xl font-extrabold text-white mb-3">Pertanyaan yang Sering Diajukan</h1>
      <p class="text-white/60 text-lg max-w-xl mx-auto">Temukan jawaban atas pertanyaanmu tentang MainYuk.</p>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-16">
      <div v-for="(cat, ci) in faqs" :key="ci" class="mb-10">
        <h2 class="text-xs font-bold uppercase tracking-widest text-[#006241] mb-4">{{ cat.category }}</h2>
        <div class="space-y-2">
          <div v-for="(faq, i) in cat.items" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm">
            <button @click="toggle(ci, i)"
              class="w-full flex items-center justify-between text-left px-5 py-4 cursor-pointer bg-transparent border-0">
              <span class="font-semibold text-gray-900 text-sm">{{ faq.q }}</span>
              <i :class="['fa-solid fa-chevron-down text-[#006241] transition-transform duration-200 text-xs flex-shrink-0 ml-4', isOpen(ci, i) ? 'rotate-180' : '']"></i>
            </button>
            <div v-if="isOpen(ci, i)" class="px-5 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-50">
              {{ faq.a }}
            </div>
          </div>
        </div>
      </div>

      <!-- Contact CTA -->
      <div class="bg-[#1E3932] rounded-2xl p-8 text-center">
        <h3 class="text-xl font-bold text-white mb-2">Masih ada pertanyaan?</h3>
        <p class="text-white/60 text-sm mb-5">Tim kami siap membantu Anda 24/7.</p>
        <a href="mailto:support@mainyuk.id"
          class="inline-block bg-[#cba258] text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-[#b8903f] transition-all">
          Hubungi Support
        </a>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const opened = ref({})
const toggle = (ci, i) => { const k = `${ci}-${i}`; opened.value[k] = !opened.value[k] }
const isOpen = (ci, i) => !!opened.value[`${ci}-${i}`]

const faqs = [
  {
    category: 'Pemesanan & Jadwal',
    items: [
      { q: 'Bagaimana cara pemesanan lapangan?', a: 'Cari lapangan di halaman Dashboard, pilih jadwal yang tersedia, masukkan ke keranjang, lalu selesaikan pembayaran via Midtrans.' },
      { q: 'Berapa lama sebelum bisa memesan?', a: 'Anda bisa memesan mulai H-30 hingga H-1 sebelum jadwal bermain.' },
      { q: 'Bisakah saya membatalkan pemesanan?', a: 'Ya, pembatalan bisa dilakukan maksimal 24 jam sebelum jadwal bermain melalui halaman Riwayat.' },
    ]
  },
  {
    category: 'Pembayaran',
    items: [
      { q: 'Metode pembayaran apa saja yang tersedia?', a: 'Kami mendukung semua metode yang tersedia di Midtrans: transfer bank, GoPay, OVO, Dana, QRIS, kartu kredit/debit, dan Alfamart/Indomaret.' },
      { q: 'Apakah ada opsi cicilan atau DP?', a: 'Ya! Anda bisa memilih bayar penuh (Lunas), DP 50%, atau DP 30% saat melakukan pembayaran.' },
      { q: 'Apakah data kartu kredit saya aman?', a: 'Sangat aman. Seluruh transaksi diproses oleh Midtrans yang telah berstandar PCI-DSS Level 1. Kami tidak pernah menyimpan data kartu Anda.' },
      { q: 'Di mana saya bisa mengunduh invoice?', a: 'Invoice tersedia di halaman Riwayat Pemesanan — klik tombol "Invoice" pada setiap pemesanan.' },
    ]
  },
  {
    category: 'Akun',
    items: [
      { q: 'Apakah mendaftar gratis?', a: 'Ya, pendaftaran 100% gratis dan Anda mendapatkan diskon 20% untuk pemesanan pertama!' },
      { q: 'Saya lupa kata sandi, bagaimana cara mereset?', a: 'Klik "Lupa Kata Sandi?" di halaman login, masukkan email, dan kami akan mengirimkan link reset via email.' },
      { q: 'Bisakah saya mengubah data profil?', a: 'Ya, pergi ke halaman Profil dan edit nama, nomor telepon, atau ubah kata sandi.' },
    ]
  },
]
</script>
