<template>
  <div ref="menuRef" class="relative inline-block text-left">
    <!-- Avatar Button -->
    <button
      @click="handleClick"
      :class="[
        'w-10 h-10 rounded-full overflow-hidden border border-gray-300 shadow-sm transition transform focus:outline-none cursor-pointer',
        isActive ? 'scale-95' : 'hover:scale-105',
      ]"
      aria-label="User menu"
    >
      <img src="https://placehold.co/400" alt="Avatar" class="w-full h-full object-cover" />
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="props.isOpen"
        class="absolute right-0 top-12 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50"
      >
        <!-- User Info -->
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
          <img
            src="https://placehold.co/400"
            alt="Avatar"
            class="rounded-full w-10 h-10 object-cover border border-gray-200"
          />
          <div class="flex flex-col">
            <span class="text-sm font-medium text-gray-800 truncate">
              {{ authStore.user?.name || 'Đang tải...' }}
            </span>
            <span class="text-xs text-gray-500 truncate">
              {{ authStore.user?.email || '' }}
            </span>
          </div>
        </div>

        <!-- Menu Items -->
        <ul class="py-1">
          <li>
            <RouterLink
              to="/admin/profile"
              class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition w-full text-left"
            >
              <SquareUser class="w-5 h-5 text-gray-500" />
              Hồ sơ cá nhân
            </RouterLink>
          </li>
        </ul>

        <!-- Logout -->
        <button
          @click="handleLogout"
          class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition w-full text-left border-t border-gray-100"
        >
          <LogOut class="w-5 h-5 text-gray-500" />
          Đăng xuất
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { SquareUser, LogOut } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const emit = defineEmits(['toggleMenu'])
const props = defineProps({ isOpen: { type: Boolean, required: true } })
const menuRef = ref(null)
defineExpose({ menuRef })

const authStore = useAuthStore()
const router = useRouter()

const isActive = ref(false)

const handleClick = () => {
  isActive.value = true
  setTimeout(() => {
    isActive.value = false
  }, 150)
  emit('toggleMenu')
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/admin/login')
}

onMounted(async () => {
  if (authStore.token && !authStore.user) {
    await authStore.getUser()
  }
})
</script>
