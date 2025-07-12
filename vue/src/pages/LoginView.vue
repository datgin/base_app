<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-md p-8">
      <div class="text-center mb-6">
        <img src="/images/logo-dark.png" alt="Logo" class="h-12 mx-auto mb-2" />
        <h1 class="text-2xl font-semibold text-gray-800">Đăng nhập</h1>
      </div>

      <form @submit="onSubmit" class="space-y-5">
        <InputComponent
          name="email"
          label="Email"
          maxlength="100"
          required
          placeholder="Nhập email"
        />

        <InputComponent
          name="password"
          label="Mật khẩu"
          type="password"
          required
          placeholder="Nhập mật khẩu"
          :showCount="false"
        />

        <div class="flex items-center justify-between">
          <label class="flex items-center space-x-2 text-sm text-gray-600">
            <InputComponent name="remember" label="Ghi nhớ đăng nhập" typeInput="checkbox" />
          </label>
          <a href="#" class="text-sm text-blue-600 hover:underline">Quên mật khẩu?</a>
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full bg-blue-600 cursor-pointer hover:bg-blue-700 text-white font-medium py-3 rounded-md transition disabled:opacity-50"
        >
          <Loader2 v-if="authStore.loading" class="animate-spin h-5 w-5 inline-block mr-2" />
          {{ authStore.loading ? 'Đang xử lý...' : 'Đăng nhập' }}
        </button>
      </form>

      <div class="mt-6">
        <button
          @click="handleGoogleLogin"
          class="w-full flex cursor-pointer items-center justify-center border border-gray-300 rounded-md py-3 text-gray-700 hover:bg-gray-50 transition"
        >
          <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
            <!-- Google icon paths -->
            <path
              fill="#4285F4"
              d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
            />
            <path
              fill="#34A853"
              d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
            />
            <path
              fill="#FBBC05"
              d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
            />
            <path
              fill="#EA4335"
              d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
            />
          </svg>
          Đăng nhập với Google
        </button>
      </div>

      <div class="mt-6 text-center text-sm text-gray-600">
        Bạn chưa có tài khoản?
        <a href="/register" class="text-blue-600 hover:underline font-medium">Đăng ký ngay</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import InputComponent from '../components/ui/InputComponent.vue'
import { message } from 'ant-design-vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

const schema = yup.object({
  email: yup.string().required().email(),
  password: yup.string().required().min(8),
  remember: yup.boolean(),
})

const { handleSubmit } = useForm({
  validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
  try {
    await authStore.login(values)
    message.success('Đăng nhập thành công!')
  } catch (err) {
    console.log(err)
    message.error(authStore.error || 'Đăng nhập thất bại')
  }
})

function handleGoogleLogin() {
  alert('Chức năng đăng nhập Google sẽ được triển khai')
}
</script>
