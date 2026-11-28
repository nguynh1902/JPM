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

      <!-- Error State -->
      <div v-else-if="error" class="exdoc-sheet text-center py-5">
        <div class="exdoc-error-icon">
          <i class="bi bi-file-earmark-x"></i>
        </div>
        <h4 class="fw-bold text-navy mb-2">Không tìm thấy báo giá</h4>
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

      <!-- Main Quotation Sheet -->
      <template v-else-if="quote">
        <!-- Toolbar mảnh phía trên (Ẩn khi in) -->
        <div class="exdoc-toolbar no-print">
          <button type="button" class="exdoc-back-btn" @click="goBack">
            <i class="bi bi-arrow-left"></i>
            <span>{{ isLoggedIn ? 'Quay lại lịch sử đơn' : 'Quay lại' }}</span>
          </button>

          <button type="button" class="exdoc-tool-btn" @click="printDocument">
            <i class="bi bi-printer"></i>
            <span>In báo giá</span>
          </button>
        </div>

        <!-- Tờ Chứng Từ Báo Giá chuẩn bố cục doanh nghiệp -->
        <article class="exdoc-sheet">
          <!-- 1. TOP HEADER: Trái = Logo tròn JPM + Thông tin đơn vị | Phải = Trạng thái gọn -->
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

            <div
              v-if="quote.trang_thai === 1 && !quote.is_expired && remainingSeconds > 0"
              class="exdoc-status-wrap"
            >
              <span class="exdoc-countdown-text">
                Hiệu lực còn: <strong>{{ formattedRemainingTime }}</strong>
              </span>
            </div>
          </header>

          <!-- 2. TIÊU ĐỀ CHỨNG TỪ CĂN GIỮA -->
          <div class="exdoc-doc-title-wrap">
            <h1 class="exdoc-doc-title">BẢNG BÁO GIÁ PHỤ TÙNG</h1>
          </div>

          <!-- 3. THÔNG TIN KHÁCH HÀNG RÕ RÀNG (Form-style gọn sạch giống Excel) -->
          <div class="exdoc-customer-rows">
            <div class="exdoc-info-row exdoc-info-row-split">
              <div class="exdoc-field-pair">
                <span class="exdoc-field-label">Tên khách hàng:</span>
                <span class="exdoc-field-value exdoc-customer-name-val">{{ quote.khach_hang_ten }}</span>
              </div>
              <div class="exdoc-field-pair">
                <span class="exdoc-field-label">SĐT:</span>
                <span class="exdoc-field-value fw-bold">{{ quote.khach_hang_sdt }}</span>
              </div>
              <div v-if="quote.khach_hang_email" class="exdoc-field-pair">
                <span class="exdoc-field-label">Email:</span>
                <span class="exdoc-field-value">{{ quote.khach_hang_email }}</span>
              </div>
            </div>

            <div class="exdoc-info-row">
              <span class="exdoc-field-label">Địa chỉ nhận hàng:</span>
              <span v-if="quote.dia_chi_giao_hang" class="exdoc-field-value">{{ quote.dia_chi_giao_hang }}</span>
              <span v-else class="text-muted fst-italic">Chưa cập nhật địa chỉ giao hàng</span>
            </div>

            <div v-if="quote.ghi_chu_khach_hang" class="exdoc-info-row">
              <span class="exdoc-field-label">Ghi chú khách hàng:</span>
              <span class="exdoc-field-value">{{ quote.ghi_chu_khach_hang }}</span>
            </div>
          </div>

          <!-- Dòng Mã Báo Giá căn phải ngay trên mép bảng (chuẩn Excel) -->
          <div class="exdoc-code-bar">
            <div class="exdoc-code-left">
              <span v-if="quote.trang_thai === 0" class="exdoc-inline-state text-warning-emphasis">
                <i class="bi bi-clock-history me-1"></i>Kỹ thuật viên đang kiểm tra tồn kho &amp; lập đơn giá...
              </span>
              <span v-else-if="quote.trang_thai === 1 && !quote.is_expired && quote.expires_at" class="exdoc-inline-state">
                <i class="bi bi-shield-check text-primary me-1"></i>
                Hiệu lực bảo lưu giá đến <strong>{{ formatDateTime(quote.expires_at) }}</strong>
              </span>
              <span v-else-if="quote.trang_thai === 2" class="exdoc-inline-state text-success">
                <i class="bi bi-check-circle-fill me-1"></i>Quý khách đã xác nhận chốt đơn. Kho JPM đang xử lý.
              </span>
              <span v-else class="exdoc-inline-state text-danger">
                <i class="bi bi-exclamation-circle me-1"></i>Báo giá đã hết hiệu lực hoặc đã đóng.
              </span>
            </div>

            <div class="exdoc-code-right">
              <span>Ngày lập: <strong>{{ formatDate(quote.created_at) }}</strong></span>
              <span class="mx-2 text-muted">|</span>
              <span>Mã Báo Giá: <strong class="font-monospace text-navy">{{ quote.ma_yeu_cau }}</strong></span>
            </div>
          </div>

          <!-- 4. BẢNG CHI TIẾT PHỤ TÙNG (Gọn nét, hiện đại, dễ nhìn) -->
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
                <tr v-for="(item, idx) in quote.chi_tiet" :key="item.id || idx">
                  <td class="text-center text-muted fw-semibold">{{ idx + 1 }}</td>
                  <td>
                    <div class="qtable-item-name">{{ item.ten_phu_tung }}</div>
                    <div class="qtable-item-oem">
                      <span class="qtable-oem-label">Mã:</span>
                      <span class="qtable-oem-code">{{ item.ma_phu_tung || 'N/A' }}</span>
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
                    <span v-if="getItemUnitPrice(item) > 0">{{ formatCurrency(getItemUnitPrice(item)) }}</span>
                    <span v-else class="text-muted fst-italic">Chờ báo giá</span>
                  </td>
                  <td class="text-end exdoc-num fw-bold text-danger">
                    <span v-if="getItemUnitPrice(item) > 0">{{ formatCurrency(item.so_luong * getItemUnitPrice(item)) }}</span>
                    <span v-else class="text-muted fw-normal">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- 5. GHI CHÚ BÊN TRÁI + TỔNG HỢP CHI PHÍ BÊN PHẢI -->
          <div class="exdoc-bottom-grid">
            <div class="exdoc-notes-section">
              <div class="exdoc-notes-heading">GHI CHÚ:</div>
              <ul class="exdoc-notes-list">
                <li>{{ settings?.company_name || 'JPM Marine' }} - cam kết phụ tùng đúng mã sản phẩm (OEM), đúng xuất xứ Nhật Bản. UY TÍN.</li>
                <li v-if="quote.trang_thai === 1 && !quote.is_expired && quote.expires_at">
                  Báo giá có hiệu lực bảo lưu đơn giá &amp; tỷ giá JPY trong vòng <strong>72 giờ</strong> (đến <strong>{{ formatDateTime(quote.expires_at) }}</strong>).
                </li>
                <li>Đối với những sản phẩm không có sẵn tại kho, thời gian đặt nhập và nhận hàng dự kiến 10 - 12 ngày.</li>
                <li>Hình thức thanh toán: Chuyển khoản đặt cọc hoặc thanh toán khi nhận hàng (COD).</li>
                <li v-if="quote.ghi_chu_zalo" class="text-dark fw-semibold">
                  Ghi chú kỹ thuật viên: {{ quote.ghi_chu_zalo }}
                </li>
              </ul>
            </div>

            <div class="qsummary-card">
              <div class="qsummary-head">Tổng Hợp Chi Phí</div>

              <div class="qsummary-rows">
                <div class="qsummary-row">
                  <span class="qsummary-label">Tạm tính ({{ totalQuantity }} sp)</span>
                  <span class="qsummary-val">
                    <template v-if="computedSubtotal > 0">
                      {{ formatCurrency(computedSubtotal) }}
                      <small v-if="unquotedCount > 0" class="d-block text-warning fw-normal">
                        (+{{ unquotedCount }} món chờ giá)
                      </small>
                    </template>
                    <span v-else class="text-muted fw-normal fst-italic">Chờ định giá</span>
                  </span>
                </div>

                <div class="qsummary-row">
                  <span class="qsummary-label">Phí vận chuyển</span>
                  <span class="qsummary-val">
                    <template v-if="quote.khach_tu_tra_cuoc">
                      <span class="text-primary fw-medium">Khách tự trả cước chành xe</span>
                    </template>
                    <template v-else-if="Number(quote.phi_van_chuyen) > 0">
                      {{ formatCurrency(quote.phi_van_chuyen) }}
                    </template>
                    <template v-else-if="quote.trang_thai === 0">
                      <span class="text-muted fw-normal fst-italic">Chờ xác nhận</span>
                    </template>
                    <template v-else>
                      <span class="text-success fw-semibold">Miễn phí</span>
                    </template>
                  </span>
                </div>

                <div class="qsummary-divider"></div>

                <div class="qsummary-row qsummary-grand">
                  <span class="qsummary-label">Tổng giá trị đơn hàng</span>
                  <span class="qsummary-val">
                    <span v-if="grandTotal !== null" class="qsummary-grand-num">
                      {{ formatCurrency(grandTotal) }}
                    </span>
                    <span v-else class="text-muted fw-normal fst-italic">Chờ hoàn tất</span>
                  </span>
                </div>

                <template v-if="Number(quote.tien_coc_yeu_cau) > 0">
                  <div class="qsummary-dashed"></div>

                  <div class="qsummary-row">
                    <span class="qsummary-label">
                      Tiền cọc yêu cầu
                      <span class="ms-1 small fw-semibold" :class="quote.trang_thai === 2 ? 'text-success' : 'text-warning-emphasis'">
                        ({{ quote.trang_thai === 2 ? 'Đã nhận cọc' : 'Chưa đặt cọc' }})
                      </span>
                    </span>
                    <span class="qsummary-val fw-bold text-navy">
                      {{ formatCurrency(quote.tien_coc_yeu_cau) }}
                    </span>
                  </div>

                  <div v-if="grandTotal !== null" class="qsummary-row">
                    <span class="qsummary-label fw-semibold text-dark">Thu khi nhận hàng (COD)</span>
                    <span class="qsummary-val fw-bold text-danger">
                      {{ formatCurrency(remainingCod) }}
                    </span>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- 6. KHU VỰC VIETQR ĐẶT CỌC (CHỈ hiện sau khi khách đã Đồng ý chốt đơn & có yêu cầu cọc) -->
          <div
            v-if="showDepositSection"
            ref="vietQrSection"
            class="exdoc-vietqr-box"
          >
            <div class="exdoc-vietqr-head d-flex flex-wrap justify-content-between align-items-center gap-2">
              <strong>THÔNG TIN CHUYỂN KHOẢN ĐẶT CỌC (VIETQR NAPAS 24/7)</strong>
              <span v-if="isDepositReported" class="badge bg-primary-subtle text-primary border border-primary-subtle">
                Chờ đối soát Sacombank
              </span>
              <span v-else-if="quote.trang_thai === 1" class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                Đã chốt · Chờ đặt cọc
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
                  <span class="text-muted">Số tiền cọc:</span>
                  <strong class="text-danger exdoc-num">{{ formatCurrency(quote.tien_coc_yeu_cau) }}</strong>
                </div>
                <div class="exdoc-bank-line">
                  <span class="text-muted">Nội dung CK:</span>
                  <code class="exdoc-transfer-code">
                    {{ vietQrData.transfer_content }}
                  </code>
                </div>
                <div v-if="quote.trang_thai === 1 && !quote.is_expired" class="pt-3 no-print">
                  <div v-if="isDepositReported" class="d-flex flex-wrap align-items-center justify-content-end gap-3">
                    <a
                      v-if="quote.anh_chuyen_khoan"
                      :href="resolveImageUrl(quote.anh_chuyen_khoan)"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="d-inline-flex align-items-center gap-2 text-decoration-none border rounded p-1 bg-white"
                      title="Xem lại ảnh biên lai đã gửi"
                    >
                      <img
                        :src="resolveImageUrl(quote.anh_chuyen_khoan)"
                        alt="Biên lai chuyển khoản"
                        style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;"
                      >
                      <span class="small text-secondary fw-medium pe-1">Ảnh biên lai</span>
                    </a>
                    <div class="d-inline-flex align-items-center gap-2 text-primary fw-semibold small">
                      <i class="bi bi-clock-history"></i>
                      <span>
                        Đã báo chuyển khoản{{ quote.thoi_gian_bao_coc ? ' lúc ' + formatDateTime(quote.thoi_gian_bao_coc) : '' }} — JPM đang đối soát Sacombank
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
          </div>

          <!-- 7. CHỮ KÝ CHỨNG TỪ (chỉ hiện sau khi JPM đã lập báo giá hoặc đã chốt đơn) -->
          <div v-if="quote.trang_thai === 1 || quote.trang_thai === 2" class="exdoc-signatures">
            <div class="exdoc-sig-col">
              <div class="exdoc-sig-spacer">&nbsp;</div>
              <div class="exdoc-sig-title">KHÁCH ĐẶT HÀNG</div>
              <div class="exdoc-sig-sub">
                {{ isCustomerAccepted ? `(Đã xác nhận chốt: ${quote.khach_hang_ten})` : '(Vui lòng bấm Đồng ý chốt đơn bên dưới)' }}
              </div>
            </div>
            <div class="exdoc-sig-col">
              <div class="exdoc-sig-date">{{ formatSignatureDate(quote.created_at) }}</div>
              <div class="exdoc-sig-title">ĐƠN VỊ BÁO GIÁ</div>
              <div class="exdoc-sig-img-wrap">
                <img src="/client/signature-jpm.png" alt="Chữ ký đơn vị báo giá" class="exdoc-sig-img">
              </div>
            </div>
          </div>

          <!-- 8. THANH NÚT THAO TÁC (Ẩn khi in) -->
          <footer class="exdoc-actions-bar no-print">
            <template v-if="quote.trang_thai === 0">
              <span class="exdoc-action-hint">Cần trao đổi gấp về mã phụ tùng hoặc thời gian giao hàng?</span>
              <div class="exdoc-action-btns">
                <a
                  :href="buildZaloWithMsg('Chào JPM Marine, tôi cần hỗ trợ về yêu cầu báo giá #' + quote.ma_yeu_cau)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn exdoc-btn exdoc-btn-primary"
                >
                  <i class="bi bi-chat-dots"></i>
                  <span>Nhắn Zalo kỹ thuật</span>
                </a>
              </div>
            </template>

            <template v-else-if="quote.trang_thai === 1 && !quote.is_expired">
              <template v-if="!isCustomerAccepted">
                <span class="exdoc-action-hint">Quý khách vui lòng kiểm tra kỹ mã sản phẩm, số lượng và tổng tiền trước khi chốt đơn.</span>
                <div class="exdoc-action-btns">
                  <button
                    type="button"
                    class="btn exdoc-btn exdoc-btn-outline"
                    :disabled="isSubmitting"
                    @click="handleAction('reject')"
                  >
                    Từ chối báo giá
                  </button>
                  <a
                    :href="buildZaloWithMsg('Chào JPM Marine, tôi muốn hỏi thêm về báo giá #' + quote.ma_yeu_cau)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn exdoc-btn exdoc-btn-outline"
                  >
                    <i class="bi bi-chat-dots"></i>
                    <span>Tư vấn Zalo</span>
                  </a>
                  <button
                    type="button"
                    class="btn exdoc-btn exdoc-btn-primary"
                    :disabled="isSubmitting"
                    @click="handleAction('accept')"
                  >
                    <i class="bi bi-check2-circle"></i>
                    <span>Đồng ý chốt đơn</span>
                  </button>
                </div>
              </template>
              <template v-else>
                <span class="exdoc-action-hint">
                  {{ isDepositReported
                    ? 'JPM đã tiếp nhận xác nhận chuyển khoản và đang đối soát tài khoản Sacombank để lên đơn.'
                    : 'Bạn đã chốt đơn. Vui lòng quét mã VietQR phía trên để chuyển khoản đặt cọc và gửi xác nhận.' }}
                </span>
                <div class="exdoc-action-btns">
                  <button
                    v-if="!isDepositReported"
                    type="button"
                    class="btn exdoc-btn exdoc-btn-outline"
                    :disabled="isSubmitting"
                    @click="handleAction('reject')"
                  >
                    Hủy đơn
                  </button>
                  <a
                    :href="buildZaloWithMsg('Chào JPM Marine, tôi cần hỗ trợ đơn báo giá #' + quote.ma_yeu_cau)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn exdoc-btn exdoc-btn-outline"
                  >
                    <i class="bi bi-chat-dots"></i>
                    <span>Tư vấn Zalo</span>
                  </a>
                </div>
              </template>
            </template>

            <template v-else-if="quote.trang_thai === 2">
              <span class="exdoc-action-hint">Đơn hàng đã được xác nhận và đang chuẩn bị xuất kho.</span>
              <div class="exdoc-action-btns">
                <a
                  :href="buildZaloWithMsg('Chào JPM Marine, tôi muốn hỏi tiến độ đơn hàng #' + quote.ma_yeu_cau)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn exdoc-btn exdoc-btn-outline"
                >
                  <i class="bi bi-chat-dots"></i>
                  <span>Hỏi tiến độ qua Zalo</span>
                </a>
              </div>
            </template>

            <template v-else>
              <span class="exdoc-action-hint">Báo giá đã quá hạn hoặc đã đóng. Cần cập nhật đơn giá mới nhất?</span>
              <div class="exdoc-action-btns">
                <a
                  :href="buildZaloWithMsg('Chào JPM Marine, tôi muốn báo giá lại yêu cầu #' + quote.ma_yeu_cau)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn exdoc-btn exdoc-btn-outline"
                >
                  <i class="bi bi-chat-dots"></i>
                  <span>Liên hệ kỹ thuật</span>
                </a>
                <button
                  type="button"
                  class="btn exdoc-btn exdoc-btn-primary"
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

      <DepositReportModal
        :show="showDepositReportModal"
        :code="quote?.ma_yeu_cau || ''"
        :amount-formatted="formatCurrency(quote?.tien_coc_yeu_cau || quote?.tong_tien || 0)"
        :transfer-content="vietQrData?.transfer_content || (quote?.ma_yeu_cau ? `COC ${quote.ma_yeu_cau}` : '')"
        :submitting="isSubmitting"
        @close="showDepositReportModal = false"
        @submit="submitQuoteDepositReport"
      />
    </div>
  </div>
</template>

<script>
import axiosClient from '../../../api/axiosClient'
import Swal from 'sweetalert2'
import { useSiteSettings } from '../../../composables/useSiteSettings'
import { useAuthStore } from '../../../stores/authStore'
import { getImageUrl } from '../../../utils/imageUrl'
import DepositReportModal from '../../../components/client/DepositReportModal.vue'

export default {
  name: 'TraCuuBaoGia',
  components: { DepositReportModal },
  setup() {
    const { settings } = useSiteSettings()
    const authStore = useAuthStore()
    return { settings, authStore }
  },
  data() {
    return {
      token: this.$route.params.token,
      quote: null,
      loading: true,
      error: null,
      isSubmitting: false,
      showDepositReportModal: false,
      vietQrData: null,
      remainingSeconds: 0,
      countdownTimer: null
    }
  },
  watch: {
    '$route.params.token'(newToken) {
      if (newToken && newToken !== this.token) {
        this.token = newToken
        this.fetchQuote()
      }
    }
  },
  computed: {
    isLoggedIn() {
      return Boolean(this.authStore?.token)
    },
    siteHost() {
      return typeof window !== 'undefined' ? window.location.origin : 'https://tauthuyennhatban.com'
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
    isCustomerAccepted() {
      return Boolean(this.quote?.khach_da_chot) || Boolean(this.quote?.da_bao_chuyen_khoan) || Number(this.quote?.trang_thai) >= 2
    },
    isDepositReported() {
      return Boolean(this.quote?.da_bao_chuyen_khoan) && Number(this.quote?.da_thu || 0) <= 0 && Number(this.quote?.trang_thai) === 1
    },
    showDepositSection() {
      return (
        this.quote &&
        (this.quote.trang_thai === 1 || this.quote.trang_thai === 2) &&
        Number(this.quote.tien_coc_yeu_cau || 0) > 0 &&
        !this.quote.is_expired &&
        (this.isCustomerAccepted || this.isDepositReported)
      )
    },
    statusLabel() {
      if (this.isDepositReported && !this.quote?.is_expired) return 'Chờ đối soát cọc'
      if (this.quote?.trang_thai === 1 && !this.quote?.is_expired && this.isCustomerAccepted && Number(this.quote?.tien_coc_yeu_cau || 0) > 0) {
        return 'Chờ đặt cọc'
      }
      const map = {
        pending: 'Chờ báo giá',
        quoted: 'Đã báo giá',
        confirmed: 'Đã chốt đơn',
        expired: 'Hết hạn / Đã hủy'
      }
      return map[this.statusKey] || 'Chờ báo giá'
    },
    totalQuantity() {
      if (!this.quote?.chi_tiet) return 0
      return this.quote.chi_tiet.reduce((acc, item) => acc + Number(item.so_luong || 0), 0)
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
      const depositOrPaid = Math.max(
        Number(this.quote.da_thu || 0),
        Number(this.quote.tien_coc_yeu_cau || 0)
      )
      return Math.max(0, this.grandTotal - depositOrPaid)
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
    resolveImageUrl(path) {
      return getImageUrl(path)
    },
    getItemUnitPrice(item) {
      if (!item) return 0
      return Number(item.don_gia || item.gia_ban || 0)
    },
    formatCurrency(value) {
      if (value === null || value === undefined || value === '') return '0 đ'
      return `${new Intl.NumberFormat('vi-VN').format(Number(value))} đ`
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    formatSignatureDate(dateStr) {
      const d = dateStr ? new Date(dateStr) : new Date()
      const day = String(d.getDate()).padStart(2, '0')
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const year = d.getFullYear()
      return `Ngày ${day}, tháng ${month}, năm ${year}`
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
      this.error = null
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
      const confirmBtnColor = '#F82249'
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
            reverseButtons: true,
            confirmButtonColor: '#F82249',
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
        if (this.isCustomerAccepted) {
          confirmTitle = 'Hủy đơn hàng?'
          confirmMsg = 'Bạn chưa hoàn tất đặt cọc. Bạn chắc chắn muốn hủy đơn này?'
          confirmBtnText = 'Đồng ý hủy'
        } else {
          confirmTitle = 'Từ chối báo giá?'
          confirmMsg = 'Bạn chắc chắn muốn từ chối bảng báo giá này?'
          confirmBtnText = 'Từ chối'
        }
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
        reverseButtons: true,
        confirmButtonColor: confirmBtnColor,
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'Quay lại'
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
            await this.fetchQuote()
            await Swal.fire({ icon: 'success', title: 'Đã cập nhật', text: message })
            if (actionType === 'accept') {
              this.$nextTick(() => {
                this.$refs.vietQrSection?.scrollIntoView({ behavior: 'smooth', block: 'center' })
              })
            }
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
    },
    async submitQuoteDepositReport(file) {
      if (!this.quote || this.isSubmitting) return
      this.isSubmitting = true
      try {
        const formData = new FormData()
        formData.append('action', 'report_deposit')
        if (file) {
          formData.append('file', file)
        }
        const res = await axiosClient.post(`/public/yeu-cau-bao-gia/${this.token}/action`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        const message = res?.message ?? res?.data?.message
        this.showDepositReportModal = false
        await this.fetchQuote()
        Swal.fire({
          icon: 'success',
          title: 'Đã tiếp nhận thông báo cọc',
          text: message || 'JPM Marine đã tiếp nhận thông báo chuyển khoản và đang kiểm tra tài khoản Sacombank.'
        })
      } catch (err) {
        const msg = err.response?.data?.message || 'Không thể gửi thông báo chuyển khoản lúc này.'
        Swal.fire({ icon: 'error', title: 'Không thể thực hiện', text: msg })
      } finally {
        this.isSubmitting = false
      }
    }
  }
}
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

/* Toolbar */
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

/* Tờ chứng từ chính */
.exdoc-sheet {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 32px 38px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
}

.exdoc-error-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 14px;
  border-radius: 50%;
  background: #fef2f2;
  color: #dc2626;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

/* 1. Top Header: Logo trái + Thông tin công ty cạnh logo, Trạng thái góc phải */
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

.exdoc-status-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
  flex-shrink: 0;
}

.exdoc-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid #cbd5e1;
}

.exdoc-status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
}

.exdoc-status-badge.is-pending {
  background: #fffbeb;
  border-color: #fde68a;
  color: #b45309;
}
.exdoc-status-badge.is-pending .exdoc-status-dot { background: #d97706; }

.exdoc-status-badge.is-quoted {
  background: #eff6ff;
  border-color: #bfdbfe;
  color: #0369a1;
}
.exdoc-status-badge.is-quoted .exdoc-status-dot { background: #0284c7; }

.exdoc-status-badge.is-confirmed {
  background: #ecfdf5;
  border-color: #a7f3d0;
  color: #047857;
}
.exdoc-status-badge.is-confirmed .exdoc-status-dot { background: #059669; }

.exdoc-status-badge.is-expired {
  background: #fef2f2;
  border-color: #fecaca;
  color: #b91c1c;
}
.exdoc-status-badge.is-expired .exdoc-status-dot { background: #dc2626; }

.exdoc-countdown-text {
  font-size: 0.78rem;
  color: #475569;
  font-variant-numeric: tabular-nums;
}

/* 2. Tiêu đề chính giữa */
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

/* 3. Thông tin khách hàng gọn gàng, nằm trực tiếp trên trang (không bọc card) */
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
  gap: 6px;
}

.exdoc-field-label {
  color: #475569;
  font-weight: 600;
  white-space: nowrap;
}

.exdoc-field-value {
  color: #0f172a;
  font-weight: 500;
}

.exdoc-customer-name-val {
  font-size: 1.04rem;
  font-weight: 700;
  color: #0A1128;
}

/* Thanh mã báo giá ngay trên mép bảng */
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

/* 4. Bảng chi tiết phụ tùng gọn nét (không kẻ dọc nặng) */
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
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

/* 5. Cụm 2 cột dưới: Ghi chú trái + Tổng hợp chi phí phải */
.exdoc-bottom-grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 24px;
  align-items: start;
  margin-bottom: 22px;
}

.exdoc-notes-section {
  padding: 14px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
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

/* 6. VietQR tối giản */
.exdoc-vietqr-box {
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  margin-bottom: 24px;
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
  letter-spacing: 0.01em;
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

/* 7. Chữ ký chứng từ */
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

/* 8. Thanh nút thao tác */
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

.exdoc-btn,
.exdoc-btn-sm {
  border-radius: 6px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none;
}

.exdoc-btn {
  height: 38px;
  padding: 0 18px;
  font-size: 0.86rem;
}

.exdoc-btn-sm {
  height: 32px;
  padding: 0 12px;
  font-size: 0.79rem;
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

/* Responsive */
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
  .exdoc-status-wrap {
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
