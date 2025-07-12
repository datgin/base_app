/**
 * Auth Middleware Guard for Vue Router
 * - Redirects to /admin/login if not logged in
 * - Redirects to /admin if already logged in and trying to access /admin/login
 */
export default function authGuard(to, from, next) {
  const isLoggedIn = !!localStorage.getItem('token')

  // Chặn vào login nếu đã đăng nhập
  if (to.name === 'login' && isLoggedIn) {
    return next('/admin')
  }

  // Chặn vào các route admin nếu chưa đăng nhập
  if (to.path.startsWith('/admin') && to.name !== 'login' && !isLoggedIn) {
    return next('/admin/login')
  }

  next()
}
