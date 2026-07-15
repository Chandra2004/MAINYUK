<template>
  <div
    class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-200 group cursor-pointer relative border border-gray-100 flex flex-col h-full"
    @click="goToDetail"
  >
    <!-- Favorite Button -->
    <button
      @click.stop="toggleFav"
      :class="[
        'absolute top-3 right-3 z-10 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200',
        localFav ? 'bg-[#006241] text-white' : 'bg-white/80 text-gray-400 hover:bg-[#006241] hover:text-white'
      ]"
    >
      <i class="fa-solid fa-heart text-xs"></i>
    </button>

    <!-- Image -->
    <div class="h-44 overflow-hidden">
      <img
        :src="imageUrl"
        :alt="court.name || court.nama"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
    </div>

    <!-- Content -->
    <div class="p-4 flex flex-col flex-grow">
      <div class="flex justify-between items-start mb-1 gap-2">
        <h3 class="font-bold text-gray-900 text-sm leading-tight pr-2 line-clamp-2">{{ court.name || court.nama }}</h3>
        <span class="flex items-center gap-1 text-amber-500 text-xs font-bold whitespace-nowrap mt-0.5">
          <i class="fa-solid fa-star text-[10px]"></i>
          {{ court.rating }}
        </span>
      </div>
      <p class="text-gray-400 text-xs flex items-center gap-1 mb-3">
        <i class="fa-solid fa-location-dot text-[#006241]"></i>
        {{ court.city || court.lokasi }}
      </p>
      
      <div class="mt-auto">
        <p class="text-[#006241] font-bold text-sm mb-3">
          Rp {{ (court.price_per_hour || court.harga || 0).toLocaleString('id-ID') }}
          <span class="text-gray-400 font-normal">/ jam</span>
        </p>
        <button
          class="block w-full text-center bg-[#006241] text-white py-2 rounded-full text-xs font-bold hover:bg-[#004d34] transition-all"
        >
          Lihat Detail
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  court:  { type: Object, required: true },
  isFav:  { type: Boolean, default: false },
})

const emit = defineEmits(['toggle-fav'])

const localFav = ref(props.isFav)

// Support both old format (img) and new DB format (images array / main_image)
const imageUrl = computed(() => {
  return props.court.main_image
    || props.court.img
    || (props.court.images && props.court.images[0])
    || 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=600&auto=format&fit=crop'
})

const toggleFav = () => {
  localFav.value = !localFav.value
  emit('toggle-fav', props.court)
}

const goToDetail = () => {
  // Support both old static data (id only) and DB data
  router.visit(route('court.detail', props.court.id))
}
</script>
