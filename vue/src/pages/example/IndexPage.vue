<template>
  <div>
    <BreadcrumbComponent :items="items" />

    <TableActions
      @search="onSearch"
      @reload="resetFilters"
      @bulk-action="handleBulkAction"
      @filter-archive="handleArchiveFilter"
      :searchText="searchText"
      :archive="archive"
      :modelName="state.modelName"
      :routeName="state.routeName"
    />

    <CardComponent>
      <a-table
        :loading="loading"
        :dataSource="dataSource"
        :pagination="pagination"
        @change="handleTableChange"
        :columns="columns"
        rowKey="id"
        :rowSelection="rowSelection"
      >
        <template #bodyCell="{ column, text, record }">
          <!-- Hiển thị ảnh nếu là cột image -->
          <template v-if="column.dataIndex === 'image'">
            <img
              v-if="column.dataIndex === 'image' && text"
              :src="text"
              alt="Image"
              class="w-12 h-12 object-cover rounded"
            />
          </template>

          <template v-if="column.dataIndex === 'is_active'">
            <span
              v-if="text"
              class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400"
              >Green
            </span>

            <span
              v-else
              class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400"
              >Red</span
            >
          </template>

          <template v-if="column.dataIndex === 'published'">
            <TableSwitchComponent
              :record="record"
              :field="column.dataIndex"
              modelName="Example"
            />
          </template>
        </template>
      </a-table>
    </CardComponent>
  </div>
</template>

<script setup>
import useTable from "@/composables/useTable.js";
import CardComponent from "@/components/ui/CardComponent.vue";
import TableSwitchComponent from "@/components/ui/TableSwitchComponent.vue";

import { ref } from "vue";

import { BreadcrumbComponent } from "@/components/layout";
import TableActions from "@/components/ui/TableActions.vue";

const items = ref([{ title: "Example" }]);

const columns = [
  { title: "ID", dataIndex: "id", sorter: true, width: "6%" },
  { title: "image", dataIndex: "image" },
  { title: "date", dataIndex: "date", sorter: true },
  { title: "status", dataIndex: "status", sorter: true },
  { title: "views", dataIndex: "views", sorter: true },
  { title: "is_active", dataIndex: "is_active", sorter: true },
  { title: "price (₫)", dataIndex: "price", sorter: true },
  { title: "published", dataIndex: "published", sorter: true },
];

const state = ref({
  modelName: "Example",
  routeName: "examples.create",
});

const {
  searchText,
  loading,
  dataSource,
  pagination,
  handleTableChange,
  onSearch,
  resetFilters,
  rowSelection,
  handleBulkAction,
  handleArchiveFilter,
  archive,
} = useTable({
  api: "/examples",
  pageSize: 10,
});
</script>

<style lang="scss" scoped></style>
