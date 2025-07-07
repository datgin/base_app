<template>
  <header class="bg-white shadow-sm border-b border-gray-200" role="banner">
    <div class="flex items-center justify-between px-4 py-[.55rem]">
      <div class="flex items-center gap-3">
        <!-- Mobile menu button -->
        <button
          @click="$emit('openMobileSidebar')"
          class="lg:hidden p-2 rounded-md hover:bg-gray-100 transition-colors"
          aria-label="Open sidebar"
        >
          <Menu class="w-5 h-5 text-gray-600" aria-hidden="true" />
        </button>

        <!-- Desktop toggle button -->
        <button
          @click="$emit('toggleCollapse')"
          class="hidden lg:block p-1.5 cursor-pointer rounded-md hover:bg-gray-100 transition-colors"
          aria-label="Toggle sidebar"
        >
          <ChevronsLeft
            :class="[
              'w-6 h-6 text-gray-600 transition-transform duration-200',
              isCollapsed ? 'rotate-180' : '',
            ]"
            aria-hidden="true"
          />
        </button>

        <!-- Search Form -->
        <form
          class="w-full max-w-md hidden lg:block"
          role="search"
          @submit.prevent
        >
          <div class="relative">
            <label for="search" class="sr-only">Search</label>
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <Search class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
            <input
              id="search"
              type="search"
              placeholder="Search for ..."
              class="block w-full pl-10 pr-3 min-w-[300px] py-2 border border-gray-300 rounded leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
              autocomplete="off"
            />
          </div>
        </form>
      </div>

      <div class="flex items-center gap-4">
        <!-- Notification -->
        <NotificationComponent
          @toggleNotification="toggleNotification"
          @closeNotification="closeNotification"
          :isNotificationOpen="isNotificationOpen"
          ref="notificationComponentRef"
        />

        <!-- Header actions placeholder -->
        <UserComponent
          @toggleMenu="toggleMenu"
          :isOpen="isOpen"
          ref="menuComponentRef"
        />
      </div>
    </div>
  </header>
</template>

<script setup>
import { ChevronsLeft, Search, Menu } from "lucide-vue-next";

import { onBeforeUnmount, onMounted, ref } from "vue";
import UserComponent from "../partials/UserComponent.vue";
import NotificationComponent from "../partials/NotificationComponent.vue";

const isOpen = ref(false);
const menuComponentRef = ref(null);

const isNotificationOpen = ref(false);
const notificationComponentRef = ref(null);

const toggleNotification = () => {
  // Mở notification thì đóng user menu
  if (!isNotificationOpen.value) {
    isOpen.value = false;
  }
  isNotificationOpen.value = !isNotificationOpen.value;
};

const closeNotification = () => {
  isNotificationOpen.value = false;
};

defineProps({
  isCollapsed: { type: Boolean, default: false },
});

// const viewAllNotifications = () => {
//   closeNotification();
// };

defineEmits(["toggleCollapse", "openMobileSidebar"]);

function toggleMenu() {
  // Mở user menu thì đóng notification
  if (!isOpen.value) {
    isNotificationOpen.value = false;
  }
  isOpen.value = !isOpen.value;
}

// Hàm xử lý click ngoài
function handleClickOutside(event) {
  if (
    menuComponentRef.value &&
    menuComponentRef.value.menuRef &&
    !menuComponentRef.value.menuRef.contains(event.target)
  ) {
    isOpen.value = false;
  }

  if (
    notificationComponentRef.value &&
    notificationComponentRef.value.notificationRef &&
    !notificationComponentRef.value.notificationRef.contains(event.target)
  ) {
    isNotificationOpen.value = false;
  }
}

// Gắn và gỡ listener
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped></style>
