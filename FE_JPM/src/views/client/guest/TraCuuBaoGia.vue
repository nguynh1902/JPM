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
          <div class="card shadow-sm border-0" style="border-radius: 12px;">
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
                  <span v-else-if="quote.trang_thai === 1" class="badge bg-warning text-dark p-2">Đã báo giá (Chờ xác nhận)</span>
                  <span v-else-if="quote.trang_thai === 2" class="badge bg-success p-2">Đã chốt đơn hàng</span>
                  <span v-else-if="quote.trang_thai === 3" class="badge bg-danger p-2">Đã hủy/Từ chối</span>
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

              <!-- Hành động -->
              <div v-if="quote.trang_thai === 1" class="d-flex flex-column flex-md-row gap-3 justify-content-center border-top pt-4">
                <button class="btn btn-outline-danger px-4 py-2" @click="handleAction('reject')" :disabled="isSubmitting">
                  <i class="bx bx-x-circle me-1"></i> Từ chối giá
                </button>
                <button class="btn btn-success px-5 py-2 fw-bold" @click="handleAction('accept')" :disabled="isSubmitting">
                  <i class="bx bx-check-circle me-1"></i> Đồng Ý Chốt Đơn
                </button>
              </div>
              
              <!-- Message sau khi chốt -->
              <div v-else-if="quote.trang_thai === 2" class="text-center border-top pt-4">
                <h5 class="text-success fw-bold"><i class="bx bx-check-circle me-2"></i>Cảm ơn bạn đã chốt đơn!</h5>
                <p class="text-muted">Đơn hàng của bạn đang được JPM xử lý và sẽ giao trong thời gian sớm nhất.</p>
                <a href="https://zalo.me/0123456789" target="_blank" class="btn btn-primary mt-2">
                  <i class="bx bx-chat me-1"></i> Chat Zalo Hỗ Trợ
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
  name: 'TraCuuBaoGia',
  data() {
    return {
      token: this.$route.params.token,
      quote: null,
      loading: true,
      error: null,
      isSubmitting: false
    }
  },
  async mounted() {
    await this.fetchQuote();
  },
  methods: {
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(value);
    },
    async fetchQuote() {
      this.loading = true;
      try {
        const res = await axiosClient.get('/public/yeu-cau-bao-gia/' + this.token);
        if (res.data.success) {
          this.quote = res.data.data;
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
      let confirmMsg = actionType === 'accept' 
        ? 'Bạn đồng ý với mức giá này và chốt đặt hàng?' 
        : 'Bạn chắc chắn muốn từ chối báo giá này?';
        
      const result = await Swal.fire({
        title: 'Xác nhận',
        text: confirmMsg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: actionType === 'accept' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy'
      });
      
      if (result.isConfirmed) {
        this.isSubmitting = true;
        try {
          const res = await axiosClient.post('/public/yeu-cau-bao-gia/' + this.token + '/action', { action: actionType });
          if (res.data.success) {
            Swal.fire({ icon: 'success', title: 'Thành công', text: res.data.message });
            await this.fetchQuote(); // Reload data
          }
        } catch (err) {
          Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra, vui lòng thử lại sau.' });
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

