export default [
  {
    path: 'products',
    name: 'products.index',
    component: () => import('@/pages/product/IndexPage.vue'),
  },
  {
    path: 'products/save',
    name: 'products.save',
    component: () => import('@/pages/product/SavePage.vue'),
  },
]
