<template>
  <CardComponent title="Xuất bản">
    <button
      type="submit"
      :disabled="props.loading"
      :class="[
        'inline-flex items-center gap-2 px-4 py-2 me-2 bg-blue-700 hover:bg-blue-600 text-white text-sm font-medium rounded transition cursor-pointer',
        props.loading ? 'opacity-50 cursor-not-allowed' : '',
      ]"
    >
      <Loader2 v-if="props.loading" class="w-4 h-4 animate-spin" />
      <Save v-else class="w-4 h-4" />
      <span>Lưu</span>
    </button>

    <button
      type="button"
      :disabled="props.loading"
      :class="[
        'inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:border-gray-400 text-gray-800 text-sm font-medium rounded transition cursor-pointer',
        props.loading ? 'opacity-50 cursor-not-allowed' : '',
      ]"
    >
      <LogOut class="w-4 h-4" />
      Lưu & Thoát
    </button>
  </CardComponent>
</template>

<script setup>
import CardComponent from "@/components/ui/CardComponent.vue";
import { Save, LogOut, Loader2 } from "lucide-vue-next";

const props = defineProps({
  loading: { type: Boolean, default: false },
});
</script>

<style lang="scss" scoped></style>
