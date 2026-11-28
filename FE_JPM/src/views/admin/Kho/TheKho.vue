<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Xuất / Nhập / Tồn
    </h4>

    <div class="card">
      <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <ul class="nav nav-pills card-header-pills" role="tablist">
          <li class="nav-item">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'xnt' }"
              @click="switchTab('xnt')"
            >
              <i class="bx bx-bar-chart-alt-2 me-1"></i> Tổng Hợp Xuất / Nhập / Tồn
            </button>
          </li>
          <li class="nav-item">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'ledger' }"
              @click="switchTab('ledger')"
            >
              <i class="bx bx-history me-1"></i> Chi Tiết Thẻ Kho (Giao Dịch)
            </button>
          </li>
        </ul>

        <button class="btn btn-primary text-nowrap shadow-sm" @click="exportExcel">
          <i class="bx bx-export me-1"></i> Xuất Excel (CSV)
        </button>
      </div>

      <!-- Bộ lọc dùng chung, truy vấn trực tiếp từ Server -->
      <div class="card-body pt-3 pb-3 border-bottom">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="flex-grow-1" style="min-width: 220px;">
            <input
              type="text"
              class="form-control"
              :placeholder="activeTab === 'xnt' ? 'Tìm mã hoặc tên sản phẩm / phụ tùng...' : 'Tìm mã SP, tên SP, mã tham chiếu (DH/PO/DC), ghi chú...'"
              v-model="filters.keyword"
            />
          </div>
          <div style="width: 190px; flex-shrink: 0;">
            <v-select
              v-model="filters.warehouse_id"
              :options="[{ value: null, label: 'Tất cả Kho chứa' }, ...warehouseOptions]"
              placeholder="Kho chứa"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div v-if="activeTab === 'ledger'" style="width: 185px; flex-shrink: 0;">
            <v-select
              v-model="filters.loai_giao_dich"
              :options="[
                { value: '', label: 'Tất cả loại giao dịch' },
                { value: 'NHAP_KHO', label: 'Nhập Kho' },
                { value: 'XUAT_KHO', label: 'Xuất Kho' },
                { value: 'DIEU_CHINH', label: 'Điều Chỉnh' }
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
              style="width: 138px; font-size: 0.85rem;"
            />
            <span class="text-muted">-</span>
            <input
              type="date"
              class="form-control px-2"
              v-model="filters.den_ngay"
              title="Đến ngày"
              style="width: 138px; font-size: 0.85rem;"
            />
          </div>
          <button class="btn btn-secondary text-nowrap flex-shrink-0" @click="resetFilters">
            Đặt lại
          </button>
        </div>

        <!-- Thanh tổng hợp nhanh của kỳ đang chọn -->
        <div v-if="activeTab === 'xnt'" class="d-flex flex-wrap gap-2 mt-3">
          <span class="badge bg-label-secondary px-3 py-2">
            Tồn đầu kỳ: <strong>{{ xntTotals.ton_dau_ky.toLocaleString('vi-VN') }}</strong>
          </span>
          <span class="badge bg-label-success px-3 py-2">
            Tổng nhập: <strong>+{{ xntTotals.nhap_trong_ky.toLocaleString('vi-VN') }}</strong>
          </span>
          <span class="badge bg-label-danger px-3 py-2">
            Tổng xuất: <strong>-{{ xntTotals.xuat_trong_ky.toLocaleString('vi-VN') }}</strong>
          </span>
          <span v-if="xntTotals.dieu_chinh_trong_ky !== 0" class="badge bg-label-warning px-3 py-2">
            Điều chỉnh: <strong>{{ formatSignedNumber(xntTotals.dieu_chinh_trong_ky) }}</strong>
          </span>
          <span class="badge bg-label-primary px-3 py-2">
            Tồn cuối kỳ: <strong>{{ xntTotals.ton_cuoi_ky.toLocaleString('vi-VN') }}</strong>
          </span>
          <span v-if="xntTotals.so_luong_dang_giu > 0" class="badge bg-label-info px-3 py-2">
            Đang giữ (Chờ PO): <strong>{{ xntTotals.so_luong_dang_giu.toLocaleString('vi-VN') }}</strong>
          </span>
        </div>
      </div>

      <!-- TAB 1: TỔNG HỢP XUẤT - NHẬP - TỒN THEO KỲ -->
      <div v-if="activeTab === 'xnt'">
        <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 330px); min-height: 380px; overflow-y: auto;">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase" style="width: 5%">STT</th>
                <th class="text-uppercase" style="width: 30%">Sản Phẩm / Phụ Tùng</th>
                <th class="text-uppercase" style="width: 15%">Kho Chứa</th>
                <th class="text-center text-uppercase" style="width: 10%">Tồn Đầu Kỳ</th>
                <th class="text-center text-uppercase" style="width: 9%">Nhập Kỳ</th>
                <th class="text-center text-uppercase" style="width: 9%">Xuất Kỳ</th>
                <th class="text-center text-uppercase" style="width: 9%">Điều Chỉnh</th>
                <th class="text-center text-uppercase" style="width: 10%">Tồn Cuối Kỳ</th>
                <th class="text-center text-uppercase" style="width: 8%">Chờ Giao</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              <tr v-if="isLoadingXnt">
                <td colspan="9" class="text-center py-5 text-muted">
                  <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                  </div>
                  <div>Đang tổng hợp báo cáo Xuất / Nhập / Tồn...</div>
                </td>
              </tr>
              <tr v-else-if="xntRows.length === 0">
                <td colspan="9" class="text-center py-5 text-muted">
                  <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                  Không có dữ liệu trong kỳ đã chọn
                </td>
              </tr>
              <tr v-for="(row, index) in xntRows" :key="row.key" v-else>
                <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ row.ma_san_pham }}</span>
                    <span class="badge bg-label-secondary" style="font-size: 0.68rem;">
                      {{ row.loai_san_pham === 'san_pham' ? 'SP' : 'PT' }}
                    </span>
                  </div>
                  <span class="text-primary fw-medium d-block text-wrap" style="font-size: 0.84rem; max-width: 320px;">
                    {{ row.ten_san_pham }}
                  </span>
                </td>
                <td>
                  <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.4px;">
                    {{ row.ten_kho }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="fw-semibold text-dark">{{ row.ton_dau_ky }}</span>
                </td>
                <td class="text-center">
                  <span :class="row.nhap_trong_ky > 0 ? 'text-success fw-bold' : 'text-muted'">
                    {{ row.nhap_trong_ky > 0 ? '+' + row.nhap_trong_ky : 0 }}
                  </span>
                </td>
                <td class="text-center">
                  <span :class="row.xuat_trong_ky > 0 ? 'text-danger fw-bold' : 'text-muted'">
                    {{ row.xuat_trong_ky > 0 ? '-' + row.xuat_trong_ky : 0 }}
                  </span>
                </td>
                <td class="text-center">
                  <span
                    :class="row.dieu_chinh_trong_ky > 0 ? 'text-primary fw-bold' : (row.dieu_chinh_trong_ky < 0 ? 'text-warning fw-bold' : 'text-muted')"
                  >
                    {{ row.dieu_chinh_trong_ky !== 0 ? formatSignedNumber(row.dieu_chinh_trong_ky) : 0 }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="fw-bold fs-6" :class="row.ton_cuoi_ky > 0 ? 'text-dark' : 'text-danger'">
                    {{ row.ton_cuoi_ky }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="fw-bold" :class="row.so_luong_dang_giu > 0 ? 'text-warning' : 'text-secondary'">
                    {{ row.so_luong_dang_giu }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer py-3 border-top d-flex justify-content-between align-items-center">
          <span class="text-muted fw-medium">Tổng cộng: {{ xntRows.length }} mặt hàng tại các kho</span>
          <small class="text-muted">Công thức kiểm chứng: Tồn cuối kỳ = Tồn đầu kỳ + Nhập - Xuất + Điều chỉnh</small>
        </div>
      </div>

      <!-- TAB 2: CHI TIẾT THẺ KHO (SỔ GIAO DỊCH) -->
      <div v-else>
        <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 300px); min-height: 380px; overflow-y: auto;">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase" style="width: 5%">STT</th>
                <th class="text-uppercase" style="width: 12%">Ngày GD</th>
                <th class="text-uppercase" style="width: 24%">Sản Phẩm / Phụ Tùng</th>
                <th class="text-uppercase" style="width: 12%">Kho</th>
                <th class="text-center text-uppercase" style="width: 10%">Loại</th>
                <th class="text-center text-uppercase" style="width: 8%">Biến Động</th>
                <th class="text-center text-uppercase" style="width: 7%">Tồn Cuối</th>
                <th class="text-uppercase" style="width: 14%">Tham Chiếu &amp; Ghi Chú</th>
                <th class="text-uppercase" style="width: 10%">Người Thực Hiện</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              <tr v-if="isLoadingLedger">
                <td colspan="9" class="text-center py-5 text-muted">
                  <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                  </div>
                  <div>Đang tải lịch sử giao dịch kho...</div>
                </td>
              </tr>
              <tr v-else-if="transactions.length === 0">
                <td colspan="9" class="text-center py-5 text-muted">
                  <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                  Không có dữ liệu giao dịch
                </td>
              </tr>
              <tr v-for="(item, index) in transactions" :key="item.id" v-else>
                <td class="text-center">
                  <strong>{{ ((ledgerMeta.current_page - 1) * ledgerMeta.per_page) + index + 1 }}</strong>
                </td>
                <td>
                  <span class="fw-medium text-dark d-block" style="font-size: 0.92rem;">{{ item.date }}</span>
                  <span class="text-secondary" style="font-size: 0.8rem;">{{ item.time }}</span>
                </td>
                <td>
                  <span class="fw-bold text-dark d-block" style="font-size: 0.92rem;">{{ item.ma_san_pham || '---' }}</span>
                  <span class="text-primary fw-medium d-block text-wrap" style="font-size: 0.83rem; max-width: 260px;">
                    {{ item.ten_san_pham || '---' }}
                  </span>
                </td>
                <td>
                  <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.4px;">
                    {{ item.ten_kho || getWarehouseName(item.warehouse_id) }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge badge-custom" :class="getTransactionBadgeClass(item.loai_giao_dich)">
                    {{ getTransactionLabel(item.loai_giao_dich) }}
                  </span>
                </td>
                <td class="text-center">
                  <span
                    class="fw-bold fs-6"
                    :class="item.so_luong > 0 ? 'text-primary' : (item.so_luong < 0 ? 'text-danger' : 'text-muted')"
                  >
                    {{ formatSignedNumber(item.so_luong) }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="fw-bold text-dark fs-6">{{ item.ton_cuoi }}</span>
                </td>
                <td>
                  <span v-if="item.tham_chieu" class="badge bg-label-dark mb-1 d-inline-block">{{ item.tham_chieu }}</span>
                  <span class="text-muted d-block text-wrap" style="font-size: 0.8rem; max-width: 210px;">
                    {{ item.ghi_chu || '---' }}
                  </span>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <img
                        v-if="item.nguoi_thuc_hien_avatar"
                        :src="getImageUrl(item.nguoi_thuc_hien_avatar)"
                        class="rounded-circle"
                        style="width: 30px; height: 30px; object-fit: cover;"
                      />
                      <span v-else class="avatar-initial rounded-circle bg-label-primary">
                        {{ (item.nguoi_thuc_hien || 'Admin').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <span class="text-dark fw-medium" style="font-size: 0.84rem;">
                      {{ item.nguoi_thuc_hien || 'Admin' }}
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card-footer py-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
          <span class="text-muted fw-medium">
            Hiển thị {{ transactions.length }} / {{ ledgerMeta.total }} giao dịch
          </span>
          <div class="d-flex align-items-center gap-2" v-if="ledgerMeta.last_page > 1">
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary"
              :disabled="ledgerPage <= 1"
              @click="ledgerPage--"
            >
              <i class="bx bx-chevron-left"></i> Trước
            </button>
            <span class="text-muted small fw-medium">Trang {{ ledgerMeta.current_page }} / {{ ledgerMeta.last_page }}</span>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary"
              :disabled="ledgerPage >= ledgerMeta.last_page"
              @click="ledgerPage++"
            >
              Sau <i class="bx bx-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import { useQuery } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

function getDefaultPeriodDates() {
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const dd = String(now.getDate()).padStart(2, '0');
  return {
    start: `${yyyy}-${mm}-01`,
    end: `${yyyy}-${mm}-${dd}`
  };
}

function sanitizeCsvCell(val) {
  let str = String(val ?? '');
  if (str !== '' && /^[=+\-@\t\r]/.test(str)) {
    str = `'${str}`;
  }
  return `"${str.replace(/"/g, '""')}"`;
}

export default {
  name: "TheKho",
  components: {
    vSelect
  },
  setup() {
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const defaultDates = getDefaultPeriodDates();

    const activeTab = ref('xnt');
    const ledgerPage = ref(1);
    const filters = reactive({
      warehouse_id: null,
      keyword: "",
      loai_giao_dich: "",
      tu_ngay: defaultDates.start,
      den_ngay: defaultDates.end
    });

    const debouncedKeyword = ref("");
    let keywordTimer = null;
    watch(() => filters.keyword, (val) => {
      clearTimeout(keywordTimer);
      keywordTimer = setTimeout(() => {
        debouncedKeyword.value = (val || '').trim();
        ledgerPage.value = 1;
      }, 250);
    });

    watch(
      [() => filters.warehouse_id, () => filters.loai_giao_dich, () => filters.tu_ngay, () => filters.den_ngay],
      () => {
        ledgerPage.value = 1;
      }
    );

    const { data: warehouses } = useQuery({
      queryKey: ['danh-sach-kho'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/kho');
        return Array.isArray(res.data) ? res.data : [];
      },
      staleTime: 1000 * 60 * 5
    });

    const buildCommonParams = () => {
      const params = {};
      if (filters.warehouse_id) params.warehouse_id = filters.warehouse_id;
      if (debouncedKeyword.value) params.keyword = debouncedKeyword.value;
      if (filters.tu_ngay) params.tu_ngay = filters.tu_ngay;
      if (filters.den_ngay) params.den_ngay = filters.den_ngay;
      return params;
    };

    const mapLedgerRows = (list) => {
      return (Array.isArray(list) ? list : []).map(item => {
        const d = new Date(item.created_at);
        return {
          id: item.id,
          warehouse_id: item.id_kho,
          ten_kho: item.kho?.ten_kho || null,
          ma_san_pham: (item.loai_san_pham === 'san_pham' ? item.san_pham?.ma_san_pham : item.phu_tung?.ma_phu_tung) || '',
          ten_san_pham: (item.loai_san_pham === 'san_pham' ? item.san_pham?.ten_san_pham : item.phu_tung?.ten_phu_tung) || '',
          loai_giao_dich: item.loai_giao_dich,
          so_luong: Number(item.so_luong_thay_doi ?? 0),
          ton_cuoi: Number(item.so_luong_ton_cuoi ?? 0),
          tham_chieu: item.ma_tham_chieu || '',
          nguoi_thuc_hien: item.nguoi_thuc_hien?.name || 'Admin',
          nguoi_thuc_hien_avatar: item.nguoi_thuc_hien?.avatar || null,
          date: isNaN(d.getTime()) ? '' : d.toLocaleDateString('vi-VN'),
          time: isNaN(d.getTime()) ? '' : d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          ghi_chu: item.ghi_chu || ''
        };
      });
    };

    const { data: xntResponse, isLoading: isLoadingXnt } = useQuery({
      queryKey: ['bao-cao-xnt', () => filters.warehouse_id, debouncedKeyword, () => filters.tu_ngay, () => filters.den_ngay],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/the-kho/bao-cao-xnt', {
          params: buildCommonParams()
        });
        return {
          rows: Array.isArray(res.data) ? res.data : [],
          totals: res.totals || {
            ton_dau_ky: 0,
            nhap_trong_ky: 0,
            xuat_trong_ky: 0,
            dieu_chinh_trong_ky: 0,
            ton_cuoi_ky: 0,
            so_luong_dang_giu: 0
          }
        };
      },
      enabled: computed(() => activeTab.value === 'xnt')
    });

    const { data: ledgerResponse, isLoading: isLoadingLedger } = useQuery({
      queryKey: ['the-kho', () => filters.warehouse_id, () => filters.loai_giao_dich, debouncedKeyword, () => filters.tu_ngay, () => filters.den_ngay, ledgerPage],
      queryFn: async () => {
        const params = {
          ...buildCommonParams(),
          per_page: 50,
          page: ledgerPage.value
        };
        if (filters.loai_giao_dich) params.loai_giao_dich = filters.loai_giao_dich;

        const res = await axiosClient.get('/admin/the-kho', { params });
        const list = Array.isArray(res.data) ? res.data : [];
        return {
          rows: mapLedgerRows(list),
          meta: res.meta || {
            current_page: 1,
            last_page: 1,
            per_page: 50,
            total: list.length
          }
        };
      },
      enabled: computed(() => activeTab.value === 'ledger')
    });

    const xntRows = computed(() => xntResponse.value?.rows || []);
    const xntTotals = computed(() => xntResponse.value?.totals || {
      ton_dau_ky: 0,
      nhap_trong_ky: 0,
      xuat_trong_ky: 0,
      dieu_chinh_trong_ky: 0,
      ton_cuoi_ky: 0,
      so_luong_dang_giu: 0
    });
    const transactions = computed(() => ledgerResponse.value?.rows || []);
    const ledgerMeta = computed(() => ledgerResponse.value?.meta || {
      current_page: 1,
      last_page: 1,
      per_page: 50,
      total: 0
    });
    const warehouseOptions = computed(() => (warehouses.value || []).map(w => ({ value: w.id, label: w.ten_kho })));

    return {
      activeTab,
      ledgerPage,
      filters,
      debouncedKeyword,
      warehouses,
      warehouseOptions,
      xntRows,
      xntTotals,
      isLoadingXnt,
      transactions,
      ledgerMeta,
      isLoadingLedger,
      mapLedgerRows,
      buildCommonParams,
      toaster
    };
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
    },
    resetFilters() {
      const defaults = getDefaultPeriodDates();
      this.filters.keyword = '';
      this.debouncedKeyword = '';
      this.filters.warehouse_id = null;
      this.filters.loai_giao_dich = '';
      this.filters.tu_ngay = defaults.start;
      this.filters.den_ngay = defaults.end;
      this.ledgerPage = 1;
    },
    formatSignedNumber(num) {
      const n = Number(num || 0);
      if (n > 0) return `+${n}`;
      if (n < 0) return `${n}`;
      return '0';
    },
    getTransactionBadgeClass(type) {
      switch (type) {
        case 'NHAP_KHO':
          return 'bg-pastel-success';
        case 'XUAT_KHO':
          return 'bg-pastel-danger';
        case 'DIEU_CHINH':
          return 'bg-pastel-warning';
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
        case 'DIEU_CHINH':
          return 'ĐIỀU CHỈNH';
        default:
          return type || 'KHÁC';
      }
    },
    getWarehouseName(id) {
      const w = (this.warehouses || []).find(x => x.id === id);
      return w ? w.ten_kho : 'Không xác định';
    },
    async exportExcel() {
      const now = new Date();
      const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}_${String(now.getHours()).padStart(2, '0')}${String(now.getMinutes()).padStart(2, '0')}`;

      if (this.activeTab === 'xnt') {
        if (!this.xntRows.length) {
          this.toaster.error("Không có dữ liệu Xuất / Nhập / Tồn để xuất!");
          return;
        }
        const headers = [
          "STT",
          "Mã sản phẩm",
          "Tên sản phẩm",
          "Loại",
          "Kho chứa",
          "Tồn đầu kỳ",
          "Nhập trong kỳ",
          "Xuất trong kỳ",
          "Điều chỉnh",
          "Tồn cuối kỳ",
          "Đang giữ (Chờ PO)"
        ];
        const rows = this.xntRows.map((r, idx) => [
          idx + 1,
          sanitizeCsvCell(r.ma_san_pham),
          sanitizeCsvCell(r.ten_san_pham),
          sanitizeCsvCell(r.loai_san_pham === 'san_pham' ? 'Sản phẩm' : 'Phụ tùng'),
          sanitizeCsvCell(r.ten_kho),
          r.ton_dau_ky,
          r.nhap_trong_ky,
          r.xuat_trong_ky,
          r.dieu_chinh_trong_ky,
          r.ton_cuoi_ky,
          r.so_luong_dang_giu
        ]);
        rows.push([
          "",
          sanitizeCsvCell("TỔNG CỘNG"),
          "",
          "",
          "",
          this.xntTotals.ton_dau_ky,
          this.xntTotals.nhap_trong_ky,
          this.xntTotals.xuat_trong_ky,
          this.xntTotals.dieu_chinh_trong_ky,
          this.xntTotals.ton_cuoi_ky,
          this.xntTotals.so_luong_dang_giu
        ]);
        this.downloadCsv(headers, rows, `Bao_Cao_Xuat_Nhap_Ton_${dateStr}.csv`);
        this.toaster.success(`Đã xuất báo cáo Xuất/Nhập/Tồn (${this.xntRows.length} mặt hàng)!`);
        return;
      }

      try {
        const params = { ...this.buildCommonParams(), per_page: 'all', limit: 5000 };
        if (this.filters.loai_giao_dich) params.loai_giao_dich = this.filters.loai_giao_dich;
        const res = await axiosClient.get('/admin/the-kho', { params });
        const allRows = this.mapLedgerRows(Array.isArray(res.data) ? res.data : []);
        if (!allRows.length) {
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
        const rows = allRows.map((t, idx) => [
          idx + 1,
          sanitizeCsvCell(t.date),
          sanitizeCsvCell(t.time),
          sanitizeCsvCell(t.tham_chieu),
          sanitizeCsvCell(t.ghi_chu),
          sanitizeCsvCell(t.ma_san_pham),
          sanitizeCsvCell(t.ten_san_pham),
          sanitizeCsvCell(t.ten_kho || this.getWarehouseName(t.warehouse_id)),
          sanitizeCsvCell(this.getTransactionLabel(t.loai_giao_dich)),
          t.so_luong,
          t.ton_cuoi,
          sanitizeCsvCell(t.nguoi_thuc_hien)
        ]);
        this.downloadCsv(headers, rows, `Chi_Tiet_The_Kho_${dateStr}.csv`);
        this.toaster.success(`Đã xuất thành công ${allRows.length} giao dịch thẻ kho!`);
      } catch (_err) {
        this.toaster.error("Lỗi khi tải dữ liệu xuất file!");
      }
    },
    downloadCsv(headers, rows, filename) {
      const csvContent = "\uFEFF" + [headers.join(","), ...rows.map(r => r.join(","))].join("\r\n");
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.setAttribute("href", url);
      link.setAttribute("download", filename);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }
  }
};
</script>

<style scoped>
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
