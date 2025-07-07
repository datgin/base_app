<template>
  <div class="w-full">
    <!-- Label -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <!-- Custom Switch -->
    <div
      :class="[
        'relative inline-block cursor-pointer transition-colors rounded-md',
        isChecked ? 'bg-[#1e8c32]' : 'bg-gray-300',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]"
      class="w-14 h-7"
      @click="toggle"
    >
      <span
        :class="[
          'absolute top-[4px] bg-white transition-transform rounded-sm shadow',
        ]"
        :style="{
          width: '20px',
          height: '20px',
          transform: isChecked ? 'translateX(30px)' : 'translateX(5px)',
        }"
      ></span>
    </div>

    <!-- Error -->
    <div v-if="errorMessage" class="text-red-500 text-sm mt-1">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useField } from "vee-validate";

const props = defineProps({
  name: { type: String, required: true },
  label: { type: String, default: "" },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "onChange"]);

// VeeValidate field
const { value, errorMessage, setTouched } = useField(props.name);

// Trạng thái bật/tắt
const isChecked = computed(() => !!value.value);

// Toggle
function toggle() {
  if (props.disabled) return;
  value.value = !isChecked.value;
  emit("update:modelValue", isChecked.value);
  emit("onChange", isChecked.value);
  setTouched(true);
}
</script>
