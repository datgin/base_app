import MasterLayout from "@/layouts/MasterLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import authGuard from "@//middleware/auth.js";
import userRoutes from "@/router/user.js";
import productRoutes from "@/router/product.js";
import exampleRoutes from "@/router/example.js";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/admin",
      component: MasterLayout,
      children: [
        {
          path: "",
          name: "dashboard",
          component: () => import("@/pages/DashboardView.vue"),
        },
        ...userRoutes,
        ...productRoutes,
        ...exampleRoutes,
      ],
    },
    {
      path: "/admin/login",
      name: "login",
      component: () => import("@/pages/LoginView.vue"),
    },
    {
      path: "/:pathMatch(.*)*",
      redirect: "/admin",
    },
  ],
});

router.beforeEach(authGuard);

export default router;
