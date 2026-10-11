<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Dữ Liệu Nền Tảng /</span> Cấu Hình Hệ Thống 
    </h4>


    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Cấu Hình Hệ Thống' : 'Thêm Mới Cấu Hình Hệ Thống' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="row">
                <div class="col-md-12 mb-4">
                  <label for="code" class="form-label fw-medium text-dark">Mã Cấu Hình Hệ Thống <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    id="code"
                    class="form-control text-uppercase"
                    placeholder="VD: YAM"
                    v-model="formData.code"
                    required
                    :disabled="isEditMode"
                  />
                </div>
                </div>
              
              <div class="mb-4">
                <label for="name" class="form-label fw-medium text-dark">Tên Cấu Hình Hệ Thống <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="name"
                  class="form-control"
                  placeholder="Nhập tên Cấu Hình Hệ Thống..."
                  v-model="formData.name"
                  required
                />
              </div>

              <div class="mb-4">
                <label for="logo" class="form-label fw-medium text-dark">Logo Cấu Hình Hệ Thống</label>
                <input
                  type="file"
                  id="logo"
                  class="form-control"
                  accept="image/png, image/jpeg, image/svg+xml"
                  @change="handleLogoUpload"
                />
                <div v-if="formData.logo_path" class="mt-2 border rounded p-1 d-inline-block bg-white position-relative" style="padding-right: 30px !important;">
                  <img :src="getImageUrl(formData.logo_path)" alt="Logo preview" style="height: 40px; object-fit: contain;">
                  <button type="button" class="btn btn-icon btn-sm text-danger position-absolute top-50 translate-middle-y end-0" @click="removeLogo" title="Xóa ảnh"><i class="bx bx-trash"></i></button>
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
                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
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
            <h5 class="mb-0 fw-bold text-dark">Danh sách Cấu hình</h5>
          </div>

          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th class="text-center" style="width: 5%">STT</th>
                  <th class="text-center" style="width: 10%">Logo</th>
                  <th style="width: 25%">Mã Cấu Hình Hệ Thống</th>
                  <th style="width: 30%">Tên Cấu Hình Hệ Thống</th>
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
                <tr v-else-if="filteredBrands.length === 0">
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in filteredBrands" :key="item.id" v-else>
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
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredBrands && filteredBrands.length) ? filteredBrands.length : 0 }} mục</span>
      </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';

export default {
  name: "CauHinhHeThong",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const toaster = window.toaster || { success: () => {}, error: () => {} };

    const fetchBrands = async () => {
      const response = await axiosClient.get('/admin/thuong-hieu');
      return response.data;
    };

    const { data: brands, isFetching, isPending } = useQuery({
      queryKey: ['brands'],
      queryFn: fetchBrands
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/thuong-hieu/${id}`),
      onError: (err, id, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['brands'] })
    });

    const saveMutation = useMutation({
      mutationFn: async ({ id, payload }) => {
        if (id) {
          payload.append('_method', 'PUT');
          return axiosClient.post(`/admin/thuong-hieu/${id}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
        return axiosClient.post('/admin/thuong-hieu', payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      },
      onError: (err, vars, context) => {
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['brands'] })
    });

    return { brands, isFetching, isPending, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      activeTab: 'MARINE',
      isEditMode: false,
      isSubmitting: false,
      logoFile: null,
      formData: {
        id: null,
        code: "",
        name: "",
        logo_path: "",
        status: 1
      }
    };
  },
  computed: {
    filteredBrands() {
      return (this.brands || []);
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
      this.resetForm();
    },
    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http')) return path;
      return `http://localhost:8000${path}`;
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = {
        id: null,
        code: "",
        name: "",
        logo_path: "",
        status: 1
      };
      this.logoFile = null;
      const fileInput = document.getElementById('logo');
      if (fileInput) fileInput.value = '';
    },
    removeLogo() {
      this.logoFile = null;
      this.formData.logo_path = null;
      const fileInput = document.getElementById('logo');
      if (fileInput) fileInput.value = '';
    },
    handleLogoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.logoFile = file;
        this.formData.logo_path = URL.createObjectURL(file); 
      } else {
        this.logoFile = null;
      }
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      this.logoFile = null;
      const fileInput = document.getElementById('logo');
      if (fileInput) fileInput.value = '';
    },
    async saveData() {
      if (!this.formData.code || !this.formData.name) {
        this.$toast?.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }

      this.isSubmitting = true;
      try {
        const payload = new FormData();
        payload.append('code', this.formData.code);
        payload.append('name', this.formData.name);
        payload.append('status', this.formData.status ? 1 : 0);
        
        if (this.logoFile) {
          payload.append('logo', this.logoFile);
        }

        await this.saveMutation.mutateAsync({ id: this.isEditMode ? this.formData.id : null, payload, data: { ...this.formData } });
        
        this.$toast?.success(this.isEditMode ? "Cập nhật Cấu Hình Hệ Thống thành công!" : "Đã thêm Cấu Hình Hệ Thống mới!");
        this.resetForm();
      } catch (error) {
        const msg = error.response?.data?.message || "Có lỗi xảy ra khi lưu dữ liệu";
        this.$toast?.error(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa Cấu Hình Hệ Thống <b>"${item.name}"</b>?`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xóa Cấu Hình Hệ Thống',
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
        try {
          await this.deleteMutation.mutateAsync(item.id);
          this.$toast?.success("Đã xóa Cấu Hình Hệ Thống!");
        } catch (error) {
          const msg = error.response?.data?.message || "Lỗi khi xóa Cấu Hình Hệ Thống";
          this.$toast?.error(msg);
        }
      }
    }
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




