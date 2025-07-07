<template>
  <div class="">
    <label
      v-if="
        props.label &&
        props.typeInput !== 'checkbox' &&
        props.typeInput !== 'radio'
      "
      :for="props.name"
      :class="props.labelClass"
    >
      {{ props.label }}
      <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
    </label>

    <!-- a-input -->
    <a-input
      v-if="props.typeInput === 'text' && props.type !== 'password'"
      v-bind="inputBindings"
    />

    <!-- a-input-password -->
    <a-input-password
      v-if="props.typeInput === 'text' && props.type === 'password'"
      v-bind="inputBindings"
    />

    <!-- a-textarea -->
    <a-textarea
      v-if="props.typeInput === 'textarea'"
      v-bind="textareaBindings"
    />

    <!-- a-checkbox -->
    <a-checkbox
      v-if="props.typeInput === 'checkbox'"
      v-model:checked="value"
      :class="props.className"
    >
      {{ props.label }}
      <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
    </a-checkbox>

    <!-- a-radio -->
    <a-radio-group
      v-if="props.typeInput === 'radio'"
      v-model:value="value"
      :options="props.options"
      :class="props.className"
    />

    <span v-if="errorMessage" class="mt-1 block text-[12px] text-red-500">
      {{ errorMessage }}
    </span>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useField } from "vee-validate";

const props = defineProps({
  typeInput: { type: String, default: "text" }, // text, textarea, checkbox, radio
  required: { type: [Boolean, String], default: false },
  label: { type: String, default: "" },
  labelClass: {
    type: String,
    default: "mb-2 block text-sm font-medium text-gray-900",
  },
  name: { type: String, required: true },
  className: { type: String, default: "" },
  type: { type: String, default: "text" }, // for text input/password
  placeholder: { type: String, default: "" },
  size: { type: String, default: "large" },
  maxlength: { type: [String, Number, Boolean], default: 0 },
  options: { type: Array, default: () => [] }, // for radio
});

// VeeValidate useField
const { value, errorMessage } = useField(props.name, undefined, {
  validateOnValueUpdate: true, // realtime validation
});

// Bindings for a-input and a-input-password
const inputBindings = computed(() => ({
  value: value.value,
  "onUpdate:value": (val) => (value.value = val),
  id: props.name,
  type: props.type,
  placeholder: props.placeholder,
  status: errorMessage.value ? "error" : "",
  size: props.size,
  allowClear: true,
  class: props.className, // bo góc 4px đồng nhất
}));

// Bindings for a-textarea
const textareaBindings = computed(() => ({
  ...inputBindings.value,
  autoSize: { minRows: 2, maxRows: 50 },
  showCount: true,
  maxlength: props.maxlength > 0 ? props.maxlength : undefined,
}));
</script>
