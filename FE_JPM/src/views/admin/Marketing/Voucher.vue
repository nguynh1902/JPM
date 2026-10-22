<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Marketing /</span> Quản Lý Mã Khuyến Mãi 
    </h4>

    <div class="card">
      <div class="card-header border-bottom">
          <h5 class="mb-0 fw-bold text-dark">Danh Sách Voucher</h5>
        </div>
  
        <!-- Bộ lọc -->
      <div class="card-body pt-3 pb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="flex-grow-1">
              <input 
                type="text" 
                class="form-control" 
                placeholder="Tìm kiếm mã voucher..." 
                v-model="filters.code"
              />
            </div>
            <div style="width: 25%;">
              <v-select
                v-model="filters.status"
                :options="[
                  {value: '', label: 'Tất cả trạng thái'},
                  {value: '1', label: 'Hoạt Động'},
                  {value: '0', label: 'Tạm Khóa'}
                ]"
                placeholder="Trạng thái"
                :can-clear="false"
              />
            </div>
            <button class="btn btn-secondary px-3 text-nowrap" @click="resetFilters" title="Đặt lại bộ lọc">
              Đặt lại
            </button>
            <button class="btn btn-primary px-4 shadow-sm text-nowrap" @click="openAddModal" data-bs-toggle="modal" data-bs-target="#modalVoucher">
              <i class="bx bx-plus me-1"></i> Thêm Mới
            </button>
          </div>
        </div>
  
        <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 20%">Mã Voucher</th>
              <th style="width: 25%">Mức Giảm Giá</th>
              <th class="text-center" style="width: 20%">Thời Gian</th>
              <th class="text-center" style="width: 15%">Trạng Thái</th>
              <th class="text-center" style="width: 15%">Hành Động</th>
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
            <tr v-else-if="filteredVouchers.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredVouchers" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <span class="fw-bold text-primary fs-6">{{ item.code }}</span>
                <small class="d-block text-muted mt-1">Đơn tối thiểu: {{ formatCurrency(item.min_order_value) }}</small>
              </td>
              <td>
                <span class="fw-bold text-danger fs-6" v-if="item.discount_type === 'PERCENT'">
                  Giảm {{ item.discount_value }}%
                </span>
                <span class="fw-bold text-danger fs-6" v-else>
                  Giảm {{ formatCurrency(item.discount_value) }}
                </span>
              </td>
              <td class="text-center">
                <div class="d-flex flex-column align-items-center">
                  <span class="badge bg-label-info mb-1" title="Từ ngày">{{ formatDate(item.start_date) || 'Không giới hạn' }}</span>
                  <span class="badge bg-label-warning" title="Đến ngày">{{ formatDate(item.end_date) || 'Không giới hạn' }}</span>
                </div>
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
                    data-bs-toggle="modal"
                    data-bs-target="#modalVoucher"
                    title="Chỉnh sửa"
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
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredVouchers && filteredVouchers.length) ? filteredVouchers.length : 0 }} mục</span>
      </div>
    </div>

    <!-- Modal Form (Layout 2) -->
    <div class="modal fade" id="modalVoucher" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form @submit.prevent="saveData" class="modal-content">
          <div class="modal-header border-bottom pb-3">
            <h5 class="modal-title fw-bold text-dark">
              {{ isEditMode ? 'Chỉnh Sửa Voucher' : 'Thêm Voucher Mới' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm"></button>
          </div>
          <div class="modal-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-medium text-dark">Mã Voucher <span class="text-danger">*</span></label>
                  <input type="text" class="form-control text-uppercase" v-model="formData.code" placeholder="VD: SUMMER2026" required />
                </div>
                
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Loại Giảm Giá <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.discount_type"
                    :options="[
                      {value: 'AMOUNT', label: 'Giảm số tiền cố định'},
                      {value: 'PERCENT', label: 'Giảm theo phần trăm'}
                    ]"
                    :can-clear="false"
                    required
                  />
                </div>
                
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Mức Giảm <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" v-model="formData.discount_value" placeholder="VD: 50000 hoặc 10" min="0" required />
                </div>

                <div class="col-12">
                  <label class="form-label fw-medium text-dark">Giá Trị Đơn Tối Thiểu</label>
                  <input type="number" class="form-control" v-model="formData.min_order_value" placeholder="VD: 200000" min="0" />
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Từ Ngày</label>
                  <input type="date" class="form-control" v-model="formData.start_date" />
                </div>
                
                <div class="col-md-6">
                  <label class="form-label fw-medium text-dark">Đến Ngày</label>
                  <input type="date" class="form-control" v-model="formData.end_date" />
                </div>

                <div class="col-12">
                  <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.status"
                    :options="[
                      {value: 1, label: 'Hoạt Động'},
                      {value: 0, label: 'Tạm Khóa'}
                    ]"
                    :can-clear="false"
                    required
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer border-top pt-3">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" @click="resetForm">Hủy Bỏ</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" data-bs-dismiss="modal" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Cập Nhật' : 'Lưu Lại' }}
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import dayjs from 'dayjs';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';

export default {
  name: "Voucher",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    
    const { data: vouchersData, isFetching, isPending } = useQuery({
      queryKey: ['vouchers'],
      queryFn: () => axiosClient.get('/admin/voucher').then(res => res.data || [])
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/voucher/${id}`),
      onError: (err, id, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['vouchers'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        if (payload.id) {
          return axiosClient.put(`/admin/voucher/${payload.id}`, payload);
        } else {
          return axiosClient.post('/admin/voucher', payload);
        }
      },
      onError: (err, vars, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['vouchers'] });
      }
    });

    return { vouchersData, isFetching, isPending, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      activeTab: 'MARINE',
      isSubmitting: false,
      filters: {
        code: '',
        status: ''
      },
      isEditMode: false,
      formData: this.getInitialFormData()
    };
  },
  computed: {
    filteredVouchers() {
      const allVouchers = this.vouchersData || [];
      return allVouchers.filter(item => {
        let match = true;
        if (this.filters.code) {
          const s = this.filters.code.toLowerCase().trim();
          if (!String(item.code || '').toLowerCase().includes(s)) match = false;
        }
        if (this.filters.status !== '') {
          const s = parseInt(this.filters.status);
          if ((item.status ? 1 : 0) !== s) match = false;
        }
        return match;
      });
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
    },
    resetFilters() {
      this.filters = {
        code: '',
        status: ''
      };
    },
    formatCurrency(value) {
      if (!value) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      return dayjs(dateStr).format('DD/MM/YYYY');
    },
    getInitialFormData() {
      return {
        id: null,
        code: '',
        discount_type: 'AMOUNT',
        discount_value: 0,
        min_order_value: 0,
        start_date: '',
        end_date: '',
        status: 1
      };
    },
    openAddModal() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    async saveData() {
      this.isSubmitting = true;
      try {
        this.formData.code = this.formData.code.toUpperCase();
        
        await this.saveMutation.mutateAsync(this.formData);
        
        this.$toast?.success(this.isEditMode ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
        this.resetForm();
      } catch (error) {
        console.error(error);
        const msg = error.response?.data?.message || 'Có lỗi xảy ra!';
        this.$toast?.error(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: "Xác nhận xóa",
        html: "Bạn có chắc chắn muốn xóa voucher <b>\"" + item.code + "\"</b>?<br><br><small class=\"text-danger\">Lưu ý: Hành động này không thể hoàn tác.</small>",
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
          this.$toast?.success('Đã xóa voucher!');
        } catch(error) {
          this.$toast?.error('Xóa thất bại!');
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

