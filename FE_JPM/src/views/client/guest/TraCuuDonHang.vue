<template>
  <main class="main-content min-vh-100 d-flex flex-column align-items-center bg-light" style="padding-top: 100px;">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 pt-5 pb-3 text-center">
              <h2 class="fw-bold mb-2" style="font-family: var(--heading-font); font-size: 28px;">Tra Cứu Giao Dịch</h2>
              <p class="text-muted small">Vui lòng nhập thông tin để kiểm tra trạng thái đơn hàng hoặc báo giá của bạn</p>
            </div>
            <div class="card-body px-5 pb-5">
              <form @submit.prevent="searchTransaction">
                <div class="mb-4">
                  <label class="form-label text-uppercase small text-muted fw-bold">Mã Giao Dịch</label>
                  <input type="text" class="form-control form-control-lg border-0 bg-light px-4" 
                         v-model="form.code" placeholder="VD: DH-XXXX hoặc YCBG-..." required>
                </div>
                <div class="mb-4">
                  <label class="form-label text-uppercase small text-muted fw-bold">Số Điện Thoại</label>
                  <input type="tel" class="form-control form-control-lg border-0 bg-light px-4" 
                         v-model="form.phone" placeholder="Số điện thoại đã dùng để đặt hàng" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 py-3 text-uppercase fw-bold shadow-sm" style="letter-spacing: 1px;" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {{ isLoading ? 'Đang Tra Cứu...' : 'Tra Cứu Ngay' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Result Section -->
      <div class="row justify-content-center mt-5" v-if="result">
        <div class="col-md-10 col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-1 fw-bold text-uppercase">{{ result.type === 'order' ? 'ĐƠN HÀNG' : 'BÁO GIÁ' }} <span class="text-primary">#{{ result.data.ma_don_hang || result.data.ma_yeu_cau }}</span></h5>
                <p class="mb-0 text-muted small">Cập nhật lúc: {{ new Date(result.data.updated_at).toLocaleString('vi-VN') }}</p>
              </div>
              <div>
                <span class="badge rounded-pill px-3 py-2" :class="statusClass">
                  {{ statusText }}
                </span>
              </div>
            </div>
            
            <div class="card-body p-4">
              <!-- Customer Info -->
              <div class="row mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <p class="text-muted small text-uppercase fw-bold mb-1">Khách Hàng</p>
                  <p class="mb-0 fw-medium">{{ result.data.khach_hang_ten }}</p>
                  <p class="mb-0 text-muted">{{ result.data.khach_hang_sdt }}</p>
                </div>
                <div class="col-sm-6" v-if="result.type === 'order'">
                  <p class="text-muted small text-uppercase fw-bold mb-1">Địa Chỉ Giao Hàng</p>
                  <p class="mb-0 fw-medium">{{ result.data.dia_chi_text || 'Chưa cung cấp' }}</p>
                </div>
              </div>

              <!-- Items Table -->
              <div class="table-responsive mb-4 rounded border">
                <table class="table table-borderless mb-0">
                  <thead class="bg-light border-bottom">
                    <tr>
                      <th class="text-muted small text-uppercase">Sản Phẩm</th>
                      <th class="text-center text-muted small text-uppercase">SL</th>
                      <th class="text-end text-muted small text-uppercase">Đơn Giá</th>
                      <th class="text-end text-muted small text-uppercase">Thành Tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, idx) in result.data.chi_tiet" :key="idx" class="border-bottom">
                      <td class="py-3">
                        <p class="mb-0 fw-medium">{{ item.ten_phu_tung }}</p>
                        <small class="text-muted">{{ item.ma_phu_tung }}</small>
                      </td>
                      <td class="text-center py-3 align-middle">{{ item.so_luong }}</td>
                      <td class="text-end py-3 align-middle">{{ formatCurrency(item.don_gia || item.gia_ban || 0) }}</td>
                      <td class="text-end py-3 align-middle fw-medium">{{ formatCurrency(item.so_luong * (item.don_gia || item.gia_ban || 0)) }}</td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-light">
                    <tr>
                      <td colspan="3" class="text-end text-muted pt-3 pb-1">Tạm Tính:</td>
                      <td class="text-end fw-medium pt-3 pb-1">{{ formatCurrency(calculateItemsTotal()) }}</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="text-end text-muted py-1">Phí Vận Chuyển:</td>
                      <td class="text-end fw-medium py-1">
                        {{ result.data.khach_tu_tra_cuoc ? 'Khách trả cước' : formatCurrency(result.data.phi_van_chuyen || 0) }}
                      </td>
                    </tr>
                    <tr v-if="result.data.tien_coc_yeu_cau > 0">
                      <td colspan="3" class="text-end text-muted py-1">Yêu Cầu Cọc:</td>
                      <td class="text-end fw-bold text-primary py-1">{{ formatCurrency(result.data.tien_coc_yeu_cau) }}</td>
                    </tr>
                    <tr v-if="result.type === 'order' && result.data.da_thu > 0">
                      <td colspan="3" class="text-end text-muted py-1">Đã Thanh Toán:</td>
                      <td class="text-end fw-bold text-success py-1">- {{ formatCurrency(result.data.da_thu) }}</td>
                    </tr>
                    <tr>
                      <td colspan="3" class="text-end text-dark fw-bold pt-1 pb-3">TỔNG CỘNG:</td>
                      <td class="text-end fw-bold text-danger pt-1 pb-3 fs-5">{{ formatCurrency(calculateGrandTotal()) }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <!-- Payment Section for Unpaid Orders -->
              <div v-if="result.type === 'order' && result.data.trang_thai_thanh_toan === 0 && result.data.trang_thai !== 3 && result.data.trang_thai !== 4" class="bg-light p-4 rounded-4 text-center mt-4 border border-primary border-opacity-25">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                  <span class="badge bg-primary px-3 py-2 rounded-pill"><i class='bx bx-qr-scan me-1'></i> VIETQR NAPAS247 TỰ ĐỘNG</span>
                  <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class='bx bx-wifi me-1'></i> ĐỐI SOÁT TỨC THÌ</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Thanh Toán / Đặt Cọc Chuyển Khoản</h5>
                <p class="text-muted small mb-4">Mở ứng dụng Mobile Banking của mọi ngân hàng (VietinBank, Vietcombank, MB, Techcombank...) quét mã bên dưới để thanh toán tự động trong vài giây.</p>
                
                <div v-if="vietQrData" class="row justify-content-center align-items-center g-4">
                  <div class="col-md-5 text-center">
                    <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm border">
                      <img :src="vietQrData.qr_url" alt="VietQR Napas247" class="img-fluid" style="max-width: 240px; border-radius: 8px;">
                    </div>
                    <div class="mt-2 text-muted small d-flex align-items-center justify-content-center gap-2">
                      <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                      <span>Đang chờ chuyển khoản...</span>
                    </div>
                  </div>
                  
                  <div class="col-md-7 text-start">
                    <div class="bg-white p-3 rounded-3 shadow-sm border">
                      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Ngân Hàng Thụ Hưởng:</span>
                        <span class="fw-bold text-dark">{{ vietQrData.bank_name }} ({{ vietQrData.bank_id }})</span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Số Tài Khoản:</span>
                        <div class="d-flex align-items-center gap-2">
                          <span class="fw-bold text-primary fs-6">{{ vietQrData.account_no }}</span>
                          <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" @click="copyText(vietQrData.account_no, 'Đã copy số tài khoản')">
                            <i class='bx bx-copy'></i>
                          </button>
                        </div>
                      </div>
                      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Tên Chủ Tài Khoản:</span>
                        <span class="fw-bold text-dark">{{ vietQrData.account_name }}</span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Số Tiền:</span>
                        <span class="fw-bold text-danger fs-5">{{ vietQrData.amount_formatted }}</span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nội Dung CK (Bắt buộc):</span>
                        <div class="d-flex align-items-center gap-2">
                          <span class="fw-bold text-dark bg-light px-2 py-1 rounded border">{{ vietQrData.transfer_content }}</span>
                          <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" @click="copyText(vietQrData.transfer_content, 'Đã copy nội dung chuyển khoản')">
                            <i class='bx bx-copy'></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <small class="text-muted d-block mt-2 fst-italic">
                      * Vui lòng giữ nguyên nội dung chuyển khoản để hệ thống ngân hàng đối soát tự động kích hoạt xuất kho.
                    </small>
                  </div>
                </div>

                <div v-else class="py-4">
                  <div class="spinner-border text-primary" role="status"></div>
                  <p class="text-muted small mt-2">Đang kết nối cổng VietQR...</p>
                </div>
              </div>
              
              <!-- Zalo Support -->
              <div class="text-center mt-5">
                <p class="text-muted small mb-2">Cần hỗ trợ thêm về giao dịch này?</p>
                <a href="https://zalo.me/0123456789" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                  <i class="bx bx-chat me-1"></i> Liên hệ Zalo CSKH
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</template>

<script>
import axiosClient from '../../../api/axiosClient'
import Swal from 'sweetalert2'

export default {
  name: 'TraCuuDonHang',
  data() {
    return {
      form: {
        code: '',
        phone: ''
      },
      isLoading: false,
      result: null,
      vietQrData: null,
      pollingTimer: null
    }
  },
  beforeUnmount() {
    this.stopPolling();
  },
  computed: {
    statusText() {
      if (!this.result) return '';
      const r = this.result.data;
      if (this.result.type === 'quote') {
        if (r.trang_thai === 0) return 'Chờ xử lý';
        if (r.trang_thai === 1) return 'Đã báo giá (Chờ chốt)';
        if (r.trang_thai === 2) return 'Hoàn tất (Đã chuyển đơn)';
        if (r.trang_thai === 3) return 'Đã hủy';
      } else {
        if (r.trang_thai === 0) return 'Chờ xác nhận';
        if (r.trang_thai === 1) return 'Đang xử lý / Đang giao';
        if (r.trang_thai === 2 || r.trang_thai === 3) return 'Hoàn thành';
        if (r.trang_thai === 4) return 'Đã hủy';
      }
      return 'Không xác định';
    },
    statusClass() {
      if (!this.result) return 'bg-secondary text-white';
      const r = this.result.data;
      if (this.result.type === 'quote') {
        if (r.trang_thai === 0) return 'bg-warning text-dark';
        if (r.trang_thai === 1) return 'bg-info text-white';
        if (r.trang_thai === 2) return 'bg-success text-white';
        if (r.trang_thai === 3) return 'bg-danger text-white';
      } else {
        if (r.trang_thai === 0) return 'bg-warning text-dark';
        if (r.trang_thai === 1) return 'bg-info text-white';
        if (r.trang_thai === 2 || r.trang_thai === 3) return 'bg-success text-white';
        if (r.trang_thai === 4) return 'bg-danger text-white';
      }
      return 'bg-secondary text-white';
    }
  },
  methods: {
    formatCurrency(value) {
      if (!value) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    copyText(text, msg) {
      if (!text) return;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
      }
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: msg || 'Đã sao chép!',
        showConfirmButton: false,
        timer: 2000
      });
    },
    calculateItemsTotal() {
      if (!this.result || !this.result.data.chi_tiet) return 0;
      return this.result.data.chi_tiet.reduce((sum, item) => {
        return sum + (item.so_luong * (item.don_gia || item.gia_ban || 0));
      }, 0);
    },
    calculateGrandTotal() {
      if (!this.result) return 0;
      const subtotal = this.calculateItemsTotal();
      const shipping = this.result.data.khach_tu_tra_cuoc ? 0 : (this.result.data.phi_van_chuyen || 0);
      const paid = this.result.data.da_thu || 0;
      return subtotal + shipping - paid;
    },
    async loadVietQR() {
      if (!this.result || this.result.type !== 'order') return;
      try {
        const res = await axiosClient.get(`/public/vietqr/don-hang/${this.result.data.ma_don_hang}`);
        const payload = res?.qr_url ? res : res?.data;
        if (payload && (res?.success || payload?.success)) {
          this.vietQrData = payload;
        }
      } catch (err) {
        console.error('Không thể tải mã VietQR:', err);
      }
    },
    startPolling() {
      this.stopPolling();
      if (!this.result || this.result.type !== 'order') return;
      if (this.result.data.trang_thai_thanh_toan === 2 || this.result.data.trang_thai === 3 || this.result.data.trang_thai === 4) return;

      const orderCode = this.result.data.ma_don_hang;
      this.pollingTimer = setInterval(async () => {
        try {
          const res = await axiosClient.get(`/public/vietqr/check-status/order/${orderCode}`);
          const payload = res?.paid_amount !== undefined || res?.is_paid !== undefined ? res : res?.data;
          if (payload && (res?.success || payload?.success)) {
            const currentPaid = parseFloat(this.result.data.da_thu || 0);
            const newPaid = parseFloat(payload.paid_amount || 0);

            if (newPaid > currentPaid || payload.is_paid || payload.is_deposit_paid) {
              this.result.data.da_thu = newPaid;
              this.result.data.trang_thai_thanh_toan = payload.payment_status;
              this.stopPolling();

              Swal.fire({
                icon: 'success',
                title: 'Thanh Toán Thành Công!',
                text: `Hệ thống đã nhận ${this.formatCurrency(newPaid)} và hoàn tất đối soát tự động.`,
                confirmButtonColor: '#0d6efd'
              });
            }
          }
        } catch (e) {
          // Silent polling errors
        }
      }, 4000);
    },
    stopPolling() {
      if (this.pollingTimer) {
        clearInterval(this.pollingTimer);
        this.pollingTimer = null;
      }
    },
    async searchTransaction() {
      if (!this.form.code || !this.form.phone) return;
      this.stopPolling();
      this.isLoading = true;
      this.result = null;
      this.vietQrData = null;
      try {
        const res = await axiosClient.post('/public/tra-cuu-nhanh', this.form);
        const payload = res?.type ? res : res?.data;
        if (payload && (res?.success || payload?.success)) {
          this.result = payload;
          if (this.result.type === 'order' && this.result.data.trang_thai_thanh_toan === 0) {
            await this.loadVietQR();
            this.startPolling();
          }
          setTimeout(() => {
            window.scrollTo({ top: 500, behavior: 'smooth' });
          }, 100);
        }
      } catch (error) {
        if (error.response && error.response.status === 404) {
          Swal.fire({
            icon: 'error',
            title: 'Không tìm thấy!',
            text: 'Mã giao dịch hoặc Số điện thoại không chính xác. Vui lòng kiểm tra lại.',
            confirmButtonColor: '#343a40'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi kết nối!',
            text: 'Có lỗi xảy ra khi tra cứu, vui lòng thử lại sau.',
            confirmButtonColor: '#343a40'
          });
        }
      } finally {
        this.isLoading = false;
      }
    }
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    if (code) {
      this.form.code = code;
    }
  }
}
</script>
