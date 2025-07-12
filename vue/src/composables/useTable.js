import { ref, computed, watch } from 'vue'
import { message } from 'ant-design-vue'
import axiosInstance from '@/config/axios.js'

export default function useTable(options = {}) {
  const {
    api, // string
    defaultParams = {},
    pageSize = 10,
    manual = false,
    enableRowSelection = true,
    enableArchive = true,
    debounceDelay = 500,
  } = options

  if (!api || typeof api !== 'string') {
    throw new Error("useTable: 'api' bắt buộc là một string endpoint.")
  }

  const loading = ref(false)
  const dataSource = ref([])
  const total = ref(0)

  const pagination = ref({
    current: 1,
    pageSize,
    showSizeChanger: true,
    pageSizeOptions: ['10', '20', '50', '100'],
    showTotal: (total) => `Total ${total} items`,
  })

  const filters = ref({ ...defaultParams })
  const sorter = ref({ field: null, order: null })
  const selectedRowKeys = ref([])
  const searchText = ref('')
  const archive = ref(1)

  let debounceTimeout = null

  const fetchData = async () => {
    loading.value = true
    try {
      const params = {
        page: pagination.value.current,
        per_page: pagination.value.pageSize,
        ...filters.value,
      }

      if (searchText.value) {
        params['searchText'] = searchText.value
      }

      if (archive.value && enableArchive) {
        params['archive'] = archive.value
      }

      if (sorter.value.field && sorter.value.order) {
        params.sort_field = sorter.value.field
        params.sort_order = sorter.value.order === 'ascend' ? 'asc' : 'desc'
      }

      const res = await axiosInstance.get(api, { params })

      const responseData = res.data.data

      dataSource.value = responseData.items || []
      total.value = responseData.total || 0
      pagination.value.total = total.value
    } catch (err) {
      message.error(err.message || 'Fetch data failed')
    } finally {
      loading.value = false
    }
  }

  const handleTableChange = (pag, filtersFromTable, sorterFromTable) => {
    pagination.value.current = pag.current
    pagination.value.pageSize = pag.pageSize

    if (sorterFromTable?.field) {
      sorter.value.field = sorterFromTable.field
      sorter.value.order = sorterFromTable.order
    } else {
      sorter.value = { field: null, order: null }
    }

    fetchData()
  }

  const onSearch = (val) => {
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
      pagination.value.current = 1
      searchText.value = val
      fetchData()
    }, debounceDelay)
  }

  const resetFilters = () => {
    filters.value = { ...defaultParams }
    searchText.value = ''
    archive.value = 1
    pagination.value.current = 1
    fetchData()
  }

  const handleBulkAction = async (action, modelName) => {
    if (!selectedRowKeys.value.length) {
      message.warning('Vui lòng chọn ít nhất một mục.')
      return
    }

    try {
      loading.value = true

      const endpoint = `/bulk/${modelName}/${action}`

      const response = await axiosInstance.post(endpoint, {
        ids: selectedRowKeys.value,
      })

      selectedRowKeys.value = []
      fetchData()
      message.success(response.data.message)
    } catch (err) {
      console.log(err)

      message.error(err.response.data.message || err.message)
    } finally {
      loading.value = false
    }
  }

  const handleArchiveFilter = (status) => {
    archive.value = status
    pagination.value.current = 1
    fetchData()
  }

  watch(
    () => pagination.value.pageSize,
    () => {
      pagination.value.current = 1
      fetchData()
    },
  )

  if (!manual) {
    fetchData()
  }

  return {
    loading,
    dataSource,
    pagination,
    fetchData,
    handleTableChange,
    onSearch,
    resetFilters,
    filters,
    sorter,
    selectedRowKeys,
    searchText,
    archive,
    handleBulkAction,
    handleArchiveFilter,
    rowSelection: enableRowSelection
      ? computed(() => ({
          selectedRowKeys: selectedRowKeys.value,
          onChange: (keys) => {
            selectedRowKeys.value = keys
          },
        }))
      : null,
  }
}
