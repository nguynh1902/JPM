<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Xuất / Nhập / Tồn
    </h4>

    <div class="card">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">Lịch Sử Xuất / Nhập / Tồn</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input
              type="text"
              class="form-control"
              placeholder="Tìm kiếm mã, tên sản phẩm, mã tham chiếu..."
              v-model="filters.ma_san_pham"
            />
          </div>
          <div style="width: 170px; flex-shrink: 0;">
            <v-select
              v-model="filters.warehouse_id"
              :options="[{value: null, label: 'Tất cả Kho chứa'}, ...warehouseOptions]"
              placeholder="Kho chứa"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 185px; flex-shrink: 0;">
            <v-select
              v-model="filters.loai_giao_dich"
              :options="[
                {value: '', label: 'Tất cả loại giao dịch'},
                {value: 'NHAP_KHO', label: 'Nhập Kho'},
                {value: 'XUAT_KHO', label: 'Xuất Kho'}
              ]"
              placeholder="Loại Giao Dịch"
              :can-clear="false"
            />
          </div>
          <div class="d-flex align-items-center gap-1 flex-shrink-0">
            <input
              type="date"
              class="form-control px-2"
              v-model="filters.tu_ngay"
              title="Từ ngày"
              style="width: 135px; font-size: 0.85rem;"
            />
            <span class="text-muted">-</span>
            <input
              type="date"
              class="form-control px-2"
              v-model="filters.den_ngay"
              title="Đến ngày"
              style="width: 135px; font-size: 0.85rem;"
            />
          </div>
          <button class="btn btn-secondary text-nowrap flex-shrink-0" @click="resetFilters">
            Đặt lại
          </button>
          <button class="btn btn-primary text-nowrap shadow-sm flex-shrink-0" @click="exportExcel">
            <i class="bx bx-export me-1"></i> Xuất Excel
          </button>
        </div>
      </div>

      <!-- Bảng dữ liệu giao dịch cuộn toàn bộ chuẩn TonKho.vue & PhuTung.vue -->
      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center text-uppercase" style="width: 5%">STT</th>
              <th class="text-uppercase" style="width: 15%">Ngày Giao Dịch</th>
              <th class="text-uppercase" style="width: 30%">Sản Phẩm</th>
              <th class="text-uppercase" style="width: 13%">Kho</th>
              <th class="text-center text-uppercase" style="width: 11%">Loại</th>
              <th class="text-center text-uppercase" style="width: 8%">Biến Động</th>
              <th class="text-center text-uppercase" style="width: 8%">Tồn Cuối</th>
              <th class="text-uppercase" style="width: 10%">Người Thực Hiện</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <!-- Loading State -->
            <tr v-if="isLoading">
              <td colspan="8" class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status">
                  <span class="visually-hidden">Đang tải...</span>
                </div>
                <div>Đang tải lịch sử giao dịch kho...</div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-else-if="filteredTransactions.length === 0">
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>

            <!-- Rows -->
            <tr v-for="(item, index) in filteredTransactions" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <span class="fw-medium text-dark d-block" style="font-size: 0.95rem;">{{ item.date }}</span>
                <span class="text-secondary" style="font-size: 0.8rem;">{{ item.time }}</span>
              </td>

              <td>
                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">{{ item.ma_san_pham }}</span>
                <span class="text-primary fw-medium" style="font-size: 0.85rem; white-space: normal; display: block; max-width: 320px;">
                  {{ item.ten_san_pham }}
                </span>
              </td>
              <td>
                <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                  {{ getWarehouseName(item.warehouse_id) }}
                </span>
              </td>
              <td class="text-center">
                <span class="badge badge-custom" :class="getTransactionBadgeClass(item.loai_giao_dich)">
                  {{ getTransactionLabel(item.loai_giao_dich) }}
                </span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="isPositiveChange(item) ? 'text-primary' : 'text-danger'">
                  {{ isPositiveChange(item) ? '+' : '-' }}{{ Math.abs(item.so_luong) }}
                </span>
              </td>
              <td class="text-center">
                <span class="fw-bold text-dark fs-6">{{ item.ton_cuoi }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-2">
                    <img
                      v-if="item.nguoi_thuc_hien_avatar"
                      :src="getImageUrl(item.nguoi_thuc_hien_avatar)"
                      class="rounded-circle"
                      style="width: 32px; height: 32px; object-fit: cover;"
                    />
                    <span v-else class="avatar-initial rounded-circle bg-label-primary">
                      {{ (item.nguoi_thuc_hien || 'Admin').charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <span class="text-dark fw-medium" style="font-size: 0.85rem;">
                    {{ item.nguoi_thuc_hien || 'Admin' }}
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredTransactions && filteredTransactions.length) ? filteredTransactions.length : 0 }} mục</span>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import { useQuery } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: "TheKho",
  components: {
    vSelect
  },
  setup() {
    const toaster = createToaster({ position: "top-right", duration: 3000 });

    const fetchWarehouses = async () => {
      const res = await axiosClient.get('/admin/kho');
      return res.data || [];
    };

    const fetchTheKho = async () => {
      try {
        const res = await axiosClient.get('/admin/the-kho', { params: { per_page: 'all' } });
        const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []);
        if (!Array.isArray(list)) {
          console.error("Dữ liệu không phải là mảng:", list, res.data);
          return [];
        }
        return list.map(item => {
          const d = new Date(item.created_at);
          const executor = item.nguoi_thuc_hien?.name || 'Admin';
          const avatar = item.nguoi_thuc_hien?.avatar || null;
          return {
            id: item.id,
            warehouse_id: item.id_kho,
            ma_san_pham: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.ma_phu_tung : item.san_pham?.ma_san_pham || '',
            ten_san_pham: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.ten_phu_tung : item.san_pham?.ten_san_pham || '',
            hinh_anh: item.loai_san_pham === 'phu_tung' ? item.phu_tung?.hinh_anh : item.san_pham?.hinh_anh_chinh || '',
            loai_giao_dich: item.loai_giao_dich,
            so_luong: item.so_luong_thay_doi || 0,
            ton_cuoi: item.so_luong_ton_cuoi || 0,
            tham_chieu: item.ma_tham_chieu || '',
            nguoi_thuc_hien: executor,
            nguoi_thuc_hien_avatar: avatar,
            date: d.toLocaleDateString('vi-VN'),
            time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
            raw_date: d,
            ghi_chu: item.ghi_chu || ''
          };
        });
      } catch (err) {
        console.error("Lỗi khi tải lịch sử thẻ kho:", err);
        return [];
      }
    };

    const { data: warehouses } = useQuery({
      queryKey: ['danh-sach-kho'],
      queryFn: fetchWarehouses,
      staleTime: 1000 * 60 * 5
    });

    const { data: transactions, isLoading } = useQuery({
      queryKey: ['the-kho'],
      queryFn: fetchTheKho
    });

    return { warehouses, transactions, isLoading, toaster };
  },
  data() {
    return {
      filters: {
        warehouse_id: null,
        ma_san_pham: "",
        loai_giao_dich: "",
        tu_ngay: "",
        den_ngay: ""
      }
    };
  },
  computed: {
    warehouseOptions() {
      return (this.warehouses || []).map(w => ({ value: w.id, label: w.ten_kho }));
    },
    filteredTransactions() {
      let result = this.transactions || [];
      if (this.filters.warehouse_id) {
        result = result.filter(t => t.warehouse_id === this.filters.warehouse_id);
      }
      if (this.filters.ma_san_pham) {
        const kw = this.filters.ma_san_pham.toLowerCase().trim();
        result = result.filter(t =>
          String(t.ma_san_pham || '').toLowerCase().includes(kw) ||
          String(t.ten_san_pham || '').toLowerCase().includes(kw) ||
          String(t.tham_chieu || '').toLowerCase().includes(kw) ||
          String(t.ghi_chu || '').toLowerCase().includes(kw)
        );
      }
      if (this.filters.loai_giao_dich) {
        result = result.filter(t => t.loai_giao_dich === this.filters.loai_giao_dich);
      }
      if (this.filters.tu_ngay) {
        const tuNgay = new Date(this.filters.tu_ngay);
        tuNgay.setHours(0, 0, 0, 0);
        result = result.filter(t => t.raw_date >= tuNgay);
      }
      if (this.filters.den_ngay) {
        const denNgay = new Date(this.filters.den_ngay);
        denNgay.setHours(23, 59, 59, 999);
        result = result.filter(t => t.raw_date <= denNgay);
      }
      return result;
    }
  },
  methods: {
    resetFilters() {
      this.filters.ma_san_pham = '';
      this.filters.warehouse_id = null;
      this.filters.loai_giao_dich = '';
      this.filters.tu_ngay = '';
      this.filters.den_ngay = '';
    },
    getTransactionBadgeClass(type) {
      switch (type) {
        case 'NHAP_KHO':
          return 'bg-pastel-success';
        case 'XUAT_KHO':
          return 'bg-pastel-danger';
        default:
          return 'bg-pastel-secondary';
      }
    },
    getTransactionLabel(type) {
      switch (type) {
        case 'NHAP_KHO':
          return 'NHẬP KHO';
        case 'XUAT_KHO':
          return 'XUẤT KHO';
        default:
          return type || 'KHÁC';
      }
    },
    isPositiveChange(item) {
      if (item.so_luong > 0) return true;
      if (item.loai_giao_dich === 'NHAP_KHO') return true;
      return false;
    },
    exportExcel() {
      if (!this.filteredTransactions || this.filteredTransactions.length === 0) {
        this.toaster.error("Không có dữ liệu giao dịch để xuất!");
        return;
      }
      const headers = [
        "STT",
        "Ngày giao dịch",
        "Giờ",
        "Mã tham chiếu",
        "Ghi chú",
        "Mã sản phẩm",
        "Tên sản phẩm",
        "Kho chứa",
        "Loại giao dịch",
        "Biến động",
        "Tồn cuối",
        "Người thực hiện"
      ];
      const rows = this.filteredTransactions.map((t, idx) => [
        idx + 1,
        `"${t.date}"`,
        `"${t.time}"`,
        `"${t.tham_chieu || ''}"`,
        `"${(t.ghi_chu || '').replace(/"/g, '""')}"`,
        `"${t.ma_san_pham}"`,
        `"${(t.ten_san_pham || '').replace(/"/g, '""')}"`,
        `"${this.getWarehouseName(t.warehouse_id)}"`,
        `"${this.getTransactionLabel(t.loai_giao_dich)}"`,
        t.so_luong,
        t.ton_cuoi,
        `"${t.nguoi_thuc_hien}"`
      ]);

      const csvContent = "\uFEFF" + [headers.join(","), ...rows.map(r => r.join(","))].join("\r\n");
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      const now = new Date();
      const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}_${String(now.getHours()).padStart(2, '0')}${String(now.getMinutes()).padStart(2, '0')}`;
      link.setAttribute("href", url);
      link.setAttribute("download", `Lich_Su_Giao_Dich_Kho_${dateStr}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
      this.toaster.success(`Đã xuất thành công ${this.filteredTransactions.length} giao dịch sang file Excel (CSV)!`);
    },
    getWarehouseName(id) {
      const w = (this.warehouses || []).find(x => x.id === id);
      return w ? w.ten_kho : 'Không xác định';
    }
  }
};
</script>
