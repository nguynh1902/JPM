<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Chuỗi Cung Ứng /</span> Gom Đơn & PO
    </h4>

    <!-- Tabs Navigation -->
    <div class="d-flex border-bottom mb-4 bg-white rounded shadow-sm px-3 pt-2">
      <div 
        class="custom-line-tab py-3 px-4 fw-bold fs-6 d-flex align-items-center gap-2"
        :class="{ active: activeTab === 'pos' }"
        @click="activeTab = 'pos'"
      >
        <i class="bx bx-receipt fs-5"></i>
        <span>Danh Sách Đơn PO</span>
        <span class="badge rounded-pill bg-label-primary ms-1">{{ pos?.length || 0 }}</span>
      </div>
      <div 
        class="custom-line-tab py-3 px-4 fw-bold fs-6 d-flex align-items-center gap-2"
        :class="{ active: activeTab === 'demand' }"
        @click="activeTab = 'demand'"
      >
        <i class="bx bx-layer-plus fs-5"></i>
        <span>Nhu Cầu Gom Hàng (Thiếu Kho)</span>
        <span class="badge rounded-pill bg-danger ms-1" v-if="rawDemands?.length > 0">{{ rawDemands.length }}</span>
      </div>
    </div>

    <!-- TAB 1: DANH SÁCH PO -->
    <div class="card" v-if="activeTab === 'pos'">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh sách Purchase Order (PO)</h5>
      </div>
      
      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input type="text" class="form-control" placeholder="Tìm theo Mã PO, NCC..." v-model="filters.search">
          </div>
          <div style="width: 20%;">
            <v-select
              v-model="filters.status"
              :options="[{value: null, label: 'Tất cả trạng thái'}, ...statusOptions]"
              placeholder="Trạng thái"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div>
            <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modalPO" @click="openAddModal">
              <i class="bx bx-plus me-1"></i> Tạo PO Mới
            </button>
          </div>
        </div>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 15%">Mã PO</th>
              <th style="width: 25%">Nhà Cung Cấp</th>
              <th class="text-center" style="width: 15%">Ngày Tạo</th>
              <th class="text-center" style="width: 15%">Dự Kiến Giao</th>
              <th class="text-end" style="width: 15%">Tổng Tiền</th>
              <th class="text-center" style="width: 15%">Trạng thái</th>
              <th class="text-center" style="width: 10%">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-for="(item, index) in filteredPOs" :key="item.id">
              <td class="text-center align-middle"><strong>{{ index + 1 }}</strong></td>
              <td class="align-middle"><span class="fw-bold text-primary">{{ item.ma_po }}</span></td>
              <td class="align-middle">
                <span class="fw-bold text-dark">{{ item.nha_cung_cap }}</span>
              </td>
              <td class="text-center align-middle"><span class="text-secondary">{{ item.ngay_tao }}</span></td>
              <td class="text-center align-middle"><span class="text-secondary">{{ item.ngay_du_kien || '?' }}</span></td>
              <td class="text-end align-middle"><span class="fw-bold text-danger">{{ formatCurrency(item.tong_tien) }}</span></td>
              <td class="text-center align-middle">
                <span v-if="item.trang_thai === 0" class="badge badge-custom bg-pastel-secondary">NHÁP</span>
                <span v-else-if="item.trang_thai === 1" class="badge badge-custom bg-pastel-warning">ĐANG GIAO</span>
                <span v-else-if="item.trang_thai === 2" class="badge badge-custom bg-pastel-success">HOÀN THÀNH</span>
                <span v-else-if="item.trang_thai === 3" class="badge badge-custom bg-pastel-danger">ĐÃ HỦY</span>
              </td>
              <td class="text-center align-middle">
                <div class="d-flex align-items-center justify-content-center">
                  <button 
                    type="button"
                    class="action-btn text-info me-1" 
                    title="Xem chi tiết"
                    @click="viewDetails(item)"
                    data-bs-toggle="modal"
                    data-bs-target="#modalChiTietPO"
                  >
                    <i class="bx bx-detail"></i>
                  </button>
                  <button 
                    type="button"
                    class="action-btn text-primary me-1" 
                    @click="openEditModal(item)" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalPO" 
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
            <tr v-if="filteredPOs.length === 0">
              <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy Đơn hàng nào phù hợp.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: NHU CẦU GOM HÀNG (THIẾU KHO) -->
    <div class="card" v-else-if="activeTab === 'demand'">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold text-dark">Nhu Cầu Linh Kiện Cần Gom Hàng (Thiếu Kho)</h5>
          <small class="text-muted">Tổng hợp danh sách các mặt hàng khách đã đặt nhưng kho đang hết hoặc đang chờ nhập từ NCC.</small>
        </div>
        <div>
          <button 
            class="btn btn-warning shadow-sm fw-bold d-flex align-items-center gap-1"
            :disabled="selectedDemands.length === 0"
            @click="taoPOGomLo"
          >
            <i class="bx bx-package fs-5"></i>
            <span>Gom vào 1 PO Mới ({{ selectedDemands.length }} món)</span>
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 5%">
                <input class="form-check-input" type="checkbox" :checked="isAllDemandsSelected" @change="toggleSelectAllDemands">
              </th>
              <th>Mã Sản Phẩm / OEM</th>
              <th>Tên Sản Phẩm / Phụ Tùng</th>
              <th class="text-center">Tồn Hiện Tại</th>
              <th class="text-center">Số Lượng Cần Nhập</th>
              <th class="text-end">Đơn Giá Dự Kiến</th>
              <th class="text-end">Thành Tiền Dự Kiến</th>
              <th class="text-center">Kho</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!rawDemands || rawDemands.length === 0">
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bx bx-check-circle text-success fs-1 d-block mb-2"></i>
                Hiện tại không có mặt hàng nào bị thiếu kho cần gom nhập!
              </td>
            </tr>
            <tr v-for="item in rawDemands" :key="item.id_ton_kho" :class="{'table-active': selectedDemands.includes(item.id_ton_kho)}">
              <td class="text-center align-middle">
                <input class="form-check-input" type="checkbox" :value="item.id_ton_kho" v-model="selectedDemands">
              </td>
              <td class="align-middle fw-bold text-primary">{{ item.ma_san_pham }}</td>
              <td class="align-middle fw-medium text-dark">{{ item.ten_san_pham }}</td>
              <td class="text-center align-middle">
                <span class="badge bg-pastel-secondary text-secondary">{{ item.so_luong_ton }}</span>
              </td>
              <td class="text-center align-middle">
                <span class="badge bg-danger fs-6 fw-bold">+{{ item.so_luong_can_nhap }}</span>
              </td>
              <td class="text-end align-middle">{{ formatCurrency(item.don_gia_du_kien) }}</td>
              <td class="text-end align-middle fw-bold text-danger">
                {{ formatCurrency(item.so_luong_can_nhap * item.don_gia_du_kien) }}
              </td>
              <td class="text-center align-middle text-muted">{{ item.kho }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalPO" tabindex="-1" aria-hidden="true" ref="modalPO">
      <div class="modal-dialog mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalPOTitle">
              {{ isEditMode ? 'Cập Nhật PO' : 'Tạo PO Mới' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2">
              
              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Mã PO <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-uppercase fw-bold" v-model="formData.ma_po" placeholder="VD: PO-20231001" required :disabled="isEditMode" />
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Nhà Cung Cấp <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="formData.nha_cung_cap" placeholder="Tên hãng / NCC" required />
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium text-dark">Ngày Tạo <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" v-model="formData.ngay_tao" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium text-dark">Dự Kiến Giao</label>
                  <input type="date" class="form-control" v-model="formData.ngay_du_kien" />
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium text-dark">Tổng Tiền (Dự kiến)</label>
                  <input type="number" class="form-control" v-model="formData.tong_tien" min="0" placeholder="0" />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.trang_thai"
                    :options="statusOptions"
                    :can-clear="false"
                  />
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-medium text-dark">Ghi chú</label>
                <textarea class="form-control" v-model="formData.ghi_chu" rows="2" placeholder="Nhập ghi chú..."></textarea>
              </div>

            </div>
            <div class="modal-footer pt-3 border-top">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ isEditMode ? 'Cập Nhật' : 'Tạo Mới' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Chi Tiet PO -->
    <div class="modal fade" id="modalChiTietPO" tabindex="-1" aria-hidden="true" ref="modalChiTietPO">
      <div class="modal-dialog modal-lg mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h5 class="modal-title fw-bold text-dark mb-0 fs-5">
              Chi Tiết Đơn Nhập Hàng: <span class="text-primary">{{ selectedPO?.ma_po }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="isFetchingDetails" class="text-center py-4">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
            </div>
            <div v-else>
              <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center" style="width: 5%">STT</th>
                      <th>Sản phẩm / Phụ tùng</th>
                      <th class="text-center">Số lượng</th>
                      <th class="text-end">Đơn giá</th>
                      <th class="text-end">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="selectedPODetails.length === 0">
                      <td colspan="5" class="text-center text-muted py-3">Không có sản phẩm nào.</td>
                    </tr>
                    <tr v-for="(ct, idx) in selectedPODetails" :key="idx">
                      <td class="text-center align-middle">{{ idx + 1 }}</td>
                      <td class="align-middle fw-medium text-dark">
                        {{ ct.phu_tung ? ct.phu_tung.ten_phu_tung : (ct.san_pham ? ct.san_pham.ten_san_pham : 'Sản phẩm ID ' + ct.id_san_pham) }}
                      </td>
                      <td class="text-center align-middle">{{ ct.so_luong_nhap }}</td>
                      <td class="text-end align-middle text-secondary">{{ formatCurrency(ct.don_gia_nhap) }}</td>
                      <td class="text-end align-middle fw-bold text-danger">{{ formatCurrency(ct.thanh_tien) }}</td>
                    </tr>
                  </tbody>
                  <tfoot v-if="selectedPODetails.length > 0">
                    <tr>
                      <td colspan="4" class="text-end fw-bold text-dark">TỔNG CỘNG:</td>
                      <td class="text-end fw-bold text-danger fs-5">{{ formatCurrency(selectedPO?.tong_tien) }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer pt-3 border-top">
            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Đóng</button>
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
  name: "PO",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();

    const fetchPOs = async () => {
      const response = await axiosClient.get('/admin/don-nhap-hang');
      return response.data || [];
    };

    const { data: pos, isFetching } = useQuery({
      queryKey: ['pos'],
      queryFn: fetchPOs
    });

    const fetchDemands = async () => {
      const response = await axiosClient.get('/admin/ton-kho/hang-can-nhap');
      return response.data || [];
    };

    const { data: rawDemands, isFetching: isFetchingDemands } = useQuery({
      queryKey: ['hang-can-nhap'],
      queryFn: fetchDemands
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/don-nhap-hang/${id}`),
      onError: (err, id, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['pos'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put(`/admin/don-nhap-hang/${payload.id}`, payload)
          : axiosClient.post('/admin/don-nhap-hang', payload);
      },
      onError: (err, vars, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['pos'] });
      }
    });

    return { pos, isFetching, rawDemands, isFetchingDemands, queryClient, deleteMutation, saveMutation };
  },

  data() {
    return {
      activeTab: 'pos', // 'pos' | 'demand'
      selectedDemands: [],
      filters: {
        search: "",
        status: null
      },
      statusOptions: [
        {value: 0, label: 'Nháp'},
        {value: 1, label: 'Đang Giao'},
        {value: 2, label: 'Hoàn Thành'},
        {value: 3, label: 'Đã Hủy'}
      ],
      isEditMode: false,
      isSubmitting: false,
      formData: {
        id: null,
        ma_po: "",
        nha_cung_cap: "",
        ngay_tao: "",
        ngay_du_kien: "",
        tong_tien: 0,
        trang_thai: 0,
        ghi_chu: ""
      },
      selectedPO: null,
      selectedPODetails: [],
      isFetchingDetails: false
    };
  },
  computed: {
    filteredPOs() {
      let result = this.pos || [];
      
      if (this.filters.search) {
        const keyword = this.filters.search.toLowerCase();
        result = result.filter(item => 
          (item.ma_po && item.ma_po.toLowerCase().includes(keyword)) || 
          (item.nha_cung_cap && item.nha_cung_cap.toLowerCase().includes(keyword))
        );
      }
      
      if (this.filters.status !== null) {
        result = result.filter(item => item.trang_thai === this.filters.status);
      }
      
      return result;
    },
    isAllDemandsSelected() {
      return this.rawDemands && this.rawDemands.length > 0 && this.selectedDemands.length === this.rawDemands.length;
    }
  },
  methods: {
    toggleSelectAllDemands() {
      if (this.isAllDemandsSelected) {
        this.selectedDemands = [];
      } else {
        this.selectedDemands = (this.rawDemands || []).map(i => i.id_ton_kho);
      }
    },
    async taoPOGomLo() {
      if (this.selectedDemands.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn mặt hàng', text: 'Vui lòng tích chọn ít nhất 1 mặt hàng cần gom!' });
        return;
      }

      const itemsToOrder = (this.rawDemands || []).filter(d => this.selectedDemands.includes(d.id_ton_kho));
      const totalEstimated = itemsToOrder.reduce((sum, i) => sum + (i.so_luong_can_nhap * i.don_gia_du_kien), 0);

      const result = await Swal.fire({
        title: 'Tạo PO Gom Lô Hàng?',
        html: `Bạn đang gom <b>${itemsToOrder.length}</b> mặt hàng thiếu vào 1 Đơn Nhập Hàng (PO)?<br/>Tổng tiền dự kiến: <b class="text-danger">${this.formatCurrency(totalEstimated)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tạo PO Gom Lô',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
          popup: 'swal-shift-up',
          confirmButton: 'btn btn-primary px-4 shadow-sm',
          cancelButton: 'btn btn-outline-secondary px-4 me-3'
        },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        try {
          const payload = {
            id_nha_cung_cap: 1,
            id_kho: 1,
            items: itemsToOrder.map(i => ({
              id_san_pham: i.id_san_pham,
              loai_san_pham: i.loai_san_pham,
              so_luong: i.so_luong_can_nhap,
              don_gia_nhap: i.don_gia_du_kien
            }))
          };
          const res = await axiosClient.post('/admin/don-nhap-hang/gom-po', payload);
          if (res.success || res.data) {
            Swal.fire({
              icon: 'success',
              title: 'Gom PO Thành Công!',
              text: res.message || 'Đã tạo PO gom lô thành công!'
            });
            this.selectedDemands = [];
            this.activeTab = 'pos';
            this.queryClient.invalidateQueries({ queryKey: ['pos'] });
            this.queryClient.invalidateQueries({ queryKey: ['hang-can-nhap'] });
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Không thể tạo PO gom lô.';
          Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
        }
      }
    },
    formatCurrency(value) {
      if (!value && value !== 0) return '';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    openAddModal() {
      this.isEditMode = false;
      const today = new Date().toISOString().split('T')[0];
      this.formData = {
        id: null,
        ma_po: "Tự động tạo",
        id_nha_cung_cap: 1, // Default, should ideally be selected
        id_kho: 1, // Default
        nha_cung_cap: "",
        ngay_tao: today,
        ngay_du_kien: "",
        tong_tien: 0,
        trang_thai: 0,
        ghi_chu: "",
        chi_tiets: [] // Assuming empty for now
      };
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
    },
    async viewDetails(item) {
      this.selectedPO = item;
      this.selectedPODetails = [];
      this.isFetchingDetails = true;
      try {
        const response = await axiosClient.get(`/admin/don-nhap-hang/${item.id}`);
        if (response && response.data && response.data.chi_tiets) {
          this.selectedPODetails = response.data.chi_tiets;
        }
      } catch (error) {
        console.error("Lỗi lấy chi tiết PO:", error);
        this.$toast.error("Không thể lấy chi tiết đơn nhập hàng!");
      } finally {
        this.isFetchingDetails = false;
      }
    },
    async saveData() {
      this.isSubmitting = true;
      try {
        await this.saveMutation.mutateAsync(this.formData);
        
        if (this.isEditMode) {
          this.$toast.success("Cập nhật PO thành công!");
        } else {
          this.$toast.success("Tạo PO mới thành công!");
        }
        
        const modalElement = this.$refs.modalPO;
        if (modalElement) {
          const modal = window.bootstrap?.Modal?.getInstance(modalElement);
          if (modal) modal.hide();
        }
      } catch (error) {
        console.error("Lỗi lưu PO:", error);
        this.$toast.error("Có lỗi xảy ra khi lưu PO!");
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xóa Purchase Order?',
        html: `Bạn có chắc chắn muốn xóa PO: <b>${item.ma_po}</b>?`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận Xóa',
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
        try {
          await this.deleteMutation.mutateAsync(item.id);
          this.$toast.success("Đã xóa PO thành công!");
        } catch (error) {
          console.error("Lỗi xóa PO:", error);
          this.$toast.error("Lỗi xóa PO, dữ liệu sẽ được khôi phục!");
        }
      }
    }
  }
};
</script>

<style scoped>
.custom-line-tab {
  cursor: pointer;
  min-width: 180px;
  text-align: center;
  color: #a1acb8;
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


