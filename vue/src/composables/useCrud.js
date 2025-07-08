// composables/useCrud.js
import { ref } from "vue";
import axios from "@/config/axios.js";

export default function useCrud() {
  const loading = ref(false);
  const error = ref(null);
  const data = ref(null);
  const meta = ref(null);

  const resetStatus = () => {
    loading.value = true;
    error.value = null;
    meta.value = null;
  };

  const getList = async (url, params = {}) => {
    resetStatus();
    try {
      const res = await axios.get(url, { params });
      const { data: payload, ...rest } = res.data;
      
      data.value = payload;
      meta.value = rest;
    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Đã có lỗi xảy ra!";
    } finally {
      loading.value = false;
    }
  };

  const getOne = async (url) => {
    resetStatus();
    try {
      const res = await axios.get(url);
      const { data: payload, ...rest } = res.data;
      data.value = payload;
      meta.value = rest;
    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Đã có lỗi xảy ra!";
    } finally {
      loading.value = false;
    }
  };

  const post = async (url, payload) => {
    resetStatus();
    try {
      const res = await axios.post(url, payload);
      const { data: result, ...rest } = res.data;
      meta.value = rest;
      return result;
    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Đã có lỗi xảy ra!";
    } finally {
      loading.value = false;
    }
  };

  const put = async (url, payload) => {
    resetStatus();
    try {
      const res = await axios.put(url, payload);
      const { data: result, ...rest } = res.data;
      meta.value = rest;
      return result;
    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Đã có lỗi xảy ra!";
    } finally {
      loading.value = false;
    }
  };

  const uploadMultipart = async (url, payload = {}, method = "post") => {
    resetStatus();
    try {
      const formData = new FormData();
      for (const [key, value] of Object.entries(payload)) {
        if (Array.isArray(value)) {
          value.forEach((v) => formData.append(`${key}[]`, v));
        } else {
          formData.append(key, value);
        }
      }

      if (method.toLowerCase() === "put") {
        formData.append("_method", "PUT");
      }

      const res = await axios.post(url, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      const { data: result, ...rest } = res.data;
      meta.value = rest;
      return result;
    } catch (err) {
      console.error(err);
      error.value = err.response?.data?.message || "Đã có lỗi xảy ra!";
    } finally {
      loading.value = false;
    }
  };

  return {
    data,
    meta,
    loading,
    error,
    getList,
    getOne,
    post,
    put,
    uploadMultipart,
  };
}
