<template>
  <div>
    <!-- Label -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <!-- Upload khung -->
    <div
      class="border border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition"
      @click="openPopup"
    >
      <div
        v-if="hasValue"
        class="grid gap-4 w-full"
        style="grid-template-columns: repeat(auto-fill, minmax(100px, 1fr))"
        @click.stop
      >
        <!-- Nếu multiple -->
        <template v-if="multiple">
          <div
            v-for="(url, idx) in value"
            :key="idx"
            class="relative aspect-square overflow-hidden rounded border border-gray-200 group cursor-pointer"
          >
            <img
              :src="url"
              alt="Ảnh đã chọn"
              class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
            />

            <div
              class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition"
            >
              <Eye class="w-5 h-5 text-white" @click.stop="handleView(idx)" />
              <Trash2
                class="w-5 h-5 text-white"
                @click.stop="handleDelete(idx)"
              />
            </div>
          </div>
        </template>

        <!-- Nếu single -->
        <template v-else>
          <div
            class="relative aspect-square overflow-hidden rounded border border-gray-200 group cursor-pointer"
          >
            <img
              :src="value"
              alt="Ảnh đã chọn"
              class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
            />

            <div
              class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition"
            >
              <Eye class="w-5 h-5 text-white" @click.stop="handleView(0)" />
              <Trash2
                class="w-5 h-5 text-white"
                @click.stop="handleDelete(0)"
              />
            </div>
          </div>
        </template>
      </div>

      <!-- Khi chưa có ảnh -->
      <div v-else class="flex flex-col items-center gap-1">
        <svg
          class="w-10 h-10 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <span class="text-gray-500 text-sm">Click để chọn ảnh</span>
      </div>
    </div>

    <!-- Error -->
    <span v-if="errorMessage" class="mt-1 block text-[12px] text-red-500">
      {{ errorMessage }}
    </span>

    <!-- Media Popup -->
    <MediaPopupComponent
      v-if="isOpen"
      :multiple="multiple"
      :selected-urls="multiple ? value : value ? [value] : []"
      :isOpen="isOpen"
      @close="isOpen = false"
      @select="handleSelect"
    />

    <!-- Lightbox -->
    <VueEasyLightbox
      :visible="previewVisible"
      :imgs="multiple ? value : [value]"
      :index="previewIndex"
      @hide="previewVisible = false"
    />
  </div>
</template>

<script setup>
import { useField } from "vee-validate";
import { ref, watch, computed } from "vue";
import { Eye, Trash2 } from "lucide-vue-next";
import VueEasyLightbox from "vue-easy-lightbox";
import MediaPopupComponent from "./MediaPopupComponent.vue";

const props = defineProps({
  multiple: { type: Boolean, default: false },
  modelValue: { type: [Array, String], default: () => [] },
  name: { type: String, required: true },
  label: { type: String, default: "" },
  required: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue"]);

// 🎯 Tích hợp useField
const { value, errorMessage, setTouched } = useField(props.name);

// Đồng bộ kiểu dữ liệu
watch(value, (newVal) => {
  if (props.multiple) {
    if (!Array.isArray(newVal)) {
      value.value = [];
    }
  } else {
    if (Array.isArray(newVal)) {
      value.value = "";
    }
  }
  emit("update:modelValue", newVal);
});

// Xử lý upload, delete, view logic
const isOpen = ref(false);
const previewVisible = ref(false);
const previewIndex = ref(0);

const hasValue = computed(() => {
  return props.multiple ? (value.value ?? []).length > 0 : !!value.value;
});

function openPopup() {
  isOpen.value = true;
}

function handleView(idx) {
  previewIndex.value = idx;
  previewVisible.value = true;
}

function handleDelete(idx) {
  if (props.multiple) {
    const temp = [...value.value];
    temp.splice(idx, 1);
    value.value = temp;
  } else {
    value.value = "";
  }
}

function handleSelect(selected) {
  const selectedUrls = selected.map((img) => img.path);

  if (props.multiple) {
    const currentValue = Array.isArray(value.value) ? value.value : [];
    const uniqueUrls = Array.from(new Set([...currentValue, ...selectedUrls]));
    value.value = uniqueUrls;
  } else {
    value.value = selected[0]?.path ?? "";
  }

  isOpen.value = false;
  setTouched(true);
}
</script>

<style scoped></style>
