<template>
  <nav
    class="flex flex-wrap items-center text-sm font-medium mb-10 space-x-1"
    aria-label="Breadcrumb"
  >
    <div class="flex items-center space-x-1">
      <TransitionGroup
        name="breadcrumb"
        tag="div"
        class="flex items-center flex-wrap space-x-1"
      >
        <div
          v-for="(item, index) in computedItems"
          :key="index"
          class="flex items-center space-x-1"
        >
          <div>
            <router-link
              v-if="item.routeName"
              :to="{ name: item.routeName }"
              class="text-xs uppercase text-gray-700 hover:text-blue-600 transition-colors duration-200"
              :class="{ 'font-semibold text-blue-600': isActive(item) }"
            >
              <span class="relative">
                {{ item.title }}
                <span
                  class="absolute -bottom-0.5 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transform scale-x-0 hover:scale-x-100 transition-transform duration-300"
                ></span>
              </span>
            </router-link>
            <span
              v-else
              class="text-xs uppercase text-gray-800 dark:text-gray-100 font-semibold cursor-default"
            >
              {{ item.title }}
            </span>
          </div>
          <div
            v-if="index < computedItems.length - 1"
            class="text-gray-400"
          >
            <ChevronRight class="w-4 h-4" />
          </div>
        </div>
      </TransitionGroup>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { ChevronRight } from "lucide-vue-next";

// Props
const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
});

// Current route for active highlight
const route = useRoute();

// Always prepend 'Tổng quan' with routeName 'dashboard'
const computedItems = computed(() => [
  { title: "Tổng quan", routeName: "dashboard" },
  ...props.items,
]);

const isActive = (item) => {
  return item.routeName === route.name;
};
</script>

<style scoped>
/* Breadcrumb transition animations */
.breadcrumb-enter-active,
.breadcrumb-leave-active {
  transition: all 0.3s ease;
}
.breadcrumb-enter-from {
  opacity: 0;
  transform: translateX(-6px) scale(0.95);
}
.breadcrumb-leave-to {
  opacity: 0;
  transform: translateX(6px) scale(0.95);
}
</style>
