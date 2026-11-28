<template>
  <div class="exdoc-page inner-page-section">
    <div class="container exdoc-container">
      <!-- Loading Skeleton -->
      <div v-if="loading" class="exdoc-sheet placeholder-glow">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
          <div class="d-flex align-items-center gap-3">
            <span class="placeholder rounded-circle" style="width: 76px; height: 76px;"></span>
            <div style="width: 260px;">
              <span class="placeholder col-10 rounded mb-2 d-block"></span>
              <span class="placeholder col-8 rounded mb-1 d-block"></span>
              <span class="placeholder col-7 rounded d-block"></span>
            </div>
          </div>
          <span class="placeholder col-2 rounded" style="height: 28px;"></span>
        </div>
        <span class="placeholder col-4 rounded mx-auto mb-4 d-block" style="height: 30px;"></span>
        <span class="placeholder col-12 rounded mb-4" style="height: 80px;"></span>
        <span class="placeholder col-12 rounded mb-4" style="height: 220px;"></span>
      </div>

      <!-- Prompt SĐT xác thực nếu link thiếu ?phone=... -->
      <div v-else-if="needsPhonePrompt" class="exdoc-sheet text-center py-5">
        <div class="exdoc-lock-icon">
          <i class="bi bi-shield-lock"></i>
        </div>
        <h4 class="fw-bold text-navy mb-2">Xác nhận số điện thoại nhận hàng</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width: 440px; font-size: 0.92rem;">
          Vui lòng nhập số điện thoại đặt hàng để xem chi tiết hóa đơn &amp; vận đơn <strong class="font-monospace text-navy">#{{ orderCode }}</strong>.
        </p>
        <form @submit.prevent="submitPhoneVerification" class="mx-auto d-flex flex-column flex-sm-row gap-2 justify-content-center" style="max-width: 400px;">
          <input
            v-model.trim="phone"
            type="tel"
            class="form-control"
            placeholder="Nhập số điện thoại đặt hàng..."
            required
          >
          <button type="submit" class="btn exdoc-btn exdoc-btn-primary text-nowrap">
            <i class="bi bi-unlock"></i>
            <span>Xem đơn hàng</span>
          </button>
        </form>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="exdoc-sheet text-center py-5">
        <div class="exdoc-error-icon">
          <i class="bi bi-file-earmark-x"></i>
        </div>
        <h4 class="fw-bold text-navy mb-2">Không tìm thấy hóa đơn đơn hàng</h4>
        <p class="text-muted mb-4 mx-auto" style="max-width: 420px; font-size: 0.92rem;">{{ error }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <router-link to="/tra-cuu-don-hang" class="btn exdoc-btn exdoc-btn-outline">
            <i class="bi bi-search"></i>
            <span>Tra cứu giao dịch</span>
          </router-link>
          <router-link to="/" class="btn exdoc-btn exdoc-btn-primary">
            <i class="bi bi-house"></i>
            <span>Về trang chủ</span>
          </router-link>
        </div>
      </div>

      <!-- Main Client Invoice / Order Sheet -->
      <template v-else-if="order">
        <!-- Toolbar mảnh phía trên (Ẩn khi in) -->
        <div class="exdoc-toolbar no-print">
          <button type="button" class="exdoc-back-btn" @click="goBack">
            <i class="bi bi-arrow-left"></i>
            <span>{{ isLoggedIn ? 'Quay lại lịch sử đơn' : 'Quay lại' }}</span>
          </button>

          <button type="button" class="exdoc-tool-btn" @click="printDocument">
            <i class="bi bi-printer"></i>
            <span>In hóa đơn</span>
          </button>
        </div>

        <!-- Tờ Hóa Đơn chuẩn bố cục chứng từ JPM -->
        <article class="exdoc-sheet">
          <!-- 1. TOP HEADER -->
          <header class="exdoc-top-header">
            <div class="exdoc-company-lockup">
              <div class="exdoc-logo-circle" aria-label="JPM Logo">
                <svg width="46" height="29" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.5 1 L14.5 19 L2 19 Z" fill="#FFFFFF" />
                  <path d="M17.5 1 L30 19 L17.5 19 Z" fill="#FFFFFF" />
                </svg>
                <span class="exdoc-logo-text">J P M</span>
              </div>

              <div class="exdoc-company-info">
                <div class="exdoc-company-title">
                  {{ settings?.company_name ? `${settings.company_name.toUpperCase()} — ` : '' }}LINH KIỆN CANO - TÀU THUYỀN NHẬT BẢN
                </div>
                <div class="exdoc-company-line">
                  <span class="exdoc-lbl">Website:</span>
                  <span>{{ siteHost }}</span>
                  <span class="exdoc-dot-sep">·</span>
                  <span class="exdoc-lbl">Email:</span>
                  <span>{{ settings?.company_email || 'tauthuyennhatbanjpvn@gmail.com' }}</span>
                </div>
                <div class="exdoc-company-line">
                  <span class="exdoc-lbl">ĐT/Zalo:</span>
                  <strong>{{ settings?.company_hotline || '0357 993 134' }}</strong>
                  <template v-if="settings?.company_address">
                    <span class="exdoc-dot-sep">·</span>
                    <span>{{ settings.company_address }}</span>
                  </template>
                </div>
              </div>
            </div>
          </header>

          <!-- 2. TIÊU ĐỀ CHỨNG TỪ CĂN GIỮA -->
          <div class="exdoc-doc-title-wrap">
            <h1 class="exdoc-doc-title">HÓA ĐƠN BÁN HÀNG</h1>
          </div>

          <!-- 3. THÔNG TIN KHÁCH HÀNG & GIAO NHẬN -->
          <div class="exdoc-customer-rows">
            <div class="exdoc-info-row exdoc-info-row-split">
              <div class="exdoc-field-pair">
                <span class="exdoc-field-label">Tên khách hàng:</span>
                <span class="exdoc-field-value exdoc-customer-name-val">{{ customerName }}</span>
              </div>
              <div v-if="customerPhone" class="exdoc-field-pair">
                <span class="exdoc-field-label">SĐT:</span>
                <span class="exdoc-field-value fw-bold">{{ customerPhone }}</span>
              </div>
              <div v-if="customerEmail" class="exdoc-field-pair">
                <span class="exdoc-field-label">Email:</span>
                <span class="exdoc-field-value">{{ customerEmail }}</span>
              </div>
            </div>

            <div class="exdoc-info-row">
              <span class="exdoc-field-label">Địa chỉ nhận hàng:</span>
              <span v-if="shippingAddress" class="exdoc-field-value">{{ shippingAddress }}</span>
              <span v-else class="text-muted fst-italic">Chưa cập nhật địa chỉ giao hàng</span>
            </div>

            <div v-if="order.ten_don_vi_van_chuyen || order.ma_van_don" class="exdoc-info-row exdoc-info-row-split">
              <div v-if="order.ten_don_vi_van_chuyen" class="exdoc-field-pair">
                <span class="exdoc-field-label">Đơn vị vận chuyển:</span>
                <span class="exdoc-field-value">{{ order.ten_don_vi_van_chuyen }}</span>
              </div>
              <div v-if="order.ma_van_don" class="exdoc-field-pair">
                <span class="exdoc-field-label">Mã vận đơn:</span>
                <span class="exdoc-field-value fw-bold" style="color: #0369a1;">{{ order.ma_van_don }}</span>
                <button
                  type="button"
                  class="exdoc-copy-mini no-print"
                  @click="copyTrackingCode"
                  title="Sao chép mã vận đơn"
                >
                  Copy
                </button>
                <a
                  v-if="isViettelPost"
                  :href="'https://viettelpost.com.vn/tra-cuu-hanh-trinh-don/?order_number=' + encodeURIComponent(order.ma_van_don)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="exdoc-track-link no-print"
                >
                  Tra cứu hành trình <i class="bi bi-box-arrow-up-right"></i>
                </a>
              </div>
            </div>

            <div v-if="cleanedCustomerNote" class="exdoc-info-row">
              <span class="exdoc-field-label">Ghi chú khách hàng:</span>
              <span class="exdoc-field-value">{{ cleanedCustomerNote }}</span>
            </div>
          </div>

          <!-- Dòng Trạng Thái & Mã Đơn Hàng ngay trên mép bảng -->
          <div class="exdoc-code-bar">
            <div class="exdoc-code-left">
              <span v-if="order.trang_thai === 3" class="exdoc-inline-state text-success">
                <i class="bi bi-check-circle-fill me-1"></i>Đơn hàng đã giao thành công &amp; hoàn tất thanh toán.
              </span>
              <span v-else-if="order.trang_thai === 4" class="exdoc-inline-state text-danger">
                <i class="bi bi-x-circle me-1"></i>Đơn hàng đã hủy.
              </span>
              <span v-else-if="order.trang_thai === 2" class="exdoc-inline-state text-primary">
                <i class="bi bi-truck me-1"></i>Đơn hàng đang trên đường giao đến Quý khách.
              </span>
              <span v-else-if="order.trang_thai === 1" class="exdoc-inline-state text-success">
                <i class="bi bi-check-circle-fill me-1"></i>
                {{ effectivePaid > 0 && remainingCod > 0 ? `Đã nhận cọc ${formatCurrency(effectivePaid)}. Kho JPM đang xử lý.` : 'Đã xác nhận đơn hàng. Kho JPM đang xử lý.' }}
              </span>
              <span v-else class="exdoc-inline-state text-warning-emphasis">
                <i class="bi bi-clock-history me-1"></i>
                {{ isDepositReported ? 'Đã tiếp nhận thông báo chuyển khoản — JPM đang đối soát.' : 'Đơn hàng đang chờ xác nhận thanh toán / tiền cọc.' }}
              </span>
            </div>

            <div class="exdoc-code-right">
              <span>Ngày lập: <strong>{{ formatDate(order.created_at) }}</strong></span>
              <span class="mx-2 text-muted">|</span>
              <span>Mã Đơn Hàng: <strong class="font-monospace text-navy">{{ order.ma_don_hang }}</strong></span>
            </div>
          </div>

          <!-- 4. BẢNG CHI TIẾT PHỤ TÙNG -->
          <div class="qtable-frame table-responsive">
            <table class="qtable">
              <thead>
                <tr>
                  <th class="text-center" style="width: 52px;">#</th>
                  <th>Tên phụ tùng &amp; Mã OEM</th>
                  <th class="text-center" style="width: 72px;">SL</th>
                  <th class="text-end" style="width: 160px;">Đơn giá</th>
                  <th class="text-end" style="width: 165px;">Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in normalizedItems" :key="item.id || idx">
                  <td class="text-center text-muted fw-semibold">{{ idx + 1 }}</td>
                  <td>
                    <div class="qtable-item-name">{{ item.ten_phu_tung }}</div>
                    <div class="qtable-item-oem" v-if="item.ma_phu_tung">
                      <span class="qtable-oem-label">Mã:</span>
                      <span class="qtable-oem-code">{{ item.ma_phu_tung }}</span>
                    </div>
                    <div v-if="item.model_may" class="qtable-item-model">
                      <span class="qtable-model-label">Model:</span>
                      <span class="qtable-model-val">{{ item.model_may }}</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="qtable-qty-badge">{{ item.so_luong }}</span>
                  </td>
                  <td class="text-end exdoc-num">
                    {{ formatCurrency(item.don_gia) }}
                  </td>
                  <td class="text-end exdoc-num fw-bold text-danger">
                    {{ formatCurrency(item.so_luong * item.don_gia) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- 5. GHI CHÚ BÊN TRÁI (không card) + TỔNG HỢP CHI PHÍ BÊN PHẢI -->
          <div class="exdoc-bottom-grid">
            <div class="exdoc-notes-section">
              <div class="exdoc-notes-heading">GHI CHÚ:</div>
              <ul class="exdoc-notes-list">
                <li>{{ settings?.company_name || 'JPM Marine' }} - cam kết phụ tùng đúng mã sản phẩm (OEM), đúng xuất xứ Nhật Bản. UY TÍN.</li>
                <li>Quý khách vui lòng kiểm tra đúng mã sản phẩm, số lượng và tình trạng đóng gói khi nhận hàng.</li>
                <li>Cần hỗ trợ kỹ thuật, bảo hành hoặc theo dõi vận đơn, vui lòng liên hệ Hotline/Zalo: <strong>{{ settings?.company_hotline || '0357 993 134' }}</strong>.</li>
                <li v-if="order.ghi_chu_zalo" class="text-dark fw-semibold">
                  Ghi chú kỹ thuật viên: {{ order.ghi_chu_zalo }}
                </li>
              </ul>
            </div>

            <div class="qsummary-card">
              <div class="qsummary-head">Tổng Hợp Chi Phí</div>

              <div class="qsummary-rows">
                <div class="qsummary-row">
                  <span class="qsummary-label">Tạm tính ({{ totalQuantity }} sp)</span>
                  <span class="qsummary-val">{{ formatCurrency(computedSubtotal) }}</span>
                </div>

                <div class="qsummary-row">
                  <span class="qsummary-label">Phí vận chuyển</span>
                  <span class="qsummary-val">
                    <template v-if="order.khach_tu_tra_cuoc">
                      <span class="text-primary fw-medium">Khách tự trả cước chành xe</span>
                    </template>
                    <template v-else-if="Number(order.phi_van_chuyen) > 0">
                      {{ formatCurrency(order.phi_van_chuyen) }}
                    </template>
                    <template v-else>
                      <span class="text-success fw-semibold">Miễn phí</span>
                    </template>
                  </span>
                </div>

                <div class="qsummary-divider"></div>

                <div class="qsummary-row qsummary-grand">
                  <span class="qsummary-label">Tổng giá trị hóa đơn</span>
                  <span class="qsummary-val">
                    <span class="qsummary-grand-num">{{ formatCurrency(grandTotal) }}</span>
                  </span>
                </div>

                <template v-if="effectivePaid > 0 || depositRequired > 0 || remainingCod > 0">
                  <div class="qsummary-dashed"></div>

                  <div v-if="effectivePaid > 0" class="qsummary-row">
                    <span class="qsummary-label">
                      {{ isPaidFull ? 'Đã thanh toán' : 'Tiền cọc đã nhận' }}
                      <span class="ms-1 small fw-semibold text-success">
                        ({{ isPaidFull ? 'Đã thanh toán đủ' : 'Đã nhận cọc' }})
                      </span>
                    </span>
                    <span class="qsummary-val fw-bold text-navy">
                      {{ formatCurrency(effectivePaid) }}
                    </span>
                  </div>

                  <div v-else-if="depositRequired > 0" class="qsummary-row">
                    <span class="qsummary-label">
                      Tiền cọc yêu cầu
                      <span class="ms-1 small fw-semibold text-warning-emphasis">(Chưa đặt cọc)</span>
                    </span>
                    <span class="qsummary-val fw-bold text-navy">
                      {{ formatCurrency(depositRequired) }}
                    </span>
                  </div>

                  <div v-if="remainingCod > 0" class="qsummary-row">
                    <span class="qsummary-label fw-semibold text-dark">Thu khi nhận hàng (COD)</span>
                    <span class="qsummary-val fw-bold text-danger">
                      {{ formatCurrency(remainingCod) }}
                    </span>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- 6. CHỮ KÝ CHỨNG TỪ -->
          <div class="exdoc-signatures">
            <div class="exdoc-sig-col">
              <div class="exdoc-sig-spacer">&nbsp;</div>
              <div class="exdoc-sig-title">KHÁCH NHẬN HÀNG</div>
              <div class="exdoc-sig-sub">(Ký, ghi rõ họ tên)</div>
            </div>
            <div class="exdoc-sig-col">
              <div class="exdoc-sig-date">{{ formatSignatureDate(order.created_at) }}</div>
              <div class="exdoc-sig-title">ĐƠN VỊ BÁN HÀNG</div>
              <div class="exdoc-sig-img-wrap">
                <img src="/client/signature-jpm.png" alt="Chữ ký đơn vị bán hàng" class="exdoc-sig-img">
              </div>
            </div>
          </div>

          <!-- 7. THANH NÚT HỖ TRỢ (Ẩn khi in) -->
          <footer class="exdoc-actions-bar no-print">
            <span class="exdoc-action-hint">
              Cần hỗ trợ thêm về vận đơn hoặc hóa đơn #{{ order.ma_don_hang }}?
            </span>
            <div class="exdoc-action-btns">
              <a
                :href="buildZaloWithMsg('Chào JPM Marine, tôi cần hỗ trợ về đơn hàng #' + order.ma_don_hang)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn exdoc-btn exdoc-btn-outline"
              >
                <i class="bi bi-chat-dots"></i>
                <span>Hỏi tiến độ qua Zalo</span>
              </a>
            </div>
          </footer>
        </article>

        <!-- KHU VỰC VIETQR ĐẶT CỌC TÁCH RIÊNG BÊN DƯỚI (Chỉ hiện khi đơn chờ cọc/thanh toán, ẩn khi đã cọc hoặc khi in) -->
        <section
          v-if="showDepositSection"
          ref="vietQrSection"
          class="exdoc-deposit-card no-print"
        >
          <div class="exdoc-vietqr-head d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>THÔNG TIN CHUYỂN KHOẢN ĐẶT CỌC (VIETQR NAPAS 24/7)</strong>
            <span v-if="isDepositReported" class="badge bg-primary-subtle text-primary border border-primary-subtle">
              Chờ đối soát Sacombank
            </span>
            <span v-else class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
              Chờ thanh toán cọc
            </span>
          </div>

          <div v-if="vietQrData" class="exdoc-vietqr-grid">
            <div class="exdoc-qr-side">
              <div class="exdoc-qr-card">
                <img :src="vietQrData.qr_url" alt="VietQR Napas247" class="exdoc-qr-img">
                <div v-if="vietQrData.account_name || vietQrData.account_no" class="exdoc-qr-account">
                  <div class="exdoc-qr-acc-name">{{ vietQrData.account_name }}</div>
                  <div class="exdoc-qr-acc-no">{{ vietQrData.account_no }}</div>
                </div>
              </div>
            </div>

            <div class="exdoc-bank-side">
              <div class="exdoc-bank-line">
                <span class="text-muted">Ngân hàng:</span>
                <strong>{{ vietQrData.bank_name }} ({{ vietQrData.bank_id }})</strong>
              </div>
              <div class="exdoc-bank-line">
                <span class="text-muted">Số tài khoản:</span>
                <strong class="font-monospace">{{ vietQrData.account_no }}</strong>
              </div>
              <div class="exdoc-bank-line">
                <span class="text-muted">Chủ tài khoản:</span>
                <strong>{{ vietQrData.account_name }}</strong>
              </div>
              <div class="exdoc-bank-line">
                <span class="text-muted">Số tiền cần chuyển:</span>
                <strong class="text-danger exdoc-num">{{ vietQrData.amount_formatted || formatCurrency(depositRequired || grandTotal) }}</strong>
              </div>
              <div class="exdoc-bank-line">
                <span class="text-muted">Nội dung CK:</span>
                <code class="exdoc-transfer-code">{{ vietQrData.transfer_content }}</code>
              </div>
              <div class="pt-3">
                <div v-if="isDepositReported" class="d-flex flex-wrap align-items-center justify-content-end gap-3">
                  <div class="d-inline-flex align-items-center gap-2 text-primary fw-semibold small">
                    <i class="bi bi-clock-history"></i>
                    <span>
                      Đã báo chuyển khoản{{ order.thoi_gian_bao_coc ? ' lúc ' + formatDateTime(order.thoi_gian_bao_coc) : '' }} — JPM đang đối soát Sacombank
                    </span>
                  </div>
                </div>
                <div v-else class="text-end">
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-success fw-semibold"
                    @click="showDepositReportModal = true"
                  >
                    <i class="bi bi-receipt me-1"></i> Xác nhận đã chuyển cọc / Tải ảnh
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </template>

      <DepositReportModal
        :show="showDepositReportModal"
        :code="order?.ma_don_hang || ''"
        :amount-formatted="vietQrData?.amount_formatted || formatCurrency(order?.tien_coc_yeu_cau || order?.tong_tien || 0)"
        :transfer-content="vietQrData?.transfer_content || (order?.ma_don_hang ? `COC ${order.ma_don_hang}` : '')"
        :submitting="isSubmittingReport"
        @close="showDepositReportModal = false"
        @submit="submitOrderDepositReport"
      />
    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient';
import { createToaster } from '../../../utils/notify';
import { useSiteSettings } from '../../../composables/useSiteSettings';
import { useAuthStore } from '../../../stores/authStore';
import DepositReportModal from '../../../components/client/DepositReportModal.vue';

export default {
  name: 'DonHangKhach',
  components: { DepositReportModal },
  setup() {
    const { settings } = useSiteSettings();
    const authStore = useAuthStore();
    const toaster = createToaster({ position: 'top-right', duration: 2800 });
    return { settings, authStore, toaster };
  },
  data() {
    return {
      orderCode: String(this.$route.params.code || '').trim(),
      phone: String(this.$route.query.phone || '').trim(),
      order: null,
      loading: false,
      needsPhonePrompt: false,
      error: null,
      vietQrData: null,
      showDepositReportModal: false,
      isSubmittingReport: false
    };
  },
  watch: {
    '$route.params.code'(newCode) {
      if (newCode && newCode !== this.orderCode) {
        this.orderCode = String(newCode).trim();
        this.phone = String(this.$route.query.phone || '').trim();
        this.initFetch();
      }
    }
  },
  computed: {
    isLoggedIn() {
      return Boolean(this.authStore?.token);
    },
    siteHost() {
      return typeof window !== 'undefined' ? window.location.origin : 'https://tauthuyennhatban.com';
    },
    zaloBaseUrl() {
      const configured = (this.settings?.social_zalo || '').trim();
      if (configured) return configured;
      const digits = String(this.settings?.company_hotline || '').replace(/[^0-9]/g, '');
      return digits ? `https://zalo.me/${digits}` : '/ho-tro';
    },
    customerName() {
      return this.order?.khach_hang_ten || this.order?.khach_hang?.ho_ten || 'Khách lẻ';
    },
    customerPhone() {
      return this.order?.khach_hang_sdt || this.order?.khach_hang?.so_dien_thoai || '';
    },
    customerEmail() {
      return this.order?.khach_hang_email || this.order?.khach_hang?.user?.email || '';
    },
    shippingAddress() {
      return (
        this.order?.dia_chi_giao_hang ||
        this.order?.dia_chi_text ||
        this.order?.dia_chi?.dia_chi_cu_the ||
        ''
      );
    },
    cleanedCustomerNote() {
      const raw = String(this.order?.ghi_chu_khach_hang || '').trim();
      if (!raw) return '';
      return raw.replace(/^Báo giá\s+#YCBG-[^\s|]+\s*\|?\s*/i, '').trim();
    },
    isViettelPost() {
      const c = String(this.order?.ten_don_vi_van_chuyen || '').toLowerCase();
      return c.includes('viettel');
    },
    normalizedItems() {
      const list = this.order?.chi_tiet || [];
      return list.map((item) => ({
        id: item.id,
        ten_phu_tung:
          item.ten_phu_tung ||
          item.phu_tung?.ten_phu_tung ||
          item.san_pham?.ten_san_pham ||
          `Sản phẩm #${item.id_san_pham || ''}`,
        ma_phu_tung:
          item.ma_phu_tung ||
          item.phu_tung?.ma_phu_tung ||
          item.san_pham?.ma_san_pham ||
          '',
        model_may: item.model_may || '',
        so_luong: Math.max(1, Number(item.so_luong || 1)),
        don_gia: Number(item.don_gia ?? item.gia_ban ?? 0)
      }));
    },
    totalQuantity() {
      return this.normalizedItems.reduce((sum, item) => sum + item.so_luong, 0);
    },
    computedSubtotal() {
      const sum = this.normalizedItems.reduce((acc, item) => acc + item.so_luong * item.don_gia, 0);
      if (sum > 0) return sum;
      return Number(this.order?.tong_tien || 0);
    },
    shippingFee() {
      if (!this.order || this.order.khach_tu_tra_cuoc) return 0;
      return Number(this.order.phi_van_chuyen || 0);
    },
    grandTotal() {
      return this.computedSubtotal + this.shippingFee;
    },
    depositRequired() {
      return Number(this.order?.tien_coc_yeu_cau || 0);
    },
    paidAmount() {
      return Number(this.order?.da_thu || 0);
    },
    isPaidFull() {
      if (!this.order) return false;
      return (
        Number(this.order.trang_thai) === 3 ||
        Number(this.order.trang_thai_thanh_toan) === 2 ||
        (this.grandTotal > 0 && this.paidAmount >= this.grandTotal)
      );
    },
    effectivePaid() {
      if (this.isPaidFull) return Math.max(this.paidAmount, this.grandTotal);
      return this.paidAmount;
    },
    remainingCod() {
      if (Number(this.order?.trang_thai) === 4) return 0;
      return Math.max(0, this.grandTotal - this.effectivePaid);
    },
    isDepositReported() {
      return Boolean(this.order?.da_bao_chuyen_khoan) && this.paidAmount <= 0 && Number(this.order?.trang_thai) === 0;
    },
    showDepositSection() {
      return (
        this.order &&
        Number(this.order.trang_thai) === 0 &&
        Number(this.order.trang_thai_thanh_toan || 0) === 0 &&
        this.paidAmount <= 0 &&
        this.grandTotal > 0
      );
    }
  },
  async mounted() {
    await this.initFetch();
  },
  methods: {
    async initFetch() {
      if (!this.orderCode) {
        this.error = 'Đường dẫn đơn hàng không hợp lệ.';
        return;
      }
      if (!this.phone) {
        this.needsPhonePrompt = true;
        return;
      }
      await this.fetchOrder();
    },
    async submitPhoneVerification() {
      if (!this.phone) return;
      this.needsPhonePrompt = false;
      await this.fetchOrder();
    },
    async fetchOrder() {
      this.loading = true;
      this.error = null;
      this.vietQrData = null;
      try {
        const res = await axiosClient.post('/public/tra-cuu-nhanh', {
          code: this.orderCode,
          phone: this.phone
        });
        const payload = res?.type ? res : res?.data;
        if (payload && (res?.success || payload?.success) && payload.type === 'order' && payload.data) {
          this.order = payload.data;
          if (typeof document !== 'undefined' && this.order.ma_don_hang) {
            document.title = `Hóa đơn ${this.order.ma_don_hang} - JPM Marine`;
          }
          if (this.showDepositSection) {
            await this.loadVietQR();
          }
        } else {
          this.error = 'Không tìm thấy đơn hàng khớp với mã và số điện thoại này.';
        }
      } catch (err) {
        this.error = err?.response?.data?.message || 'Không tìm thấy đơn hàng hoặc số điện thoại chưa chính xác.';
      } finally {
        this.loading = false;
      }
    },
    async loadVietQR() {
      if (!this.order?.ma_don_hang) return;
      try {
        const res = await axiosClient.get(`/public/vietqr/don-hang/${this.order.ma_don_hang}`);
        const payload = res?.qr_url ? res : res?.data;
        if (payload && (res?.success || payload?.success)) {
          this.vietQrData = payload;
        }
      } catch (err) {
        console.error('Không thể nạp mã VietQR:', err);
      }
    },
    async submitOrderDepositReport(file) {
      if (!this.order || this.isSubmittingReport) return;
      this.isSubmittingReport = true;
      try {
        const formData = new FormData();
        formData.append('phone', this.phone || this.customerPhone || '');
        if (file) {
          formData.append('file', file);
        }
        const res = await axiosClient.post(`/public/don-hang/${this.order.ma_don_hang}/bao-chuyen-khoan`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        const updated = res?.data?.ma_don_hang ? res.data : (res?.data?.data?.ma_don_hang ? res.data.data : null);
        if (updated) {
          this.order = { ...this.order, ...updated };
        } else {
          this.order.da_bao_chuyen_khoan = true;
        }
        this.showDepositReportModal = false;
        this.toaster.success(res?.message || res?.data?.message || 'Đã tiếp nhận thông báo chuyển khoản.');
      } catch (err) {
        this.toaster.error(err?.response?.data?.message || 'Không thể gửi thông báo chuyển khoản lúc này.');
      } finally {
        this.isSubmittingReport = false;
      }
    },
    copyTrackingCode() {
      if (!this.order?.ma_van_don) return;
      navigator.clipboard?.writeText(this.order.ma_van_don);
      this.toaster.success('Đã sao chép mã vận đơn.');
    },
    buildZaloWithMsg(msg) {
      if (!msg) return this.zaloBaseUrl;
      const sep = this.zaloBaseUrl.includes('?') ? '&' : '?';
      return `${this.zaloBaseUrl}${sep}text=${encodeURIComponent(msg)}`;
    },
    goBack() {
      if (this.isLoggedIn) {
        this.$router.push('/lich-su-don-hang');
      } else if (window.history.length > 1) {
        this.$router.back();
      } else {
        this.$router.push('/');
      }
    },
    printDocument() {
      window.print();
    },
    formatCurrency(value) {
      if (value === null || value === undefined || value === '') return '0 đ';
      return `${new Intl.NumberFormat('vi-VN').format(Number(value))} đ`;
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    formatDateTime(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    formatSignatureDate(dateStr) {
      const d = dateStr ? new Date(dateStr) : new Date();
      const day = String(d.getDate()).padStart(2, '0');
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const year = d.getFullYear();
      return `Ngày ${day}, tháng ${month}, năm ${year}`;
    }
  }
};
</script>

<style scoped>
.exdoc-page {
  min-height: calc(100vh - 80px);
  background: #f1f5f9;
  padding: 1.75rem 0 4rem;
  font-family: var(--jpm-font-family, 'Inter', sans-serif);
  color: #0f172a;
}

.exdoc-container {
  max-width: 980px;
}

.text-navy {
  color: #0A1128;
}

.exdoc-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.exdoc-back-btn {
  border: none;
  background: transparent;
  color: #475569;
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 6px 2px;
  cursor: pointer;
}

.exdoc-back-btn:hover {
  color: #0A1128;
}

.exdoc-tool-btn {
  height: 35px;
  padding: 0 13px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #1e293b;
  font-size: 0.82rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.exdoc-tool-btn:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}

.exdoc-sheet {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 32px 38px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
}

.exdoc-error-icon,
.exdoc-lock-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 14px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.exdoc-error-icon {
  background: #fef2f2;
  color: #dc2626;
}

.exdoc-lock-icon {
  background: #eff6ff;
  color: #0284c7;
}

.exdoc-top-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
  padding-bottom: 8px;
}

.exdoc-company-lockup {
  display: flex;
  align-items: center;
  gap: 16px;
}

.exdoc-logo-circle {
  width: 78px;
  height: 78px;
  border-radius: 50%;
  background: #0f172a;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  flex-shrink: 0;
}

.exdoc-logo-text {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 2.5px;
  color: #ffffff;
  line-height: 1;
  margin-left: 2px;
}

.exdoc-company-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.exdoc-company-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.01em;
}

.exdoc-company-line {
  font-size: 0.84rem;
  color: #334155;
  line-height: 1.45;
}

.exdoc-lbl {
  color: #64748b;
  margin-right: 4px;
}

.exdoc-dot-sep {
  margin: 0 8px;
  color: #cbd5e1;
}

.exdoc-doc-title-wrap {
  text-align: center;
  padding: 12px 0 18px;
}

.exdoc-doc-title {
  margin: 0;
  font-size: 1.65rem;
  font-weight: 800;
  color: #0A1128;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}

.exdoc-customer-rows {
  padding: 2px 0 8px;
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.exdoc-info-row {
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 8px;
  font-size: 0.92rem;
  line-height: 1.55;
}

.exdoc-info-row-split {
  gap: 28px;
}

.exdoc-field-pair {
  display: inline-flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 6px;
}

.exdoc-field-label {
  color: #64748b;
  font-weight: 500;
  white-space: nowrap;
}

.exdoc-field-value {
  color: #0f172a;
  font-weight: 600;
}

.exdoc-customer-name-val {
  font-size: 0.98rem;
  font-weight: 700;
  color: #0A1128;
}

.exdoc-copy-mini {
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #1e293b;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 4px;
  cursor: pointer;
}

.exdoc-copy-mini:hover {
  border-color: #0A1128;
}

.exdoc-track-link {
  font-size: 0.78rem;
  font-weight: 600;
  color: #0284c7;
  text-decoration: none;
  margin-left: 4px;
}

.exdoc-track-link:hover {
  text-decoration: underline;
}

.exdoc-code-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  font-size: 0.84rem;
  color: #334155;
  margin-bottom: 8px;
}

.qtable-frame {
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #ffffff;
  margin-bottom: 22px;
}

.qtable {
  width: 100%;
  border-collapse: collapse;
  margin: 0;
  font-size: 0.88rem;
}

.qtable thead th {
  background: #f8fafc;
  color: #334155;
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 12px 16px;
  border-bottom: 1.5px solid #cbd5e1;
  white-space: nowrap;
}

.qtable tbody td {
  padding: 14px 16px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle;
}

.qtable tbody tr:last-child td {
  border-bottom: none;
}

.qtable tbody tr:hover td {
  background: #f8fafc;
}

.qtable-item-name {
  font-weight: 600;
  font-size: 0.9rem;
  color: #0f172a;
  line-height: 1.4;
  margin-bottom: 3px;
}

.qtable-item-oem {
  font-size: 0.8rem;
  line-height: 1.35;
}

.qtable-oem-label {
  color: #94a3b8;
  font-weight: 400;
}

.qtable-oem-code {
  font-family: 'Roboto Mono', monospace;
  font-weight: 700;
  color: #0284c7;
  margin-left: 4px;
}

.qtable-item-model {
  font-size: 0.74rem;
  line-height: 1.35;
  margin-top: 2px;
}

.qtable-model-label {
  color: #94a3b8;
  font-weight: 400;
  margin-right: 4px;
}

.qtable-model-val {
  color: #64748b;
  font-weight: 500;
}

.qtable-qty-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 30px;
  height: 26px;
  padding: 0 8px;
  border-radius: 6px;
  background: #f1f5f9;
  color: #0f172a;
  font-weight: 700;
  font-size: 0.84rem;
}

.exdoc-num {
  font-family: var(--jpm-font-family, 'Inter', sans-serif);
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
}

.exdoc-bottom-grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 24px;
  align-items: start;
  margin-bottom: 22px;
}

.exdoc-notes-section {
  padding: 6px 4px 4px 0;
}

.exdoc-notes-heading {
  font-size: 0.84rem;
  font-weight: 800;
  color: #0A1128;
  text-decoration: underline;
  margin-bottom: 8px;
}

.exdoc-notes-list {
  margin: 0;
  padding-left: 18px;
  font-size: 0.83rem;
  color: #334155;
  line-height: 1.6;
}

.qsummary-card {
  padding: 18px 20px;
  border-radius: 10px;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
}

.qsummary-head {
  font-size: 0.76rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #0A1128;
  padding-bottom: 10px;
  margin-bottom: 10px;
  border-bottom: 1px solid #cbd5e1;
}

.qsummary-rows {
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.qsummary-row {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 12px;
  font-size: 0.86rem;
}

.qsummary-label {
  color: #475569;
}

.qsummary-val {
  text-align: right;
  font-weight: 600;
  color: #0f172a;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

.qsummary-divider {
  border-top: 1px solid #cbd5e1;
  margin: 3px 0;
}

.qsummary-dashed {
  border-top: 1px dashed #cbd5e1;
  margin: 3px 0;
}

.qsummary-grand .qsummary-label {
  font-weight: 700;
  color: #0A1128;
  font-size: 0.92rem;
}

.qsummary-grand-num {
  font-size: 1.18rem;
  font-weight: 800;
  color: #dc2626;
}

.exdoc-signatures {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  text-align: center;
  padding: 14px 0 8px;
}

.exdoc-sig-date,
.exdoc-sig-spacer {
  font-size: 0.84rem;
  font-style: italic;
  color: #475569;
  margin-bottom: 4px;
}

.exdoc-sig-title {
  font-size: 0.88rem;
  font-weight: 800;
  color: #0A1128;
  letter-spacing: 0.02em;
}

.exdoc-sig-sub {
  font-size: 0.78rem;
  color: #64748b;
  margin-top: 2px;
}

.exdoc-sig-img-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 78px;
  margin: 6px 0 4px;
}

.exdoc-sig-img {
  height: 74px;
  width: auto;
  max-width: 220px;
  object-fit: contain;
  user-select: none;
  pointer-events: none;
}

.exdoc-actions-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-top: 22px;
  padding-top: 18px;
  border-top: 1.5px solid #e2e8f0;
}

.exdoc-action-hint {
  font-size: 0.83rem;
  color: #64748b;
}

.exdoc-action-btns {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
  flex-wrap: wrap;
}

.exdoc-btn {
  height: 38px;
  padding: 0 18px;
  border-radius: 6px;
  font-size: 0.86rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none;
}

.exdoc-btn-outline {
  background: #ffffff;
  border: 1.5px solid #cbd5e1;
  color: #1e293b;
}

.exdoc-btn-outline:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #94a3b8;
}

.exdoc-btn-primary {
  background: #F82249;
  border: 1.5px solid #F82249;
  color: #ffffff;
}

.exdoc-btn-primary:hover:not(:disabled) {
  background: #D9163A;
  border-color: #D9163A;
  color: #ffffff;
}

/* Khối VietQR tách riêng bên dưới */
.exdoc-deposit-card {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  margin-top: 20px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
  overflow: hidden;
}

.exdoc-vietqr-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #cbd5e1;
  font-size: 0.82rem;
  color: #0A1128;
}

.exdoc-vietqr-grid {
  display: grid;
  grid-template-columns: 230px 1fr;
  gap: 24px;
  padding: 18px 20px;
  align-items: center;
}

.exdoc-qr-side {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.exdoc-qr-card {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #ffffff;
  padding: 6px 8px 10px;
  text-align: center;
  width: 215px;
}

.exdoc-qr-img {
  width: 100%;
  height: auto;
  display: block;
}

.exdoc-qr-account {
  margin-top: 2px;
  line-height: 1.35;
}

.exdoc-qr-acc-name {
  font-size: 0.76rem;
  font-weight: 700;
  color: #1e293b;
}

.exdoc-qr-acc-no {
  font-size: 0.78rem;
  font-weight: 600;
  color: #475569;
  font-family: 'Roboto Mono', monospace;
  font-variant-numeric: tabular-nums;
}

.exdoc-bank-side {
  display: flex;
  flex-direction: column;
  gap: 7px;
  font-size: 0.85rem;
}

.exdoc-bank-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-bottom: 6px;
  border-bottom: 1px dashed #e2e8f0;
}

.exdoc-bank-line:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.exdoc-transfer-code {
  font-family: 'Roboto Mono', monospace;
  font-weight: 700;
  color: #0A1128;
  background: #f1f5f9;
  padding: 1px 7px;
  border-radius: 4px;
}

@media (max-width: 767px) {
  .exdoc-sheet {
    padding: 20px 16px;
  }
  .exdoc-top-header {
    flex-direction: column;
  }
  .exdoc-company-lockup {
    flex-direction: column;
    align-items: flex-start;
  }
  .exdoc-doc-title {
    font-size: 1.32rem;
  }
  .exdoc-bottom-grid,
  .exdoc-vietqr-grid,
  .exdoc-signatures {
    grid-template-columns: 1fr;
  }
  .exdoc-actions-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .exdoc-action-btns {
    flex-direction: column;
    width: 100%;
  }
  .exdoc-action-btns .exdoc-btn {
    width: 100%;
  }
}

@media print {
  .no-print {
    display: none !important;
  }
  .exdoc-logo-circle,
  .qtable thead th,
  .qtable-qty-badge,
  .qsummary-card {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .exdoc-page {
    background: #ffffff !important;
    padding: 0 !important;
  }
  .exdoc-sheet {
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
}
</style>
