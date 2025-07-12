<template>
  <div>
    <label v-if="label" class="block mb-1 font-medium">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <QuillEditor v-model:content="value" toolbar="full" theme="snow" content-type="html" />

    <span v-if="errorMessage" class="text-red-500 text-sm mt-1 block">
      {{ errorMessage }}
    </span>
  </div>
</template>

<script setup>
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { QuillEditor } from '@vueup/vue-quill'
import { useField } from 'vee-validate'

// Props
const props = defineProps({
  required: {
    type: [Boolean, String],
    default: false,
  },
  label: {
    type: String,
    required: true,
  },
  labelClass: {
    type: String,
    default: 'mb-1 block text-sm font-medium text-gray-900',
  },
  name: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
})

// Vee-validate useField
const { value, errorMessage } = useField(props.name)
</script>

<style>
.ql-editor {
  min-height: 300px;
}
</style>
