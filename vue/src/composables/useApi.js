import { ref } from "vue";
import axiosInstance from "@/config/axios.js";

export default function useApi(basePath) {
  const loading = ref(false);
  const error = ref(null);
  const data = ref(null);

  const getAll = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get(basePath, { params });
      data.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getOne = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get(`${basePath}/${id}`);
      data.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const create = async (payload) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.post(basePath, payload);
      data.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const update = async (id, payload) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.put(`${basePath}/${id}`, payload);
      data.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const destroy = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.delete(`${basePath}/${id}`);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    data,
    loading,
    error,
    getAll,
    getOne,
    create,
    update,
    destroy,
  };
}
