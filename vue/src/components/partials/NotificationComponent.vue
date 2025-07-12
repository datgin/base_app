<template>
  <div ref="notificationRef" class="relative inline-block text-left">
    <!-- Notification Button -->
    <button
      @click="$emit('toggleNotification')"
      class="relative w-10 h-10 bg-white hover:bg-gray-50 cursor-pointer rounded-full flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow transition-all duration-200 hover:shadow-md"
      aria-label="Notifications"
    >
      <Bell class="w-5 h-5 text-gray-700" />
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Notification Popup -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95 translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 translate-y-1"
    >
      <div
        v-if="props.isNotificationOpen"
        class="absolute right-0 top-13 w-80 bg-white rounded shadow-xl border border-gray-200 z-50 overflow-hidden"
      >
        <!-- Header -->
        <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200">
          <h3 class="text-base font-semibold text-gray-800">Notifications</h3>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="text-sm text-blue-600 hover:underline"
          >
            Mark all as read
          </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="flex items-start px-4 py-3 hover:bg-gray-50 cursor-pointer"
            @click="markAsRead(notification.id)"
          >
            <img
              v-if="notification.avatar"
              :src="notification.avatar"
              alt="avatar"
              class="w-10 h-10 rounded-full object-cover mr-3"
            />
            <div
              v-else
              class="w-10 h-10 rounded-full flex items-center justify-center mr-3"
              :class="getNotificationIconClass(notification.type)"
            >
              <component :is="getNotificationIcon(notification.type)" class="w-5 h-5" />
            </div>

            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900">
                {{ notification.title }}
              </p>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ notification.message }}
              </p>
              <div class="flex items-center text-xs text-gray-400 mt-0.5">
                <Clock class="w-3 h-3 mr-1" />
                {{ notification.time }}
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <button
          @click="viewAllNotifications"
          class="w-full py-2 text-center text-sm text-blue-600 hover:underline bg-gray-50"
        >
          View More..
        </button>
      </div>
    </transition>

    <!-- Backdrop -->
    <!-- <div
      v-if="props.isNotificationOpen"
      @click="$emit('closeNotification')"
      class="fixed inset-0 z-40"
    ></div> -->
  </div>
</template>

<script setup>
import { Bell, Clock, MessageCircle, Heart, Gift, AlertCircle, CheckCircle } from 'lucide-vue-next'
import { ref, computed } from 'vue'

const notificationRef = ref(null)

defineExpose({ notificationRef })

defineEmits(['closeNotification'])

const props = defineProps({
  isNotificationOpen: { type: Boolean, required: true },
})

const notifications = ref([
  {
    id: 1,
    type: 'order',
    title: 'Your order is placed',
    message: 'If several languages coalesce the grammar',
    time: '3 min ago',
    read: false,
  },
  {
    id: 2,
    avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
    title: 'James Lemire',
    message: 'It will seem like simplified English.',
    time: '1 hour ago',
    read: false,
  },
  {
    id: 3,
    type: 'shipped',
    title: 'Your item is shipped',
    message: 'If several languages coalesce the grammar',
    time: '2 hours ago',
    read: true,
  },
  {
    id: 4,
    type: 'shipped',
    title: 'Your item is shipped',
    message: 'If several languages coalesce the grammar',
    time: '2 hours ago',
    read: true,
  },
  {
    id: 5,
    type: 'shipped',
    title: 'Your item is shipped',
    message: 'If several languages coalesce the grammar',
    time: '2 hours ago',
    read: true,
  },
  {
    id: 6,
    type: 'shipped',
    title: 'Your item is shipped',
    message: 'If several languages coalesce the grammar',
    time: '2 hours ago',
    read: true,
  },
])

const markAsRead = (id) => {
  const notification = notifications.value.find((n) => n.id === id)
  if (notification) {
    notification.read = true
  }
}

const unreadCount = computed(() => notifications.value.filter((n) => !n.read).length)

const markAllAsRead = () => notifications.value.forEach((n) => (n.read = true))

const getNotificationIcon = (type) => {
  const icons = {
    order: CheckCircle,
    shipped: Gift,
    alert: AlertCircle,
    message: MessageCircle,
    like: Heart,
  }
  return icons[type] || Bell
}

const getNotificationIconClass = (type) => {
  const classes = {
    order: 'bg-blue-100 text-blue-600',
    shipped: 'bg-green-100 text-green-600',
    alert: 'bg-yellow-100 text-yellow-600',
    message: 'bg-blue-100 text-blue-600',
    like: 'bg-pink-100 text-pink-600',
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
