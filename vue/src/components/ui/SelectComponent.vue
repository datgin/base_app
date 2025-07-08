<template>
  <div>
    <label v-if="label" :for="name" :class="labelClass">
      {{ label }}
      <span v-if="required" class="font-semibold text-red-500">(*)</span>
    </label>

    <a-select
      v-bind="selectBindings"
      :options="options"
      :mode="mode"
      :show-search="showSearch"
      :filter-option="filterOption"
      :placeholder="placeholder"
      :status="errorMessage ? 'error' : ''"
      :size="size"
      :allow-clear="allowClear"
      :id="name"
      class="w-full"
    />

    <span v-if="errorMessage" class="mt-[6px] block text-[12px] text-red-500">{{
      errorMessage
    }}</span>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useField } from "vee-validate";

const props = defineProps({
  name: { type: String, required: true },
  label: { type: String, default: "" },
  labelClass: {
    type: String,
    default: "mb-2 block text-sm font-medium text-gray-900",
  },
  required: { type: [Boolean, String], default: false },
  placeholder: { type: String, default: "Chọn..." },
  size: { type: String, default: "large" },
  allowClear: { type: Boolean, default: true },
  options: { type: Array, required: true }, // [{ label: '', value: '' }]
  multiple: { type: Boolean, default: false },
  allowInput: { type: Boolean, default: false }, // Cho phép nhập ngoài options
  showSearch: { type: Boolean, default: true },
});

// Mode của select
const mode = computed(() => (props.multiple ? "multiple" : undefined));

// Tạo field với vee-validate
const { value, errorMessage } = useField(props.name, undefined, {
  validateOnValueUpdate: true,
});

// Logic filter option khi search:
const filterOption = computed(() => {
  if (props.allowInput) {
    return true; // Cho nhập tự do
  }
  // Chỉ lọc option tồn tại, không cho nhập ngoài
  return (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
  };
});

// Bindings
const selectBindings = computed(() => ({
  value: value.value,
  "onUpdate:value": (val) => (value.value = val),
}));
</script>
