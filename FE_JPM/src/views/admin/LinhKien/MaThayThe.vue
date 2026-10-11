<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Phụ Tùng /</span> Mã Thay Thế 
    </h4>


    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header border-bottom pb-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
              {{ isEditMode ? 'Cập Nhật Mã Thay Thế' : 'Thêm Mã Thay Thế' }}
            </h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Phụ Tùng Cũ (Bị thay thế) <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.phu_tung_cu_id"
                  :options="phuTungOptions"
                  placeholder="-- Chọn Phụ Tùng Cũ --"
                  :searchable="true"
                  :can-clear="true"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Phụ Tùng Mới (Sẽ thay thế) <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.phu_tung_moi_id"
                  :options="phuTungOptions"
                  placeholder="-- Chọn Phụ Tùng Mới --"
                  :searchable="true"
                  :can-clear="true"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Loại Thay Thế <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.loai_thay_the"
                  :options="[
                    {value: 'Thay thế trực tiếp', label: 'Thay thế trực tiếp (Direct)'},
                    {value: 'Thay thế một chiều', label: 'Thay thế một chiều (Forward Only)'},
                    {value: 'Thay thế cần adapter', label: 'Thay thế cần adapter (Requires Adapter)'}
                  ]"
                  :can-clear="false"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Ghi Chú (Nếu có)</label>
                <textarea class="form-control" rows="3" v-model="formData.ghi_chu" placeholder="Nhập ghi chú kỹ thuật..."></textarea>
              </div>

              <div class="mb-4">
                <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.trang_thai"
                  :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]"
                  :can-clear="false"
                />
              </div>

              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary" @click="resetForm" v-if="isEditMode">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary px-4" :disabled="mutationSave.isPending.value">
                  <span v-if="mutationSave.isPending.value" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  <i class="bx bx-save me-1" v-else></i> 
                  {{ isEditMode ? 'Cập Nhật' : 'Lưu Dữ Liệu' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Cột Danh Sách -->
      <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header border-bottom pb-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">Danh Sách Mã Thay Thế</h5>
            <div style="width: 250px;">
              <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm mã phụ tùng..." v-model="searchQuery">
            </div>
          </div>
            <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
              <table class="table table-hover mb-0">
                <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                  <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 35%">Phụ Tùng Cũ</th>
                    <th style="width: 35%">Phụ Tùng Mới</th>
                    <th style="width: 15%">Loại Thay Thế</th>
                    <th class="text-center" style="width: 10%">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="isPending">
                    <td colspan="5" class="text-center py-4">
                      <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredList.length === 0">
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                      Không có dữ liệu
                    </td>
                  </tr>
                  <tr v-else v-for="(item, index) in filteredList" :key="item.id">
                    <td class="text-center align-middle">{{ index + 1 }}</td>
                    <td class="align-middle">
                      <div class="fw-bold text-danger">{{ item.phu_tung_cu?.ma_phu_tung }}</div>
                      <div class="text-muted small">{{ item.phu_tung_cu?.ten_phu_tung }}</div>
                    </td>
                    <td class="align-middle">
                      <i class="bx bx-right-arrow-alt text-success me-1"></i>
                      <div class="fw-bold text-success d-inline-block">{{ item.phu_tung_moi?.ma_phu_tung }}</div>
                      <div class="text-muted small">{{ item.phu_tung_moi?.ten_phu_tung }}</div>
                    </td>
                    <td class="align-middle">
                      <span class="badge bg-label-info">{{ item.loai_thay_the }}</span>
                    </td>
                    <td class="text-center align-middle">
                      <div class="d-flex justify-content-center">
                        <button class="btn btn-sm btn-icon action-btn text-primary me-1" @click="editItem(item)" title="Sửa">
                          <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-icon action-btn text-danger" @click="confirmDelete(item.id)" title="Xóa">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredList && filteredList.length) ? filteredList.length : 0 }} mục</span>
      </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right", duration: 3000 });
const queryClient = useQueryClient();

// State
const activeTab = ref('MARINE');
const searchQuery = ref("");
const isEditMode = ref(false);

const formData = ref({
  id: null,
  phu_tung_cu_id: null,
  phu_tung_moi_id: null,
  loai_thay_the: 'Thay thế trực tiếp',
  ghi_chu: "",
  trang_thai: 1
});

// Switch Tab
const switchTab = (tab) => {
  activeTab.value = tab;
  resetForm();
};

// Fetch Data
const fetchApi = async (url) => {
  const res = await axiosClient.get(url);
  return res.data;
};

// Queries
const { data: rawPhuTungList } = useQuery({
  queryKey: ['phu-tung'],
  queryFn: () => fetchApi('/admin/phu-tung')
});

const { data: rawSupersessionList, isFetching, isPending } = useQuery({
  queryKey: ['ma-thay-the'],
  queryFn: () => fetchApi('/admin/ma-thay-the')
});

// Computed
const phuTungOptions = computed(() => {
  if (!rawPhuTungList.value) return [];
  // Lọc phụ tùng theo tab MARINE/AUTO
  const filtered = rawPhuTungList.value.data ? rawPhuTungList.value.data : (Array.isArray(rawPhuTungList.value) ? rawPhuTungList.value : []);
  return filtered
    .filter(pt => pt.trang_thai === 1 || pt.id === formData.value.phu_tung_cu_id || pt.id === formData.value.phu_tung_moi_id)
    .map(pt => ({
    value: pt.id,
    label: pt.ma_phu_tung + ' - ' + pt.ten_phu_tung
  }));
});

const filteredList = computed(() => {
  if (!rawSupersessionList.value) return [];
  
  const items = rawSupersessionList.value.data ? rawSupersessionList.value.data : (Array.isArray(rawSupersessionList.value) ? rawSupersessionList.value : []);
  return items.filter(item => {
    // Check if the parts belong to the current active ecosystem tab
    const isEcosystemMatch = true;
    
    if (!isEcosystemMatch) return false;

    if (!searchQuery.value) return true;
    
    const q = searchQuery.value.toLowerCase();
    const maCu = (item.phu_tung_cu?.ma_phu_tung || '').toLowerCase();
    const maMoi = (item.phu_tung_moi?.ma_phu_tung || '').toLowerCase();
    return maCu.includes(q) || maMoi.includes(q);
  });
});

// Reset
const resetForm = () => {
  isEditMode.value = false;
  formData.value = {
    id: null,
    phu_tung_cu_id: null,
    phu_tung_moi_id: null,
    loai_thay_the: 'Thay thế trực tiếp',
    ghi_chu: "",
    trang_thai: 1
  };
};

const editItem = (item) => {
  isEditMode.value = true;
  formData.value = {
    id: item.id,
    phu_tung_cu_id: item.phu_tung_cu_id,
    phu_tung_moi_id: item.phu_tung_moi_id,
    loai_thay_the: item.loai_thay_the || 'Thay thế trực tiếp',
    ghi_chu: item.ghi_chu || "",
    trang_thai: item.trang_thai !== undefined ? item.trang_thai : 1
  };
};

// Mutations
const mutationSave = useMutation({
  mutationFn: async (payload) => {
    if (isEditMode.value) {
      const res = await axiosClient.put(`/admin/ma-thay-the/${formData.value.id}`, payload);
      return res;
    } else {
      const res = await axiosClient.post('/admin/ma-thay-the', payload);
      return res;
    }
  },
  onMutate: async (newItem) => {
    // Cancel any outgoing refetches
    await queryClient.cancelQueries({ queryKey: ['ma-thay-the'] });
    const previousItems = queryClient.getQueryData(['ma-thay-the']);
    // Optimistic Update can be complex with relations, skipping detailed cache mutation here to rely on invalidate
    return { previousItems };
  },
  onError: (err, newItem, context) => {
    queryClient.setQueryData(['ma-thay-the'], context.previousItems);
    let msg = "Có lỗi xảy ra!";
    if (err.response?.data?.message) {
      msg = err.response.data.message;
    }
    toaster.error(msg);
  },
  onSuccess: (res) => {
    if (res.success) {
      toaster.success(res.message);
      resetForm();
    }
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['ma-thay-the'] });
  }
});

const saveData = () => {
  if (formData.value.phu_tung_cu_id === formData.value.phu_tung_moi_id) {
    toaster.error("Phụ tùng thay thế không được trùng với phụ tùng cũ!");
    return;
  }
  mutationSave.mutate(formData.value);
};

const mutationDelete = useMutation({
  mutationFn: (id) => axiosClient.delete(`/admin/ma-thay-the/${id}`),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['ma-thay-the'] });
    const previousItems = queryClient.getQueryData(['ma-thay-the']);
    queryClient.setQueryData(['ma-thay-the'], old => (old || []).filter(item => item.id !== id));
    return { previousItems };
  },
  onSuccess: () => {
    toaster.success("Đã xóa mã thay thế!");
  },
  onError: (err, id, context) => {
    queryClient.setQueryData(['ma-thay-the'], context.previousItems);
    toaster.error("Có lỗi xảy ra khi xóa!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['ma-thay-the'] });
  }
});

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Xác nhận xóa?',
    text: "Bạn có chắc chắn muốn xóa mã thay thế này?",
    icon: 'warning',
          iconColor: '#ff3e1d',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý xóa',
    cancelButtonText: 'Hủy bỏ'
  }).then((result) => {
    if (result.isConfirmed) {
      mutationDelete.mutate(id);
    }
  });
};

</script>

<style scoped>
.custom-line-tab {
  cursor: pointer;
  border-bottom: 2px solid transparent;
  color: #6c757d;
  transition: all 0.3s;
}
.custom-line-tab.active {
  border-bottom-color: #696cff;
  color: #696cff;
}
.custom-line-tab:hover:not(.active) {
  color: #5f61e6;
}
.action-btn {
  background: transparent;
  border: none;
  font-size: 1.15rem;
  padding: 0.25rem 0.5rem;
  transition: transform 0.2s;
}
.action-btn:hover {
  transform: scale(1.15);
}
</style>




