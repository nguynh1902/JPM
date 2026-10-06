<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Động cơ & Phương tiện /</span> Nhóm Phụ Tùng 
    </h4>

    

    <div class="row">
      <!-- Cột Form (Thêm/Sửa) -->
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật Nhóm Phụ Tùng' : 'Thêm Nhóm Phụ Tùng' }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              
              
                              <div class="mb-4" v-if="isEditMode">
                <label for="ma_nhom" class="form-label fw-medium text-dark">MÃ NHÓM</label>
                <input type="text" id="ma_nhom" class="form-control bg-light" v-model="formData.ma_nhom" readonly />
              </div>
<div class="mb-4">
                <label for="ten_nhom" class="form-label fw-medium text-dark">Tên Nhóm Phụ Tùng <span class="text-danger">*</span></label>
                <input
                  type="text"
                  id="ten_nhom"
                  class="form-control"
                  placeholder="VD: Nhóm Động Cơ (Engine)"
                  v-model="formData.ten_nhom"
                  
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
            <h5 class="mb-0 fw-bold text-dark">Danh sách Nhóm Phụ Tùng</h5>
            <div style="width: 250px;">
              <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm mã, tên..." v-model="searchKeyword">
            </div>
          </div>

          <div class="table-responsive text-nowrap custom-scroll " style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center " style="width: 5%">STT</th>
                  <th class="" style="width: 20%">Mã Nhóm</th>
                  <th class="" style="width: 45%">Tên Nhóm Phụ Tùng</th>
                  <th class="text-center " style="width: 15%">Trạng thái</th>
                  <th class="text-center " style="width: 15%">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isPending">
                  <td colspan="5" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="filteredNhomPhuTung.length === 0">
                  <td colspan="5" class="text-center py-4 text-muted">Không tìm thấy dữ liệu.</td>
                </tr>
                <tr v-for="(item, index) in filteredNhomPhuTung" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td class="align-middle"><span class="fw-bold text-primary">{{ item.ma_nhom }}</span></td>
                  <td class="align-middle">
                    <span class="fw-bold text-dark d-block">{{ item.ten_nhom }}</span>
                  </td>

                  <td class="text-center">
                    <span v-if="item.trang_thai === 1 || item.trang_thai === true" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
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
          <span class="text-muted fw-medium">Tổng cộng: {{ filteredNhomPhuTung.length }} mục</span>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import { computed } from 'vue';
  import { useAuthStore } from '../../../stores/authStore';
  import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";
import Swal from 'sweetalert2';

export default {
  name: "NhomPhuTung",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
      const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const { data: nhomPhuTung, isFetching, isPending } = useQuery({
      queryKey: ['nhom-phu-tung'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/nhom-phu-tung');
        return res.data;
      }
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => await axiosClient.delete(`/admin/nhom-phu-tung/${id}`),
      onError: (err, id, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi xóa danh mục");
      },
      onSuccess: () => toaster.success("Đã xóa danh mục này!"),
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['nhom-phu-tung'] })
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        if (payload.id) {
          return await axiosClient.put(`/admin/nhom-phu-tung/${payload.id}`, payload);
        }
        return await axiosClient.post('/admin/nhom-phu-tung', payload);
      },
      onError: (err, payload, context) => {
        toaster.error("Lỗi lưu dữ liệu!");
      },
      onSuccess: (data, payload) => {
        toaster.success(payload.id ? "Cập nhật danh mục thành công!" : "Đã thêm danh mục mới!");
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['nhom-phu-tung'] })
    });

    return { queryClient, nhomPhuTung, isFetching, isPending, deleteMutation, saveMutation, toaster };
  },
  data() {
    return {
      activeTab: 'MARINE',
        searchKeyword: '',
        isSubmitting: false,
      isEditMode: false,
      formData: {
        id: null,
        ma_nhom: "",
        ten_nhom: "",
        trang_thai: 1
      }
    };
  },
  computed: {
    filteredNhomPhuTung() {
      const items = Array.isArray(this.nhomPhuTung) ? this.nhomPhuTung : (this.nhomPhuTung?.data || []);
      return items;
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = {
        id: null,
        ma_nhom: "",
        ten_nhom: "",
        trang_thai: 1
      };
    },
    
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      // Chuẩn hóa kiểu dữ liệu trang_thai về 1 hoặc 0 để v-select hiển thị đúng
      if (this.formData.trang_thai === true) this.formData.trang_thai = 1;
      else if (this.formData.trang_thai === false) this.formData.trang_thai = 0;
    },
    async saveData() {
      if (!this.formData.ten_nhom) {
        this.toaster.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }
      const payload = { ...this.formData,  };
      this.isSubmitting = true;
        this.saveMutation.mutate(payload, {
            onSuccess: () => {
              this.resetForm();
            },
            onSettled: () => {
              this.isSubmitting = false;
            }
          });
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: "Xác nhận xóa",
        html: `Bạn có chắc chắn muốn xóa nhóm <b>"${item.ten_nhom}"</b>?<br><br><small class="text-danger">Lưu ý: Dữ liệu sau khi xóa sẽ không thể khôi phục.</small>`,
        icon: "warning",
        iconColor: "#ff3e1d",
        showCancelButton: true,
        confirmButtonText: "Đồng ý xóa",
        cancelButtonText: "Hủy",
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
        if (this.isEditMode && this.formData.id === item.id) { this.resetForm(); }
      this.deleteMutation.mutate(item.id);
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

/* Đẩy thanh cuộn xuống dưới thanh header của bảng */
.custom-scroll::-webkit-scrollbar-track {
  margin-top: 42px; /* Chiều cao xấp xỉ của thẻ th */
}
</style>










