<template>
  <main class="tra-cuu-page bg-light py-5">
    <div class="container" v-if="loading">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3 text-muted">Đang tải thông tin báo giá...</p>
      </div>
    </div>
    
    <div class="container" v-else-if="error">
      <div class="alert alert-danger text-center">
        <h4><i class="bx bx-error-circle"></i> Lỗi</h4>
        <p>{{ error }}</p>
        <router-link to="/" class="btn btn-primary mt-3">Về Trang Chủ</router-link>
      </div>
    </div>

    <div class="container" v-else-if="quote">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-4 text-center">
              <h4 class="fw-bold text-dark mb-0">Tra Cứu Báo Giá</h4>
              <p class="text-muted mb-0 mt-2">Mã yêu cầu: <span class="fw-bold text-primary">{{ quote.ma_yeu_cau }}</span></p>
            </div>
            
            <div class="card-body p-4 p-md-5">
              <!-- Thông tin chung -->
              <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                  <h6 class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem;">Thông tin Khách Hàng</h6>
                  <p class="mb-1 fw-bold text-dark">{{ quote.khach_hang_ten }}</p>
                  <p class="mb-1 text-muted"><i class="bx bx-phone me-2"></i>{{ quote.khach_hang_sdt }}</p>
                  <p class="mb-0 text-muted"><i class="bx bx-calendar me-2"></i>{{ new Date(quote.created_at).toLocaleDateString('vi-VN') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                  <h6 class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem;">Trạng Thái</h6>
                  <span v-if="quote.trang_thai === 0" class="badge bg-secondary p-2">Chờ báo giá</span>
                  <span v-else-if="quote.trang_thai === 1 && !quote.is_expired" class="badge bg-warning text-dark p-2">Đã báo giá (Còn hiệu lực)</span>
                  <span v-else-if="quote.trang_thai === 2" class="badge bg-success p-2">Đã chốt đơn hàng</span>
                  <span v-else class="badge bg-danger p-2">Hết hạn hiệu lực / Đã hủy</span>
                </div>
              </div>

              <!-- Banner Hiệu Lực Báo Giá -->
              <div v-if="quote.trang_thai === 1 && !quote.is_expired" class="alert alert-warning border-warning d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                  <i class="bx bx-time-five fs-2 text-warning"></i>
                  <div>
                    <strong class="d-block text-dark">Báo Giá Có Hiệu Lực Trong 72 Giờ</strong>
                    <span class="text-muted small">Cam kết giữ đơn giá phụ tùng theo tỷ giá JPY hiện tại đến {{ formatDateTime(quote.expires_at) }}.</span>
                  </div>
                </div>
                <div class="text-end" v-if="remainingSeconds > 0">
                  <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm">
                    <i class="bx bx-stopwatch me-1"></i> Còn {{ formattedRemainingTime }}
                  </span>
                </div>
              </div>

              <!-- Banner Hết Hạn Báo Giá -->
              <div v-else-if="quote.trang_thai === 3 || quote.is_expired" class="alert alert-danger border-danger p-3 mb-4 rounded-3 shadow-sm">
                <div class="d-flex align-items-start gap-3">
                  <i class="bx bx-error-circle fs-2 text-danger mt-1"></i>
                  <div class="flex-grow-1">
                    <strong class="d-block text-dark fs-6 mb-1">Báo Giá Này Đã Quá Hạn Hiệu Lực (72 Giờ)</strong>
                    <p class="text-muted small mb-2">
                      Do tỷ giá Yên Nhật và cước vận chuyển quốc tế biến động theo từng đợt, mức giá này đã hết hiệu lực bảo lưu. Quý khách vui lòng bấm nút bên dưới để nhân viên JPM cập nhật bảng giá mới nhất.
                    </p>
                    <button class="btn btn-primary fw-bold px-4 py-2 shadow-sm" @click="handleAction('re_quote')" :disabled="isSubmitting">
                      <i class="bx bx-refresh me-1"></i> Yêu Cầu Báo Giá Lại
                    </button>
                  </div>
                </div>
              </div>

              <!-- Banner Đang Chờ Báo Giá -->
              <div v-else-if="quote.trang_thai === 0" class="alert alert-info border-info p-3 mb-4 rounded-3 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                  <i class="bx bx-loader-alt bx-spin fs-2 text-primary"></i>
                  <div>
                    <strong class="d-block text-dark">Đang Kiểm Tra & Tính Toán Giá Từ Nhật</strong>
                    <span class="text-muted small">Kỹ thuật viên JPM đang đối soát thông số động cơ và liên hệ hãng tại Nhật. Báo giá sẽ được gửi qua Zalo và cập nhật tại trang này ngay khi hoàn tất.</span>
                  </div>
                </div>
              </div>

              <!-- Chi tiết báo giá -->
              <h6 class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem;">Chi tiết phụ tùng</h6>
              <div class="table-responsive mb-4">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center" style="width: 5%">STT</th>
                      <th>Tên & Mã Phụ Tùng</th>
                      <th class="text-center" style="width: 10%">SL</th>
                      <th class="text-end" style="width: 20%">Đơn Giá</th>
                      <th class="text-end" style="width: 25%">Thành Tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, idx) in quote.chi_tiet" :key="item.id">
                      <td class="text-center">{{ idx + 1 }}</td>
                      <td>
                        <span class="d-block fw-bold text-dark">{{ item.ten_phu_tung }}</span>
                        <span class="text-muted" style="font-size: 0.8rem;">Mã: {{ item.ma_phu_tung || 'N/A' }}</span>
                      </td>
                      <td class="text-center">{{ item.so_luong }}</td>
                      <td class="text-end">{{ quote.trang_thai > 0 ? formatCurrency(item.don_gia) : 'Đang cập nhật' }}</td>
                      <td class="text-end fw-bold text-danger">
                        {{ quote.trang_thai > 0 ? formatCurrency(item.so_luong * item.don_gia) : '---' }}
                      </td>
                    </tr>
                  </tbody>
                  <tfoot class="table-light">
                    <tr>
                      <td colspan="4" class="text-end fw-bold text-dark text-muted">Phí Vận Chuyển:</td>
                      <td class="text-end fw-bold text-danger">
                        <span v-if="quote.trang_thai > 0 && quote.khach_tu_tra_cuoc">Người nhận trả cước</span>
                        <span v-else-if="quote.trang_thai > 0">{{ formatCurrency(quote.phi_van_chuyen || 0) }}</span>
                        <span v-else>---</span>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" class="text-end fw-bold text-dark text-muted">Yêu Cầu Cọc:</td>
                      <td class="text-end fw-bold text-primary">{{ quote.trang_thai > 0 ? formatCurrency(quote.tien_coc_yeu_cau || 0) : '---' }}</td>
                    </tr>
                    <tr>
                      <td colspan="4" class="text-end fw-bold text-dark">Tổng tiền (Tạm tính):</td>
                      <td class="text-end fw-bold text-danger fs-5">{{ quote.trang_thai > 0 ? formatCurrency(quote.tong_tien + (quote.khach_tu_tra_cuoc ? 0 : (quote.phi_van_chuyen || 0))) : 'Chờ báo giá' }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              
              <!-- Ghi chú Admin (nếu có) -->
              <div v-if="quote.ghi_chu_zalo" class="alert alert-info mb-4">
                <strong><i class="bx bx-message-dots me-1"></i> Ghi chú từ JPM:</strong>
                <p class="mb-0 mt-1" style="white-space: pre-wrap;">{{ quote.ghi_chu_zalo }}</p>
              </div>

              <!-- VietQR Đặt Cọc Báo Giá -->
              <div v-if="(quote.trang_thai === 1 || quote.trang_thai === 2) && quote.tien_coc_yeu_cau > 0 && !quote.is_expired" class="card bg-light border-primary border-opacity-25 p-4 mb-4 text-center rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                  <span class="badge bg-primary px-3 py-2 rounded-pill"><i class='bx bx-qr-scan me-1'></i> VIETQR ĐẶT CỌC GIỮ HÀNG</span>
                  <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class='bx bx-check-shield me-1'></i> TỰ ĐỘNG ĐỐI SOÁT</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Quét QR Đặt Cọc - Đặt Hàng Nhật Báo Về</h5>
                <p class="text-muted small mb-3">Mở app Ngân hàng quét mã bên dưới để chuyển khoản tiền cọc <strong>{{ formatCurrency(quote.tien_coc_yeu_cau) }}</strong>. Hệ thống tự động ghi nhận và chuyển đơn.</p>
                
                <div v-if="vietQrData" class="row justify-content-center align-items-center g-3 text-start">
                  <div class="col-md-5 text-center">
                    <div class="d-inline-block bg-white p-2 rounded-3 shadow-sm border">
                      <img :src="vietQrData.qr_url" alt="VietQR Napas247" class="img-fluid" style="max-width: 220px; border-radius: 6px;">
                    </div>
                    <div class="mt-2 text-muted small d-flex align-items-center justify-content-center gap-2">
                      <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
                      <span>Lắng nghe giao dịch tự động...</span>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="bg-white p-3 rounded-3 border">
                      <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-muted">Ngân hàng:</span>
                        <span class="fw-bold">{{ vietQrData.bank_name }} ({{ vietQrData.bank_id }})</span>
                      </div>
                      <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-muted">STK Thụ Hưởng:</span>
                        <div class="d-flex align-items-center gap-1">
                          <span class="fw-bold text-primary">{{ vietQrData.account_no }}</span>
                          <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" @click="copyText(vietQrData.account_no, 'Đã copy STK')">
                            <i class='bx bx-copy'></i>
                          </button>
                        </div>
                      </div>
                      <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-muted">Tên Thụ Hưởng:</span>
                        <span class="fw-bold">{{ vietQrData.account_name }}</span>
                      </div>
                      <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-muted">Số Tiền Cọc:</span>
                        <span class="fw-bold text-danger fs-6">{{ vietQrData.amount_formatted }}</span>
                      </div>
                      <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">Nội dung CK:</span>
                        <div class="d-flex align-items-center gap-1">
                          <span class="fw-bold text-dark bg-light px-2 py-0 rounded border">{{ vietQrData.transfer_content }}</span>
                          <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1" @click="copyText(vietQrData.transfer_content, 'Đã copy nội dung')">
                            <i class='bx bx-copy'></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hành động -->
              <div v-if="quote.trang_thai === 1 && !quote.is_expired" class="d-flex flex-column flex-md-row gap-3 justify-content-center border-top pt-4">
                <button class="btn btn-outline-danger px-4 py-2" @click="handleAction('reject')" :disabled="isSubmitting">
                  <i class="bx bx-x-circle me-1"></i> Từ chối giá
                </button>
                <button class="btn btn-success px-5 py-2 fw-bold" @click="handleAction('accept')" :disabled="isSubmitting">
                  <i class="bx bx-check-circle me-1"></i> Đồng Ý Chốt Đơn
                </button>
              </div>
              
              <!-- Message sau khi chốt -->
              <div v-else-if="quote.trang_thai === 2" class="text-center border-top pt-4">
                <h5 class="text-success fw-bold"><i class="bx bx-check-circle me-2"></i>Cảm ơn Quý khách đã chốt đơn!</h5>
                <p class="text-muted">Đơn hàng của Quý khách đang được JPM xử lý và tiến hành gom đơn nhập hàng từ Nhật Bản.</p>
                <a href="https://zalo.me/0123456789" target="_blank" class="btn btn-primary mt-2">
                  <i class="bx bx-chat me-1"></i> Chat Zalo Hỗ Trợ Kỹ Thuật
                </a>
              </div>

              <!-- Message khi hết hạn mà chưa bấm yêu cầu lại -->
              <div v-else-if="quote.trang_thai === 3 || quote.is_expired" class="text-center border-top pt-4">
                <p class="text-muted mb-3">Quý khách cần cập nhật giá mới hoặc đàm phán số lượng lớn cho công trình/tàu cá?</p>
                <div class="d-flex justify-content-center gap-2">
                  <button class="btn btn-primary px-4 py-2 fw-bold" @click="handleAction('re_quote')" :disabled="isSubmitting">
                    <i class="bx bx-refresh me-1"></i> Yêu Cầu Báo Giá Lại
                  </button>
                  <a href="https://zalo.me/0123456789" target="_blank" class="btn btn-outline-primary px-4 py-2">
                    <i class="bx bx-chat me-1"></i> Liên Hệ Kỹ Thuật
                  </a>
                </div>
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
  name: 'TraCuuBaoGia',
  data() {
    return {
      token: this.$route.params.token,
      quote: null,
      loading: true,
      error: null,
      isSubmitting: false,
      vietQrData: null,
      pollingTimer: null,
      remainingSeconds: 0,
      countdownTimer: null
    }
  },
  computed: {
    formattedRemainingTime() {
      if (this.remainingSeconds <= 0) return '00:00:00';
      const hours = Math.floor(this.remainingSeconds / 3600);
      const minutes = Math.floor((this.remainingSeconds % 3600) / 60);
      const seconds = this.remainingSeconds % 60;
      if (hours >= 24) {
        const days = Math.floor(hours / 24);
        const remHours = hours % 24;
        return `${days} ngày ${remHours} giờ`;
      }
      return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
  },
  async mounted() {
    await this.fetchQuote();
  },
  beforeUnmount() {
    this.stopPolling();
    this.stopCountdown();
  },
  methods: {
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(value);
    },
    formatDateTime(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' });
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
    startCountdown() {
      this.stopCountdown();
      if (this.remainingSeconds <= 0) return;
      this.countdownTimer = setInterval(() => {
        if (this.remainingSeconds > 0) {
          this.remainingSeconds--;
        } else {
          this.stopCountdown();
          if (this.quote) {
            this.quote.is_expired = true;
          }
        }
      }, 1000);
    },
    stopCountdown() {
      if (this.countdownTimer) {
        clearInterval(this.countdownTimer);
        this.countdownTimer = null;
      }
    },
    async loadVietQR() {
      if (!this.quote || !this.quote.ma_yeu_cau) return;
      try {
        const res = await axiosClient.get(`/public/vietqr/bao-gia/${this.quote.ma_yeu_cau}`);
        const payload = res?.qr_url ? res : res?.data;
        if (payload && (res.success || payload.success)) {
          this.vietQrData = payload;
        }
      } catch (err) {
        console.error('Không thể nạp mã VietQR cọc:', err);
      }
    },
    startPolling() {
      this.stopPolling();
      if (!this.quote || (this.quote.trang_thai !== 1 && this.quote.trang_thai !== 2)) return;

      const quoteCode = this.quote.ma_yeu_cau;
      this.pollingTimer = setInterval(async () => {
        try {
          const res = await axiosClient.get(`/public/vietqr/check-status/quote/${quoteCode}`);
          const payload = res?.is_deposit_paid !== undefined ? res : res?.data;
          if (payload && (res.success || payload.success)) {
            if (payload.is_deposit_paid || payload.quote_status === 2) {
              this.stopPolling();
              await this.fetchQuote();

              Swal.fire({
                icon: 'success',
                title: 'Đã Nhận Tiền Cọc Thành Công!',
                text: 'Hệ thống đã tự động đối soát giao dịch và tiến hành gom đơn nhập từ Nhật Bản.',
                confirmButtonColor: '#28a745'
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
    async fetchQuote() {
      this.loading = true;
      try {
        const res = await axiosClient.get('/public/yeu-cau-bao-gia/' + this.token);
        const ok = res?.success ?? res?.data?.success;
        const quoteData = res?.data?.data ?? res?.data;
        if (ok && quoteData) {
          this.quote = quoteData;
          if (this.quote.remaining_seconds && this.quote.remaining_seconds > 0) {
            this.remainingSeconds = this.quote.remaining_seconds;
            this.startCountdown();
          } else if (this.quote.is_expired) {
            this.remainingSeconds = 0;
            this.stopCountdown();
          }
          if ((this.quote.trang_thai === 1 || this.quote.trang_thai === 2) && this.quote.tien_coc_yeu_cau > 0 && !this.quote.is_expired) {
            await this.loadVietQR();
            this.startPolling();
          }
        } else {
          this.error = "Không tìm thấy dữ liệu báo giá.";
        }
      } catch (err) {
        this.error = "Link báo giá không hợp lệ hoặc đã hết hạn.";
      } finally {
        this.loading = false;
      }
    },
    async handleAction(actionType) {
      let confirmMsg = '';
      let confirmBtnText = 'Đồng ý';
      let confirmBtnColor = '#28a745';

      if (actionType === 'accept') {
        confirmMsg = 'Bạn đồng ý với mức giá này và chốt đặt hàng?';
      } else if (actionType === 'reject') {
        confirmMsg = 'Bạn chắc chắn muốn từ chối báo giá này?';
        confirmBtnColor = '#dc3545';
      } else if (actionType === 're_quote') {
        confirmMsg = 'Gửi yêu cầu đến JPM để cập nhật lại bảng giá phụ tùng mới nhất?';
        confirmBtnText = 'Gửi yêu cầu';
        confirmBtnColor = '#0d6efd';
      }
        
      const result = await Swal.fire({
        title: 'Xác nhận',
        text: confirmMsg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'Hủy'
      });
      
      if (result.isConfirmed) {
        this.isSubmitting = true;
        try {
          const res = await axiosClient.post('/public/yeu-cau-bao-gia/' + this.token + '/action', { action: actionType });
          const message = res?.message ?? res?.data?.message;
          if (res?.success || res?.data?.success) {
            Swal.fire({ icon: 'success', title: 'Thành công', text: message });
            await this.fetchQuote(); // Reload data
          } else {
            Swal.fire({ icon: 'warning', title: 'Thông báo', text: message });
            await this.fetchQuote();
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại sau.';
          Swal.fire({ icon: 'error', title: 'Lỗi', text: msg });
          await this.fetchQuote();
        } finally {
          this.isSubmitting = false;
        }
      }
    }
  }
}
</script>

<style scoped>
.tra-cuu-page {
  min-height: calc(100vh - 80px);
}
</style>

