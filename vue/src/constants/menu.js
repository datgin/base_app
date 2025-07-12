import { Home, Users, Package, FlaskConical } from 'lucide-vue-next'

export const menuItems = [
  {
    name: 'Tổng quan',
    routeName: 'dashboard',
    icon: Home,
  },
  {
    name: 'Khách hàng',
    routeName: 'users.index',
    icon: Users,
  },
  {
    name: 'Examples',
    routeName: 'examples.index',
    icon: FlaskConical,
  },
  {
    name: 'Sản phẩm',
    icon: Package,
    children: [
      {
        name: 'Quản lý sản phẩm',
        routeName: 'products.index',
      },
    ],
  },
]
