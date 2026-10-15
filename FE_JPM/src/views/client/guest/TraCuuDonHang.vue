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
              <div v-if="result.type === 'order' && result.data.trang_thai_thanh_toan === 0 && result.data.trang_thai !== 3 && result.data.trang_thai !== 4" class="bg-light p-4 rounded-4 text-center mt-4">
                <h6 class="fw-bold text-uppercase text-dark mb-3"><i class='bx bx-qr-scan me-2'></i>Thanh Toán Nhanh Qua VietQR</h6>
                <p class="text-muted small mb-4">Mở ứng dụng ngân hàng và quét mã QR bên dưới để thanh toán.</p>
                <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm mb-3">
                  <img :src="getVietQrUrl()" alt="VietQR" class="img-fluid" style="max-width: 250px;">
                </div>
                <div class="text-muted small">
                  <p class="mb-1">Ngân hàng: <strong>MB Bank</strong></p>
                  <p class="mb-1">STK: <strong>123456789</strong> (JPM MARINE)</p>
                  <p class="mb-0">Nội dung: <strong>Thanh toan {{ result.data.ma_don_hang }}</strong></p>
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
      result: null
    }
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
    getVietQrUrl() {
      if (!this.result || this.result.type !== 'order') return '';
      const amount = this.calculateGrandTotal();
      const addInfo = `Thanh toan ${this.result.data.ma_don_hang}`;
      return `https://img.vietqr.io/image/970422-123456789-compact2.png?amount=${amount}&addInfo=${encodeURIComponent(addInfo)}&accountName=JPM%20MARINE`;
    },
    async searchTransaction() {
      if (!this.form.code || !this.form.phone) return;
      this.isLoading = true;
      this.result = null;
      try {
        const res = await axiosClient.post('/public/tra-cuu-nhanh', this.form);
        if (res.data.success) {
          this.result = res.data;
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
