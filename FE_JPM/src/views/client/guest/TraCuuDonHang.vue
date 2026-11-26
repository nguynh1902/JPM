<template>
  <div class="lux-lookup-page inner-page-section">
    <div class="container lux-lookup-container">

      <!-- 1. Form Tra cứu giao dịch (Minimalist Luxury) -->
      <section class="lux-search-card">
        <div class="lux-search-head">
          <span class="lux-overline">{{ settings?.company_name || 'JPM Marine' }} · Tra cứu trực tuyến</span>
          <h1 class="lux-search-title">Tra cứu đơn hàng &amp; báo giá</h1>
          <p class="lux-search-sub">
            Nhập mã giao dịch và số điện thoại đặt hàng để kiểm tra tiến độ xử lý, bảng báo giá hoặc thông tin vận chuyển.
          </p>
        </div>

        <form @submit.prevent="searchTransaction" class="lux-search-form">
          <div class="lux-form-grid">
            <div class="lux-field">
              <label for="lookupCode" class="lux-label">Mã đơn hàng / báo giá</label>
              <input
                id="lookupCode"
                v-model.trim="form.code"
                type="text"
                class="lux-input font-monospace"
                placeholder="VD: YCBG-261126-41C5 hoặc DH-..."
                required
              >
            </div>

            <div class="lux-field">
              <label for="lookupPhone" class="lux-label">Số điện thoại nhận hàng</label>
              <input
                id="lookupPhone"
                v-model.trim="form.phone"
                type="tel"
                class="lux-input"
                placeholder="Số điện thoại đã dùng khi gửi yêu cầu"
                required
              >
            </div>
          </div>

          <button type="submit" class="btn btn-danger lux-submit-btn" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <i v-else class="bi bi-search"></i>
            <span>{{ isLoading ? 'Đang tra cứu...' : 'Tra cứu giao dịch' }}</span>
          </button>
        </form>
      </section>

      <!-- 2. Kết quả tra cứu (Đồng bộ 100% chuẩn Chứng từ JPM) -->
      <article v-if="result" ref="resultSection" class="lux-doc-sheet mt-4">
        <!-- Header chứng từ -->
        <header class="lux-doc-header">
          <div>
            <span class="lux-overline">
              {{ result.type === 'order' ? 'Đơn hàng phụ tùng' : 'Yêu cầu báo giá' }}
            </span>
            <h2 class="lux-doc-code-heading">
              #{{ result.data.ma_don_hang || result.data.ma_yeu_cau }}
            </h2>
            <p class="lux-doc-updated mb-0">
              Cập nhật lúc {{ formatDateTime(result.data.updated_at || result.data.created_at) }}
            </p>
          </div>

          <span class="lux-status-indicator" :class="'status-' + statusTone">
            <span class="status-dot"></span>
            <span>{{ statusText }}</span>
          </span>
        </header>

        <!-- Thông tin khách hàng & giao nhận -->
        <section class="lux-info-grid">
          <div>
            <span class="lux-section-label">Thông tin khách hàng</span>
            <div class="lux-info-primary">{{ result.data.khach_hang_ten }}</div>
            <div class="lux-info-sub">{{ result.data.khach_hang_sdt }}</div>
          </div>

          <div>
            <span class="lux-section-label">Địa chỉ giao hàng</span>
            <div v-if="result.data.dia_chi_text || result.data.dia_chi_giao_hang" class="lux-info-address">
              {{ result.data.dia_chi_text || result.data.dia_chi_giao_hang }}
            </div>
            <div v-else class="lux-info-muted fst-italic">Chưa cung cấp</div>
          </div>
        </section>

        <!-- Thông tin vận đơn (nếu là đơn hàng đang giao) -->
        <section
          v-if="result.type === 'order' && result.data.ma_van_don"
          class="lux-callout callout-navy"
        >
          <div class="lux-callout-icon"><i class="bi bi-truck"></i></div>
          <div class="lux-callout-body flex-grow-1">
            <strong>{{ result.data.ten_don_vi_van_chuyen || 'Đơn vị vận chuyển' }}</strong>
            <p>
              Mã vận đơn: <code class="lux-inline-code">{{ result.data.ma_van_don }}</code>
            </p>
          </div>
          <a
            v-if="result.data.ten_don_vi_van_chuyen && result.data.ten_don_vi_van_chuyen.toLowerCase().includes('viettel')"
            :href="'https://viettelpost.com.vn/tra-cuu-hanh-trinh-don/?order_number=' + result.data.ma_van_don"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-outline-secondary lux-btn-sm flex-shrink-0"
          >
            Tra cứu ViettelPost
          </a>
        </section>

        <!-- Dải trạng thái báo giá (nếu là Quote) -->
        <template v-if="result.type === 'quote'">
          <section v-if="result.data.trang_thai === 1" class="lux-callout callout-amber">
            <div class="lux-callout-icon"><i class="bi bi-shield-check"></i></div>
            <div class="lux-callout-body flex-grow-1">
              <strong>Bảng báo giá đã sẵn sàng (Hiệu lực 72 giờ)</strong>
              <p>
                Kỹ thuật viên JPM đã hoàn tất kiểm tra phụ tùng, tính cước vận chuyển và mức cọc. Bạn có thể mở bảng báo giá chi tiết để xác nhận chốt đơn.
              </p>
              <div class="mt-3" v-if="result.data.token || result.data.token_xac_nhan">
                <router-link
                  :to="'/bao-gia/' + (result.data.token || result.data.token_xac_nhan)"
                  class="btn btn-danger lux-btn-sm"
                >
                  <i class="bi bi-file-earmark-text"></i>
                  <span>Mở bảng báo giá chi tiết</span>
                </router-link>
              </div>
            </div>
          </section>

          <section v-else-if="result.data.trang_thai === 0" class="lux-callout callout-navy">
            <div class="lux-callout-icon"><i class="bi bi-clock-history"></i></div>
            <div class="lux-callout-body">
              <strong>Bộ phận kỹ thuật đang kiểm tra &amp; lập báo giá</strong>
              <p>
                Hệ thống đang đối soát mã OEM, kiểm tra tồn kho và cước vận chuyển liên tỉnh để gửi bảng báo giá chính xác nhất qua Zalo.
              </p>
            </div>
          </section>

          <section v-else-if="result.data.trang_thai === 2" class="lux-callout callout-emerald">
            <div class="lux-callout-icon"><i class="bi bi-check-circle"></i></div>
            <div class="lux-callout-body">
              <strong>Yêu cầu đã được xác nhận chốt đơn</strong>
              <p>
                Đơn hàng của bạn đã được chuyển sang quy trình chuẩn bị phụ tùng và xuất kho giao nhận.
              </p>
            </div>
          </section>

          <section v-else-if="result.data.trang_thai === 3" class="lux-callout callout-danger">
            <div class="lux-callout-icon"><i class="bi bi-x-circle"></i></div>
            <div class="lux-callout-body">
              <strong>Yêu cầu báo giá đã đóng hoặc quá hạn 72 giờ</strong>
              <p>
                Bảng báo giá này không còn hiệu lực. Bạn có thể liên hệ kỹ thuật viên JPM để nhận bảng báo giá cập nhật mới nhất.
              </p>
            </div>
          </section>
        </template>

        <!-- Bảng chi tiết sản phẩm -->
        <section class="lux-table-section">
          <span class="lux-section-label mb-3">
            Chi tiết phụ tùng ({{ (result.data.chi_tiet || []).length }})
          </span>
          <div class="table-responsive">
            <table class="lux-parts-table">
              <thead>
                <tr>
                  <th>Phụ tùng &amp; mã OEM</th>
                  <th class="text-center col-qty">SL</th>
                  <th class="text-end col-price">Đơn giá</th>
                  <th class="text-end col-total">Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in result.data.chi_tiet" :key="idx">
                  <td>
                    <div class="lux-part-name">{{ item.ten_phu_tung }}</div>
                    <div class="lux-part-oem" v-if="item.ma_phu_tung">
                      Mã OEM: <code>{{ item.ma_phu_tung }}</code>
                    </div>
                  </td>
                  <td class="text-center col-qty fw-semibold">{{ item.so_luong }}</td>
                  <td class="text-end col-price">
                    <span v-if="getItemPrice(item) > 0" class="lux-num">
                      {{ formatCurrency(getItemPrice(item)) }}
                    </span>
                    <span v-else class="lux-pending-txt">Chờ báo giá</span>
                  </td>
                  <td class="text-end col-total">
                    <span v-if="getItemPrice(item) > 0" class="lux-num fw-semibold text-navy">
                      {{ formatCurrency(item.so_luong * getItemPrice(item)) }}
                    </span>
                    <span v-else class="text-muted">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Tổng kết tài chính căn phải -->
        <section class="lux-summary-section">
          <div class="lux-summary-box">
            <div class="lux-sum-row">
              <span class="lux-sum-label">Tạm tính tiền hàng</span>
              <span class="lux-sum-val">
                <template v-if="calculateItemsTotal() > 0">{{ formatCurrency(calculateItemsTotal()) }}</template>
                <template v-else><span class="lux-pending-txt">Chờ định giá</span></template>
              </span>
            </div>

            <div class="lux-sum-row">
              <span class="lux-sum-label">Phí vận chuyển</span>
              <span class="lux-sum-val">
                <template v-if="result.data.khach_tu_tra_cuoc">Khách tự trả cước chành xe</template>
                <template v-else-if="Number(result.data.phi_van_chuyen) > 0">{{ formatCurrency(result.data.phi_van_chuyen) }}</template>
                <template v-else><span class="lux-pending-txt">Chờ xác nhận</span></template>
              </span>
            </div>

            <div class="lux-sum-row lux-sum-grand">
              <span class="lux-sum-label">Tổng giá trị đơn hàng</span>
              <span class="lux-sum-val">
                <strong v-if="calculateItemsTotal() > 0" class="lux-grand-num">
                  {{ formatCurrency(calculateItemsTotal() + (result.data.khach_tu_tra_cuoc ? 0 : Number(result.data.phi_van_chuyen || 0))) }}
                </strong>
                <span v-else class="lux-pending-txt fw-normal">Đang cập nhật</span>
              </span>
            </div>

            <template v-if="Number(result.data.tien_coc_yeu_cau) > 0">
              <div class="lux-sum-row lux-sum-deposit">
                <span class="lux-sum-label">
                  Tiền cọc yêu cầu
                  <span
                    class="lux-deposit-pill"
                    :class="Number(result.data.da_thu || 0) >= Number(result.data.tien_coc_yeu_cau) ? 'is-paid' : 'is-unpaid'"
                  >
                    {{ Number(result.data.da_thu || 0) >= Number(result.data.tien_coc_yeu_cau) ? 'Đã cọc' : 'Chưa cọc' }}
                  </span>
                </span>
                <span class="lux-sum-val fw-semibold text-navy">
                  {{ formatCurrency(result.data.tien_coc_yeu_cau) }}
                </span>
              </div>

              <div v-if="calculateItemsTotal() > 0" class="lux-sum-row">
                <span class="lux-sum-label">Còn lại khi nhận hàng (COD)</span>
                <span class="lux-sum-val fw-bold text-danger">
                  {{ formatCurrency(Math.max(0, calculateGrandTotal())) }}
                </span>
              </div>
            </template>
          </div>
        </section>

        <!-- Thanh toán VietQR (cho đơn hàng chưa thanh toán) -->
        <section
          v-if="result.type === 'order' && result.data.trang_thai_thanh_toan === 0 && result.data.trang_thai !== 3 && result.data.trang_thai !== 4"
          class="lux-vietqr-panel"
        >
          <div class="lux-vietqr-head">
            <div>
              <span class="lux-section-label mb-1">Thanh toán chuyển khoản · VietQR Napas 24/7</span>
              <h5 class="lux-vietqr-title">Quét mã QR đặt cọc trực tiếp</h5>
            </div>
            <span v-if="vietQrData?.amount_formatted" class="lux-vietqr-amount">
              {{ vietQrData.amount_formatted }}
            </span>
          </div>

          <div v-if="vietQrData" class="lux-vietqr-grid">
            <div class="lux-vietqr-img-col">
              <div class="lux-qr-frame">
                <img :src="vietQrData.qr_url" alt="VietQR Napas247">
              </div>
              <a
                :href="buildZaloUrl('Tôi vừa chuyển cọc cho đơn #' + (result.data.ma_don_hang || result.data.ma_yeu_cau))"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-outline-secondary lux-btn-sm mt-3"
              >
                <i class="bi bi-send"></i>
                <span>Gửi biên lai qua Zalo</span>
              </a>
            </div>

            <div class="lux-vietqr-info-col">
              <div class="lux-bank-row">
                <span class="lux-bank-label">Ngân hàng</span>
                <span class="lux-bank-val">{{ vietQrData.bank_name }} ({{ vietQrData.bank_id }})</span>
              </div>
              <div class="lux-bank-row">
                <span class="lux-bank-label">Số tài khoản</span>
                <div class="d-flex align-items-center gap-2">
                  <span class="lux-bank-val font-monospace">{{ vietQrData.account_no }}</span>
                  <button
                    type="button"
                    class="lux-inline-copy"
                    @click="copyText(vietQrData.account_no, 'Đã sao chép số tài khoản')"
                  >
                    Sao chép
                  </button>
                </div>
              </div>
              <div class="lux-bank-row">
                <span class="lux-bank-label">Chủ tài khoản</span>
                <span class="lux-bank-val">{{ vietQrData.account_name }}</span>
              </div>
              <div class="lux-bank-row">
                <span class="lux-bank-label">Số tiền cọc</span>
                <span class="lux-bank-val text-danger fw-bold">{{ vietQrData.amount_formatted }}</span>
              </div>
              <div class="lux-bank-row">
                <span class="lux-bank-label">Nội dung chuyển khoản</span>
                <div class="d-flex align-items-center gap-2">
                  <code class="lux-transfer-code">{{ vietQrData.transfer_content }}</code>
                  <button
                    type="button"
                    class="lux-inline-copy"
                    @click="copyText(vietQrData.transfer_content, 'Đã sao chép nội dung chuyển khoản')"
                  >
                    Sao chép
                  </button>
                </div>
              </div>
              <p class="lux-bank-hint">
                Sau khi nhận biến động số dư, kỹ thuật viên JPM sẽ xác nhận và cập nhật trạng thái đơn hàng ngay.
              </p>
            </div>
          </div>

          <div v-else class="text-center py-4 text-muted small">
            <span class="spinner-border spinner-border-sm text-danger me-2" role="status"></span>
            <span>Đang nạp thông tin mã VietQR...</span>
          </div>
        </section>

        <!-- Footer hỗ trợ -->
        <footer class="lux-doc-footer">
          <span class="lux-footer-hint">Cần hỗ trợ thêm về giao dịch này?</span>
          <a
            :href="buildZaloUrl('Chào JPM Marine, tôi cần hỗ trợ giao dịch #' + (result.data.ma_don_hang || result.data.ma_yeu_cau))"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-outline-secondary lux-btn"
          >
            <i class="bi bi-chat-dots"></i>
            <span>Liên hệ Zalo CSKH</span>
          </a>
        </footer>
      </article>

    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient'
import Swal from 'sweetalert2'
import { createToaster } from '@meforma/vue-toaster'
import { useSiteSettings } from '../../../composables/useSiteSettings'

export default {
  name: 'TraCuuDonHang',
  setup() {
    const { settings } = useSiteSettings()
    const toaster = createToaster({ position: 'top-right', duration: 2200 })
    const buildZaloUrl = (text = '') => {
      const base = (settings.value?.social_zalo || '').trim() || `https://zalo.me/${String(settings.value?.company_hotline || '').replace(/[^0-9]/g, '')}`
      if (!text) return base
      const sep = base.includes('?') ? '&' : '?'
      return `${base}${sep}text=${encodeURIComponent(text)}`
    }
    return { settings, toaster, buildZaloUrl }
  },
  data() {
    return {
      form: {
        code: '',
        phone: ''
      },
      isLoading: false,
      result: null,
      vietQrData: null
    }
  },
  computed: {
    statusText() {
      if (!this.result) return ''
      const r = this.result.data
      if (this.result.type === 'quote') {
        if (r.trang_thai === 0) return 'Chờ báo giá'
        if (r.trang_thai === 1) return 'Đã báo giá'
        if (r.trang_thai === 2) return 'Đang xử lý'
        if (r.trang_thai === 3) return 'Đã hủy / Hết hạn'
        if (r.trang_thai === 4) return 'Hoàn thành'
      } else {
        if (r.trang_thai === 0) return 'Chờ xác nhận'
        if (r.trang_thai === 1) return 'Đang xử lý'
        if (r.trang_thai === 2) return 'Đang giao hàng'
        if (r.trang_thai === 3) return 'Hoàn thành'
        if (r.trang_thai === 4) return 'Đã hủy'
      }
      return 'Đang cập nhật'
    },
    statusTone() {
      if (!this.result) return 'muted'
      const r = this.result.data
      if (this.result.type === 'quote') {
        if (r.trang_thai === 0) return 'pending'
        if (r.trang_thai === 1) return 'quoted'
        if (r.trang_thai === 2) return 'processing'
        if (r.trang_thai === 3) return 'muted'
        if (r.trang_thai === 4) return 'confirmed'
      } else {
        if (r.trang_thai === 0) return 'pending'
        if (r.trang_thai === 1) return 'processing'
        if (r.trang_thai === 2) return 'shipping'
        if (r.trang_thai === 3) return 'confirmed'
        if (r.trang_thai === 4) return 'muted'
      }
      return 'muted'
    }
  },
  methods: {
    getItemPrice(item) {
      return Number(item?.don_gia || item?.gia_ban || 0)
    },
    formatCurrency(value) {
      if (!value) return '0₫'
      return `${new Intl.NumberFormat('vi-VN').format(value)}₫`
    },
    formatDateTime(dateStr) {
      if (!dateStr) return ''
      return new Date(dateStr).toLocaleString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    copyText(text, msg) {
      if (!text) return
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
      }
      this.toaster.success(msg || 'Đã sao chép')
    },
    calculateItemsTotal() {
      if (!this.result || !this.result.data.chi_tiet) return 0
      return this.result.data.chi_tiet.reduce((sum, item) => {
        return sum + (Number(item.so_luong || 1) * this.getItemPrice(item))
      }, 0)
    },
    calculateGrandTotal() {
      if (!this.result) return 0
      const subtotal = this.calculateItemsTotal()
      const shipping = this.result.data.khach_tu_tra_cuoc ? 0 : Number(this.result.data.phi_van_chuyen || 0)
      const paid = Number(this.result.data.da_thu || 0)
      return subtotal + shipping - paid
    },
    async loadVietQR() {
      if (!this.result || this.result.type !== 'order') return
      try {
        const res = await axiosClient.get(`/public/vietqr/don-hang/${this.result.data.ma_don_hang}`)
        const payload = res?.qr_url ? res : res?.data
        if (payload && (res?.success || payload?.success)) {
          this.vietQrData = payload
        }
      } catch (err) {
        console.error('Không thể tải mã VietQR:', err)
      }
    },
    async searchTransaction() {
      if (!this.form.code || !this.form.phone) return
      this.isLoading = true
      this.result = null
      this.vietQrData = null
      try {
        const res = await axiosClient.post('/public/tra-cuu-nhanh', this.form)
        const payload = res?.type ? res : res?.data
        if (payload && (res?.success || payload?.success)) {
          this.result = payload
          if (this.result.type === 'order' && this.result.data.trang_thai_thanh_toan === 0) {
            await this.loadVietQR()
          }
          this.$nextTick(() => {
            this.$refs.resultSection?.scrollIntoView({ behavior: 'smooth', block: 'start' })
          })
        }
      } catch (error) {
        if (error.response && error.response.status === 404) {
          Swal.fire({
            icon: 'error',
            title: 'Không tìm thấy giao dịch',
            text: 'Mã giao dịch hoặc số điện thoại chưa chính xác. Vui lòng kiểm tra lại.',
            confirmButtonText: 'Đã hiểu'
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Không thể tra cứu lúc này',
            text: 'Hệ thống đang bận hoặc kết nối gián đoạn. Vui lòng thử lại sau.',
            confirmButtonText: 'Đóng'
          })
        }
      } finally {
        this.isLoading = false
      }
    }
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search)
    const code = urlParams.get('code')
    const phone = urlParams.get('phone')
    if (code) this.form.code = code
    if (phone) this.form.phone = phone
    if (code && phone) {
      this.searchTransaction()
    }
  }
}
</script>

<style scoped>
/* Tinh chỉnh cục bộ riêng cho form Tra cứu nhanh (kế thừa .lux-* dùng chung từ main.css) */
.lux-lookup-page {
  min-height: calc(100vh - 80px);
  background: #f5f6fa;
  padding: 2.5rem 0 4.5rem;
  font-family: var(--default-font, 'Inter', sans-serif);
}

.lux-lookup-container {
  max-width: 860px;
}

.text-navy {
  color: #0A1128;
}

.lux-search-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  padding: 34px 36px;
  box-shadow: 0 4px 24px -6px rgba(10, 17, 40, 0.06);
}

.lux-search-head {
  margin-bottom: 24px;
}

.lux-search-title {
  margin: 0 0 8px;
  font-size: 1.5rem;
  font-weight: 700;
  color: #0A1128;
  letter-spacing: -0.02em;
}

.lux-search-sub {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
  line-height: 1.55;
}

.lux-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 18px;
}

.lux-label {
  display: block;
  font-size: 0.82rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}

.lux-input {
  width: 100%;
  height: var(--jpm-control-height, 37px);
  padding: 0 12px;
  border-radius: var(--jpm-radius-control, 7px);
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #0A1128;
  font-size: 0.96rem;
  transition: border-color 0.18s ease, box-shadow 0.18s ease;
}

.lux-input::placeholder {
  color: #94a3b8;
  font-family: var(--default-font, 'Inter', sans-serif);
}

.lux-input:focus {
  outline: none;
  border-color: #0A1128;
  box-shadow: 0 0 0 3px rgba(10, 17, 40, 0.08);
}

.lux-submit-btn {
  width: 100%;
  height: var(--jpm-control-height, 37px);
  border-radius: var(--jpm-radius-control, 7px) !important;
  background: var(--jpm-color-red-marine, #F82249) !important;
  border: 1px solid var(--jpm-color-red-marine, #F82249) !important;
  color: #ffffff !important;
  font-size: 0.92rem !important;
  font-weight: 600 !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: none !important;
  transition: all 0.2s ease;
}

.lux-submit-btn:hover:not(:disabled) {
  background: var(--jpm-color-red-hover, #D9163A) !important;
  border-color: var(--jpm-color-red-hover, #D9163A) !important;
  transform: translateY(-1px);
}

.lux-doc-code-heading {
  margin: 0 0 4px;
  font-family: 'Roboto Mono', monospace;
  font-size: 1.35rem;
  font-weight: 700;
  color: #0A1128;
}

.lux-doc-updated {
  font-size: 0.82rem;
  color: #64748b;
}

@media (max-width: 767px) {
  .lux-search-card {
    padding: 24px 20px;
  }

  .lux-form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
