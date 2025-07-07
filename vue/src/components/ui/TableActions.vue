<template>
  <div
    class="flex items-center shadow rounded justify-between gap-4 p-4 bg-white border-b border-gray-200 mb-3"
  >
    <!-- Left: Bulk Actions + Search -->
    <div class="flex items-center gap-3 flex-1">
      <!-- Bulk Actions Dropdown -->
      <button
        @click="goBack"
        class="flex items-center gap-1 text-gray-600 hover:text-blue-600 hover:bg-gray-100 px-2 py-1 cursor-pointer rounded text-sm transition"
      >
        <ArrowLeft class="w-4 h-4" />
        <span>Quay lại</span>
      </button>

      <div v-if="modelName" class="relative">
        <button
          @click="toggleBulkActions"
          class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          Hành động hàng loạt
          <ChevronDownIcon class="w-4 h-4" />
        </button>

        <div
          v-if="showBulkActions"
          class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded shadow-lg z-10"
        >
          <div class="py-1">
            <button
              @click="handleBulkAction('delete')"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Xoá đã chọn
            </button>
            <button
              @click="handleBulkAction('archive')"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Lưu trữ đã chọn
            </button>
          </div>
        </div>
      </div>

      <a-select
        v-if="modelName"
        v-model:value="localArchive"
        size="large"
        class="w-40 archive-filter"
        @change="onChangeArchiveFilter"
      >
        <a-select-option :value="1">Đang hoạt động</a-select-option>
        <a-select-option :value="2">Đã lưu trữ</a-select-option>
      </a-select>

      <!-- Search -->
      <div class="relative flex-1 max-w-sm">
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <SearchIcon class="h-4 w-4 text-gray-400" />
        </div>
        <input
          type="search"
          placeholder="Tìm kiếm..."
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
          v-model="localSearchText"
          @input="onSearchInput"
        />
      </div>
    </div>

    <!-- Right: Create + Reload -->
    <div class="flex items-center gap-3">
      <button
        @click="emits('create')"
        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-700 border border-transparent rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      >
        <PlusIcon class="w-4 h-4" />
        Tạo mới
      </button>

      <button
        @click="emits('reload')"
        class="flex items-center gap-2 px-3 py-2 cursor-pointer text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      >
        <RotateCcwIcon class="w-4 h-4" />
        Tải lại
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import {
  ChevronDownIcon,
  SearchIcon,
  PlusIcon,
  RotateCcwIcon,
  ArrowLeft,
} from "lucide-vue-next";
import { useRouter } from "vue-router";
import { Modal } from "ant-design-vue";

const router = useRouter();

// Props
const props = defineProps({
  searchText: { type: String, default: "" },
  archive: { type: Number, default: 1 },
  modelName: { type: String },
});

// Emits
const emits = defineEmits([
  "search",
  "create",
  "reload",
  "bulk-action",
  "filter-archive",
]);

const showBulkActions = ref(false);
const localSearchText = ref(props.searchText);
const localArchive = ref(props.archive);

const archiveFilter = ref(null);

const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value;
};

const handleBulkAction = (action) => {
  Modal.confirm({
    title: "Xác nhận hành động",
    content: `Bạn có chắc chắn muốn ${
      action === "delete" ? "xoá" : "lưu trữ"
    } các mục đã chọn không?`,
    okText: "Xác nhận",
    cancelText: "Huỷ",
    okType: action === "delete" ? "danger" : "primary",
    centered: true,
    onOk() {
      emits("bulk-action", action, props.modelName);
      showBulkActions.value = false;
    },
  });
};

const onChangeArchiveFilter = (value) => {
  archiveFilter.value = value;
  emits("filter-archive", value); // emit lên component cha
};

const onSearchInput = () => {
  emits("search", localSearchText.value);
}; // Theo dõi khi props thay đổi (ví dụ khi reset từ cha)
watch(
  () => props.searchText,
  (val) => {
    localSearchText.value = val;
  }
);

const goBack = () => {
  router.back(); // ← sẽ quay lại history trước đó
};
</script>
