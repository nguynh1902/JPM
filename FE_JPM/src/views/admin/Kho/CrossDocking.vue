<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Kho /</span> Trạm Cross-Docking
    </h4>

    <div class="row">
      <!-- Cột Quét Mã Vạch -->
      <div class="col-md-5 mb-4">
        <div class="card h-100">
          <div class="card-header border-bottom bg-white">
            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-barcode-reader me-2 text-primary"></i> Máy Quét Mã Vạch</h5>
          </div>
          <div class="card-body pt-4 d-flex flex-column align-items-center justify-content-center text-center">
            <div class="mb-4">
              <i class="bx bx-scan text-primary" style="font-size: 5rem; opacity: 0.8;"></i>
            </div>
            <h6 class="text-dark fw-bold mb-3">Sẵn sàng quét mã vận đơn (Bill of Lading) hoặc Mã Sản Phẩm</h6>
            <p class="text-muted small mb-4">
              Sử dụng súng bắn mã vạch quét trực tiếp lên tem dán trên thùng hàng. Hệ thống sẽ tự động đối chiếu và điều hướng hàng xuất đi ngay lập tức.
            </p>
            
                        <form @submit.prevent="handleScan" class="w-100">
              <div class="d-flex gap-3 mb-3">
                <input 
                  type="text" 
                  class="form-control form-control-lg flex-grow-1 shadow-sm" 
                  style="border-radius: 8px; border: 1px solid #d9dee3;"
                  placeholder="Nhập mã vạch vào đây..." 
                  v-model="barcode"
                  ref="barcodeInput"
                  autofocus
                >
                <button class="btn btn-primary btn-lg shadow-sm px-4 text-nowrap" style="border-radius: 8px;" type="submit">
                  <i class="bx bx-check me-1"></i> Xác nhận
                </button>
              </div>
            </form>

            <div v-if="lastScanned" class="alert alert-success w-100 mt-3 text-start" role="alert">
              <div class="fw-bold"><i class="bx bx-check-circle me-1"></i> Quét thành công: {{ lastScanned }}</div>
              <small>Hệ thống đã ghi nhận và cập nhật trạng thái xuất bãi.</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột Danh sách Hàng Đang Xử Lý -->
      <div class="col-md-7 mb-4">
        <div class="card h-100">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Lô Hàng Chờ Cross-Docking Hôm Nay</h5>
            <span class="badge bg-pastel-primary text-primary fs-6">{{ (pendingShipments || []).length }} Lô Hàng</span>
          </div>

          <div class="table-responsive text-nowrap" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover">
                            <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center text-uppercase" style="width: 5%">STT</th>
                  <th class="text-uppercase" style="width: 20%">Mã Vận Đơn</th>
                  <th class="text-uppercase" style="width: 35%">Nội dung</th>
                  <th class="text-center text-uppercase" style="width: 20%">Khách Hàng</th>
                  <th class="text-center text-uppercase" style="width: 20%">Trạng Thái</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="(pendingShipments || []).length === 0">
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bx bx-check-double fs-1 mb-2"></i><br>
                    Tuyệt vời! Không còn lô hàng nào tồn đọng tại trạm.
                  </td>
                </tr>
                <tr v-for="(item, index) in (pendingShipments || [])" :key="item.id" :class="{'table-success': item.status === 'SCANNED'}">
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td>
                    <span class="fw-bold text-primary">{{ item.ma_van_don }}</span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark d-block">{{ item.ten_san_pham }}</span>
                    <small class="fw-semibold text-secondary">SL: {{ item.so_luong }} chiếc</small>
                  </td>
                  <td class="text-center">
                    <span class="text-dark fw-medium">{{ item.khach_hang }}</span>
                  </td>
                  <td class="text-center">
                    <span v-if="item.status === 'WAITING'" class="badge bg-pastel-danger text-dark">CHỜ QUÉT</span>
                    <span v-else-if="item.status === 'SCANNED'" class="badge bg-pastel-success text-dark">ĐÃ XUẤT BÃI</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (pendingShipments && pendingShipments.length) ? pendingShipments.length : 0 }} mục</span>
      </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from '@meforma/vue-toaster';
import Swal from 'sweetalert2';

export default {
  name: "CrossDocking",
  setup() {
    const queryClient = useQueryClient();
    const toaster = createToaster({ position: 'top-right' });

    const fetchPending = async () => {
      const res = await axiosClient.get('/admin/cross-docking');
      return res.data || [];
    };

    const { data: pendingShipments, isFetching } = useQuery({
      queryKey: ['crossDockingPending'],
      queryFn: fetchPending
    });

    const scanMutation = useMutation({
      mutationFn: async (barcode) => {
        return await axiosClient.post('/admin/cross-docking', { barcode });
      },
      onSuccess: (res) => {
        if (res.success) {
          toaster.success(res.message);
          queryClient.invalidateQueries({ queryKey: ['crossDockingPending'] });
        } else {
          toaster.error(res.message || 'Lỗi quét mã.');
        }
      },
      onError: (err) => {
        const errorMsg = err.response?.data?.message || err.message;
        toaster.error('Lỗi hệ thống: ' + errorMsg);
      }
    });

    return { pendingShipments, isFetching, scanMutation, toaster };
  },
  data() {
    return {
      barcode: "",
      lastScanned: ""
    };
  },
  mounted() {
    this.keepFocus();
    // Keep focus whenever user clicks anywhere on the document (optional but helpful)
    document.addEventListener('click', this.keepFocus);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.keepFocus);
  },
  methods: {
    keepFocus() {
      if (this.$refs.barcodeInput) {
        this.$refs.barcodeInput.focus();
      }
    },
    playBeep(isSuccess = true) {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        
        if (isSuccess) {
          oscillator.type = 'sine';
          oscillator.frequency.value = 1000;
          gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
          oscillator.start();
          oscillator.stop(audioCtx.currentTime + 0.1);
        } else {
          oscillator.type = 'sawtooth';
          oscillator.frequency.value = 200;
          gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
          oscillator.start();
          oscillator.stop(audioCtx.currentTime + 0.4);
        }
      } catch (e) {
        console.error("Audio Context not supported");
      }
    },
    async handleScan() {
      if (!this.barcode.trim()) return;
      
      const code = this.barcode.trim();
      
      // Call API via mutation
      this.scanMutation.mutate(code, {
        onSuccess: (res) => {
          if (res.success) {
            this.playBeep(true);
            this.lastScanned = res.data?.phu_tung + ' - ' + res.data?.don_hang;
          } else {
            this.playBeep(false);
          }
        },
        onError: () => {
          this.playBeep(false);
        }
      });
      
      this.barcode = "";
      this.$refs.barcodeInput.focus();
    }
  }
};
</script>


