<template>
  <CardComponent title="Xuất bản">
    <button
      type="submit"
      class="inline-flex items-center gap-2 px-4 py-2 me-2 bg-blue-700 hover:bg-blue-600 text-white text-sm font-medium rounded-md transition cursor-pointer"
    >
      <Save class="w-4 h-4" />
      Lưu
    </button>

    <button
      type="button"
      class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:border-gray-400 text-gray-800 text-sm font-medium rounded-md transition cursor-pointer"
    >
      <LogOut class="w-4 h-4" />
      Lưu & Thoát
    </button>
  </CardComponent>
</template>

<script setup>
import CardComponent from "@/components/ui/CardComponent.vue";
import { Save, LogOut } from "lucide-vue-next";
</script>

<style lang="scss" scoped></style>
