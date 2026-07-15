<template>
  <header class="fixed top-0 left-0 w-full z-50 bg-[#1E3932] py-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.20)]">
    <div class="max-w-screen-xl mx-auto px-6 sm:px-10 flex justify-between items-center">
      <!-- Logo -->
      <Link :href="route('home')" class="text-[26px] font-extrabold tracking-[-0.03em] !text-white">
        Main<span class="text-[#cba258]">Yuk</span>
      </Link>

      <!-- Nav Links -->
      <nav class="hidden md:flex">
        <ul class="flex gap-7 items-center">
          <li>
            <Link :href="route('dashboard')" :class="navClass('dashboard')">
              Cari Lapangan
            </Link>
          </li>
          <li>
            <Link :href="route('history')" :class="navClass('history')">
              Riwayat
            </Link>
          </li>
          <li>
            <Link :href="route('favorites')" :class="navClass('favorites')">
              Favorit
            </Link>
          </li>
          <li>
            <Link :href="route('profile')" :class="navClass('profile')">
              Profil
            </Link>
          </li>
          <li>
            <Link :href="route('pro.index')" :class="[navClass('pro.index'), 'flex items-center gap-1.5 !text-[#cba258]']">
              <i class="fa-solid fa-crown text-xs"></i> PRO
            </Link>
          </li>
        </ul>
      </nav>

      <!-- User Info, Notifications & Logout -->
      <div v-if="$page.props.auth?.user" class="hidden md:flex gap-4 items-center">
        <Link :href="route('notifications')" class="relative flex items-center justify-center w-8 h-8 rounded-full hover:bg-white/10">
          <i class="fa-solid fa-bell text-lg text-white/90"></i>
          <span v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 border-2 border-[#1E3932] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </Link>
        <span class="text-sm font-semibold text-white/75">Halo, {{ userName }}</span>
        <button
          @click="logout"
          class="border border-white/50 text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-white/10 transition-all bg-transparent cursor-pointer"
        >Keluar</button>
      </div>
      <div v-else class="hidden md:flex gap-2.5 items-center">
        <Link
          :href="route('login')"
          class="inline-block border border-white/50 !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-white/10 transition-all cursor-pointer"
        >Masuk</Link>
        <Link
          :href="route('register')"
          class="inline-block bg-[#00754A] !text-white px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-[#005a38] transition-all cursor-pointer"
        >Daftar Gratis</Link>
      </div>

      <!-- Mobile Toggle -->
      <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white text-[22px] bg-transparent border-0 cursor-pointer">
        <i :class="mobileOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div v-if="mobileOpen" class="md:hidden bg-[#1E3932] border-t border-white/10 px-6 py-4 flex flex-col gap-3">
      <Link :href="route('dashboard')" @click="mobileOpen = false" class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors">Cari Lapangan</Link>
      <Link :href="route('history')" @click="mobileOpen = false" class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors">Riwayat</Link>
      <Link :href="route('notifications')" @click="mobileOpen = false" class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors">Notifikasi</Link>
      <Link :href="route('favorites')" @click="mobileOpen = false" class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors">Favorit</Link>
      <Link :href="route('pro.index')" @click="mobileOpen = false" class="!text-[#cba258] font-semibold py-1 transition-colors">PRO</Link>
      <Link :href="route('profile')" @click="mobileOpen = false" class="!text-white/85 font-semibold py-1 hover:!text-white transition-colors">Profil</Link>

      <template v-if="$page.props.auth?.user">
        <button @click="logout" class="text-left text-white/85 font-semibold py-1 hover:text-white transition-colors bg-transparent border-0 cursor-pointer">Keluar</button>
      </template>
      <template v-else>
        <div class="flex gap-2 pt-2">
          <Link :href="route('login')"
            class="block flex-1 text-center border border-white/50 !text-white py-2 rounded-full text-sm font-semibold hover:bg-white/10 transition-all cursor-pointer">
            Masuk
          </Link>
          <Link :href="route('register')"
            class="block flex-1 text-center bg-[#00754A] !text-white py-2 rounded-full text-sm font-semibold hover:bg-[#005a38] transition-all cursor-pointer">
            Daftar
          </Link>
        </div>
      </template>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const mobileOpen = ref(false)
const page       = usePage()

const userName   = computed(() => {
  const user = page.props.auth?.user
  return user ? `${user.username || user.name}` : ''
})
const unreadCount = computed(() => page.props.auth?.unread_notifications_count ?? 0)

const navClass = (routeName) => [
  'text-[14px] font-semibold relative transition-colors duration-300',
  'after:absolute after:bottom-[-4px] after:left-0 after:h-0.5 after:bg-[#cba258] after:rounded-sm after:transition-all after:duration-200',
  route().current(routeName)
    ? '!text-white after:w-full'
    : '!text-white/75 hover:!text-white after:w-0 hover:after:w-full'
]

const logout = () => {
  router.post(route('logout'))
}
</script>
