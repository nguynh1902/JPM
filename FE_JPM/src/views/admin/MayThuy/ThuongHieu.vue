<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Máy Thủy /</span> Thương Hiệu
    </h4>

    

    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Thương Hiệu' : 'Thêm Mới Thương Hiệu' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              
              

<div class="mb-4">
                <label for="name" class="form-label fw-medium text-dark">Tên Thương Hiệu <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  class="form-control"
                  placeholder="Nhập tên thương hiệu..."
                  v-model="formData.name"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="logo" class="form-label fw-medium text-dark">Logo Thương Hiệu</label>
                <input
                  type="file"
                  id="logo"
                  class="form-control mb-2"
                  accept="image/png, image/jpeg, image/svg+xml"
                  @change="handleLogoUpload"
                />
                <div class="d-flex justify-content-center align-items-center border rounded bg-light mt-2 position-relative" style="height: 180px;">
                  <img v-if="formData.logo_path" :src="getImageUrl(formData.logo_path)" style="max-height: 160px; max-width: 100%; object-fit: contain;">
                  <button v-if="formData.logo_path" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeLogo" style="width: 28px; height: 28px; padding: 0;" title="Xóa logo">
                    <i class="bx bx-trash"></i>
                  </button>
                  <span class="text-muted" v-else><i class='bx bx-image fs-1'></i></span>
                </div>
              </div>

              <div class="mb-4">
                <label for="status" class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.status"
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
        <div class="col-md-8 mb-4">
          <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-dark">Danh sách Thương Hiệu</h5>
              
            </div>

          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th class="text-center" style="width: 5%">STT</th>
                  <th class="text-center" style="width: 10%">Logo</th>
                  <th style="width: 25%">Mã Thương Hiệu</th>
                  <th style="width: 30%">Tên Thương Hiệu</th>
                  <th class="text-center" style="width: 15%">Trạng thái</th>
                  <th class="text-center" style="width: 15%">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isPending">
                  <td colspan="6" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="(!brands || brands.length === 0)">
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in brands" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td class="text-center">
                    <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 60px; height: 60px; background-color: #fff;">
                      <img v-if="item.logo_path" :src="getImageUrl(item.logo_path)" alt="Logo" style="max-height: 40px; max-width: 50px; object-fit: contain;" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'">
                      <i class="bx bx-image text-muted fs-3" :style="{ display: item.logo_path ? 'none' : 'inline-block' }"></i>
                    </div>
                  </td>
                  <td><span class="fw-bold text-primary">{{ item.code }}</span></td>
                  <td>
                    <span class="fw-bold text-dark">{{ item.name }}</span>
                  </td>
                  <td class="text-center">
                    <span v-if="item.status === 1 || item.status === true" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                    <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <button 
                        type="button" 
                        class="action-btn text-primary me-1" 
                        @click="openEditModal(item)"
                        title="Sửa"
                      >
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button 
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
          <span class="text-muted fw-medium">Tổng cộng: {{ (brands ? brands.length : 0) }} mục</span>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default { name: 'ThuongHieu' }
</script>

<script setup>
import { ref, computed, watch } from 'vue';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right", duration: 3000 });
const queryClient = useQueryClient();

// State
const activeTab = ref('MARINE');
const searchQuery = ref('');
const isEditMode = ref(false);
const logoFile = ref(null);
const formData = ref({
  id: null,
  code: "",
  name: "",
  logo_path: "",
  
  status: 1
});

// Switch Tab
const switchTab = (tab) => {
  activeTab.value = tab;
  resetForm();
};


  const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('blob:') || path.startsWith('http')) return path;
  if (path.startsWith('/client/image/')) return path; // Use frontend public asset directly
  return `http://localhost:8000/storage/${path.replace(/^\/?storage\//, '')}`;
};

// Reset Form
const resetForm = () => {
  isEditMode.value = false;
  formData.value = {
    id: null,
    code: "",
    name: "",
    logo_path: "",
    
    status: 1
  };
  logoFile.value = null;
  const fileInput = document.getElementById('logo');
  if (fileInput) fileInput.value = '';
};

const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    logoFile.value = file;
    formData.value.logo_path = URL.createObjectURL(file); 
  } else {
    logoFile.value = null;
  }
};

const removeLogo = () => {
  logoFile.value = null;
  formData.value.logo_path = "";
  const fileInput = document.getElementById('logo');
  if (fileInput) fileInput.value = '';
};

const openEditModal = (item) => {
  isEditMode.value = true;
  formData.value = { ...item };
  logoFile.value = null;
  const fileInput = document.getElementById('logo');
  if (fileInput) fileInput.value = '';
};

// Vue Query: Fetch Data
const { data: brands, isFetching, isPending } = useQuery({
  queryKey: ['brands'],
  queryFn: async () => {
    const response = await axiosClient.get('/admin/thuong-hieu');
    return response.data || response;
  }
});

const filteredBrands = computed(() => {
  let list = Array.isArray(brands.value) ? brands.value : (brands.value?.data || []);
  list = list;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(b => b.code?.toLowerCase().includes(q) || b.name?.toLowerCase().includes(q));
  }
  return list;
});

// Vue Query: Optimistic Mutation (Add/Edit)
const saveMutation = useMutation({
  mutationFn: async (payloadParams) => {
    const payload = new FormData();
    payload.append('code', formData.value.code);
    payload.append('name', formData.value.name);
    
    payload.append('status', formData.value.status ? 1 : 0);
    
    if (logoFile.value) {
      payload.append('logo', logoFile.value);
    } else if (isEditMode.value && !formData.value.logo_path) {
      payload.append('logo', '');
    }

    if (isEditMode.value) {
      payload.append('_method', 'PUT');
      return await axiosClient.post(`/admin/thuong-hieu/${formData.value.id}`, payload, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    return await axiosClient.post('/admin/thuong-hieu', payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  },
  onError: (err, variables, context) => {
    toaster.error(err.response?.data?.message || "Có lỗi xảy ra khi lưu dữ liệu");
  },
  onSuccess: () => {
    toaster.success(isEditMode.value ? "Cập nhật thương hiệu thành công!" : "Đã thêm thương hiệu mới!");
    resetForm();
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['brands'] });
  }
});


  

  const saveData = () => {
    if (!formData.value.name) {
      toaster.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
      return;
    }
    
    
    
    saveMutation.mutate();
  };


// Vue Query: Optimistic Mutation (Delete)
const deleteMutation = useMutation({
  mutationFn: async (id) => {
    return await axiosClient.delete(`/admin/thuong-hieu/${id}`);
  },
  onError: (err, id, context) => {
    toaster.error(err.response?.data?.message || "Lỗi khi xóa thương hiệu");
  },
  onSuccess: () => {
    toaster.success("Đã xóa thương hiệu!");
  },
  onSettled: () => {
    queryClient.invalidateQueries({ queryKey: ['brands'] });
  }
});

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: 'Xác nhận xóa',
    html: `Bạn có chắc chắn muốn xóa thương hiệu <b>"${item.name}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu liên kết có thể bị ảnh hưởng.</small>` ,
    icon: 'warning',
          iconColor: '#ff3e1d',
    showCancelButton: true,
    confirmButtonText: 'Xóa Thương hiệu',
    cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
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

<style scoped>
.custom-line-tab {
  cursor: pointer;
  min-width: 160px;
  text-align: center;
  color: #a1acb8; /* text-muted */
  border-bottom: 3px solid transparent;
  transition: all 0.2s ease;
}
.custom-line-tab.active {
  color: var(--bs-primary);
  border-bottom: 3px solid var(--bs-primary);
}
.custom-line-tab:hover:not(.active) {
  color: #697a8d;
}
</style>









