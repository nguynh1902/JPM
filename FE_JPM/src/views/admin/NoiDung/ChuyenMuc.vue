<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Nội Dung /</span> Quản Lý Chuyên Mục
    </h4>

    <div class="row">
      <!-- Cột trái: Form nhập liệu (Layout 1) -->
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
              {{ isEditMode ? 'Chỉnh sửa Chuyên Mục' : 'Thêm Chuyên Mục Mới' }}
            </h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Loại Ứng Dụng <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.type"
                  :options="[
                    {value: 'NEWS', label: 'Dùng cho Tin Tức'},
                    {value: 'FAQ', label: 'Dùng cho FAQ'}
                  ]"
                  :can-clear="false"
                  placeholder="Chọn loại ứng dụng"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Tên Chuyên Mục <span class="text-danger">*</span></label>
                <input 
                  type="text"
                  class="form-control" 
                  v-model="formData.name" 
                  placeholder="Nhập tên chuyên mục..." 
                  required
                />
              </div>

              <div class="mb-4">
                <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.status"
                  :options="[
                    {value: 'ACTIVE', label: 'Hiển thị'},
                    {value: 'HIDDEN', label: 'Tạm ẩn'}
                  ]"
                  :can-clear="false"
                  required
                />
              </div>

              <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" @click="resetForm" v-if="isEditMode">
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

      <!-- Cột phải: Bảng dữ liệu -->
      <div class="col-md-8 mb-4">
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark">Danh Sách Chuyên Mục</h5>
            
            <div class="d-flex gap-2" style="max-width: 420px; width: 100%;">
              <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm tên..." v-model="searchKeyword">
              <div style="min-width: 170px;">
                <v-select
                  v-model="filterType"
                  :options="[
                    {value: '', label: 'Tất cả ứng dụng'},
                    {value: 'NEWS', label: 'Chỉ Tin Tức'},
                    {value: 'FAQ', label: 'Chỉ FAQ'}
                  ]"
                  :can-clear="false"
                  class="w-100"
                />
              </div>
            </div>
          </div>

          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center" style="width: 5%">STT</th>
                  <th style="width: 40%">Tên Chuyên Mục</th>
                  <th class="text-center" style="width: 20%">Phân Loại</th>
                  <th class="text-center" style="width: 15%">Trạng thái</th>
                  <th class="text-center" style="width: 15%">Hành động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="filteredCategories.length === 0">
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in filteredCategories" :key="item.id" :class="{'table-active': isEditMode && formData.id === item.id}">
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td>
                    <span class="fw-bold text-dark">{{ item.name }}</span>
                  </td>
                  <td class="text-center">
                    <span v-if="item.type === 'NEWS'" class="fw-semibold text-primary">Tin Tức</span>
                    <span v-else class="fw-semibold text-secondary">FAQ</span>
                  </td>
                  <td class="text-center">
                    <span v-if="item.status === 'ACTIVE'" class="badge badge-custom bg-pastel-success">HIỂN THỊ</span>
                    <span v-else class="badge badge-custom bg-pastel-danger">TẠM ẨN</span>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <button type="button" class="action-btn text-primary me-2" @click="editItem(item)" title="Sửa">
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button type="button" class="action-btn text-danger" @click="confirmDelete(item)" title="Xóa">
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
            <span class="text-muted fw-medium">Tổng cộng: {{ filteredCategories.length }} mục</span>
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
  name: "ChuyenMuc",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();

    const fetchCategories = async () => {
      const res = await axiosClient.get('/admin/chuyen-muc');
      return res.data || [];
    };

    
    const { data: categories } = useQuery({
      queryKey: ['chuyen-muc'],
      queryFn: fetchCategories,
      staleTime: 1000 * 60 * 5
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/chuyen-muc/${id}`),
      onError: (err, id, context) => {
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['chuyen-muc'] });
        queryClient.invalidateQueries({ queryKey: ['client-tin-tuc-categories'] });
        queryClient.invalidateQueries({ queryKey: ['client-tin-tuc'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        if (payload.id) {
          return axiosClient.put(`/admin/chuyen-muc/${payload.id}`, payload);
        }
        return axiosClient.post('/admin/chuyen-muc', payload);
      },
      onError: (err, vars, context) => {
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['chuyen-muc'] });
        queryClient.invalidateQueries({ queryKey: ['tin-tuc'] });
        queryClient.invalidateQueries({ queryKey: ['client-tin-tuc-categories'] });
        queryClient.invalidateQueries({ queryKey: ['client-tin-tuc'] });
      }
    });

    return { categories, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      searchKeyword: '',
      filterType: '',
      isEditMode: false,
      formData: this.getInitialFormData()
    };
  },
  computed: {
    filteredCategories() {
      let list = this.categories || [];
      if (this.filterType) {
        list = list.filter(item => item.type === this.filterType);
      }
      if (this.searchKeyword) {
        const q = this.searchKeyword.toLowerCase().trim();
        list = list.filter(item => String(item.name || '').toLowerCase().includes(q));
      }
      return list;
    }
  },
  methods: {
    getInitialFormData() {
      return {
        id: null,
        name: '',
        type: 'NEWS',
        status: 'ACTIVE'
      };
    },
    editItem(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    async saveData() {
      try {
        await this.saveMutation.mutateAsync(this.formData);
        this.$toast?.success(this.isEditMode ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
        this.resetForm();
      } catch (error) {
        console.error(error);
        this.$toast?.error('Lỗi khi lưu chuyên mục!');
      }
    },
    async confirmDelete(item) {
        const result = await Swal.fire({
          title: "Xác nhận xóa",
          html: `Bạn có chắc chắn muốn xóa chuyên mục <b>"${item.name}"</b>?<br><br><small class="text-danger">Lưu ý: Dữ liệu sau khi xóa sẽ không thể khôi phục.</small>`,
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
          try {
            await this.deleteMutation.mutateAsync(item.id);
            this.$toast?.success('Đã xóa chuyên mục!');
          } catch(error) {
            this.$toast?.error('Xóa thất bại!');
          }
        }
      }
    }
};
</script>










