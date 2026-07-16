<template>
  <div class="min-h-screen bg-[#f2f0eb] flex flex-col">
    <!-- Dashboard Navbar (solid dark, authenticated) -->
    <NavbarDashboard />

    <!-- Main content with padding-top to offset fixed navbar -->
    <main class="flex-grow pt-[68px]">
      <slot />
    </main>
  </div>
</template>

<script setup>
import NavbarDashboard from '@/Components/NavbarDashboard.vue'
</script>
