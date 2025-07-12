<template>
  <transition name="fade-scale" appear :duration="{ enter: 300, leave: 300 }">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 lg:px-[3rem] py-[3rem] px-0"
    >
      <div class="bg-white rounded-lg shadow-lg w-full h-full flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
          <h2 class="font-medium text-gray-700 text-2xl flex items-center gap-2">
            📁 Thư viện ảnh
          </h2>
          <button
            @click="$emit('close')"
            class="group relative p-1 rounded cursor-pointer border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition-colors duration-200"
          >
            <XIcon
              class="w-5 h-5 transition-transform font-semibold duration-200 group-hover:rotate-90"
            />
            <span class="sr-only">Đóng</span>
          </button>
        </div>

        <!-- Content -->
        <div class="flex flex-1 overflow-hidden">
          <div
            v-if="loading"
            class="absolute inset-0 bg-white/70 z-50 flex items-center justify-center"
          >
            <span class="loader"></span>
          </div>

          <!-- Left: Image Grid -->
          <div class="w-full lg:w-[calc(100%-300px)] flex flex-col">
            <!-- Toolbar -->
            <div
              class="flex flex-col md:flex-row md:items-center gap-2 px-4 py-3 border-b border-gray-200"
            >
              <div class="flex gap-2 items-center">
                <button
                  type="button"
                  @click="triggerFileInput"
                  class="w-2/4 md:w-auto px-4 py-2 border border-blue-600 text-white rounded font-semibold bg-blue-600 hover:text-blue-600 hover:bg-white transition flex items-center gap-2 cursor-pointer"
                >
                  <ImageUp class="w-5 h-5" /> Tải ảnh lên
                </button>

                <input
                  type="file"
                  ref="fileInput"
                  accept="image/*"
                  multiple
                  class="hidden"
                  @change="handleFileUpload"
                />

                <button
                  @click="handleDelete"
                  type="button"
                  class="flex-1 md:flex-none px-4 py-2 border border-red-600 text-red-600 hover:bg-red-600 rounded font-semibold bg-white hover:text-white transition flex items-center gap-2 cursor-pointer"
                >
                  <Trash class="w-5 h-5" /> Xoá ảnh đã chọn
                </button>
              </div>
              <div class="relative w-full md:w-80 md:ml-auto">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"
                    />
                  </svg>
                </span>
                <input
                  @input="debouncedSearch"
                  v-model="searchText"
                  type="search"
                  placeholder="Tìm kiếm ảnh..."
                  class="pl-10 pr-3 py-2 border border-gray-300 rounded w-full text-sm focus:border-blue-400 focus:ring focus:ring-blue-100 focus:outline-none transition"
                />
              </div>
            </div>

            <!-- Grid with Scroll -->
            <div class="flex-1 min-h-0 overflow-y-auto">
              <!-- Hiển thị khi không có ảnh -->
              <div
                v-if="!loading && !images.length"
                class="p-6 text-center text-gray-500 animate-bounce-slow"
              >
                <p class="text-sm">Không có ảnh nào được tìm thấy</p>
              </div>

              <!-- Hiển thị khi có ảnh -->
              <div v-else>
                <div class="p-4 grid grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                  <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="border border-gray-300 rounded cursor-pointer relative group hover:shadow"
                    :class="{ 'ring-2 ring-blue-400': isSelected(image) }"
                    @click="toggleSelect(image)"
                  >
                    <div
                      class="w-full pb-[100%] relative bg-gray-100 rounded flex items-center justify-center overflow-hidden"
                    >
                      <img
                        :src="image.path"
                        alt=""
                        class="absolute inset-0 object-contain w-full h-full"
                      />
                    </div>
                    <div class="my-1 px-1 text-sm text-center text-gray-700 truncate">
                      {{ image.name }}
                    </div>
                    <div
                      v-if="isSelected(image)"
                      class="absolute top-1 right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                    >
                      <CheckCircle class="w-5 h-5" />
                    </div>
                  </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center my-4" v-if="total > 0">
                  <a-pagination
                    :total="total"
                    :page-size="pageSize"
                    :current="currentPage"
                    :page-size-options="['10', '20', '50', '100']"
                    show-size-changer
                    @change="onPageChange"
                    @showSizeChange="onPageChange"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Info Panel -->
          <div class="hidden lg:flex w-[300px] border-l border-gray-200 p-4 flex-col">
            <div class="overflow-y-auto max-h-[60vh] w-full">
              <div v-if="selectedImages.length">
                <div
                  class="w-full h-60 bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden"
                >
                  <img
                    :src="selectedImages[selectedImages.length - 1].path"
                    alt=""
                    class="object-contain max-w-full max-h-full"
                  />
                </div>

                <div class="text-sm text-gray-700 leading-loose">
                  <p>
                    <strong>Tên:</strong>
                    {{ selectedImages[selectedImages.length - 1].name }}
                  </p>
                  <p class="flex items-center gap-1">
                    <strong>URL:</strong>
                    <span
                      class="truncate max-w-[200px] block"
                      :title="selectedImages[selectedImages.length - 1].path"
                    >
                      {{ selectedImages[selectedImages.length - 1].path }}
                    </span>
                    <button
                      @click="copyUrl(selectedImages[selectedImages.length - 1].path)"
                      class="text-blue-500 hover:text-blue-700 cursor-pointer"
                    >
                      <Copy class="w-4 h-4" />
                    </button>
                  </p>
                  <p>
                    <strong>Kích thước:</strong>
                    {{ selectedImages[selectedImages.length - 1].size }}
                  </p>
                  <p>
                    <strong>Kích thước ảnh:</strong>
                    {{ selectedImages[selectedImages.length - 1].width }} x
                    {{ selectedImages[selectedImages.length - 1].height }} px
                  </p>
                  <p>
                    <strong>Ngày đăng:</strong>
                    {{ selectedImages[selectedImages.length - 1].created_at }}
                  </p>
                </div>
              </div>
              <div v-else class="text-center text-gray-400 text-sm">
                Chọn một ảnh để xem chi tiết
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 p-4 flex justify-end">
          <button
            @click="confirmSelection"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-2 font-semibold cursor-pointer flex items-center gap-3"
            :disabled="!selectedImages.length"
          >
            <ImageDown class="w-5 h-5" /> Chọn ảnh
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { debounce } from '@/utils/helpers.js'
import { XIcon, ImageUp, CheckCircle, Trash, Copy, ImageDown } from 'lucide-vue-next'
import { message, Modal } from 'ant-design-vue'
import axios from '@/config/axios'
import useCrud from '@/composables/useCrud.js'

const { uploadMultipart, getList, data, loading } = useCrud()

const props = defineProps({
  multiple: { type: Boolean, default: false },
  isOpen: { type: Boolean, default: false },
})

const fileInput = ref(null)
const total = ref(0)
const images = ref([])
const currentPage = ref(1)
const pageSize = ref(20)
const searchText = ref(null)

function triggerFileInput() {
  fileInput.value?.click()
}

async function handleFileUpload(event) {
  const files = Array.from(event.target.files)

  if (files.length === 0) return

  if (files.length > 10) {
    message.error('Chỉ được chọn tối đa 10 ảnh')
    return
  }

  const oversize = files.find((file) => file.size > 10 * 1024 * 1024)
  if (oversize) {
    message.error('Mỗi ảnh phải nhỏ hơn 10MB')
    return
  }

  try {
    await uploadMultipart('/media/upload', { images: files })
    fetchImages()
    message.success('Tải ảnh thành công')
  } catch (error) {
    console.error('Lỗi upload:', error)
    message.error('Tải ảnh thất bại')
  } finally {
    fileInput.value.value = ''
  }
}

async function handleDelete() {
  if (selectedImages.value.length === 0) {
    message.warning('Bạn chưa chọn ảnh nào để xoá')
    return
  }

  Modal.confirm({
    title: 'Xác nhận xoá ảnh',
    content: `Bạn có chắc chắn muốn xoá ${selectedImages.value.length} ảnh đã chọn?`,
    okText: 'Xoá',
    okType: 'danger',
    cancelText: 'Huỷ',
    async onOk() {
      try {
        const ids = selectedImages.value.map((img) => img.id)
        await axios.delete('/media/destroy', { data: { ids } })
        selectedImages.value = []
        await fetchImages()
        message.success('Xoá ảnh thành công')
      } catch (error) {
        console.error('Lỗi khi xoá ảnh:', error)
        message.error('Xoá ảnh thất bại')
      }
    },
  })
}

const debouncedSearch = debounce(() => {
  fetchImages(1, pageSize.value) // reset về page 1, giữ nguyên pageSize
}, 400)

const emit = defineEmits(['close', 'select'])

const fetchImages = async (page = 1, perPage = pageSize.value) => {
  try {
    await getList('/media', {
      page,
      per_page: perPage,
      searchText: searchText.value,
    })

    images.value = data.value.items
    total.value = data.value.total
    currentPage.value = page
  } catch (e) {
    console.log(e)
    message.error('Không thể tải danh sách ảnh')
  }
}

function onPageChange(page, perPage) {
  pageSize.value = perPage
  fetchImages(page, perPage)
}

onMounted(() => {
  fetchImages()
})

const selectedImages = ref([])

function toggleSelect(image) {
  if (props.multiple) {
    const index = selectedImages.value.findIndex((img) => img.path === image.path)

    if (index !== -1) {
      selectedImages.value.splice(index, 1)
    } else {
      selectedImages.value.push(image)
    }
  } else {
    selectedImages.value = [image]
  }
}

function isSelected(image) {
  return selectedImages.value.some((img) => img.path === image.path)
}

function copyUrl(path) {
  navigator.clipboard
    .writeText(path)
    .then(() => {
      message.success('Sao chép thành công đường dẫn')
    })
    .catch((err) => {
      console.error('Lỗi khi copy:', err)
    })
}

function confirmSelection() {
  if (props.multiple) {
    emit('select', selectedImages.value)
  } else {
    emit('select', [selectedImages.value[0]]) // Trả về dưới dạng mảng luôn
  }
}
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(20px);
}

.fade-scale-enter-to,
.fade-scale-leave-from {
  opacity: 1;
  transform: scale(1) translateY(0);
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3490dc;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
