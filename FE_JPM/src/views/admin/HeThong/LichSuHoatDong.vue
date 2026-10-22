<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ thống /</span> Lịch sử hoạt động
    </h4>

    <div class="card">
      <!-- Card Header chuẩn DonHang.vue -->
      <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bx bx-history text-primary fs-4"></i>
            <span>Nhật Ký Hệ Thống (Audit Log)</span>
          </h5>
          <small class="text-muted">Theo dõi toàn bộ lịch sử thao tác của nhân sự trên hệ thống</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <button 
            type="button" 
            class="btn btn-outline-primary btn-sm shadow-sm"
            @click="applyFilters"
            :disabled="isFetching"
            title="Làm mới danh sách"
          >
            <i class="bx bx-refresh me-1" :class="{ 'bx-spin': isFetching }"></i>
            Làm mới
          </button>
        </div>
      </div>

      <!-- Bộ lọc (Filters) chuẩn DonHang.vue -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <!-- Ô tìm kiếm nội dung / IP chuẩn DonHang.vue -->
          <div class="flex-grow-1" style="min-width: 240px;">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
              <input 
                type="text" 
                class="form-control border-start-0 ps-0" 
                placeholder="Tìm kiếm nội dung mô tả, địa chỉ IP, tên nhân viên..." 
                v-model="filters.search"
                @keyup.enter="applyFilters"
              >
              <button v-if="filters.search" class="btn btn-outline-secondary" type="button" @click="filters.search = ''; applyFilters()">
                <i class="bx bx-x"></i>
              </button>
            </div>
          </div>

          <!-- Chọn Nhân Viên -->
          <div style="width: 185px;" class="flex-shrink-0">
            <v-select
              v-model="filters.user_id"
              :options="employeeOptions"
              placeholder="Chọn Nhân Viên"
              :searchable="true"
              :can-clear="false"
            />
          </div>

          <!-- Loại Hành Động -->
          <div style="width: 175px;" class="flex-shrink-0">
            <v-select
              v-model="filters.action_type"
              :options="[
                {value: '', label: 'Tất cả hành động'},
                {value: 'LOGIN', label: 'Đăng nhập'},
                {value: 'CREATE', label: 'Thêm mới'},
                {value: 'UPDATE', label: 'Cập nhật'},
                {value: 'DELETE', label: 'Xóa'}
              ]"
              placeholder="Loại Hành Động"
              :can-clear="false"
            />
          </div>

          <!-- Khoảng ngày Từ - Đến -->
          <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <input 
              type="date" 
              class="form-control text-muted px-2" 
              v-model="filters.date_from" 
              title="Từ ngày"
              style="width: 135px;"
            >
            <span class="text-muted fw-medium text-nowrap">đến</span>
            <input 
              type="date" 
              class="form-control text-muted px-2" 
              v-model="filters.date_to" 
              title="Đến ngày"
              style="width: 135px;"
            >
          </div>

          <!-- Nhóm nút bấm -->
          <button class="btn btn-secondary px-3 text-nowrap flex-shrink-0" @click="resetFilters" :disabled="isFetching && !logs.length">
            Đặt lại
          </button>
          <button class="btn btn-primary text-nowrap shadow-sm flex-shrink-0" @click="applyFilters" :disabled="isFetching && !logs.length">
            <i class="bx bx-search me-1"></i> Tra cứu
          </button>
        </div>
      </div>

      <!-- Bảng Dữ Liệu chuẩn DonHang.vue -->
      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 290px); min-height: 420px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead style="position: sticky; top: 0; z-index: 2; background: #fff;">
            <tr>
              <th class="text-center text-uppercase" style="width: 5%">STT</th>
              <th class="text-uppercase" style="width: 14%">Thời Gian</th>
              <th class="text-uppercase" style="width: 22%">Nhân Viên</th>
              <th class="text-center text-uppercase" style="width: 13%">Hành Động</th>
              <th class="text-uppercase" style="width: 33%">Mô Tả Chi Tiết</th>
              <th class="text-uppercase" style="width: 13%">Địa Chỉ IP</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isFetching && logs.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in logs" :key="item.id || index">
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <div v-if="item.created_at">
                  <span class="d-block text-dark fw-medium mb-1" style="font-size: 0.85rem;">
                    <i class="bx bx-calendar-alt text-primary me-1"></i>{{ item.created_at.split(' ')[0] }}
                  </span>
                  <span class="text-muted" style="font-size: 0.75rem;">
                    <i class="bx bx-time-five me-1"></i>{{ item.created_at.split(' ')[1] }}
                  </span>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-2 flex-shrink-0">
                    <img 
                      v-if="item.avatar" 
                      :src="item.avatar" 
                      alt="Avatar" 
                      class="rounded-circle"
                      style="width: 32px; height: 32px; object-fit: cover;"
                    />
                    <span v-else class="avatar-initial rounded-circle bg-label-primary fw-semibold" style="width: 32px; height: 32px; line-height: 32px; font-size: 0.8rem; display: inline-block; text-align: center;">
                      {{ (item.user_name || 'U').charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">{{ item.user_name }}</span>
                    <span class="text-muted text-capitalize" style="font-size: 0.78rem;">{{ item.role }}</span>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <span class="badge badge-custom" :class="getActionBadgeClass(item.action)">
                  <i :class="getActionIcon(item.action)" class="me-1"></i>{{ getActionLabel(item.action) }}
                </span>
              </td>
              <td>
                <span class="fw-medium text-dark text-wrap" style="font-size: 0.9rem;">{{ item.description }}</span>
              </td>
              <td>
                <span class="text-muted font-monospace" style="font-size: 0.85rem;">
                  <i class="bx bx-globe me-1 text-secondary"></i>{{ item.ip_address }}
                </span>
              </td>
            </tr>
            
            <tr v-if="nextCursor">
              <td colspan="6" class="text-center py-4 bg-transparent border-0">
                <button class="btn btn-outline-primary btn-sm shadow-sm" @click="loadMore" :disabled="isFetching">
                  <span v-if="isFetching" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  <i v-else class="bx bx-down-arrow-alt me-1"></i>
                  Tải thêm nhật ký cũ hơn...
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer chuẩn DonHang.vue -->
      <div class="card-footer py-3 border-top d-flex justify-content-between align-items-center bg-white">
        <span class="text-muted fw-medium" style="font-size: 0.85rem;">
          Hiển thị <strong>{{ logs.length }}</strong> bản ghi nhật ký
        </span>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';

const employees = ref([]);
const logs = ref([]);
const nextCursor = ref(null);
const isFetching = ref(false);

const filters = reactive({
  search: "",
  user_id: null,
  action_type: "",
  date_from: "",
  date_to: ""
});

const employeeOptions = computed(() => {
  const options = employees.value.map(e => ({ value: (e.id_tai_khoan || e.id), label: e.name || e.ho_ten }));
  return [{value: null, label: 'Tất cả nhân viên'}, ...options];
});

const fetchEmployees = async () => {
  try {
    const res = await axiosClient.get('/admin/nhan-vien');
    employees.value = res?.data?.data || res?.data || res || [];
  } catch (error) {
    console.error(error);
  }
};

const fetchLogs = async (cursor = null) => {
  if (isFetching.value) return;
  isFetching.value = true;
  try {
    const params = {
      search: filters.search,
      user_id: filters.user_id,
      action_type: filters.action_type,
      date_from: filters.date_from,
      date_to: filters.date_to,
      cursor: cursor
    };
    const res = await axiosClient.get('/admin/system-logs', { params });
    const dataObj = res.data || {};
    
    if (cursor) {
      logs.value.push(...(dataObj.data || []));
    } else {
      logs.value = dataObj.data || [];
    }
    
    nextCursor.value = dataObj.meta?.next_cursor || null;
  } catch (error) {
    console.error(error);
  } finally {
    isFetching.value = false;
  }
};

const applyFilters = () => {
  fetchLogs();
};

const resetFilters = () => {
  filters.search = "";
  filters.user_id = null;
  filters.action_type = "";
  filters.date_from = "";
  filters.date_to = "";
  fetchLogs();
};

const loadMore = () => {
  if (nextCursor.value) {
    fetchLogs(nextCursor.value);
  }
};

onMounted(() => {
  fetchEmployees();
  fetchLogs();
});

const getActionBadgeClass = (action) => {
  switch (action) {
    case 'LOGIN': return 'bg-pastel-info';
    case 'CREATE': return 'bg-pastel-success';
    case 'UPDATE': return 'bg-pastel-warning';
    case 'DELETE': return 'bg-pastel-danger';
    default: return 'bg-pastel-secondary';
  }
};

const getActionIcon = (action) => {
  switch (action) {
    case 'LOGIN': return 'bx bx-log-in-circle';
    case 'CREATE': return 'bx bx-plus-circle';
    case 'UPDATE': return 'bx bx-edit-alt';
    case 'DELETE': return 'bx bx-trash';
    default: return 'bx bx-info-circle';
  }
};

const getActionLabel = (action) => {
  switch (action) {
    case 'LOGIN': return 'ĐĂNG NHẬP';
    case 'CREATE': return 'THÊM MỚI';
    case 'UPDATE': return 'CẬP NHẬT';
    case 'DELETE': return 'XÓA';
    default: return 'KHÁC';
  }
};
</script>

<style scoped>
.badge-custom {
  border-radius: 0.25rem;
  padding: 0.4em 0.8em;
  font-size: 0.75rem;
  font-weight: 600;
}
.custom-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #d9dee3;
  border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}
</style>
