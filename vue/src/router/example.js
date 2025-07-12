export default [
  {
    path: 'examples',
    name: 'examples.index',
    component: () => import('@/pages/example/IndexPage.vue'),
  },
  {
    path: 'examples/save',
    name: 'examples.create',
    component: () => import('@/pages/example/SavePage.vue'),
  },
]
