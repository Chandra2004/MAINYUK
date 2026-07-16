<template>
  <Head title="Pembayaran Langganan PRO" />
  <DashboardLayout>
    <div class="max-w-md mx-auto px-4 py-12 text-center">
      
      <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
        <i class="fa-solid fa-crown text-amber-500 text-4xl"></i>
      </div>
      
      <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Menyelesaikan Pembayaran</h1>
      <p class="text-gray-500 text-sm mb-8">
        Selesaikan pembayaran untuk mengaktifkan MainYuk! PRO Anda. Total: <strong>Rp {{ amount.toLocaleString('id-ID') }}</strong>
      </p>

      <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 shadow-sm mb-6">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Instruksi</p>
        <p class="text-sm text-gray-600 font-medium">
          Jika popup pembayaran tidak muncul secara otomatis, silakan klik tombol di bawah ini.
        </p>
      </div>

      <button @click="pay"
        class="w-full bg-[#006241] text-white py-4 rounded-xl font-bold hover:bg-[#004d34] transition-all shadow-md flex items-center justify-center gap-2">
        <i class="fa-solid fa-credit-card"></i> Buka Pembayaran
      </button>

      <Link :href="route('pro.index')" class="block mt-6 text-sm font-bold text-gray-400 hover:text-gray-600">
        Batal
      </Link>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  snap_token: String,
  amount: Number,
  order_id: String,
})

const pay = () => {
  if (window.snap) {
    window.snap.pay(props.snap_token, {
      onSuccess: function(result) {
        router.visit(route('membership.success', { order_id: result.order_id }))
      },
      onPending: function(result) {
        alert('Menunggu pembayaran Anda.')
        router.visit(route('pro.index'))
      },
      onError: function(result) {
        router.visit(route('membership.failed', { order_id: result.order_id }))
      },
      onClose: function() {
        router.visit(route('membership.failed', { order_id: props.order_id }))
      }
    })
  } else {
    alert('Sistem pembayaran belum siap. Silakan muat ulang halaman.')
  }
}

onMounted(() => {
  // Tunggu script snap midtrans load (ada di app.blade.php)
  setTimeout(() => {
    pay()
  }, 1000)
})
</script>
