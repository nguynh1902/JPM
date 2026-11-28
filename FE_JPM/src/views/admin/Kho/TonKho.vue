<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Tồn Kho
    </h4>

    <div class="card">
      <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Tồn Kho</h5>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge bg-label-primary px-3 py-2">
            Tổng tồn kho: <strong>{{ totalStockQty.toLocaleString('vi-VN') }}</strong>
          </span>
          <span v-if="totalPendingQty > 0" class="badge bg-label-warning px-3 py-2">
            Chờ giao / Cần nhập: <strong>{{ totalPendingQty.toLocaleString('vi-VN') }}</strong>
          </span>
        </div>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="flex-grow-1" style="min-width: 220px;">
            <input type="text" class="form-control" placeholder="Tìm kiếm mã, tên sản phẩm..." v-model="filters.search">
          </div>
          <div style="width: 220px;">
            <v-select
              v-model="filters.warehouse"
              :options="[{ value: '', label: 'Tất cả kho chứa' }, ...warehouseOptions]"
              placeholder="Kho chứa"
              :can-clear="false"
            />
          </div>
          <div style="width: 190px;">
            <v-select
              v-model="filters.stockState"
              :options="[
                { value: '', label: 'Tất cả trạng thái tồn' },
                { value: 'in_stock', label: 'Còn hàng (Tồn > 0)' },
                { value: 'out_of_stock', label: 'Hết hàng (Tồn = 0)' },
                { value: 'pending', label: 'Có hàng chờ giao' }
              ]"
              placeholder="Trạng thái tồn"
              :can-clear="false"
            />
          </div>
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button v-if="canUpdateInventory" class="btn btn-primary text-nowrap shadow-sm" @click="openAddModal">
            <i class="bx bx-plus me-1"></i> Thêm Tồn Kho
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 280px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="text-center text-uppercase" style="width: 5%">STT</th>
              <th class="text-center text-uppercase" style="width: 9%">Hình ảnh</th>
              <th class="text-uppercase" :style="{ width: canUpdateInventory ? '34%' : '44%' }">Sản Phẩm / Phụ Tùng</th>
              <th class="text-uppercase" style="width: 20%">Kho Chứa</th>
              <th class="text-center text-uppercase" style="width: 11%">SL Tồn</th>
              <th class="text-center text-uppercase" style="width: 11%">Chờ giao</th>
              <th class="text-center text-uppercase" style="width: 10%" v-if="canUpdateInventory">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isLoading">
              <td :colspan="canUpdateInventory ? 7 : 6" class="text-center py-4 text-muted">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredInventory.length === 0">
              <td :colspan="canUpdateInventory ? 7 : 6" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredInventory" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td class="text-center">
                <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 56px; height: 56px; background-color: #fff;">
                  <img
                    v-if="item.hinh_anh && !brokenImages[item.id]"
                    :src="getImageUrl(item.hinh_anh)"
                    alt="img"
                    style="max-height: 44px; max-width: 48px; object-fit: contain;"
                    @error="markImageBroken(item.id)"
                  />
                  <i v-else class="bx bx-image text-muted fs-4"></i>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ item.ma_san_pham || '---' }}</span>
                  <span class="badge bg-label-secondary" style="font-size: 0.7rem;">
                    {{ item.loai_san_pham === 'san_pham' ? 'Sản phẩm' : 'Phụ tùng' }}
                  </span>
                </div>
                <span class="text-primary fw-medium d-block text-wrap" style="font-size: 0.85rem; max-width: 360px;">
                  {{ item.ten_san_pham || '---' }}
                </span>
              </td>
              <td>
                <span class="text-secondary fw-bold text-uppercase d-block" style="font-size: 0.78rem; letter-spacing: 0.4px;">
                  {{ getWarehouseName(item) }}
                </span>
                <span v-if="item.kho_deleted_at" class="badge bg-label-danger mt-1" style="font-size: 0.68rem;">Kho đã xóa</span>
                <span v-else-if="item.kho_trang_thai === 0" class="badge bg-label-warning mt-1" style="font-size: 0.68rem;">Đang tạm khóa</span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="item.so_luong_ton > 0 ? 'text-dark' : 'text-danger'">{{ item.so_luong_ton }}</span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="item.so_luong_cho_giao > 0 ? 'text-warning' : 'text-secondary'">{{ item.so_luong_cho_giao }}</span>
              </td>
              <td class="text-center" v-if="canUpdateInventory">
                <div class="d-flex align-items-center justify-content-center gap-2">
                  <button type="button" class="action-btn text-primary" @click="openEditForm(item)" title="Điều chỉnh số lượng">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="action-btn text-danger" @click="confirmDelete(item)" title="Xóa tồn kho">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ filteredInventory.length }} mục</span>
      </div>
    </div>

    <!-- Modal (Thêm/Sửa) -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" ref="formModal">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom pb-3">
            <h5 class="modal-title fw-bold text-dark" id="modalCenterTitle">
              {{ isEditMode ? 'Điều Chỉnh Tồn Kho' : 'Thêm Tồn Kho Mới' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pt-4">
              <!-- Giao diện khi THÊM MỚI -->
              <div class="row mb-4" v-if="!isEditMode">
                <div class="col-md-5">
                  <label class="form-label fw-medium text-dark">Mã Sản Phẩm / Phụ Tùng <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.product_key"
                    :options="productOptions"
                    label="code"
                    placeholder="Tìm theo mã..."
                    searchable
                  >
                    <template #option="{ option }">
                      <div>
                        <span class="fw-bold text-dark">{{ option.code }}</span>
                        <small class="text-muted d-block text-truncate" style="max-width: 240px;">{{ option.name }}</small>
                      </div>
                    </template>
                    <template #noResults>
                      <span class="text-muted">Không tìm thấy mã phù hợp</span>
                    </template>
                    <template #noOptions>
                      <span class="text-muted">Đang tải hoặc chưa có dữ liệu</span>
                    </template>
                  </v-select>
                </div>
                <div class="col-md-7">
                  <label class="form-label fw-medium text-dark">Tên Sản Phẩm / Phụ Tùng <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.product_key"
                    :options="productOptions"
                    label="name"
                    placeholder="Tìm theo tên..."
                    searchable
                  >
                    <template #option="{ option }">
                      <div>
                        <span class="fw-medium text-dark">{{ option.name }}</span>
                        <small class="text-primary d-block">{{ option.code }} · {{ option.type }}</small>
                      </div>
                    </template>
                    <template #noResults>
                      <span class="text-muted">Không tìm thấy sản phẩm phù hợp</span>
                    </template>
                    <template #noOptions>
                      <span class="text-muted">Đang tải hoặc chưa có dữ liệu</span>
                    </template>
                  </v-select>
                </div>
                <div class="col-12 mt-1">
                  <small class="text-muted"><i class="bx bx-info-circle"></i> Chỉ cần chọn Mã hoặc Tên, ô còn lại sẽ tự động đồng bộ.</small>
                </div>
              </div>

              <!-- Giao diện khi CẬP NHẬT -->
              <div class="mb-4" v-else>
                <label class="form-label fw-medium text-dark">Sản Phẩm &amp; Kho Đang Điều Chỉnh</label>
                <div class="p-3 bg-label-secondary rounded border border-secondary" style="border-style: dashed !important;">
                  <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                      <div class="fw-bold text-dark fs-6 mb-1">{{ formData.ma_san_pham }}</div>
                      <div class="text-primary fw-medium" style="white-space: normal; word-break: break-word;">{{ formData.ten_san_pham }}</div>
                    </div>
                    <span class="badge bg-label-dark text-uppercase">{{ getWarehouseName(formData) }}</span>
                  </div>
                </div>
              </div>

              <div class="mb-4" v-if="!isEditMode">
                <label for="warehouse_id" class="form-label fw-medium text-dark">Kho Chứa <span class="text-danger">*</span></label>
                <v-select
                  v-model="formData.warehouse_id"
                  :options="activeWarehouseOptions"
                  placeholder="Chọn kho đang hoạt động..."
                  searchable
                />
                <small v-if="activeWarehouseOptions.length === 0" class="text-danger d-block mt-1">
                  Hiện chưa có kho nào ở trạng thái Hoạt Động.
                </small>
              </div>

              <div class="row mb-4">
                <div class="col-md-5">
                  <label for="so_luong_ton" class="form-label fw-medium text-dark">Số Lượng Tồn <span class="text-danger">*</span></label>
                  <input
                    type="number"
                    id="so_luong_ton"
                    class="form-control"
                    v-model.number="formData.so_luong_ton"
                    min="0"
                    step="1"
                    required
                  />
                  <small v-if="isEditMode" class="text-muted mt-1 d-block">
                    Tồn trước điều chỉnh: <strong>{{ originalStockQty }}</strong>
                    <span v-if="stockDiff !== 0" :class="stockDiff > 0 ? 'text-success fw-bold' : 'text-danger fw-bold'">
                      ({{ stockDiff > 0 ? '+' + stockDiff : stockDiff }})
                    </span>
                  </small>
                </div>
                <div class="col-md-7">
                  <label for="ghi_chu" class="form-label fw-medium text-dark">Ghi chú / Lý do</label>
                  <input
                    type="text"
                    id="ghi_chu"
                    class="form-control"
                    v-model.trim="formData.ghi_chu"
                    :placeholder="isEditMode ? 'VD: Kiểm kê thực tế, hàng hư hỏng...' : 'VD: Khởi tạo tồn kho đầu kỳ...'"
                    maxlength="255"
                  />
                </div>
              </div>

              <small class="text-muted d-block">
                <i class="bx bx-info-circle"></i> Số lượng "Chờ giao" được hệ thống tự động giữ chỗ từ đơn đặt hàng thiếu tồn và giải tỏa khi nhập PO.
              </small>
            </div>

            <div class="modal-footer border-top pt-3">
              <button type="button" class="btn btn-outline-secondary px-4" @click="closeModal">
                Hủy
              </button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="saveMutation.isPending.value">
                <span v-if="saveMutation.isPending.value" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Cập Nhật Tồn Kho' : 'Thêm Tồn Kho' }}
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
import { useAuthStore } from '../../../stores/authStore';
import { createToaster } from '../../../utils/notify';

export default {
  name: "TonKho",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });

    const fetchTonKho = async () => {
      const res = await axiosClient.get('/admin/ton-kho');
      const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []);
      return list.map(item => ({
        id: item.id,
        warehouse_id: item.id_kho,
        ten_kho: item.kho?.ten_kho || '',
        kho_trang_thai: item.kho?.trang_thai ?? 1,
        kho_deleted_at: item.kho?.deleted_at || null,
        id_san_pham: item.id_san_pham,
        loai_san_pham: item.loai_san_pham || 'phu_tung',
        ma_san_pham: (item.loai_san_pham === 'san_pham' ? item.san_pham?.ma_san_pham : item.phu_tung?.ma_phu_tung) || '',
        ten_san_pham: (item.loai_san_pham === 'san_pham' ? item.san_pham?.ten_san_pham : item.phu_tung?.ten_phu_tung) || '',
        hinh_anh: (item.loai_san_pham === 'san_pham' ? item.san_pham?.hinh_anh_chinh : item.phu_tung?.hinh_anh) || '',
        so_luong_ton: Number(item.so_luong_ton ?? 0),
        so_luong_cho_giao: Number(item.so_luong_dang_giu ?? 0)
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
      const res = await axiosClient.get('/admin/phu-tung?all=true');
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
      queryFn: fetchSanPhams,
      staleTime: 1000 * 60 * 5
    });

    const { data: phuTungs } = useQuery({
      queryKey: ['danh-sach-phu-tung'],
      queryFn: fetchPhuTungs,
      staleTime: 1000 * 60 * 5
    });

    const fetchWarehouses = async () => {
      const res = await axiosClient.get('/admin/kho');
      return Array.isArray(res.data) ? res.data : [];
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

    const invalidateInventoryQueries = () => {
      queryClient.invalidateQueries({ queryKey: ['ton-kho'] });
      queryClient.invalidateQueries({ queryKey: ['the-kho'] });
      queryClient.invalidateQueries({ queryKey: ['bao-cao-xnt'] });
    };

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete('/admin/ton-kho/' + id),
      onSettled: invalidateInventoryQueries
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id
          ? axiosClient.put('/admin/ton-kho/' + payload.id, payload)
          : axiosClient.post('/admin/ton-kho', payload);
      },
      onSettled: invalidateInventoryQueries
    });

    return { authStore, inventory, isLoading, warehouses, sanPhams, phuTungs, queryClient, deleteMutation, saveMutation, toaster };
  },
  data() {
    return {
      filters: {
        search: '',
        warehouse: '',
        stockState: ''
      },
      isEditMode: false,
      originalStockQty: 0,
      modalInstance: null,
      brokenImages: {},
      formData: this.getInitialFormData()
    };
  },
  computed: {
    canUpdateInventory() {
      return this.authStore?.isAdmin
        || this.authStore?.hasPermission('Cập nhật Tồn kho')
        || this.authStore?.hasPermission('Quản lý Danh sách kho');
    },
    productOptions() {
      return [...(this.sanPhams || []), ...(this.phuTungs || [])];
    },
    warehouseOptions() {
      return (this.warehouses || []).map(w => ({
        value: w.id,
        label: Number(w.trang_thai) === 0 ? `${w.ten_kho} (Tạm khóa)` : w.ten_kho
      }));
    },
    activeWarehouseOptions() {
      return (this.warehouses || [])
        .filter(w => Number(w.trang_thai) === 1)
        .map(w => ({ value: w.id, label: w.ten_kho }));
    },
    filteredInventory() {
      return (this.inventory || []).filter(item => {
        const s = (this.filters.search || '').toLowerCase().trim();
        const matchSearch = !s ||
          String(item.ma_san_pham || '').toLowerCase().includes(s) ||
          String(item.ten_san_pham || '').toLowerCase().includes(s);
        const matchWarehouse = this.filters.warehouse ? Number(item.warehouse_id) === Number(this.filters.warehouse) : true;
        let matchState = true;
        if (this.filters.stockState === 'in_stock') {
          matchState = item.so_luong_ton > 0;
        } else if (this.filters.stockState === 'out_of_stock') {
          matchState = item.so_luong_ton <= 0;
        } else if (this.filters.stockState === 'pending') {
          matchState = item.so_luong_cho_giao > 0;
        }
        return matchSearch && matchWarehouse && matchState;
      });
    },
    totalStockQty() {
      return this.filteredInventory.reduce((sum, item) => sum + Number(item.so_luong_ton || 0), 0);
    },
    totalPendingQty() {
      return this.filteredInventory.reduce((sum, item) => sum + Number(item.so_luong_cho_giao || 0), 0);
    },
    stockDiff() {
      return Number(this.formData.so_luong_ton || 0) - Number(this.originalStockQty || 0);
    }
  },
  mounted() {
    this.modalInstance = new window.bootstrap.Modal(this.$refs.formModal, {
      backdrop: 'static',
      keyboard: false
    });
  },
  methods: {
    markImageBroken(id) {
      this.brokenImages = { ...this.brokenImages, [id]: true };
    },
    resetFilters() {
      this.filters.search = '';
      this.filters.warehouse = '';
      this.filters.stockState = '';
    },
    getWarehouseName(item) {
      if (item && item.ten_kho) return item.ten_kho;
      const id = typeof item === 'object' ? item?.warehouse_id : item;
      const w = (this.warehouses || []).find(x => x.id === id);
      return w ? w.ten_kho : 'Không xác định';
    },
    getInitialFormData() {
      return {
        id: null,
        product_key: null,
        warehouse_id: null,
        ten_kho: "",
        ma_san_pham: "",
        ten_san_pham: "",
        so_luong_ton: 0,
        so_luong_cho_giao: 0,
        ghi_chu: ""
      };
    },
    resetForm() {
      this.isEditMode = false;
      this.originalStockQty = 0;
      this.formData = this.getInitialFormData();
    },
    openAddModal() {
      this.resetForm();
      if (this.activeWarehouseOptions.length === 1) {
        this.formData.warehouse_id = this.activeWarehouseOptions[0].value;
      }
      this.modalInstance.show();
    },
    openEditForm(item) {
      this.isEditMode = true;
      this.originalStockQty = Number(item.so_luong_ton ?? 0);
      this.formData = {
        ...item,
        ghi_chu: ""
      };
      this.modalInstance.show();
    },
    closeModal() {
      this.modalInstance.hide();
      this.resetForm();
    },
    async saveData() {
      if (!this.formData.warehouse_id || (!this.isEditMode && !this.formData.product_key)) {
        this.toaster.error("Vui lòng chọn đầy đủ sản phẩm và kho chứa!");
        return;
      }

      const qty = Number(this.formData.so_luong_ton);
      if (!Number.isInteger(qty) || qty < 0) {
        this.toaster.error("Số lượng tồn kho phải là số nguyên không âm!");
        return;
      }

      let id_san_pham = null;
      let loai_san_pham = null;

      if (!this.isEditMode && this.formData.product_key) {
        const parts = String(this.formData.product_key).split("_");
        id_san_pham = parts.pop();
        loai_san_pham = parts.join("_");
      }

      const payload = {
        id: this.formData.id,
        id_kho: this.formData.warehouse_id,
        id_san_pham,
        loai_san_pham,
        so_luong_ton: qty,
        ghi_chu: String(this.formData.ghi_chu || '').trim() || undefined
      };

      this.saveMutation.mutate(payload, {
        onSuccess: () => {
          this.toaster.success(this.isEditMode ? "Cập nhật tồn kho thành công!" : "Thêm tồn kho thành công!");
          this.closeModal();
        },
        onError: (error) => {
          this.toaster.error(error?.response?.data?.message || "Lỗi khi lưu dữ liệu tồn kho!");
        }
      });
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa mã <b>"${item.ma_san_pham || item.ten_san_pham}"</b> khỏi kho?<br><br><small class="text-danger">Lưu ý: Chỉ xóa được dòng khi tồn kho và chờ giao đều bằng 0.</small>`,
        icon: 'warning',
        iconColor: '#dc3545',
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
          onError: (error) => {
            this.toaster.error(error?.response?.data?.message || "Không thể xóa dòng tồn kho!");
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
