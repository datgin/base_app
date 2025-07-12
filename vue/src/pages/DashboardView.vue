<template>
  <div>
    <!-- Breadcrumbs nằm ngoài bg-gray-50 -->
    <Breadcrumbs />

    <!-- Phần nền bg-gray-50 -->
    <div class="mt-4">
      <form @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
          <div class="md:col-span-9">
            <CardComponent>
              <div class="mb-3">
                <InputComponent
                  name="name"
                  label="Tên sản phẩm"
                  placeholder="Nhập tên sản phẩm"
                  required
                />
              </div>
              <div class="mb-3">
                <SelectComponent
                  name="attribute_id"
                  label="Thuộc tính"
                  :options="options"
                  placeholder="Chọn thuộc tính"
                  required
                  :multiple="true"
                />
              </div>
              <div class="mb-3">
                <MediaComponent name="thumbnails" :multiple="true" />
              </div>
            </CardComponent>
          </div>
          <div class="md:col-span-3">
            <SubmitComponent />

            <CardComponent>
              <MediaComponent name="image" />
            </CardComponent>

            <CardComponent>
              <DateComponent
                name="start_date"
                label="Ngày bắt đầu"
                type="date"
                required
                :showTime="false"
              />
            </CardComponent>

            <CardComponent>
              <DateComponent
                name="end_date"
                label="Ngày kết thúc"
                type="date"
                required
                :showTime="true"
              />
            </CardComponent>

            <CardComponent>
              <DateComponent name="date_range" label="Khoảng ngày" type="date-range" required />
            </CardComponent>
            <CardComponent>
              <FormSwitchComponent
                name="is_active"
                label="Trạng thái"
                checked-label="Bật"
                unchecked-label="Tắt"
                required
              />
            </CardComponent>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import Breadcrumbs from '../components/layout/BreadcrumbComponent.vue'
import InputComponent from '../components/ui/InputComponent.vue'
import SubmitComponent from '../components/ui/SubmitComponent.vue'

import CardComponent from '@/components/ui/CardComponent.vue'

import SelectComponent from '@/components/ui/SelectComponent.vue'
import MediaComponent from '@/components/ui/MediaComponent.vue'

import { useForm } from 'vee-validate'

const options = [
  { label: 'Sách', value: 'sach' },
  { label: 'Điện tử', value: 'dien-tu' },
  { label: 'Thời trang', value: 'thoi-trang' },
]

import * as yup from 'yup'
import DateComponent from '../components/ui/DateComponent.vue'
import FormSwitchComponent from '../components/ui/FormSwitchComponent.vue'

const schema = yup.object({
  attribute_id: yup.mixed().required('Vui lòng chọn thuộc tính'),
  name: yup.string().required('Vui lòng chọn tên sản phẩm'),
  image: yup.string().required('Ảnh đại diện là bắt buộc'),
  thumbnails: yup
    .array()
    .min(1, 'Bắt buộc chọn ít nhất 1 ảnh')
    .required('Ảnh đại diện là bắt buộc'),
  start_date: yup.string().required('Vui lòng chọn ngày'),
  end_date: yup.string().required('Vui lòng chọn ngày'),
  date_range: yup
    .array()
    .of(yup.string())
    .length(2, 'Vui lòng chọn khoảng ngày')
    .required('Vui lòng chọn khoảng ngày'),
  is_active: yup
    .boolean()
    .required('Ảnh đại diện là bắt buộc')
    .oneOf([true], 'Bạn cần bật trạng thái'),
})

const { handleSubmit } = useForm({
  validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
  console.log(values)
})
</script>
