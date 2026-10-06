<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Marketing /</span> Quản Lý Banner 
    </h4>

    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Banner' : 'Thêm Mới Banner' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-4">
                <label for="title" class="form-label fw-medium text-dark">Tiêu Đề Banner</label>
                <input
                  type="text"
                  id="title"
                  class="form-control"
                  placeholder="VD: Khuyến mãi mùa hè..."
                  v-model="formData.title"
                />
              </div>

              <div class="mb-4">
                <label for="link_url" class="form-label fw-medium text-dark">Đường dẫn liên kết (Link URL)</label>
                <input
                  type="text"
                  id="link_url"
                  class="form-control"
                  placeholder="VD: https://..."
                  v-model="formData.link_url"
                />
              </div>

              <div class="mb-4">
                <label for="image" class="form-label fw-medium text-dark">Hình Ảnh Banner <span class="text-danger" v-if="!isEditMode">*</span></label>
                <input
                  type="file"
                  id="image"
                  class="form-control mb-2"
                    accept="image/png, image/jpeg, image/svg+xml"
                  @change="handleImageUpload"
                  :required="!isEditMode && !formData.image_url"
                />
                <div class="d-flex justify-content-center align-items-center border rounded bg-light mt-2" style="height: 180px;">
                    <img v-if="formData.image_url" :src="getImageUrl(formData.image_url)" style="max-height: 160px; max-width: 100%; object-fit: contain;">
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
            <h5 class="mb-0 fw-bold text-dark">Danh sách Banner</h5>
          </div>

          <div class="table-responsive text-nowrap">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="text-center" style="width: 5%">STT</th>
                  <th class="text-center" style="width: 25%">Hình Ảnh</th>
                  <th style="width: 40%">Tiêu Đề & Link</th>
                  <th class="text-center" style="width: 15%">Trạng thái</th>
                  <th class="text-center" style="width: 15%">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isPending">
                  <td colspan="5" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="filteredBanners.length === 0">
                  <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu</td>
                </tr>
                <tr v-for="(item, index) in filteredBanners" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td class="text-center">
                    <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 120px; height: 60px; background-color: #f8f9fa;">
                      <img v-if="item.image_url" :src="getImageUrl(item.image_url)" alt="Banner" style="max-height: 50px; max-width: 110px; object-fit: contain;" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'">
                      <i class="bx bx-image text-muted fs-3" :style="{ display: item.image_url ? 'none' : 'inline-block' }"></i>
                    </div>
                  </td>
                  <td>
                    <span :class="!item.title ? 'text-muted d-block text-wrap fst-italic' : 'fw-bold text-dark d-block text-wrap'" style="max-width: 200px;">{{ item.title || 'N/A' }}</span>
                    <a v-if="item.link_url" :href="item.link_url" target="_blank" class="small text-primary text-truncate d-block" style="max-width: 200px;">{{ item.link_url }}</a>
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
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredBanners && filteredBanners.length) ? filteredBanners.length : 0 }} mục</span>
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
  name: "Banner",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    
    const { data: bannersData, isFetching, isPending } = useQuery({
      queryKey: ['banners'],
      queryFn: () => axiosClient.get('/admin/banner').then(res => res.data || [])
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/banner/${id}`),
      onError: (err, id, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['banners'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async ({ payload, isEdit, id }) => {
        if (isEdit) {
          payload.append('_method', 'PUT');
          return axiosClient.post(`/admin/banner/${id}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        } else {
          return axiosClient.post('/admin/banner', payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
      },
      onError: (err, variables, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['banners'] });
      }
    });

    return { bannersData, isFetching, isPending, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      activeTab: 'MARINE', // Tab mặc định
      isEditMode: false,
      isSubmitting: false,
      imageFile: null,
      formData: {
        id: null,
        title: "",
        link_url: "",
        image_url: "",
        status: 1
      }
    };
  },
  computed: {
    filteredBanners() {
      return this.bannersData || [];
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
      this.resetForm();
    },
    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http') || path.startsWith('data:')) return path;
      return `http://localhost:8000${path}`;
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = {
        id: null,
        title: "",
        link_url: "",
        image_url: "",
        status: 1
      };
      this.imageFile = null;
      const fileInput = document.getElementById('image');
      if (fileInput) fileInput.value = '';
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.imageFile = file;
        this.formData.image_url = URL.createObjectURL(file); 
      } else {
        this.imageFile = null;
      }
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      this.imageFile = null;
      const fileInput = document.getElementById('image');
      if (fileInput) fileInput.value = '';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    async saveData() {
      if (!this.isEditMode && !this.imageFile) {
        this.$toast?.error("Vui lòng tải lên hình ảnh banner!");
        return;
      }

      this.isSubmitting = true;
      try {
        const payload = new FormData();
        payload.append('title', this.formData.title || '');
        payload.append('link_url', this.formData.link_url || '');
        payload.append('status', this.formData.status ? 1 : 0);
        
        if (this.imageFile) {
          payload.append('image', this.imageFile);
        }

        const optimisticRecord = {
          ...this.formData,
          image_url: this.imageFile ? URL.createObjectURL(this.imageFile) : this.formData.image_url
        };

        await this.saveMutation.mutateAsync({
          payload,
          isEdit: this.isEditMode,
          id: this.formData.id,
          optimisticRecord
        });

        this.$toast?.success(this.isEditMode ? "Cập nhật banner thành công!" : "Đã thêm banner mới!");
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
        title: "Xác nhận xóa",
        html: "Bạn có chắc chắn muốn xóa banner <b>\"" + (item.title || "này") + "\"</b>?<br><br><small class=\"text-danger\">Lưu ý: Không thể hoàn tác.</small>",
        icon: "warning",
        iconColor: "#ff3e1d",
        showCancelButton: true,
        confirmButtonText: "Đồng ý xóa",
        cancelButtonText: "Hủy",
        reverseButtons: true,
        animation: false,
        customClass: { popup: "swal-shift-up", confirmButton: "btn btn-danger px-4 shadow-sm", cancelButton: "btn btn-outline-secondary px-4 me-3" },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          await this.deleteMutation.mutateAsync(item.id);
          this.$toast?.success("Đã xóa banner!");
        } catch (error) {
          const msg = error.response?.data?.message || "Lỗi khi xóa banner";
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

