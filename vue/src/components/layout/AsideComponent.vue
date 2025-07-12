<template>
  <aside
    :class="[
      'fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col',
      props.isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      props.isCollapsed ? 'lg:w-19' : 'lg:w-64',
    ]"
  >
    <!-- Header -->
    <header
      class="flex items-center justify-center h-16 px-4 border-b border-gray-200 bg-white z-10"
    >
      <img src="/images/logo-dark.png" alt="Logo" class="h-8 object-contain" />
    </header>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto relative" aria-label="Main navigation">
      <ul class="space-y-1">
        <li
          v-for="item in menuItems"
          :key="item.name"
          class="relative"
          @mouseenter="handleMouseEnter(item)"
          @mouseleave="handleMouseLeave"
          :ref="(el) => (refsMap[item.name] = el)"
        >
          <!-- Nếu có children: dùng button -->
          <button
            v-if="item.children"
            @click="handleItemClick(item)"
            :class="[
              'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150',
              isActive(item)
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900',
            ]"
          >
            <div class="flex items-center gap-2">
              <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
              <span v-if="!props.isCollapsed">{{ item.name }}</span>
            </div>
            <ChevronDown
              v-if="!props.isCollapsed && item.children"
              :class="[
                'w-4 h-4 transition-transform duration-200',
                openSubmenu === item.name ? 'rotate-180' : '',
              ]"
            />
          </button>

          <!-- Nếu KHÔNG có children: dùng router-link -->
          <router-link
            v-else
            :to="{ name: item.routeName }"
            :class="[
              'w-full flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150',
              isActive(item)
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900',
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span v-if="!props.isCollapsed">{{ item.name }}</span>
          </router-link>

          <!-- Submenu -->
          <div
            v-if="!props.isCollapsed && item.children"
            :class="[
              'overflow-hidden transition-all duration-300 ease-in-out',
              openSubmenu === item.name ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0',
            ]"
          >
            <ul class="pl-8 space-y-1">
              <li v-for="subItem in item.children" :key="subItem.name">
                <router-link
                  :to="{ name: subItem.routeName }"
                  :class="[
                    'block px-3 py-2 text-sm rounded-md transition-colors duration-150',
                    route.name === subItem.routeName
                      ? 'bg-blue-50 text-blue-700'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                  ]"
                >
                  {{ subItem.name }}
                </router-link>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </nav>

    <!-- Flyout submenu when collapsed -->
    <Teleport to="body">
      <div
        v-if="props.isCollapsed && hoveredItem && hoveredItem.children"
        @mouseenter="handleFlyoutMouseEnter"
        @mouseleave="handleFlyoutMouseLeave"
        :style="flyoutStyle"
        class="fixed bg-white shadow-lg rounded-md border border-gray-200 w-48 z-[1000]"
      >
        <ul class="py-1">
          <li v-for="subItem in hoveredItem.children" :key="subItem.name">
            <router-link
              :to="{ name: subItem.routeName }"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
            >
              {{ subItem.name }}
            </router-link>
          </li>
        </ul>
      </div>
    </Teleport>
  </aside>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next'
import { ref, defineProps, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { menuItems } from '@/constants/menu.js'

const props = defineProps({
  isCollapsed: Boolean,
  isMobileOpen: Boolean,
})

const route = useRoute()
const refsMap = reactive({})
const hoveredItem = ref(null)
const flyoutStyle = ref({ top: '0px', left: '0px' })
let hideTimeout = null
const isMouseOverFlyout = ref(false)
const openSubmenu = ref(null)

watch(
  () => route.name,
  () => {
    const parent = menuItems.find((item) =>
      item.children?.some((child) => child.routeName === route.name),
    )
    if (parent) {
      openSubmenu.value = parent.name
    } else {
      openSubmenu.value = null // 👈 Đóng toàn bộ nếu không còn nằm trong children
    }
  },
  { immediate: true },
)

const isActive = (item) => {
  if (item.routeName) {
    return route.name === item.routeName
  }
  if (item.children) {
    return item.children.some((child) => child.routeName === route.name)
  }
  return false
}

const handleItemClick = (item) => {
  if (item.children) {
    if (!props.isCollapsed) {
      openSubmenu.value = openSubmenu.value === item.name ? null : item.name
    }
  }
}

const handleMouseEnter = (item) => {
  if (props.isCollapsed && item.children) {
    clearHideTimeout()
    hoveredItem.value = item
    setTimeout(() => {
      const triggerEl = refsMap[item.name]
      if (triggerEl) {
        const rect = triggerEl.getBoundingClientRect()
        flyoutStyle.value = {
          top: `${rect.top}px`,
          left: `${rect.right + 4}px`,
        }
      }
    }, 0)
  }
}

const handleMouseLeave = () => {
  if (props.isCollapsed) {
    hideTimeout = setTimeout(() => {
      if (!isMouseOverFlyout.value) hoveredItem.value = null
    }, 500)
  }
}

const clearHideTimeout = () => {
  if (hideTimeout) {
    clearTimeout(hideTimeout)
    hideTimeout = null
  }
}

const handleFlyoutMouseEnter = () => {
  clearHideTimeout()
  isMouseOverFlyout.value = true
}

const handleFlyoutMouseLeave = () => {
  isMouseOverFlyout.value = false
  handleMouseLeave()
}
</script>

<style scoped></style>
