<template>
  <div>
    <BreadcrumbComponent :items="items" />

    <TableActions
      @search="onSearch"
      @reload="resetFilters"
      @bulk-action="handleBulkAction"
      @filter-archive="handleArchiveFilter"
      :searchText="searchText"
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
      />
    </CardComponent>
  </div>
</template>

<script setup>
import useTable from "@/composables/useTable.js";
import CardComponent from "@/components/ui/CardComponent.vue";

import { ref } from "vue";

import { BreadcrumbComponent } from "@/components/layout";
import TableActions from "../../components/ui/TableActions.vue";

const items = ref([{ title: "Khách hàng" }]);

const columns = [
  { title: "ID", dataIndex: "id", sorter: true, width: "6%" },
  { title: "Name", dataIndex: "name", sorter: true },
  { title: "Email", dataIndex: "email", sorter: true },
];

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
} = useTable({
  api: "/users",
  pageSize: 10,
  enableArchive: false,
});
</script>

<style lang="scss" scoped></style>
