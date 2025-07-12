<template>
  <div class="w-full">
    <div
      :class="[
        'relative inline-block cursor-pointer transition-colors rounded-md',
        isChecked ? 'bg-[#1e8c32]' : 'bg-gray-300',
        loading ? 'opacity-50 cursor-wait' : '',
      ]"
      class="w-12 h-6"
      @click="toggle"
    >
      <span
        :class="['absolute top-[3px] bg-white transition-transform rounded-sm shadow']"
        :style="{
          width: '18px',
          height: '18px',
          transform: isChecked ? 'translateX(25px)' : 'translateX(5px)',
        }"
      ></span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import useCrud from '@/composables/useCrud.js'
import { message } from 'ant-design-vue'

const { post, meta, error, loading } = useCrud()

const props = defineProps({
  record: { type: Object, required: true },
  field: { type: String, required: true },
  modelName: { type: String, required: true },
})

// Xác định trạng thái check: record[field] == 1 là bật
const isChecked = computed(() => props.record[props.field] == 1)

// Toggle và call API
async function toggle() {
  const newValue = isChecked.value ? 2 : 1

  try {
    await post(`/bulk/${props.modelName}/publish`, {
      ids: [props.record.id],
    })
    // eslint-disable-next-line vue/no-mutating-props
    props.record[props.field] = newValue
    message.success(meta.value.message)
  } catch (err) {
    console.log(err)

    message.error(error.value)
  }
}
</script>

<style lang="scss" scoped></style>
