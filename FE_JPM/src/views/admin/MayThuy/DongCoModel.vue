<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Dữ Liệu Nền Tảng /</span> Model Động Cơ
    </h4>



    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh sách Model Động Cơ</h5>
      </div>
      
      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
          <div class="flex-grow-1" style="min-width: 160px;">
            <input type="text" class="form-control" placeholder="Tìm tên, mã..." v-model="filters.search">
          </div>
          <div style="width: 150px;">
            <v-select
              v-model="filters.thuong_hieu_id"
              :options="[{value: null, label: 'Tất cả'}, ...brandOptions]"
              placeholder="Thương hiệu"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 150px;">
            <v-select
              v-model="filters.loai_dong_co_id"
              :options="[{value: null, label: 'Tất cả'}, ...typeOptions]"
              placeholder="Loại Động Cơ"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 105px;">
            <v-select
              v-model="filters.year"
              :options="[{value: null, label: 'Tất cả'}, ...yearOptions]"
              placeholder="Năm"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 115px;">
            <v-select
              v-model="filters.ma_luc"
              :options="[{value: null, label: 'Tất cả'}, ...horsepowerOptions]"
              placeholder="Mã Lực"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          
            <button class="btn btn-secondary text-nowrap" @click="resetFilters">
              Đặt lại
            </button>
            <button v-if="canAdd" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modalModel" @click="openAddModal">
              <i class="bx bx-plus me-1"></i> Thêm Mới
            </button>
          </div>
        </div>

      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
                <th style="width: 32%">Model Động Cơ</th>
                <th style="width: 14%">Thương Hiệu</th>
                <th style="width: 14%">Loại Động Cơ</th>
                <th class="text-center" style="width: 8%">Năm</th>
                <th class="text-center" style="width: 8%">Mã Lực</th>
                <th class="text-center" style="width: 7%">Loại Thì</th>
                <th class="text-center" style="width: 7%">Trạng thái</th>
                <th class="text-center" style="width: 5%" v-if="canManage">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td :colspan="canManage ? 9 : 8" class="text-center py-4 text-muted">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredModels.length === 0">
              <td :colspan="canManage ? 9 : 8" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredModels" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <span class="fw-bold text-primary">{{ item.ten_model }}</span>
              </td>
              <td><span class="fw-bold text-dark">{{ getBrandName(item.thuong_hieu_id) }}</span></td>
              <td><span class="fw-semibold text-secondary">{{ getTypeName(item.loai_dong_co_id) }}</span></td>
              <td class="text-center">
                <span class="fw-bold text-dark">{{ item.nam_san_xuat || 'N/A' }}</span>
              </td>
              <td class="text-center">
                <span class="fw-semibold text-secondary" v-if="item.ma_luc">{{ item.ma_luc }} HP</span>
                <span class="fw-semibold text-secondary" v-else>N/A</span>
              </td>
              <td class="text-center"><span class="fw-bold text-dark">{{ item.loai_nhip ? item.loai_nhip.toString().replace(/[^0-9]/g, '') : 'N/A' }}</span></td>
              <td class="text-center">
                <span v-if="item.trang_thai === 1 || item.trang_thai === true" class="badge badge-custom text-uppercase bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom text-uppercase bg-pastel-danger">TẠM KHÓA</span>
              </td>
              <td class="text-center" v-if="canManage">
                <div class="d-flex align-items-center justify-content-center">
                  
                  <button 
                    v-if="canEdit"
                    type="button"
                    class="action-btn text-primary me-1" 
                    @click="openEditModal(item)" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalModel" 
                    title="Chỉnh sửa"
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
          <span class="text-muted fw-medium">Tổng cộng: {{ filteredModels.length }} mục</span>
        </div>
    </div>
  </div>

  <!-- Modal Form -->
    <div class="modal fade" id="modalModel" tabindex="-1" aria-hidden="true" ref="modalModel">
      <div class="modal-dialog mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalModelTitle">
              {{ isEditMode ? 'Cập Nhật Model Động Cơ' : 'Thêm Model Mới' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModal" @click="resetForm"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2 pt-4">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-dark">Thương Hiệu <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.thuong_hieu_id"
                      :options="brandFormOptions"
                      placeholder="Chọn thương hiệu..."
                      searchable
                      required
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium text-dark">Loại Động Cơ <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.loai_dong_co_id"
                      :options="typeOptions"
                      placeholder="Chọn loại động cơ..."
                      searchable
                      required
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label for="ten_model" class="form-label fw-medium text-dark">Tên Model Động Cơ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="ten_model" v-model="formData.ten_model" placeholder="VD: Yamaha F150" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label for="nam_san_xuat" class="form-label fw-medium text-dark">Năm Sản Xuất</label>
                    <input type="number" id="nam_san_xuat" class="form-control" placeholder="YYYY" v-model="formData.nam_san_xuat" min="1900" max="2100"/>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                    <label for="ma_luc" class="form-label fw-medium text-dark">Mã Lực (HP)</label>
                    <input
                      type="text"
                      id="ma_luc"
                      class="form-control"
                      placeholder="VD: 150"
                      v-model="formData.ma_luc"
                    />
                  </div>
                  
                  <div class="col-md-4 mb-3">
                    <label for="loai_nhip" class="form-label fw-medium text-dark">Thì</label>
                      <v-select
                        v-model="formData.loai_nhip"
                        :options="[{value: '2 Thì', label: '2 Thì'}, {value: '4 Thì', label: '4 Thì'}]"
                        :searchable="true"
                        :create-option="true"
                        placeholder="Chọn/Gõ..."
                      />
                  </div>
                </div>

                <div class="row">
                  
<div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.trang_thai"
                      :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]"
                      :can-clear="false"
                    />
                  </div>
                </div>


              </div>

              <div class="modal-footer border-top py-3 mt-2 bg-light">
              <button type="button" class="btn btn-outline-secondary px-4 me-3" data-bs-dismiss="modal" @click="resetForm">
                Hủy
              </button>
              <button type="submit" class="btn btn-primary px-5 shadow-sm" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Lưu Thay Đổi' : 'Thêm Model' }}
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
import { computed } from 'vue';
  import { useAuthStore } from '../../../stores/authStore';
  import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: "DongCoModel",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
      const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });

    const { data: models, isFetching: isFetchingModels , isPending: isPendingmodels } = useQuery({
      queryKey: ['engineModels'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/dong-co-model');
        return res.data;
      }
    });

    const { data: brands, isFetching: isFetchingBrands , isPending: isPendingbrands } = useQuery({
      queryKey: ['brands'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/thuong-hieu?all=true');
        return res.data;
      }
    });

    const { data: equipmentTypes, isFetching: isFetchingTypes , isPending: isPendingequipmentTypes } = useQuery({
      queryKey: ['equipmentTypes'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/loai-dong-co?all=true');
        return res.data;
      }
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => await axiosClient.delete(`/admin/dong-co-model/${id}`),
      onError: (err, id, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi xóa model");
      },
      onSuccess: () => toaster.success("Đã xóa model!"),
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['engineModels'] })
    });

    const saveMutation = useMutation({
      mutationFn: async ({ id, payload }) => {
        if (id) {
          return await axiosClient.put(`/admin/dong-co-model/${id}`, payload);
        }
        return await axiosClient.post('/admin/dong-co-model', payload);
      },
      onError: (err, vars, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi lưu model");
      },
      onSuccess: (data, vars) => {
        toaster.success(vars.id ? "Cập nhật model thành công!" : "Đã thêm model mới!");
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['engineModels'] })
    });

    return { 
      queryClient, models, isFetchingModels, brands, isFetchingBrands, 
      equipmentTypes, isFetchingTypes, deleteMutation, saveMutation, toaster,
      authStore
    };
  },
  data() {
    return {
      activeTab: 'MARINE',
      filters: {
        search: "",
        thuong_hieu_id: null,
        loai_dong_co_id: null,
        year: null,
        ma_luc: null
      },
      isSubmitting: false,
      isEditMode: false,
      formData: {
        id: null,
        thuong_hieu_id: null,
        loai_dong_co_id: null,
        ma_model: "",
        ten_model: "",
        nam_san_xuat: null,
        ma_luc: "",
        loai_nhip: null,
        trang_thai: 1
      }
    };
  },
  computed: {
    canAdd() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Thêm Động cơ & Thương hiệu') || this.authStore.hasPermission('Quản lý Model & Động cơ');
    },
    canEdit() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Sửa Động cơ & Thương hiệu') || this.authStore.hasPermission('Quản lý Model & Động cơ');
    },
    canDelete() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Xóa Động cơ & Thương hiệu') || this.authStore.hasPermission('Quản lý Model & Động cơ');
    },
    canManage() {
      return this.canEdit || this.canDelete;
    },
    isFetching() {
      return this.isFetchingModels || this.isFetchingBrands || this.isFetchingTypes;
    },
    brandOptions() {
      return (this.brands || []).map(b => ({ value: b.id, label: b.name }));
    },
    brandFormOptions() {
      return (this.brands || [])
        .filter(b => b.status === 1 || b.id === this.formData.thuong_hieu_id)
        .map(b => ({ value: b.id, label: b.name }));
    },
    typeOptions() {
      return (this.equipmentTypes || [])
        .filter(t => t.trang_thai === 1 || t.id === this.formData.loai_dong_co_id || t.id === this.filterType)
        .map(t => ({ value: t.id, label: t.ten_loai_dong_co }));
    },
    yearOptions() {
      const activeModels = (this.models || []);
      const startYears = activeModels.map(m => m.nam_san_xuat).filter(y => y);
      const uniqueYears = [...new Set(startYears)].sort((a, b) => b - a);
      return uniqueYears.map(y => ({ value: y, label: String(y) }));
    },
    horsepowerOptions() {
      const activeModels = (this.models || []);
      const hps = activeModels.map(m => m.ma_luc).filter(h => h);
      const uniqueHps = [...new Set(hps)].sort((a, b) => parseFloat(a) - parseFloat(b));
      return uniqueHps.map(h => ({ value: h, label: String(h) }));
    },
    filteredModels() {
      return (this.models || []).filter(m => {
        let match = true;
        if (!match) return false;

        if (this.filters.search) {
          const s = this.filters.search.toLowerCase().trim();
          match = match && (
            String(m.ten_model || '').toLowerCase().includes(s) || 
            String(m.ma_model || '').toLowerCase().includes(s)
          );
        }
        if (this.filters.thuong_hieu_id) {
          match = match && m.thuong_hieu_id === this.filters.thuong_hieu_id;
        }
        if (this.filters.loai_dong_co_id) {
          match = match && m.loai_dong_co_id === this.filters.loai_dong_co_id;
        }
        if (this.filters.year) {
          match = match && m.nam_san_xuat === this.filters.year;
        }
        if (this.filters.ma_luc) {
          match = match && String(m.ma_luc) === String(this.filters.ma_luc);
        }
        return match;
      });
    }
  },

  watch: {
    'formData.ten_model'(newVal) {
      if (!this.isEditMode) {
        if(newVal) {
            this.formData.ma_model = newVal
              .toUpperCase()
              .normalize('NFD')
              .replace(/[\u0300-\u036f]/g, '')
              .replace(/[^A-Z0-9]+/g, '-')
              .replace(/^-|-$/g, '');
        } else {
            this.formData.ma_model = '';
        }
      }
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
      this.resetFilters();
    },
    resetFilters() {
      this.filters = {
        search: "",
        thuong_hieu_id: null,
        loai_dong_co_id: null,
        year: null,
        ma_luc: null
      };
    },
    getBrandName(id) {
      const brand = (this.brands || []).find(b => b.id === id);
      return brand ? brand.name : 'N/A';
    },
    getTypeName(id) {
      const type = (this.equipmentTypes || []).find(t => t.id === id);
      return type ? type.ten_loai_dong_co : 'N/A';
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = {
        id: null,
        thuong_hieu_id: null,
        loai_dong_co_id: null,
        ma_model: "",
        ten_model: "",
        nam_san_xuat: null,
        ma_luc: "",
        loai_nhip: null,
        trang_thai: 1
      };
    },
    async openAddModal() {
      this.resetForm();
    },
    async openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
    },
    async saveData() {
      if (!this.formData.ma_model || !this.formData.ten_model || !this.formData.thuong_hieu_id || !this.formData.loai_dong_co_id) {
        this.toaster.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }

      this.isSubmitting = true;
      try {
        const payload = { ...this.formData };
        delete payload.hinh_anh;
        delete payload.hinh_anh_url;
        
        if (this.isEditMode) {
          await this.saveMutation.mutateAsync({ id: this.formData.id, payload });
        } else {
          await this.saveMutation.mutateAsync({ payload });
        }

        document.getElementById('btnCloseModal')?.click();
        this.resetForm();
      } catch (error) {
        this.toaster.error("Có lỗi xảy ra khi lưu dữ liệu");
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa model <b>"${item.ten_model}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu liên kết có thể bị ảnh hưởng.</small>` ,
        icon: 'warning',
          iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xóa Model',
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
</style>















