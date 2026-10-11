<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Tồn Kho
    </h4>

    <div class="card">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Tồn Kho</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input type="text" class="form-control" placeholder="Tìm kiếm mã, tên sản phẩm..." v-model="filters.search">
          </div>
          <div style="width: 25%;">
            <v-select
              v-model="filters.warehouse"
              :options="[{value: '', label: 'Tất cả kho chứa'}, ...warehouseOptions]"
              placeholder="Kho chứa"
              :can-clear="false"
            />
          </div>
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button v-if="currentUserRole === 'ADMIN'" class="btn btn-primary text-nowrap shadow-sm" @click="openAddModal">
            <i class="bx bx-plus me-1"></i> Thêm Tồn Kho
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center text-uppercase" style="width: 5%">STT</th>
              <th class="text-center text-uppercase" style="width: 10%">Hình ảnh</th>
              <th class="text-uppercase" :style="{ width: currentUserRole === 'ADMIN' ? '35%' : '45%' }">Sản Phẩm</th>
              <th class="text-uppercase" style="width: 20%">Kho Chứa</th>
              <th class="text-center text-uppercase" style="width: 10%">SL Tồn</th>
              <th class="text-center text-uppercase" style="width: 10%">Chờ giao</th>
              <th class="text-center text-uppercase" style="width: 10%" v-if="currentUserRole === 'ADMIN'">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isLoading">
              <td colspan="7" class="text-center py-4 text-muted">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredInventory.length === 0">
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredInventory" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td class="text-center">
                <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 60px; height: 60px; background-color: #fff;">
                  <img v-if="item.hinh_anh" :src="getImageUrl(item.hinh_anh)" alt="img" style="max-height: 40px; max-width: 50px; object-fit: contain;" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'" />
                  <i v-else class="bx bx-image text-muted fs-4"></i>
                </div>
              </td>
              <td>
                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">{{ item.ma_san_pham }}</span>
                <span class="text-primary fw-medium" style="font-size: 0.85rem;">{{ item.ten_san_pham }}</span>
              </td>
              <td>
                <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ getWarehouseName(item.warehouse_id) }}</span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="item.so_luong_ton > 0 ? 'text-dark' : 'text-danger'">{{ item.so_luong_ton }}</span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="item.so_luong_cho_giao > 0 ? 'text-warning' : 'text-secondary'">{{ item.so_luong_cho_giao }}</span>
              </td>
              <td class="text-center" v-if="currentUserRole === 'ADMIN'">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="action-btn text-primary" @click="openEditForm(item)" title="Điều chỉnh số lượng">
                      <i class="bx bx-edit-alt"></i>
                    </button>
                    <button type="button" class="action-btn text-danger" @click="deleteItem(item)" title="Xóa tồn kho">
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
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredInventory && filteredInventory.length) ? filteredInventory.length : 0 }} mục</span>
      </div>
    </div>

    <!-- Modal (Thêm/Sửa) -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" ref="formModal">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom pb-3">
            <h5 class="modal-title fw-bold text-dark" id="modalCenterTitle">{{ isEditMode ? 'Cập Nhật Tồn Kho' : 'Điều Chỉnh Tồn Kho' }}</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            
<div class="modal-body pt-4">
  <!-- Giao diện khi THÊM MỚI (Hiện Dropdown) -->
  <div class="row mb-4" v-if="!isEditMode">
    <div class="col-md-5">
      <label class="form-label fw-medium text-dark">Mã Sản Phẩm <span class="text-danger">*</span></label>
      <v-select
        v-model="formData.product_key"
        :options="productOptions"
        label="code"
        :reduce="option => option.value"
        placeholder="Chọn Mã..."
        searchable
        required
      >
        <template #option="{ option }">
            <span>{{ option.code }}</span>
          </template>
          <template #noResults>
            <span class="text-success fw-medium"><i class="bx bx-check-circle"></i> Đã chọn mã này</span>
          </template>
          <template #noOptions>
            <span class="text-muted">Không có dữ liệu</span>
          </template>
      </v-select>
    </div>
    <div class="col-md-7">
      <label class="form-label fw-medium text-dark">Tên Sản Phẩm <span class="text-danger">*</span></label>
      <v-select
        v-model="formData.product_key"
        :options="productOptions"
        label="name"
        :reduce="option => option.value"
        placeholder="Chọn Tên..."
        searchable
        required
      >
        <template #option="{ option }">
            <span>{{ option.name }}</span>
          </template>
          <template #noResults>
            <span class="text-success fw-medium"><i class="bx bx-check-circle"></i> Đã chọn tên này</span>
          </template>
          <template #noOptions>
            <span class="text-muted">Không có dữ liệu</span>
          </template>
      </v-select>
    </div>
    <div class="col-12 mt-1">
      <small class="text-muted"><i class="bx bx-info-circle"></i> Chỉ cần chọn Mã hoặc Tên, ô còn lại sẽ tự động điền.</small>
    </div>
  </div>

  <!-- Giao diện khi CẬP NHẬT (Chỉ hiện Text, không bị cắt bớt) -->
  <div class="mb-4" v-else>
    <label class="form-label fw-medium text-dark">Sản Phẩm Đang Cập Nhật</label>
    <div class="p-3 bg-label-secondary rounded border border-secondary" style="border-style: dashed !important;">
      <div class="fw-bold text-dark fs-6 mb-1">{{ formData.ma_san_pham }}</div>
      <div class="text-primary fw-medium" style="white-space: normal; word-break: break-word;">{{ formData.ten_san_pham }}</div>
    </div>
  </div>

  <div class="mb-4">
    <label for="warehouse_id" class="form-label fw-medium text-dark">Kho Chứa <span class="text-danger">*</span></label>
    <v-select
      v-model="formData.warehouse_id"
      :options="warehouseOptions"
      :reduce="option => option.value"
      placeholder="Chọn kho..."
      searchable
      required
    />
  </div>

  <div class="mb-4">
    <label for="so_luong_ton" class="form-label fw-medium text-dark">Số Lượng Tồn <span class="text-danger">*</span></label>
    <input
      type="number"
      id="so_luong_ton"
      class="form-control"
      v-model="formData.so_luong_ton"
      min="0"
      required
    />
    <small class="text-muted mt-2 d-block"><i class="bx bx-info-circle"></i> Số lượng "Chờ Giao" được tính tự động từ đơn hàng.</small>
  </div>
</div>
<div class="modal-footer border-top pt-3">
              <button type="button" class="btn btn-outline-secondary px-4" @click="closeModal">
                Hủy
              </button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="saveMutation.isPending.value">
                <span v-if="saveMutation.isPending.value" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Cập Nhật' : 'Lưu Thay Đổi' }}
              </button>
            </div>
          </form>
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
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: "TonKho",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: "top-right", duration: 3000 });

    const fetchTonKho = async () => {
      const res = await axiosClient.get('/admin/ton-kho');
      const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []); return list.map(item => ({
        id: item.id,
        warehouse_id: item.id_kho,
        id_san_pham: item.id_san_pham,
        loai_san_pham: item.loai_san_pham,
        ma_san_pham: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.ma_phu_tung : item.san_pham?.ma_san_pham || '',
        ten_san_pham: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.ten_phu_tung : item.san_pham?.ten_san_pham || '',
        hinh_anh: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.hinh_anh : item.san_pham?.hinh_anh_chinh || '',
        so_luong_ton: item.so_luong_ton,
        so_luong_cho_giao: item.so_luong_dang_giu || 0
      }));
    };

        const fetchSanPhams = async () => {
      const res = await axiosClient.get('/admin/san-pham?all=true');
      const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []);
      return list.map(sp => ({ 
        value: 'san_pham_' + sp.id, 
        code: sp.ma_san_pham || '', 
        name: sp.ten_san_pham || '',
        type: 'Sản Phẩm',
        label: (sp.ma_san_pham || '') + ' - ' + (sp.ten_san_pham || '')
      }));
    };

    const fetchPhuTungs = async () => {
      const res = await axiosClient.get('/admin/phu-tung');
      const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []);
      return list.map(pt => ({ 
        value: 'phu_tung_' + pt.id, 
        code: pt.ma_phu_tung || '', 
        name: pt.ten_phu_tung || '',
        type: 'Phụ Tùng',
        label: (pt.ma_phu_tung || '') + ' - ' + (pt.ten_phu_tung || '')
      }));
    };

    const { data: sanPhams } = useQuery({
      queryKey: ['danh-sach-san-pham'],
      queryFn: fetchSanPhams
    });

    const { data: phuTungs } = useQuery({
      queryKey: ['danh-sach-phu-tung'],
      queryFn: fetchPhuTungs
    });

    const fetchWarehouses = async () => {
      const res = await axiosClient.get('/admin/kho');
      return res.data || [];
    };

    const { data: inventory, isLoading } = useQuery({
      queryKey: ['ton-kho'],
      queryFn: fetchTonKho,
      staleTime: 1000 * 60 * 5
    });

    const { data: warehouses } = useQuery({
      queryKey: ['danh-sach-kho'],
      queryFn: fetchWarehouses,
      staleTime: 1000 * 60 * 5
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete('/admin/ton-kho/' + id),
      onError: (err, id, context) => {
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['ton-kho'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put('/admin/ton-kho/' + payload.id, payload)
          : axiosClient.post('/admin/ton-kho', payload);
      },
      onError: (err, vars, context) => {
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['ton-kho'] });
      }
    });

    return { inventory, isLoading, warehouses, sanPhams, phuTungs, queryClient, deleteMutation, saveMutation, toaster };
  },
  data() {
    return {
      currentUserRole: JSON.parse(localStorage.getItem('auth_user') || '{}').roles?.[0]?.toUpperCase() || 'ADMIN',
      filters: {
        search: '',
        warehouse: ''
      },
      isEditMode: false,
      modalInstance: null,
      formData: this.getInitialFormData()
    };
  },
  computed: {
    productOptions() {
      return [...(this.sanPhams || []), ...(this.phuTungs || [])];
    },
    warehouseOptions() {
      return (this.warehouses || []).map(w => ({ value: w.id, label: w.ten_kho }));
    },
    filteredInventory() {
      return (this.inventory || []).filter(item => {
        const searchRegex = new RegExp(this.filters.search, 'i');
        const matchSearch = searchRegex.test(item.ma_san_pham) || searchRegex.test(item.ten_san_pham);
        const matchWarehouse = this.filters.warehouse ? item.warehouse_id === this.filters.warehouse : true;
        return matchSearch && matchWarehouse;
      });
    }
  },
  mounted() {
    this.modalInstance = new window.bootstrap.Modal(this.$refs.formModal, {
      backdrop: 'static',
      keyboard: false
    });
  },
  methods: {
      getImageUrl(path) {
        if (!path) return "";
        if (path.startsWith("blob:") || path.startsWith("http")) return path;
        // Ensure starting slash
        return `http://localhost:8000${path.startsWith("/") ? "" : "/"}${path}`;
      },
    resetFilters() {
      this.filters.search = '';
      this.filters.warehouse = '';
    },
    getWarehouseName(id) {
      const w = (this.warehouses || []).find(x => x.id === id);
      return w ? w.ten_kho : 'Không xác định';
    },
    getInitialFormData() {
      return {
        id: null,
        warehouse_id: null,
        ma_san_pham: "",
        ten_san_pham: "",
        so_luong_ton: 0,
        so_luong_cho_giao: 0
      };
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    openAddModal() {
      this.resetForm();
      this.modalInstance.show();
    },
    openEditForm(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      this.modalInstance.show();
    },
    closeModal() {
      this.modalInstance.hide();
      this.resetForm();
    },
    async saveData() {
        if (!this.formData.warehouse_id || (!this.isEditMode && !this.formData.product_key)) {
          this.toaster.error("Vui lòng điền các trường bắt buộc!");
          return;
        }

        let id_san_pham = null;
        let loai_san_pham = null;

        if (!this.isEditMode && this.formData.product_key) {
          const parts = this.formData.product_key.split("_");
          id_san_pham = parts.pop();
          loai_san_pham = parts.join("_");
        }

        const payload = {
          id: this.formData.id,
          id_kho: this.formData.warehouse_id, // Fix BUG-002: mapped to id_kho
          id_san_pham: id_san_pham,           // Fix BUG-001
          loai_san_pham: loai_san_pham,       // Fix BUG-001
          so_luong_ton: this.formData.so_luong_ton
        };

        this.saveMutation.mutate(payload, {
        onSuccess: () => {
          this.toaster.success(this.isEditMode ? "Cập nhật tồn kho thành công!" : "Thêm tồn kho thành công!");
          this.closeModal();
        },
        onError: (error) => {
          this.toaster.error(error.response?.data?.message || "Lỗi khi lưu dữ liệu tồn kho!");
        }
      });
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: 'Bạn có chắc chắn muốn xóa mã <b>"' + item.ma_san_pham + '"</b> khỏi kho?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu sau khi xóa sẽ không thể khôi phục.</small>',
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xóa Tồn kho',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up shadow-sm',
          confirmButton: 'btn btn-danger px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            this.toaster.success("Đã xóa tồn kho!");
          },
          onError: () => {
            this.toaster.error("Lỗi khi xóa!");
          }
        });
      }
    }
  }
};
</script>

<style scoped>
.action-btn {
  background: transparent;
  border: none;
  padding: 0.25rem 0.5rem;
  font-size: 1.2rem;
  transition: all 0.2s;
  border-radius: 4px;
}
.action-btn:hover {
  background-color: rgba(0,0,0,0.05);
}
.custom-scroll::-webkit-scrollbar {
  width: 6px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #d9dee3;
  border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}
</style>



