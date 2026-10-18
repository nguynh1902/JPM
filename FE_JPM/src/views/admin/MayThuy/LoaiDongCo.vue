<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Động cơ & Phương tiện /</span> Loại Động Cơ 
    </h4>

    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4" v-if="showForm">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Loại Động Cơ' : 'Thêm Mới Loại Động Cơ' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-4">
                <label for="name" class="form-label fw-medium text-dark">Tên Loại Động Cơ <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  class="form-control"
                  placeholder="Nhập tên loại động cơ..."
                  v-model="formData.ten_loai_dong_co"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="status" class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.trang_thai"
                  :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]"
                  :can-clear="false"
                />
              </div>

              <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" @click="resetForm">
                  Hủy
                </button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="saveMutation.isPending.value">
                  <span v-if="saveMutation.isPending.value" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  {{ isEditMode ? 'Cập Nhật' : 'Thêm Mới' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Cột Bảng (Danh sách) -->
      <div :class="showForm ? 'col-md-8' : 'col-md-12'" class="mb-4">
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Danh sách Loại Động Cơ</h5>
            <div style="width: 250px;">
              <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm mã, tên..." v-model="searchQuery">
            </div>
          </div>

          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th class="text-center" style="width: 5%">STT</th>
                  <th style="width: 30%">Mã Loại Động Cơ</th>
                  <th :style="{ width: canManage ? '35%' : '50%' }">Tên Loại Động Cơ</th>
                  <th class="text-center" style="width: 15%">Trạng thái</th>
                  <th class="text-center" style="width: 15%" v-if="canManage">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isPending">
                  <td :colspan="canManage ? 5 : 4" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="!filteredLoaiDongCos || filteredLoaiDongCos.length === 0">
                  <td :colspan="canManage ? 5 : 4" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in filteredLoaiDongCos" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td><span class="fw-bold text-primary">{{ item.ma_loai_dong_co }}</span></td>
                  <td>
                    <span class="fw-bold text-dark">{{ item.ten_loai_dong_co }}</span>
                  </td>
                  <td class="text-center">
                    <span v-if="item.trang_thai === 1 || item.trang_thai === true" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                    <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
                  </td>
                  <td class="text-center" v-if="canManage">
                    <div class="d-flex align-items-center justify-content-center">
                      <button 
                        v-if="canEdit"
                        type="button" 
                        class="action-btn text-primary me-1" 
                        @click="openEditModal(item)"
                        title="Sửa"
                      >
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button 
                        v-if="canDelete"
                        type="button" 
                        class="action-btn text-danger" 
                        @click="confirmDelete(item)" 
                        title="Xóa"
                      >
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
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredLoaiDongCos && filteredLoaiDongCos.length) ? filteredLoaiDongCos.length : 0 }} mục</span>
      </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useAuthStore } from '../../../stores/authStore';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right", duration: 3000 });
const queryClient = useQueryClient();
const authStore = useAuthStore();

const canAdd = computed(() => authStore.isAdmin || authStore.hasPermission('Thêm Động cơ & Thương hiệu') || authStore.hasPermission('Quản lý Model & Động cơ'));
const canEdit = computed(() => authStore.isAdmin || authStore.hasPermission('Sửa Động cơ & Thương hiệu') || authStore.hasPermission('Quản lý Model & Động cơ'));
const canDelete = computed(() => authStore.isAdmin || authStore.hasPermission('Xóa Động cơ & Thương hiệu') || authStore.hasPermission('Quản lý Model & Động cơ'));
const canManage = computed(() => canEdit.value || canDelete.value);
const showForm = computed(() => canAdd.value || (canEdit.value && isEditMode.value));

// State
const activeTab = ref('MARINE');
const searchQuery = ref('');
const isEditMode = ref(false);
const formData = ref({
  id: null,
  ma_loai_dong_co: "",
  ten_loai_dong_co: "",
  trang_thai: 1
});

// Reset Form
const resetForm = () => {
  isEditMode.value = false;
  formData.value = {
    id: null,
    ma_loai_dong_co: "",
    ten_loai_dong_co: "",
    trang_thai: 1
  };
};

// Mở modal sửa
const openEditModal = (item) => {
  isEditMode.value = true;
  formData.value = { ...item };
};

// Vue Query: Fetch Data
const { data: loaiDongCos, isFetching, isPending } = useQuery({
  queryKey: ['engineTypes'],
  queryFn: async () => {
    const response = await axiosClient.get('/admin/loai-dong-co');
    return response.data || response;
  }
});
const filteredLoaiDongCos = computed(() => {
  let list = Array.isArray(loaiDongCos.value) ? loaiDongCos.value : (loaiDongCos.value?.data || []);
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter(item => 
      String(item.ma_loai_dong_co || '').toLowerCase().includes(q) || 
      String(item.ten_loai_dong_co || '').toLowerCase().includes(q)
    );
  }
  return list;
});

// Vue Query: Optimistic Mutation (Add/Edit)
const saveMutation = useMutation({
  mutationFn: async (payload) => {
    if (payload.id) {
      return await axiosClient.put(`/admin/loai-dong-co/${payload.id}`, payload);
    }
    return await axiosClient.post('/admin/loai-dong-co', payload);
  },
  onError: (err, newRecord, context) => {
    // Rollback on error
    toaster.error(err.response?.data?.message || "Có lỗi xảy ra khi lưu dữ liệu");
  },
  onSuccess: () => {
    toaster.success(isEditMode.value ? "Cập nhật thành công!" : "Đã thêm mới!");
    resetForm();
  },
  onSettled: () => {
    // Luôn fetch lại data thật từ DB
    queryClient.invalidateQueries({ queryKey: ['engineTypes'] });
  }
});


  const getNextCode = () => {
    let maxNum = 0;
    let list = [];
    if (loaiDongCos.value) {
      list = Array.isArray(loaiDongCos.value) ? loaiDongCos.value : (loaiDongCos.value.data || []);
    }
    list.forEach(item => {
      if (item.ma_loai_dong_co && item.ma_loai_dong_co.startsWith('LDC')) {
        const num = parseInt(item.ma_loai_dong_co.replace('LDC', ''), 10);
        if (!isNaN(num) && num > maxNum) maxNum = num;
      }
    });
    return "LDC" + String(maxNum + 1).padStart(2, '0');
  };

  const saveData = () => {
    if (!formData.value.ten_loai_dong_co) {
      toaster.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
      return;
    }
    
    if (!isEditMode.value) {
      // Backend will auto-generate code
    }
    
    saveMutation.mutate({ ...formData.value,  }, { onSuccess: () => { resetForm(); } });
  };


// Vue Query: Optimistic Mutation (Delete)
const deleteMutation = useMutation({
  mutationFn: async (id) => {
    return await axiosClient.delete(`/admin/loai-dong-co/${id}`);
  },
  onError: (err, id, context) => {
    toaster.error(err.response?.data?.message || "Lỗi khi xóa!");
  },
  onSuccess: () => {
    toaster.success("Đã xóa thành công!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['engineTypes'] });
  }
});

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: 'Xác nhận xóa',
    html: `Bạn có chắc chắn muốn xóa <b>"${item.ten_loai_dong_co}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu liên kết có thể bị ảnh hưởng.</small>` ,
    icon: 'warning',
          iconColor: '#ff3e1d',
    showCancelButton: true,
    confirmButtonText: 'Xóa ngay',
    cancelButtonText: 'Hủy',
          reverseButtons: true,
    customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-danger px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
    buttonsStyling: false
  });

  if (result.isConfirmed) {
    if (isEditMode.value && formData.value.id === item.id) { resetForm(); }
      deleteMutation.mutate(item.id);
  }
};
</script>





