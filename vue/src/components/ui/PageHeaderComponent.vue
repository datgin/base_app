<template>
  <div class="bg-white mb-3 shadow flex items-center justify-between">
    <a-page-header :title="title" @back="goBack" />

    <div class="px-5" v-if="routeNameCreate">
      <router-link
        :to="{ name: routeNameCreate }"
        class="flex items-center gap-2 px-4 py-2 text-md font-medium text-white bg-blue-700 border border-transparent rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      >
        <PlusIcon class="w-4 h-4" />
        Tạo mới
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

defineProps({
  title: { type: String, required: true, default: () => 'Danh sách' },
  routeNameCreate: { type: String },
})

const router = useRouter()

const goBack = () => {
  router.back() // ← sẽ quay lại history trước đó
}
</script>
