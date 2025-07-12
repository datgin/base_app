<template>
  <div>
    <BreadcrumbComponent :items="items" />

    <form @submit="onSubmit">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-9">
          <CardComponent>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
              <div class="md:col-span-12">
                <MediaComponent label="Thư viện ảnh" name="albums" :multiple="true" />
              </div>
              <div class="md:col-span-4">
                <DateComponent name="date" label="Ngày" placeholder="Chọn ngày " />
              </div>
              <div class="md:col-span-4">
                <InputComponent
                  typeInput="number"
                  label="Views"
                  name="views"
                  placeholder="Nhập views"
                />
              </div>
              <div class="md:col-span-4">
                <InputComponent
                  typeInput="number"
                  label="Price"
                  name="price"
                  placeholder="Nhập giá"
                />
              </div>
              <div class="md:col-span-12">
                <InputComponent
                  typeInput="textarea"
                  label="Mô tả"
                  name="description"
                  placeholder="Nhập mô tả"
                />
              </div>

              <div class="md:col-span-12">
                <EditorComponent name="content" label="Nội dung" />
              </div>
            </div>
          </CardComponent>
        </div>
        <div class="md:col-span-3">
          <SubmitComponent :loading="loading" />

          <CardComponent title="Ảnh nổi bật">
            <MediaComponent name="image" />
          </CardComponent>

          <CardComponent title="Trạng thái">
            <SelectComponent name="status" :options="options" />
          </CardComponent>

          <CardComponent title="is active">
            <FormSwitchComponent name="is_active" />
          </CardComponent>

          <CardComponent title="published">
            <FormSwitchComponent name="published" />
          </CardComponent>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { BreadcrumbComponent } from '@/components/layout'
import {
  CardComponent,
  SubmitComponent,
  MediaComponent,
  DateComponent,
  SelectComponent,
  InputComponent,
  EditorComponent,
  FormSwitchComponent,
} from '@/components/ui'
import { stripHtml } from '@/utils/helpers.js'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import useCrud from '@/composables/useCrud.js'
import { message } from 'ant-design-vue'

const { post, loading, error } = useCrud()

const state = reactive({
  title: 'Tạo mới Example',
})

const options = [
  { label: 'bản nháp', value: 'draft' },
  { label: 'được xuất bản', value: 'published' },
  { label: 'đã lưu trữ', value: 'archived' },
]

const schema = yup.object({
  image: yup.string().required('Ảnh nổi bật không được để trống!'),
  albums: yup
    .array()
    .min(1, 'Phải chọn ít nhất 1 ảnh')
    .required('Thư viện ảnh không được để trông!'),
  date: yup.string().required('Ngày không được để trống!'),
  status: yup.string().required('Trạng thái không được để trống!'),
  content: yup
    .string()
    .test('is-empty', 'Nội dung không được để trống!', (value) => stripHtml(value || '').length > 0)
    .required('Nội dung không được để trống!'),
  views: yup.number().positive('Phải là số dương').required('Không được bỏ trống'),
  price: yup.number().min(0).required(),
  description: yup.string().trim().required('Mô tả không được để trống!'),
  is_active: yup.boolean().oneOf([true], 'Bạn phải bật trạng thái hoạt động!'),
})

const { handleSubmit } = useForm({
  validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
  try {
    await post('/examples', values)
  } catch (err) {
    console.log(err)
    message.error(error.value)
  }
})

const items = ref([{ title: 'Example', routeName: 'examples.index' }, { title: state.title }])
</script>

<style lang="scss" scoped></style>
