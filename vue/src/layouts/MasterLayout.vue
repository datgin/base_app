<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Overlay for mobile -->
    <div
      v-if="isMobileOpen"
      class="fixed inset-0 bg-[#8e8e8e] bg-opacity-50 z-40 lg:hidden"
      @click="closeMobileSidebar"
    ></div>

    <!-- Sidebar as aside element -->
    <AsideComponent
      @toggleSubmenu="toggleSubmenu"
      :isCollapsed="isCollapsed"
      :openSubmenu="openSubmenu"
      :isMobileOpen="isMobileOpen"
    />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Header -->
      <HeaderComponent
        @toggleCollapse="toggleCollapse"
        :isCollapsed="isCollapsed"
        @openMobileSidebar="openMobileSidebar"
      />

      <!-- Main Content -->
      <main
        class="flex-1 overflow-y-auto p-5 max-w-screen-xl w-full mx-auto hide-scrollbar"
        role="main"
      >
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

import AsideComponent from '@/components/layout/AsideComponent.vue'
import HeaderComponent from '@/components/layout/HeaderComponent.vue'

const isCollapsed = ref(false)
const isMobileOpen = ref(false)
const openSubmenu = ref(null)

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value

  if (isCollapsed.value) {
    openSubmenu.value = null
  }
}

const openMobileSidebar = () => {
  isMobileOpen.value = true
}

const closeMobileSidebar = () => {
  isMobileOpen.value = false
}

const toggleSubmenu = (menuName) => {
  if (isCollapsed.value) return

  // Chỉ cho phép mở một submenu tại một thời điểm
  if (openSubmenu.value === menuName) {
    openSubmenu.value = null
  } else {
    openSubmenu.value = menuName
  }
}

// Handle window resize
const handleResize = () => {
  if (window.innerWidth >= 1024) {
    isMobileOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>
