import { defineStore } from 'pinia'
import { ref } from 'vue'
import router from '@/router'
import axiosRaw from 'axios'
import useCrud from '@/composables/useCrud.js'
import { message } from 'ant-design-vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const tokenExpiry = ref(parseInt(localStorage.getItem('tokenExpiry') || '0'))

  const { getOne, post, data, error, loading } = useCrud()

  async function login(payload) {
    try {
      const response = await post('/auth/login', payload)

      token.value = response.access_token
      tokenExpiry.value = Date.now() + response.expires_in * 1000

      localStorage.setItem('token', token.value)
      localStorage.setItem('tokenExpiry', tokenExpiry.value.toString())

      await getUser()
      router.push('/admin')
    } catch (err) {
      error.value = err.response?.data?.error || 'Đăng nhập thất bại'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function getUser() {
    try {
      await getOne('/auth/me')

      user.value = data.value
    } catch (err) {
      console.log(err)
      message.error(error.value)
    }
  }

  async function refreshToken() {
    try {
      const response = await axiosRaw.post(
        import.meta.env.VITE_APP_API_URL + '/api/auth/refresh-token',
        {},
        {
          headers: {
            Authorization: `Bearer ${token.value}`,
          },
        },
      )

      token.value = response.data.token
      tokenExpiry.value = Date.now() + response.data.expires_in * 1000

      localStorage.setItem('token', token.value)
      localStorage.setItem('tokenExpiry', tokenExpiry.value.toString())

      console.log('✅ Token đã được làm mới thành công')
    } catch (err) {
      console.error('❌ Làm mới token thất bại', err)
      logout()
    }
  }

  function logout() {
    token.value = null
    tokenExpiry.value = null
    user.value = null
    localStorage.clear()
    router.push('/admin/login')
  }

  return {
    user,
    token,
    tokenExpiry,
    error,
    loading,
    login,
    getUser,
    refreshToken,
    logout,
  }
})
