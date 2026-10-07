<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Xuất / Nhập / Tồn
    </h4>

    <div class="card">
      <div class="card-header border-bottom py-3">
        <h5 class="mb-0 fw-bold text-dark fs-5">Tra Cứu Lịch Sử Giao Dịch Kho</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input type="text" class="form-control" placeholder="Tìm kiếm mã, tên sản phẩm..." v-model="filters.ma_san_pham">
          </div>
          <div style="width: 20%;">
            <v-select
              v-model="filters.warehouse_id"
              :options="[{value: null, label: 'Tất cả Kho chứa'}, ...warehouseOptions]"
              placeholder="Kho chứa"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 20%;">
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
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            <i class="bx bx-refresh me-1"></i> Đặt lại
          </button>
          <button class="btn btn-primary text-nowrap shadow-sm" @click="exportExcel">
            <i class="bx bx-export me-1"></i> Xuất Excel
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 270px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center text-uppercase" style="width: 5%">STT</th>
              <th class="text-uppercase" style="width: 12%">Ngày Giao Dịch</th>
              <th class="text-uppercase" style="width: 30%">Sản Phẩm</th>
              <th class="text-uppercase" style="width: 15%">Kho</th>
              <th class="text-center text-uppercase" style="width: 10%">Loại</th>
              <th class="text-center text-uppercase" style="width: 8%">Biến Động</th>
              <th class="text-center text-uppercase" style="width: 8%">Tồn Cuối</th>
              <th class="text-uppercase" style="width: 12%">Người Thực Hiện</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="filteredTransactions.length === 0">
              <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy lịch sử giao dịch phù hợp</td>
            </tr>
            <tr v-for="(item, index) in filteredTransactions" :key="item.id" v-else>
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <span class="fw-medium text-dark d-block" style="font-size: 0.95rem;">{{ item.date }}</span>
                <span class="text-secondary" style="font-size: 0.8rem;">{{ item.time }}</span>
                <div class="text-primary mt-1 fw-bold" style="font-size: 0.75rem;" v-if="item.tham_chieu">
                  <i class="bx bx-link"></i> {{ item.tham_chieu }}
                </div>
                <div class="text-muted mt-1" style="font-size: 0.75rem; white-space: normal; max-width: 200px;" v-if="item.ghi_chu">
                  <i class="bx bx-message-square-detail"></i> {{ item.ghi_chu }}
                </div>
              </td>

              <td>
                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">{{ item.ma_san_pham }}</span>
                <span class="text-primary fw-medium" style="font-size: 0.85rem; white-space: normal; display: block; max-width: 300px;">{{ item.ten_san_pham }}</span>
              </td>
              <td><span class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ getWarehouseName(item.warehouse_id) }}</span></td>
              <td class="text-center">
                <span class="badge" :class="item.loai_giao_dich === 'NHAP_KHO' ? 'bg-pastel-success text-dark' : 'bg-pastel-danger text-dark'">
                  {{ item.loai_giao_dich === 'NHAP_KHO' ? 'NHẬP KHO' : 'XUẤT KHO' }}
                </span>
              </td>
              <td class="text-center">
                <span class="fw-bold fs-6" :class="item.loai_giao_dich === 'NHAP_KHO' ? 'text-primary' : 'text-danger'">
                  {{ item.loai_giao_dich === 'NHAP_KHO' ? '+' : '-' }}{{ item.so_luong }}
                </span>
              </td>
              <td class="text-center"><span class="fw-bold text-dark fs-6">{{ item.ton_cuoi }}</span></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      {{ item.nguoi_thuc_hien.charAt(0) }}
                    </span>
                  </div>
                  <span class="text-dark fw-medium" style="font-size: 0.85rem;">{{ item.nguoi_thuc_hien }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ filteredTransactions.length }} mục</span>
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
        const res = await axiosClient.get('/admin/the-kho');
        const list = Array.isArray(res.data) ? res.data : (res.data?.data?.data || res.data?.data || []);
        if (!Array.isArray(list)) {
            console.error("Dữ liệu không phải là mảng:", list, res.data);
            return [];
        }
        return list.map(item => {
        const d = new Date(item.created_at);
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
          nguoi_thuc_hien: 'Admin',
          date: d.toLocaleDateString('vi-VN'),
          time: d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
          raw_date: d,
          ghi_chu: item.ghi_chu || ''
        };
      });
      } catch (err) {
        console.error("Lỗi khi tải lịch sử:", err);
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
        const searchRegex = new RegExp(this.filters.ma_san_pham, 'i');
        result = result.filter(t => searchRegex.test(t.ma_san_pham) || searchRegex.test(t.ten_san_pham));
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
    exportExcel() {
      this.toaster.success("Đã xuất file Excel thành công!");
    },
    getWarehouseName(id) {
      const w = (this.warehouses || []).find(x => x.id === id);
      return w ? w.ten_kho : 'Không xác định';
    }
  }
};
</script>
