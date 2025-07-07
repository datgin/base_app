<template>
  <div class="">
    <label v-if="props.label" :for="props.name" :class="props.labelClass">
      {{ props.label }}
      <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
    </label>
    <!-- Date Picker -->
    <a-date-picker
      v-if="props.type === 'date'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :show-time="props.showTime"
      :placeholder="props.placeholder"
      :format="props.format"
      @change="handleChange"
    />

    <!-- Date Range Picker -->
    <a-range-picker
      v-if="props.type === 'date-range'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :show-time="props.showTime"
      :placeholder="props.rangePlaceholder"
      :format="props.format"
      @change="handleChange"
    />

    <!-- Time Picker -->
    <a-time-picker
      v-if="props.type === 'time'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :placeholder="props.placeholder"
      :format="props.timeFormat"
      @change="handleChange"
    />

    <!-- Time Range Picker -->
    <a-time-range-picker
      v-if="props.type === 'time-range'"
      v-model:value="innerValue"
      :class="className"
      :id="props.name"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :placeholder="props.rangePlaceholder"
      :format="props.timeFormat"
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
  type: { type: String, default: "date" }, // 'date', 'date-range', 'time', 'time-range'
  required: { type: [Boolean, String], default: false },
  label: { type: String, default: "" },
  labelClass: {
    type: String,
    default: "mb-2 block text-sm font-medium text-gray-900",
  },
  name: { type: String, required: true },
  className: { type: String, default: "w-full" },
  size: { type: String, default: "large" },
  showTime: { type: Boolean, default: false },
  placeholder: { type: String, default: "Vui lòng chọn thời gian" },
  rangePlaceholder: {
    type: Array,
    default: () => ["Chọn bắt đầu", "Chọn kết thúc"],
  },
  format: { type: String, default: "DD/MM/YYYY" },
  timeFormat: { type: String, default: "HH:mm:ss" },
});

// Emits
const emits = defineEmits(["onChange"]);

// VeeValidate
const { value, errorMessage } = useField(props.name);

// Inner value để binding với Ant Design Picker
const innerValue = ref(null);

// Đồng bộ ngược lại khi innerValue thay đổi để lưu dữ liệu string
watch(
  innerValue,
  (newVal) => {
    let formattedValue;
    if (props.type === "date") {
      formattedValue = newVal ? dayjs(newVal).format(props.format) : "";
    } else if (props.type === "date-range") {
      formattedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d).format(props.format) : ""))
          : null;
    } else if (props.type === "time") {
      formattedValue = newVal ? dayjs(newVal).format(props.timeFormat) : "";
    } else if (props.type === "time-range") {
      formattedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d).format(props.timeFormat) : ""))
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

// Đồng bộ khi value bên ngoài thay đổi (VD: reset form)
watch(
  value,
  (newVal) => {
    let parsedValue;
    if (props.type === "date") {
      parsedValue = newVal ? dayjs(newVal, props.format) : null;
    } else if (props.type === "date-range") {
      parsedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d, props.format) : null))
          : null;
    } else if (props.type === "time") {
      parsedValue = newVal ? dayjs(newVal, props.timeFormat) : null;
    } else if (props.type === "time-range") {
      parsedValue =
        Array.isArray(newVal) && newVal.length === 2
          ? newVal.map((d) => (d ? dayjs(d, props.timeFormat) : null))
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

// Event khi user change
const handleChange = (val) => {
  emits("onChange", val);
};
</script>

<style scoped></style>
