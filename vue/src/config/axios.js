import axios from "axios";
import { useAuthStore } from "@/stores/authStore";
import router from "@/router";

const axiosInstance = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL + "/api",
  headers: { "Content-Type": "application/json" },
});

const updateLastActivity = () => {
  localStorage.setItem("lastActivity", Date.now());
};

const checkInactivity = () => {
  const lastActivity = localStorage.getItem("lastActivity");
  const currentTime = Date.now();
  if (lastActivity && currentTime - lastActivity > 15 * 60 * 1000) {
    console.warn("⚠️ Phiên làm việc hết hạn do không hoạt động");
    const auth = useAuthStore();
    auth.logout();
  }
};
setInterval(checkInactivity, 60000);

axiosInstance.interceptors.request.use(
  async (config) => {
    const auth = useAuthStore();

    if (auth.token) {
      updateLastActivity();
      const currentTime = Date.now();

      // const tokenExpiryTime = new Date(auth.tokenExpiry).toLocaleString(
      //   "vi-VN"
      // );
      // const currentTimeFormatted = new Date(currentTime).toLocaleString(
      //   "vi-VN"
      // );
      // const preExpiryTime = new Date(
      //   auth.tokenExpiry - 5 * 60 * 1000
      // ).toLocaleString("vi-VN");
      // const minutesLeft = Math.floor((auth.tokenExpiry - currentTime) / 60000);

      // console.log("🕒 Thời gian hết hạn token:", tokenExpiryTime);
      // console.log("🕒 Thời gian hiện tại:", currentTimeFormatted);
      // console.log("🕒 Sẽ làm mới token trước:", preExpiryTime);
      // console.log(
      //   `⏳ Còn lại khoảng: ${minutesLeft} phút trước khi hết hạn token`
      // );

      if (auth.tokenExpiry && currentTime > auth.tokenExpiry - 5 * 60 * 1000) {
        console.log("🔄 Token sắp hết hạn → đang làm mới...");
        await auth.refreshToken(); // ⚠️ GỌI AN TOÀN vì dùng axiosRaw bên trong
      }

      config.headers["Authorization"] = `Bearer ${auth.token}`;
    }

    return config;
  },
  (error) => Promise.reject(error)
);

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    const auth = useAuthStore();
    if (error.response) {
      if (error.response.status === 401) {
        auth.logout();
      } else if (error.response.status === 403) {
        router.push("/forbidden");
      }
    }
    return Promise.reject(error);
  }
);

export default axiosInstance;
