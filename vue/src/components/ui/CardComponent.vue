<template>
  <div class="bg-white rounded shadow-sm border border-gray-200 mb-3">
    <!-- Header -->
    <div
      v-if="title"
      class="px-4 py-2 border-b border-gray-200 flex items-center justify-between"
    >
      <h3 class="text-md font-semibold text-gray-700">{{ title }}</h3>
      <!-- <button class="text-gray-400 hover:text-gray-600 transition">...</button> -->
    </div>

    <!-- Body -->
    <div class="px-4 py-3">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String },
});
</script>

<style lang="scss" scoped></style>
