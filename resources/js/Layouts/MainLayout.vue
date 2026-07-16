<template>
  <div class="min-h-screen bg-[#f2f0eb] flex flex-col">
    <Navbar />
    <main class="grow">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
</script>