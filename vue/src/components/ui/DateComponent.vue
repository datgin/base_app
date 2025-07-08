<template>
  <div>
    <label v-if="props.label" :for="props.name" :class="props.labelClass">
      {{ props.label }}
      <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
    </label>

    <!-- Date Picker -->
    <a-date-picker
      v-if="props.type === 'date' || props.type === 'datetime'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :show-time="props.type === 'datetime'"
      :placeholder="props.placeholder"
      :format="props.format"
      @change="handleChange"
    />

    <!-- Date Range Picker -->
    <a-range-picker
      v-if="props.type === 'range-date' || props.type === 'range-datetime'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :show-time="props.type === 'range-datetime'"
      :placeholder="props.rangePlaceholder"
      :format="props.format"
      @change="handleChange"
    />

    <span v-if="errorMessage" class="mt-[6px] block text-[12px] text-red-500">
      {{ errorMessage }}
    </span>
  </div>
</template>

<script setup>
import { useField } from "vee-validate";
import { ref, watch } from "vue";
import dayjs from "dayjs";
import isEqual from "lodash/isEqual";

// Props
const props = defineProps({
  type: { type: String, default: "date" }, // 'date', 'datetime', 'range-date', 'range-datetime'
  required: { type: [Boolean, String], default: false },
  label: { type: String, default: "" },
  labelClass: {
    type: String,
    default: "mb-1 block text-sm font-medium text-gray-900",
  },
  name: { type: String, required: true },
  className: { type: String, default: "w-full" },
  size: { type: String, default: "large" },
  placeholder: { type: String, default: "Vui lòng chọn thời gian" },
  rangePlaceholder: {
    type: Array,
    default: () => ["Chọn bắt đầu", "Chọn kết thúc"],
  },
  format: { type: String, default: "DD/MM/YYYY" },
});

// Emits
const emits = defineEmits(["onChange"]);

// VeeValidate
const { value, errorMessage } = useField(props.name);

// Inner value để binding với Ant Design Picker
const innerValue = ref(null);

// Đồng bộ innerValue -> value
watch(
  innerValue,
  (newVal) => {
    let formattedValue;

    if (props.type === "date" || props.type === "datetime") {
      formattedValue = newVal ? dayjs(newVal).format(props.format) : "";
    } else if (props.type === "range-date" || props.type === "range-datetime") {
      formattedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d).format(props.format) : ""))
          : [];
    } else {
      formattedValue = newVal;
    }

    if (!isEqual(value.value, formattedValue)) {
      value.value = formattedValue;
    }
  },
  { deep: true }
);

// Đồng bộ value -> innerValue (VD: reset form)
watch(
  value,
  (newVal) => {
    let parsedValue;

    if (props.type === "date" || props.type === "datetime") {
      parsedValue = newVal ? dayjs(newVal, props.format) : null;
    } else if (props.type === "range-date" || props.type === "range-datetime") {
      parsedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d, props.format) : null))
          : [];
    } else {
      parsedValue = newVal;
    }

    if (!isEqual(innerValue.value, parsedValue)) {
      innerValue.value = parsedValue;
    }
  },
  { immediate: true }
);

// Emit onChange khi user change
const handleChange = (val) => {
  emits("onChange", val);
};
</script>

<style scoped></style>
