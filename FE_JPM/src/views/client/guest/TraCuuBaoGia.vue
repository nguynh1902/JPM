<template>
  <div class="lux-quote-page inner-page-section">
    <div class="container lux-quote-container">

      <!-- Loading Skeleton -->
      <div v-if="loading" class="lux-doc-sheet lux-loading-sheet placeholder-glow">
        <div class="d-flex justify-content-between align-items-start mb-4 pb-4 border-bottom">
          <div style="width: 45%;">
            <span class="placeholder col-4 rounded mb-2 d-block"></span>
            <span class="placeholder col-8 rounded mb-2 d-block" style="height: 26px;"></span>
            <span class="placeholder col-5 rounded d-block"></span>
          </div>
          <span class="placeholder col-2 rounded" style="height: 30px;"></span>
        </div>
        <div class="row g-3 mb-4">
          <div class="col-md-6"><span class="placeholder col-12 rounded" style="height: 76px;"></span></div>
          <div class="col-md-6"><span class="placeholder col-12 rounded" style="height: 76px;"></span></div>
        </div>
        <span class="placeholder col-12 rounded mb-3" style="height: 150px;"></span>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="lux-doc-sheet lux-error-sheet text-center">
        <div class="lux-error-icon">
          <i class="bi bi-file-earmark-x"></i>
        </div>
        <h4 class="lux-error-title">Không tìm thấy báo giá</h4>
        <p class="lux-error-desc">{{ error }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <router-link to="/tra-cuu-don-hang" class="btn btn-outline-secondary lux-btn">
            Tra cứu giao dịch
          </router-link>
          <router-link to="/" class="btn btn-danger lux-btn">
            Về trang chủ
          </router-link>
        </div>
      </div>

      <!-- Main Quotation Document -->
      <template v-else-if="quote">
        <!-- Toolbar mảnh phía trên (Ẩn khi in) -->
        <div class="lux-doc-toolbar no-print">
          <button type="button" class="lux-back-btn" @click="goBack">
            <i class="bi bi-arrow-left"></i>
            <span>{{ isLoggedIn ? 'Quay lại lịch sử đơn' : 'Quay lại' }}</span>
          </button>

          <div class="d-flex align-items-center gap-2">
            <button
              type="button"
              class="lux-tool-btn"
              @click="copyText(quote.ma_yeu_cau, 'Đã sao chép mã yêu cầu')"
            >
              <i class="bi bi-copy"></i>
              <span>Sao chép mã</span>
            </button>
            <button type="button" class="lux-tool-btn" @click="printDocument">
              <i class="bi bi-printer"></i>
              <span>In báo giá</span>
            </button>
          </div>
        </div>

        <!-- Chứng từ Báo giá -->
        <article class="lux-doc-sheet">
          <!-- 1. Header Chứng từ -->
          <header class="lux-doc-header">
            <div class="lux-doc-brand">
              <span class="lux-doc-overline">{{ settings?.company_name || 'JPM Marine' }} · Chứng từ báo giá</span>
              <h1 class="lux-doc-title">Bảng báo giá phụ tùng</h1>
              <div class="lux-doc-code-row">
                <span class="lux-doc-code">#{{ quote.ma_yeu_cau }}</span>
                <span class="lux-meta-dot" aria-hidden="true">·</span>
                <span class="lux-doc-date">Ngày lập {{ formatDate(quote.created_at) }}</span>
              </div>
            </div>

            <div class="lux-doc-status-col">
              <span class="lux-status-indicator" :class="'status-' + statusKey">
                <span class="status-dot"></span>
                <span>{{ statusLabel }}</span>
              </span>
              <span v-if="quote.trang_thai === 1 && !quote.is_expired && remainingSeconds > 0" class="lux-countdown-chip">
                <i class="bi bi-clock"></i>
                <span>Còn {{ formattedRemainingTime }}</span>
              </span>
            </div>
          </header>

          <!-- 2. Thông tin khách hàng & giao nhận -->
          <section class="lux-info-grid">
            <div class="lux-info-block">
              <span class="lux-section-label">Thông tin khách hàng</span>
              <div class="lux-info-primary">{{ quote.khach_hang_ten }}</div>
              <div class="lux-info-sub">
                <span>{{ quote.khach_hang_sdt }}</span>
                <template v-if="quote.khach_hang_email">
                  <span class="lux-meta-dot">·</span>
                  <span>{{ quote.khach_hang_email }}</span>
                </template>
              </div>
            </div>

            <div class="lux-info-block">
              <span class="lux-section-label">Địa chỉ nhận hàng</span>
              <div v-if="quote.dia_chi_giao_hang" class="lux-info-address">
                {{ quote.dia_chi_giao_hang }}
              </div>
              <div v-else class="lux-info-muted fst-italic">
                Chưa cập nhật địa chỉ giao hàng
              </div>
            </div>
          </section>

          <!-- 3. Dải thông báo tiến độ tinh tế (Thay thế hoàn toàn cục alert xanh lơ chói) -->
          <section v-if="quote.trang_thai === 0" class="lux-callout callout-pending">
            <div class="lux-callout-icon">
              <i class="bi bi-clock-history"></i>
            </div>
            <div class="lux-callout-body">
              <strong>Đang kiểm tra &amp; lập báo giá</strong>
              <p>
                Kỹ thuật viên JPM Marine đang đối soát mã OEM, kiểm tra tồn kho và tính cước vận chuyển. Báo giá chính thức sẽ được gửi qua Zalo và cập nhật trực tiếp tại trang này.
              </p>
            </div>
          </section>

          <section v-else-if="quote.trang_thai === 1 && !quote.is_expired" class="lux-callout callout-active">
            <div class="lux-callout-icon">
              <i class="bi bi-shield-check"></i>
            </div>
            <div class="lux-callout-body">
              <strong>Báo giá có hiệu lực trong 72 giờ</strong>
              <p>
                Đơn giá phụ tùng và tỷ giá JPY được bảo lưu đến <strong>{{ formatDateTime(quote.expires_at) }}</strong>. Vui lòng kiểm tra chi tiết bên dưới và xác nhận để tiến hành xử lý đơn.
              </p>
            </div>
          </section>

          <section v-else-if="quote.trang_thai === 3 || quote.is_expired" class="lux-callout callout-expired">
            <div class="lux-callout-icon">
              <i class="bi bi-exclamation-circle"></i>
            </div>
            <div class="lux-callout-body">
              <strong>Báo giá đã quá hạn hiệu lực</strong>
              <p>
                Do tỷ giá và cước vận chuyển quốc tế thay đổi theo từng đợt, bảng giá này không còn hiệu lực bảo lưu. Bạn có thể yêu cầu kỹ thuật viên JPM cập nhật lại bảng giá mới nhất.
              </p>
            </div>
          </section>

          <section v-else-if="quote.trang_thai === 2" class="lux-callout callout-confirmed">
            <div class="lux-callout-icon">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="lux-callout-body">
              <strong>Đơn hàng đã được xác nhận</strong>
              <p>
                Cảm ơn bạn đã chốt báo giá. Bộ phận kỹ thuật và kho vận JPM Marine đang tiến hành chuẩn bị phụ tùng và cập nhật tiến độ giao hàng qua Zalo.
              </p>
            </div>
          </section>

          <!-- 4. Bảng chi tiết phụ tùng (Không kẻ dọc, tinh gọn chuẩn chứng từ) -->
          <section class="lux-table-section">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="lux-section-label mb-0">Chi tiết phụ tùng ({{ (quote.chi_tiet || []).length }})</span>
            </div>

            <div class="table-responsive">
              <table class="lux-parts-table">
                <thead>
                  <tr>
                    <th class="col-stt">#</th>
                    <th>Phụ tùng &amp; mã OEM</th>
                    <th class="text-center col-qty">SL</th>
                    <th class="text-end col-price">Đơn giá</th>
                    <th class="text-end col-total">Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in quote.chi_tiet" :key="item.id || idx">
                    <td class="col-stt text-muted">{{ idx + 1 }}</td>
                    <td>
                      <div class="lux-part-name">{{ item.ten_phu_tung }}</div>
                      <div class="lux-part-oem">
                        Mã OEM: <code>{{ item.ma_phu_tung || 'N/A' }}</code>
                      </div>
                    </td>
                    <td class="text-center col-qty fw-semibold">{{ item.so_luong }}</td>
                    <td class="text-end col-price">
                      <span v-if="getItemUnitPrice(item) > 0" class="lux-num">
                        {{ formatCurrency(getItemUnitPrice(item)) }}
                      </span>
                      <span v-else class="lux-pending-txt">Chờ báo giá</span>
                    </td>
                    <td class="text-end col-total">
                      <span v-if="getItemUnitPrice(item) > 0" class="lux-num fw-semibold text-navy">
                        {{ formatCurrency(item.so_luong * getItemUnitPrice(item)) }}
                      </span>
                      <span v-else class="text-muted">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- 5. Tổng kết tài chính tách riêng căn phải -->
          <section class="lux-summary-section">
            <div class="lux-summary-box">
              <div class="lux-sum-row">
                <span class="lux-sum-label">Tạm tính tiền hàng</span>
                <span class="lux-sum-val">
                  <template v-if="computedSubtotal > 0">
                    {{ formatCurrency(computedSubtotal) }}
                    <span v-if="unquotedCount > 0" class="lux-sum-note">(+{{ unquotedCount }} món chờ giá)</span>
                  </template>
                  <template v-else>
                    <span class="lux-pending-txt">Chờ định giá</span>
                  </template>
                </span>
              </div>

              <div class="lux-sum-row">
                <span class="lux-sum-label">Phí vận chuyển</span>
                <span class="lux-sum-val">
                  <template v-if="quote.khach_tu_tra_cuoc">
                    Khách tự trả cước chành xe
                  </template>
                  <template v-else-if="Number(quote.phi_van_chuyen) > 0">
                    {{ formatCurrency(quote.phi_van_chuyen) }}
                  </template>
                  <template v-else-if="quote.trang_thai === 0">
                    <span class="lux-pending-txt">Chờ xác nhận</span>
                  </template>
                  <template v-else>
                    <span class="text-success fw-medium">Miễn phí</span>
                  </template>
                </span>
              </div>

              <div class="lux-sum-row lux-sum-grand">
                <span class="lux-sum-label">Tổng giá trị đơn hàng</span>
                <span class="lux-sum-val">
                  <template v-if="grandTotal !== null">
                    <strong class="lux-grand-num">{{ formatCurrency(grandTotal) }}</strong>
                  </template>
                  <template v-else>
                    <span class="lux-pending-txt fw-normal">Chờ hoàn tất báo giá</span>
                  </template>
                </span>
              </div>

              <template v-if="Number(quote.tien_coc_yeu_cau) > 0">
                <div class="lux-sum-row lux-sum-deposit">
                  <span class="lux-sum-label">
                    Tiền cọc yêu cầu
                    <span
                      class="lux-deposit-pill"
                      :class="quote.trang_thai === 2 ? 'is-paid' : 'is-unpaid'"
                    >
                      {{ quote.trang_thai === 2 ? 'Đã nhận cọc' : 'Chưa đặt cọc' }}
                    </span>
                  </span>
                  <span class="lux-sum-val fw-semibold text-navy">
                    {{ formatCurrency(quote.tien_coc_yeu_cau) }}
                  </span>
                </div>

                <div v-if="grandTotal !== null" class="lux-sum-row lux-sum-cod">
                  <span class="lux-sum-label">Còn lại khi nhận hàng (COD)</span>
                  <span class="lux-sum-val fw-bold text-danger">
                    {{ formatCurrency(remainingCod) }}
                  </span>
                </div>
              </template>
            </div>
          </section>

          <!-- 6. Ghi chú từ Kỹ thuật viên JPM (nếu có) -->
          <section v-if="quote.ghi_chu_zalo" class="lux-note-box">
            <span class="lux-section-label">Ghi chú từ kỹ thuật viên JPM</span>
            <p class="lux-note-content">{{ quote.ghi_chu_zalo }}</p>
          </section>

          <!-- 7. Khu vực VietQR Đặt cọc (chỉ hiển thị khi có yêu cầu cọc) -->
          <section
            v-if="(quote.trang_thai === 1 || quote.trang_thai === 2) && Number(quote.tien_coc_yeu_cau) > 0 && !quote.is_expired"
            class="lux-vietqr-panel"
          >
            <div class="lux-vietqr-head">
              <div>
                <span class="lux-section-label mb-1">Chuyển khoản đặt cọc · VietQR Napas 24/7</span>
                <h5 class="lux-vietqr-title">Quét mã QR để giữ hàng &amp; lên đơn</h5>
              </div>
              <span class="lux-vietqr-amount">{{ formatCurrency(quote.tien_coc_yeu_cau) }}</span>
            </div>

            <div v-if="vietQrData" class="lux-vietqr-grid">
              <div class="lux-vietqr-img-col">
                <div class="lux-qr-frame">
                  <img :src="vietQrData.qr_url" alt="VietQR Napas247">
                </div>
                <button
                  type="button"
                  class="btn btn-outline-secondary lux-btn-sm mt-3 no-print"
                  @click="openZaloReceipt"
                >
                  <i class="bi bi-send"></i>
                  <span>Gửi biên lai qua Zalo</span>
                </button>
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
                      class="lux-inline-copy no-print"
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
                      class="lux-inline-copy no-print"
                      @click="copyText(vietQrData.transfer_content, 'Đã sao chép nội dung chuyển khoản')"
                    >
                      Sao chép
                    </button>
                  </div>
                </div>
                <p class="lux-bank-hint">
                  Sau khi chuyển khoản, kỹ thuật viên JPM sẽ đối soát ngân hàng và xác nhận đơn ngay. Bạn có thể gửi biên lai qua Zalo để ưu tiên xử lý nhanh.
                </p>
              </div>
            </div>
          </section>

          <!-- 8. Chân chứng từ & Hành động chính (Ẩn khi in) -->
          <footer class="lux-doc-footer no-print">
            <!-- Trạng thái 0: Chờ báo giá -->
            <template v-if="quote.trang_thai === 0">
              <span class="lux-footer-hint">Cần tư vấn gấp về mã phụ tùng hoặc thời gian giao?</span>
              <a
                :href="buildZaloWithMsg('Chào JPM Marine, tôi cần hỗ trợ về yêu cầu báo giá #' + quote.ma_yeu_cau)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-danger lux-btn"
              >
                <i class="bi bi-chat-dots"></i>
                <span>Nhắn Zalo kỹ thuật</span>
              </a>
            </template>

            <!-- Trạng thái 1: Đã báo giá còn hiệu lực -->
            <template v-else-if="quote.trang_thai === 1 && !quote.is_expired">
              <span class="lux-footer-hint">Bằng việc chốt đơn, bạn xác nhận thông tin phụ tùng và chi phí trên.</span>
              <div class="lux-footer-btns">
                <button
                  type="button"
                  class="btn btn-outline-secondary lux-btn"
                  :disabled="isSubmitting"
                  @click="handleAction('reject')"
                >
                  Từ chối báo giá
                </button>
                <button
                  type="button"
                  class="btn btn-danger lux-btn"
                  :disabled="isSubmitting"
                  @click="handleAction('accept')"
                >
                  <i class="bi bi-check2-circle"></i>
                  <span>Đồng ý chốt đơn</span>
                </button>
              </div>
            </template>

            <!-- Trạng thái 2: Đã chốt đơn -->
            <template v-else-if="quote.trang_thai === 2">
              <span class="lux-footer-hint">Đơn hàng đang trong quy trình xử lý của JPM Marine.</span>
              <a
                :href="buildZaloWithMsg('Chào JPM Marine, tôi muốn hỏi tiến độ đơn hàng #' + quote.ma_yeu_cau)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-outline-secondary lux-btn"
              >
                <i class="bi bi-chat-dots"></i>
                <span>Hỏi tiến độ qua Zalo</span>
              </a>
            </template>

            <!-- Trạng thái 3 / Hết hạn -->
            <template v-else>
              <span class="lux-footer-hint">Cần cập nhật lại đơn giá mới nhất cho danh sách phụ tùng này?</span>
              <div class="lux-footer-btns">
                <a
                  :href="buildZaloWithMsg('Chào JPM Marine, tôi muốn báo giá lại yêu cầu #' + quote.ma_yeu_cau)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-outline-secondary lux-btn"
                >
                  <i class="bi bi-chat-dots"></i>
                  <span>Liên hệ kỹ thuật</span>
                </a>
                <button
                  type="button"
                  class="btn btn-danger lux-btn"
                  :disabled="isSubmitting"
                  @click="handleAction('re_quote')"
                >
                  <i class="bi bi-arrow-repeat"></i>
                  <span>Yêu cầu báo giá lại</span>
                </button>
              </div>
            </template>
          </footer>
        </article>
      </template>

    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient'
import Swal from 'sweetalert2'
import { createToaster } from '@meforma/vue-toaster'
import { useSiteSettings } from '../../../composables/useSiteSettings'
import { useAuthStore } from '../../../stores/authStore'

export default {
  name: 'TraCuuBaoGia',
  setup() {
    const { settings } = useSiteSettings()
    const authStore = useAuthStore()
    const toaster = createToaster({ position: 'top-right', duration: 2200 })
    return { settings, authStore, toaster }
  },
  data() {
    return {
      token: this.$route.params.token,
      quote: null,
      loading: true,
      error: null,
      isSubmitting: false,
      vietQrData: null,
      remainingSeconds: 0,
      countdownTimer: null
    }
  },
  computed: {
    isLoggedIn() {
      return Boolean(this.authStore?.token)
    },
    zaloBaseUrl() {
      const configured = (this.settings?.social_zalo || '').trim()
      if (configured) return configured
      const digits = String(this.settings?.company_hotline || '').replace(/[^0-9]/g, '')
      return digits ? `https://zalo.me/${digits}` : '/ho-tro'
    },
    statusKey() {
      if (!this.quote) return 'pending'
      if (this.quote.trang_thai === 0) return 'pending'
      if (this.quote.trang_thai === 1 && !this.quote.is_expired) return 'quoted'
      if (this.quote.trang_thai === 2) return 'confirmed'
      return 'expired'
    },
    statusLabel() {
      const map = {
        pending: 'Chờ báo giá',
        quoted: 'Đã báo giá',
        confirmed: 'Đã chốt đơn',
        expired: 'Hết hạn / Đã hủy'
      }
      return map[this.statusKey] || 'Chờ báo giá'
    },
    unquotedCount() {
      if (!this.quote?.chi_tiet) return 0
      return this.quote.chi_tiet.filter(item => this.getItemUnitPrice(item) <= 0).length
    },
    computedSubtotal() {
      if (!this.quote) return 0
      const items = this.quote.chi_tiet || []
      const sum = items.reduce((acc, item) => acc + (Number(item.so_luong || 1) * this.getItemUnitPrice(item)), 0)
      if (sum > 0) return sum
      return Number(this.quote.tong_tien || 0)
    },
    grandTotal() {
      if (!this.quote) return null
      if (this.quote.trang_thai === 0 && this.unquotedCount > 0) return null
      if (this.computedSubtotal <= 0 && this.quote.trang_thai === 0) return null
      const ship = this.quote.khach_tu_tra_cuoc ? 0 : Number(this.quote.phi_van_chuyen || 0)
      return this.computedSubtotal + ship
    },
    remainingCod() {
      if (this.grandTotal === null) return 0
      const paidDeposit = this.quote.trang_thai === 2 ? Number(this.quote.tien_coc_yeu_cau || 0) : 0
      return Math.max(0, this.grandTotal - paidDeposit)
    },
    formattedRemainingTime() {
      if (this.remainingSeconds <= 0) return '00:00:00'
      const hours = Math.floor(this.remainingSeconds / 3600)
      const minutes = Math.floor((this.remainingSeconds % 3600) / 60)
      const seconds = this.remainingSeconds % 60
      if (hours >= 24) {
        const days = Math.floor(hours / 24)
        const remHours = hours % 24
        return `${days} ngày ${remHours} giờ`
      }
      return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
    }
  },
  async mounted() {
    await this.fetchQuote()
  },
  beforeUnmount() {
    this.stopCountdown()
  },
  methods: {
    getItemUnitPrice(item) {
      if (!item) return 0
      return Number(item.don_gia || item.gia_ban || 0)
    },
    formatCurrency(value) {
      if (!value) return '0₫'
      return `${new Intl.NumberFormat('vi-VN').format(value)}₫`
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    formatDateTime(dateStr) {
      if (!dateStr) return ''
      const d = new Date(dateStr)
      return d.toLocaleString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    buildZaloWithMsg(msg) {
      if (!msg) return this.zaloBaseUrl
      const sep = this.zaloBaseUrl.includes('?') ? '&' : '?'
      return `${this.zaloBaseUrl}${sep}text=${encodeURIComponent(msg)}`
    },
    goBack() {
      if (this.isLoggedIn) {
        this.$router.push('/lich-su-don-hang')
      } else if (window.history.length > 1) {
        this.$router.back()
      } else {
        this.$router.push('/')
      }
    },
    printDocument() {
      window.print()
    },
    copyText(text, msg) {
      if (!text) return
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
      }
      this.toaster.success(msg || 'Đã sao chép')
    },
    openZaloReceipt() {
      if (!this.quote) return
      const depositText = this.formatCurrency(this.quote.tien_coc_yeu_cau)
      const msg = `Chào JPM Marine, tôi vừa chuyển cọc ${depositText} cho yêu cầu báo giá #${this.quote.ma_yeu_cau}. Nhờ kỹ thuật viên kiểm tra thông báo ngân hàng và xác nhận đơn giúp tôi.`
      window.open(this.buildZaloWithMsg(msg), '_blank', 'noopener,noreferrer')
    },
    startCountdown() {
      this.stopCountdown()
      if (this.remainingSeconds <= 0) return
      this.countdownTimer = setInterval(() => {
        if (this.remainingSeconds > 0) {
          this.remainingSeconds--
        } else {
          this.stopCountdown()
          if (this.quote) {
            this.quote.is_expired = true
          }
        }
      }, 1000)
    },
    stopCountdown() {
      if (this.countdownTimer) {
        clearInterval(this.countdownTimer)
        this.countdownTimer = null
      }
    },
    async loadVietQR() {
      if (!this.quote || !this.quote.ma_yeu_cau) return
      try {
        const res = await axiosClient.get(`/public/vietqr/bao-gia/${this.quote.ma_yeu_cau}`)
        const payload = res?.qr_url ? res : res?.data
        if (payload && (res.success || payload.success)) {
          this.vietQrData = payload
        }
      } catch (err) {
        console.error('Không thể nạp mã VietQR cọc:', err)
      }
    },
    async fetchQuote() {
      this.loading = true
      try {
        const res = await axiosClient.get('/public/yeu-cau-bao-gia/' + this.token)
        const ok = res?.success ?? res?.data?.success
        const quoteData = res?.data?.data ?? res?.data
        if (ok && quoteData) {
          this.quote = quoteData
          if (this.quote.remaining_seconds && this.quote.remaining_seconds > 0) {
            this.remainingSeconds = this.quote.remaining_seconds
            this.startCountdown()
          } else if (this.quote.is_expired) {
            this.remainingSeconds = 0
            this.stopCountdown()
          }
          if (quoteData.vietqr_data) {
            this.vietQrData = quoteData.vietqr_data
          } else if ((this.quote.trang_thai === 1 || this.quote.trang_thai === 2) && this.quote.tien_coc_yeu_cau > 0 && !this.quote.is_expired) {
            await this.loadVietQR()
          }
        } else {
          this.error = 'Không tìm thấy dữ liệu báo giá tương ứng với đường dẫn này.'
        }
      } catch (err) {
        this.error = 'Đường dẫn báo giá không hợp lệ hoặc đã hết hạn.'
      } finally {
        this.loading = false
      }
    },
    async handleAction(actionType) {
      let confirmTitle = 'Xác nhận thao tác'
      let confirmMsg = ''
      let confirmBtnText = 'Xác nhận'
      let confirmBtnColor = '#f82249'
      let deliveryAddress = this.quote.dia_chi_giao_hang || ''

      if (actionType === 'accept') {
        confirmTitle = 'Xác nhận chốt đơn?'
        confirmMsg = 'Bạn đồng ý với đơn giá và chi phí trong bảng báo giá này?'
        confirmBtnText = 'Đồng ý chốt đơn'
        if (!deliveryAddress) {
          const { value: inputAddress } = await Swal.fire({
            title: 'Địa chỉ giao hàng',
            input: 'textarea',
            inputLabel: 'Vui lòng cung cấp địa chỉ nhận hàng (bến tàu, chành xe hoặc số nhà):',
            inputPlaceholder: 'Ví dụ: Bến tàu Rạch Giá, Kiên Giang hoặc Số 123 đường ABC...',
            showCancelButton: true,
            confirmButtonColor: '#f82249',
            confirmButtonText: 'Tiếp tục',
            cancelButtonText: 'Hủy',
            inputValidator: (value) => {
              if (!value || !value.trim()) {
                return 'Vui lòng nhập địa chỉ nhận hàng để JPM chuẩn bị giao nhận.'
              }
            }
          })
          if (!inputAddress) return
          deliveryAddress = inputAddress.trim()
        }
      } else if (actionType === 'reject') {
        confirmTitle = 'Từ chối báo giá?'
        confirmMsg = 'Bạn chắc chắn muốn từ chối bảng báo giá này?'
        confirmBtnText = 'Từ chối'
      } else if (actionType === 're_quote') {
        confirmTitle = 'Yêu cầu báo giá lại?'
        confirmMsg = 'Gửi yêu cầu đến kỹ thuật viên JPM để cập nhật lại bảng giá mới nhất?'
        confirmBtnText = 'Gửi yêu cầu'
      }

      const result = await Swal.fire({
        title: confirmTitle,
        text: confirmMsg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'Hủy'
      })

      if (result.isConfirmed) {
        this.isSubmitting = true
        try {
          const res = await axiosClient.post('/public/yeu-cau-bao-gia/' + this.token + '/action', {
            action: actionType,
            dia_chi_giao_hang: deliveryAddress
          })
          const message = res?.message ?? res?.data?.message
          if (res?.success || res?.data?.success) {
            Swal.fire({ icon: 'success', title: 'Đã cập nhật', text: message })
            await this.fetchQuote()
          } else {
            Swal.fire({ icon: 'warning', title: 'Thông báo', text: message })
            await this.fetchQuote()
          }
        } catch (err) {
          const msg = err.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại sau.'
          Swal.fire({ icon: 'error', title: 'Không thể thực hiện', text: msg })
          await this.fetchQuote()
        } finally {
          this.isSubmitting = false
        }
      }
    }
  }
}
</script>

<style scoped>
/* Tinh chỉnh cục bộ riêng cho trang Chứng từ Báo giá (kế thừa .lux-* dùng chung từ main.css) */
.lux-quote-page {
  min-height: calc(100vh - 80px);
  background: #f5f6fa;
  padding: 2.25rem 0 4.5rem;
  font-family: var(--default-font, 'Inter', sans-serif);
}

.lux-quote-container {
  max-width: 880px;
}

.text-navy {
  color: #0A1128;
}

/* Toolbar phía trên */
.lux-doc-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.lux-back-btn {
  border: none;
  background: transparent;
  color: #475569;
  font-size: 0.875rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 2px;
  cursor: pointer;
  transition: color 0.18s ease;
}

.lux-back-btn:hover {
  color: #0A1128;
}

.lux-tool-btn {
  height: 36px;
  padding: 0 14px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #334155;
  font-size: 0.825rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
}

.lux-tool-btn:hover {
  border-color: #cbd5e1;
  color: #0A1128;
  background: #f8fafc;
}

.lux-loading-sheet {
  padding: 36px 40px;
}

.lux-error-sheet {
  padding: 56px 32px;
}

.lux-error-icon {
  width: 54px;
  height: 54px;
  margin: 0 auto 16px;
  border-radius: var(--jpm-radius-circle, 50%);
  background: #fef2f2;
  color: var(--jpm-color-red-marine, #F82249);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.lux-error-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #0A1128;
  margin-bottom: 8px;
}

.lux-error-desc {
  font-size: 0.9rem;
  color: #64748b;
  max-width: 400px;
  margin: 0 auto 24px;
}

.lux-doc-overline {
  display: block;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
  margin-bottom: 6px;
}

.lux-doc-title {
  margin: 0 0 8px;
  font-size: 1.45rem;
  font-weight: 700;
  color: #0A1128;
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.lux-doc-code-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  font-size: 0.875rem;
}

.lux-doc-code {
  font-family: 'Roboto Mono', monospace;
  font-weight: 700;
  color: #0A1128;
}

.lux-doc-date {
  color: #64748b;
}

.lux-doc-status-col {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
  flex-shrink: 0;
}

.lux-countdown-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-family: 'Roboto Mono', monospace;
  font-size: 0.78rem;
  font-weight: 600;
  color: #b91c1c;
}

.lux-note-box {
  padding: 16px 18px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: var(--jpm-radius-control, 8px);
  margin-bottom: 24px;
}

.lux-note-content {
  margin: 0;
  font-size: 0.875rem;
  color: #334155;
  line-height: 1.6;
  white-space: pre-wrap;
}

@media (max-width: 767px) {
  .lux-doc-status-col {
    align-items: flex-start;
  }
}

@media print {
  .lux-quote-page {
    background: #ffffff !important;
    padding: 0 !important;
  }
}
</style>
