<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Hệ thống /</span> Lịch sử hoạt động
    </h4>

    <div class="card">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Nhật Ký Hệ Thống (Audit Log)</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
          <div style="width: 220px;">
            <v-select
              v-model="filters.user_id"
              :options="employeeOptions"
              placeholder="Chọn Nhân Viên"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 220px;">
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
          <div class="d-flex align-items-center gap-2" style="max-width: 360px;">
            <input type="date" class="form-control text-muted" v-model="filters.date_from" title="Từ ngày">
            <span class="text-muted fw-medium text-nowrap">đến</span>
            <input type="date" class="form-control text-muted" v-model="filters.date_to" title="Đến ngày">
          </div>
          <button class="btn btn-secondary px-3 text-nowrap" @click="resetFilters" :disabled="isFetching && !logs.length">
            Đặt lại
          </button>
          <button class="btn btn-primary px-4 shadow-sm text-nowrap" @click="applyFilters" :disabled="isFetching && !logs.length">
            <i class="bx bx-search me-1"></i> Tra cứu
          </button>
        </div>
      </div>

      <!-- Bảng Dữ Liệu -->
      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 280px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 15%">Thời Gian</th>
              <th style="width: 20%">Nhân Viên</th>
              <th class="text-center" style="width: 15%">Hành Động</th>
              <th style="width: 35%">Mô Tả Chi Tiết</th>
              <th style="width: 10%">Địa Chỉ IP</th>
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
              <td colspan="6" class="text-center py-5 text-secondary">Không tìm thấy lịch sử hoạt động nào phù hợp.</td>
            </tr>
            <tr v-for="(item, index) in logs" :key="item.id || index">
              <td class="text-center"><span class="fw-bold text-dark">{{ index + 1 }}</span></td>
              <td>
                <div v-if="item.created_at" class="d-flex flex-column">
                  <span class="fw-medium text-dark">{{ item.created_at.split(' ')[0] }}</span>
                  <small class="fw-semibold text-secondary">{{ item.created_at.split(' ')[1] }}</small>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="d-inline-flex justify-content-center align-items-center border rounded me-3 bg-white" style="width: 50px; height: 50px; flex-shrink: 0;">
                    <img v-if="item.avatar" :src="item.avatar" alt="Avatar" style="max-height: 35px; max-width: 40px; object-fit: contain;">
                    <i v-else class="bx bx-image text-muted fs-4"></i>
                  </div>
                  <div>
                    <span class="fw-bold text-primary d-block">{{ item.user_name }}</span>
                    <small class="text-muted text-capitalize">{{ item.role }}</small>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <span class="badge" :class="getActionBadgeClass(item.action)" style="width: 100px;">
                  {{ getActionLabel(item.action) }}
                </span>
              </td>
              <td>
                <span class="fw-medium text-dark">{{ item.description }}</span>
              </td>
              <td>
                <span class="fw-semibold text-secondary">{{ item.ip_address }}</span>
              </td>
            </tr>
            
            <tr v-if="nextCursor">
              <td colspan="6" class="text-center py-4 bg-transparent border-0">
                <button class="btn btn-outline-primary" @click="loadMore" :disabled="isFetching">
                  <span v-if="isPending" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  <i v-else class="bx bx-down-arrow-alt me-1"></i>
                  Tải thêm lịch sử cũ hơn...
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (logs && logs.length) ? logs.length : 0 }} mục</span>
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
      user_id: filters.user_id,
      action_type: filters.action_type,
      date_from: filters.date_from,
      date_to: filters.date_to,
      cursor: cursor
    };
    const res = await axiosClient.get('/admin/system-logs', { params });
    // res is the unwrapped body due to interceptor.
    // res.data contains { data: [...], meta: { next_cursor: ... } }
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

