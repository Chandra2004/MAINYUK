<template>
  <div>
    <!-- Join Strip -->
    <div class="bg-[#1E3932] text-white/80 text-center py-2.5 px-4 text-[13px] font-semibold tracking-wide">
      Bergabung gratis dan dapatkan diskon 20% untuk booking pertamamu!
      <Link :href="route('register')" class="join-strip-link">
        Daftar Sekarang →
      </Link>
    </div>

    <!-- Navbar -->
    <header
      ref="headerEl"
      :class="[
        'sticky top-0 w-full z-50 transition-all duration-300',
        scrolled
          ? 'bg-[#1E3932] py-3 shadow-[0_2px_8px_rgba(0,0,0,0.20)]'
          : 'bg-[#1E3932]/95 py-4'
      ]"
    >
      <div class="max-w-screen-xl mx-auto px-6 sm:px-10 flex justify-between items-center">
        <!-- Logo -->
        <Link :href="route('home')" class="text-[26px] font-extrabold tracking-[-0.03em] !text-white">
          Main<span class="text-[#cba258]">Yuk</span>
        </Link>

        <!-- Nav Links (desktop) -->
        <nav class="hidden md:flex">
          <ul class="flex gap-7 items-center">
            <li v-for="link in navLinks" :key="link.label">
              <a
                :href="link.href"
                :class="[
                  'text-[14px] font-semibold relative transition-colors duration-300 no-underline',
                  'after:absolute after:bottom-[-4px] after:left-0 after:h-0.5 after:bg-[#cba258] after:rounded-sm after:transition-all after:duration-200',
                  link.active
                    ? '!text-white after:w-full'
                    : '!text-white/85 hover:!text-white after:w-0 hover:after:w-full'
                ]"
              >{{ link.label }}</a>
            </li>
          </ul>
        </nav>

        <!-- Auth Buttons / User Menu -->
        <div class="hidden md:flex gap-2.5 items-center">
          <template v-if="!$page.props.auth?.user">
            <Link
              :href="route('login')"
              class="inline-block border border-white/50 !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-white/10 transition-all cursor-pointer"
            >Masuk</Link>
            <Link
              :href="route('register')"
              class="inline-block bg-[#00754A] !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-[#005a38] transition-all cursor-pointer"
            >Daftar Gratis</Link>
          </template>
          <template v-else>
            <Link :href="route('dashboard')"
              class="inline-block border border-white/50 !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-white/10 transition-all cursor-pointer">
              Dashboard
            </Link>
            <Link :href="route('logout')" method="post" as="button"
              class="inline-block bg-[#00754A] !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-[#005a38] transition-all cursor-pointer">
              Keluar
            </Link>
          </template>
        </div>

        <!-- Mobile Toggle -->
        <button @click="mobileOpen = !mobileOpen"
          class="md:hidden text-white text-[22px] bg-transparent border-0 cursor-pointer">
          <i :class="mobileOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'"></i>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileOpen" class="md:hidden bg-[#1E3932] border-t border-white/10 px-6 py-4 flex flex-col gap-3">
        <a v-for="link in navLinks" :key="link.label"
          :href="link.href"
          @click="mobileOpen = false"
          class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors no-underline">
          {{ link.label }}
        </a>
        <div v-if="!$page.props.auth?.user" class="flex gap-2 pt-2">
          <Link :href="route('login')"
            class="block flex-1 text-center border border-white/50 !text-white py-2 rounded-full text-sm font-semibold hover:bg-white/10 transition-all cursor-pointer">
            Masuk
          </Link>
          <Link :href="route('register')"
            class="block flex-1 text-center bg-[#00754A] !text-white py-2 rounded-full text-sm font-semibold hover:bg-[#005a38] transition-all cursor-pointer">
            Daftar
          </Link>
        </div>
        <div v-else class="flex gap-2 pt-2">
          <Link :href="route('dashboard')"
            class="block flex-1 text-center border border-white/50 !text-white py-2 rounded-full text-sm font-semibold cursor-pointer">
            Dashboard
          </Link>
          <Link :href="route('logout')" method="post" as="button"
            class="block flex-1 text-center bg-[#00754A] !text-white py-2 rounded-full text-sm font-semibold cursor-pointer">
            Keluar
          </Link>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const scrolled   = ref(false)
const mobileOpen = ref(false)
const headerEl   = ref(null)
const page       = usePage()

// Cek apakah halaman saat ini adalah home page
const isHome = computed(() => page.url === '/' || page.url.startsWith('/?'))

// navLinks menggunakan href langsung — bukan route() —
// sehingga aman untuk anchor (#section) dan tidak error untuk route yang tidak ada
const navLinks = computed(() => [
  {
    href:   route('home'),
    label:  'Beranda',
    active: isHome.value && !page.url.includes('#'),
  },
  {
    href:   route('home') + '#kategori',
    label:  'Kategori',
    active: false,
  },
  {
    href:   route('home') + '#cara-booking',
    label:  'Cara Pemesanan',
    active: false,
  },
  {
    href:   route('dashboard'),
    label:  'Lapangan',
    active: route().current('dashboard'),
  },
  {
    href:   route('faq'),
    label:  'FAQ',
    active: route().current('faq'),
  },
])

const handleScroll = () => { scrolled.value = window.scrollY > 40 }

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>