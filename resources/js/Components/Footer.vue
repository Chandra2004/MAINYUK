<template>
  <footer class="bg-[#1E3932] text-white">
    <div class="max-w-[1200px] mx-auto px-10 py-14 grid grid-cols-1 md:grid-cols-3 gap-10">
      <!-- Brand -->
      <div>
        <Link :href="route('home')" class="text-[26px] font-extrabold tracking-[-0.03em] text-white">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>
        <p class="mt-3 text-white/70 text-sm leading-relaxed">
          Platform booking lapangan olahraga terpercaya di Indonesia. Lebih dari 500 lapangan tersedia.
        </p>
        <div class="flex gap-4 mt-5">
          <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:border-white transition-all">
            <i class="fa-brands fa-instagram text-sm"></i>
          </a>
          <a href="#" aria-label="Twitter" class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:border-white transition-all">
            <i class="fa-brands fa-twitter text-sm"></i>
          </a>
          <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:border-white transition-all">
            <i class="fa-brands fa-facebook text-sm"></i>
          </a>
          <a href="#" aria-label="TikTok" class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:border-white transition-all">
            <i class="fa-brands fa-tiktok text-sm"></i>
          </a>
        </div>
      </div>

      <!-- Layanan -->
      <div>
        <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Layanan</h3>
        <ul class="flex flex-col gap-2.5">
          <li v-for="item in layanan" :key="item">
            <a href="#" class="text-sm text-white/70 hover:text-white transition-colors">{{ item }}</a>
          </li>
        </ul>
      </div>

      <!-- Perusahaan -->
      <div>
        <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Perusahaan</h3>
        <ul class="flex flex-col gap-2.5">
          <li><a href="#" class="text-sm text-white/70 hover:text-white transition-colors">Tentang Kami</a></li>
          <li><Link :href="route('faq')" class="text-sm text-white/70 hover:text-white transition-colors">FAQ</Link></li>
          <li><a href="#" class="text-sm text-white/70 hover:text-white transition-colors">Syarat & Ketentuan</a></li>
          <li><a href="#" class="text-sm text-white/70 hover:text-white transition-colors">Kebijakan Privasi</a></li>
          <li><a href="#" class="text-sm text-white/70 hover:text-white transition-colors">Karir</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-white/10 text-center py-4 text-xs text-white/40">
      © 2026 MainYuk. All rights reserved.
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const layanan = ['Futsal', 'Bulu Tangkis', 'Basket', 'Tenis', 'Padel']
</script>
