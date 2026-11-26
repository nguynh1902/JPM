<template>
  <div class="lux-orders-wrapper">
    <!-- ========================================================================= -->
    <!-- THANH TAB LỌC TRẠNG THÁI (MINIMALIST LUXURY) -->
    <!-- ========================================================================= -->
    <div class="lux-tabs-bar" role="tablist">
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === 'all' }"
        @click="currentTab = 'all'"
      >
        Tất cả
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '1' }"
        @click="currentTab = '1'"
      >
        Đợi báo giá
        <span v-if="countByStage(1) > 0" class="lux-tab-count">{{ countByStage(1) }}</span>
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '2' }"
        @click="currentTab = '2'"
      >
        Báo giá &amp; Cọc
        <span v-if="countByStage(2) > 0" class="lux-tab-count">{{ countByStage(2) }}</span>
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '3' }"
        @click="currentTab = '3'"
      >
        Đang xử lý
        <span v-if="countByStage(3) > 0" class="lux-tab-count">{{ countByStage(3) }}</span>
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '4' }"
        @click="currentTab = '4'"
      >
        Đang giao
        <span v-if="countByStage(4) > 0" class="lux-tab-count">{{ countByStage(4) }}</span>
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '5' }"
        @click="currentTab = '5'"
      >
        Hoàn thành
      </button>
      <button
        type="button"
        class="lux-tab-btn"
        :class="{ active: currentTab === '-1' }"
        @click="currentTab = '-1'"
      >
        Đã hủy
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div class="d-flex flex-column gap-3 mt-3" v-if="isLoading">
      <div class="lux-order-card placeholder-glow p-4" v-for="i in 2" :key="i">
        <div class="d-flex justify-content-between mb-3">
          <span class="placeholder col-3 rounded bg-secondary"></span>
          <span class="placeholder col-2 rounded bg-secondary"></span>
        </div>
        <div class="d-flex gap-3">
          <div class="placeholder bg-secondary rounded" style="width: 56px; height: 56px;"></div>
          <div class="flex-grow-1">
            <span class="placeholder col-6 mb-2 rounded bg-secondary"></span><br>
            <span class="placeholder col-3 rounded bg-secondary"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div class="lux-empty-state text-center py-5" v-else-if="displayedOrders.length === 0">
      <div class="empty-icon-wrap mb-3">
        <i class="bi bi-receipt"></i>
      </div>
      <h5 class="mb-2 order-empty-heading">Chưa có đơn hàng &amp; báo giá</h5>
      <p class="text-muted mb-4 order-empty-desc">
        Bạn chưa có đơn đặt hàng hoặc yêu cầu báo giá nào trong mục này.
      </p>
      <router-link to="/san-pham" class="btn btn-danger px-4 d-inline-flex align-items-center gap-2">
        <i class="bi bi-cart3"></i>
        <span>Khám phá sản phẩm</span>
      </router-link>
    </div>

    <!-- DANH SÁCH THẺ ĐƠN HÀNG (GỌN GÀNG - ĐỦ NGHIỆP VỤ) -->
    <div class="d-flex flex-column gap-3 mt-3" v-else>
      <article
        v-for="order in displayedOrders"
        :key="order._type + order.id"
        class="lux-order-card"
      >
        <!-- 1. Header: Loại phiếu + Mã đơn | Ngày đặt bên trái | Trạng thái bên phải -->
        <div class="lux-card-head">
          <div class="lux-head-meta">
            <span class="lux-order-type">
              {{ order._type === 'quote' ? 'Phiếu báo giá:' : 'Đơn hàng:' }}
            </span>
            <span class="lux-order-code">#{{ order.code }}</span>
            <span class="lux-meta-divider" aria-hidden="true">|</span>
            <span class="lux-order-date">
              <i class="bi bi-calendar3 me-1"></i>Ngày đặt: <strong>{{ formatDate(order.created_at) }}</strong>
            </span>
          </div>

          <span class="lux-status-indicator" :class="'status-stage-' + getStage(order)">
            <span class="status-dot"></span>
            <span>{{ getStatusText(order) }}</span>
          </span>
        </div>

        <!-- 2. Sản phẩm đại diện (1 dòng phụ gọn ngang hàng: Mã | SL | +X phụ tùng khác) -->
        <div
          class="lux-card-body"
          role="button"
          tabindex="0"
          @click="openProductModal(order)"
          @keydown.enter="openProductModal(order)"
        >
          <div class="lux-product-row">
            <div class="lux-item-thumb">
              <img
                v-if="getItemProducts(order)[0]?.image"
                :src="getImageUrl(getItemProducts(order)[0].image)"
                :alt="getItemProducts(order)[0]?.name"
              >
              <i v-else class="bi bi-box-seam"></i>
            </div>

            <div class="lux-item-info">
              <div class="lux-item-name">
                {{ getItemProducts(order)[0]?.name || 'Phụ tùng máy thủy' }}
              </div>
              <div class="lux-item-sub">
                <span class="lux-sub-label">Mã:</span>
                <span class="lux-sku-code">{{ getItemProducts(order)[0]?.code || 'N/A' }}</span>
                <span class="lux-meta-divider" aria-hidden="true">|</span>
                <span class="lux-sub-label">SL:</span>
                <strong class="lux-sub-qty">{{ getItemProducts(order)[0]?.qty || 1 }}</strong>
                <template v-if="getItemProducts(order).length > 1">
                  <span class="lux-meta-divider" aria-hidden="true">|</span>
                  <span class="lux-more-items-inline">
                    +{{ getItemProducts(order).length - 1 }} phụ tùng khác
                  </span>
                </template>
              </div>
            </div>

            <div class="lux-item-price">
              <span v-if="getItemProducts(order)[0]?.price > 0" class="lux-item-unit-price">
                {{ formatPrice(getItemProducts(order)[0].price) }} ₫
              </span>
              <span v-else class="lux-price-pending">Chờ báo giá</span>
            </div>
          </div>
        </div>

        <!-- 3. Khung VietQR cọc (chỉ bung ra khi khách bấm Quét QR cọc ở bước 2) -->
        <div v-if="activeQrOrderCode === order.code" class="qr-deposit-box">
          <div v-if="isLoadingQr" class="text-center py-4 text-muted small">
            <span class="spinner-border spinner-border-sm text-danger me-2"></span>
            Đang tải thông tin thanh toán VietQR từ hệ thống...
          </div>
          <div v-else-if="vietQrError || !vietQrData?.qr_url" class="text-center py-4">
            <p class="text-danger small mb-2 fw-semibold">
              <i class="bi bi-exclamation-triangle me-1"></i>
              {{ vietQrError || 'Không thể tải mã VietQR lúc này. Vui lòng thử lại hoặc liên hệ Zalo kỹ thuật.' }}
            </p>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="loadVietQR(order)">
              <i class="bi bi-arrow-clockwise me-1"></i> Thử tải lại mã QR
            </button>
          </div>
          <div v-else class="qr-inner">
            <div class="text-center qr-img-col">
              <img :src="vietQrData.qr_url" alt="VietQR" class="img-fluid rounded border p-1 bg-white" style="max-width: 175px;">
              <div class="mt-2 text-muted small d-flex align-items-center justify-content-center gap-1">
                <span class="spinner-grow spinner-grow-sm text-danger"></span>
                <span>Đang chờ chuyển khoản...</span>
              </div>
            </div>
            <div class="qr-info-col">
              <div class="info-row">
                <span class="text-muted">Ngân hàng</span>
                <strong>{{ `${vietQrData.bank_name} (${vietQrData.bank_id})` }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tài khoản</span>
                <div>
                  <strong class="font-monospace">{{ vietQrData.account_no }}</strong>
                  <button type="button" class="btn-copy" @click="copyText(vietQrData.account_no, 'Đã copy STK')">Copy</button>
                </div>
              </div>
              <div class="info-row">
                <span class="text-muted">Chủ tài khoản</span>
                <strong>{{ vietQrData.account_name }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tiền cọc</span>
                <strong class="text-danger">{{ vietQrData.amount_formatted || (formatPrice(order.tien_coc_yeu_cau || 0) + '₫') }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Nội dung CK</span>
                <div>
                  <strong class="font-monospace text-danger">{{ vietQrData.transfer_content || order.code }}</strong>
                  <button type="button" class="btn-copy" @click="copyText(vietQrData.transfer_content || order.code, 'Đã copy nội dung')">Copy</button>
                </div>
              </div>
              <div class="mt-3 text-end">
                <button type="button" class="btn btn-sm btn-outline-success" @click="confirmManualPaid(order)">
                  <i class="bi bi-check-lg me-1"></i> Tôi đã chuyển khoản cọc
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Dải Tổng Kết Tài Chính Mảnh 1 Dòng (Căn phải gọn gàng, không hiện PII khách ở ngoài) -->
        <div class="lux-summary-strip">
          <div class="lux-summary-right">
            <div class="lux-fin-inline">
              <div class="lux-fin-item">
                <span class="lux-metric-label">Vận chuyển:</span>
                <span class="lux-metric-val">
                  <template v-if="getStage(order) === 1 && Number(order.phi_van_chuyen || 0) <= 0">
                    <span class="lux-metric-status">Chờ báo giá</span>
                  </template>
                  <template v-else-if="order.khach_tu_tra_cuoc"><span class="lux-metric-status">Khách tự trả chành xe</span></template>
                  <template v-else-if="Number(order.phi_van_chuyen) > 0"><span class="lux-metric-status">{{ formatPrice(order.phi_van_chuyen) }} ₫</span></template>
                  <template v-else><span class="text-success fw-semibold">Miễn phí</span></template>
                </span>
              </div>

              <div class="lux-fin-item" v-if="getDepositRequired(order) > 0">
                <span class="lux-meta-divider" aria-hidden="true">|</span>
                <span class="lux-metric-label">Tiền cọc:</span>
                <span class="lux-metric-val">
                  <strong class="text-navy">{{ formatPrice(getDepositRequired(order)) }} ₫</strong>
                  <span
                    class="lux-deposit-state"
                    :class="getDepositStatus(order) === 'paid' ? 'is-paid' : 'is-unpaid'"
                  >
                    {{ getDepositStatus(order) === 'paid' ? 'Đã cọc' : 'Chưa cọc' }}
                  </span>
                </span>
              </div>

              <div class="lux-fin-item">
                <span class="lux-meta-divider" aria-hidden="true">|</span>
                <span class="lux-total-label">
                  {{ getGrandTotal(order) !== null && getGrandTotal(order) > 0 ? 'Tổng đơn hàng:' : 'Tạm tính:' }}
                </span>
                <span class="lux-total-val">
                  <template v-if="getGrandTotal(order) !== null && getGrandTotal(order) > 0">
                    <strong class="lux-price-value">{{ formatPrice(getGrandTotal(order)) }} ₫</strong>
                  </template>
                  <template v-else-if="getItemSubtotal(order) > 0">
                    <strong class="lux-price-value">{{ formatPrice(getItemSubtotal(order)) }} ₫</strong>
                  </template>
                  <template v-else>
                    <span class="lux-metric-status">Chờ báo giá</span>
                  </template>
                </span>
              </div>

              <div
                v-if="getGrandTotal(order) > 0 && (getStage(order) >= 2 || getDepositRequired(order) > 0)"
                class="lux-fin-item"
              >
                <span class="lux-meta-divider" aria-hidden="true">|</span>
                <span class="lux-metric-label">Còn lại (COD):</span>
                <strong class="text-danger tabular-nums">{{ formatPrice(getRemainingAmount(order)) }} ₫</strong>
              </div>
            </div>

            <div
              v-if="getGrandTotal(order) === null && getItemSubtotal(order) > 0 && getUnquotedCount(order) > 0"
              class="lux-metric-note"
            >
              (+{{ getUnquotedCount(order) }} món chờ tư vấn giá)
            </div>
          </div>
        </div>

        <!-- 5. Footer: Ghi chú ngắn bên trái | Tối đa 2-3 nút gọn bên phải -->
        <div class="lux-card-footer">
          <div class="lux-footer-note">
            {{ getProcessNote(order) }}
          </div>

          <div class="lux-footer-actions">
            <button
              type="button"
              class="btn btn-outline-secondary lux-action-btn"
              @click="openProductModal(order)"
            >
              Xem chi tiết ({{ getItemProducts(order).length }})
            </button>

            <!-- Chỉ hiện link Bảng báo giá khi báo giá ĐÃ được gửi giá chính thức (giai đoạn >= 2) -->
            <router-link
              v-if="order.token && getStage(order) >= 2"
              :to="'/bao-gia/' + order.token"
              class="btn btn-outline-secondary lux-action-btn"
            >
              Bảng báo giá
            </router-link>

            <!-- CTA chính theo từng giai đoạn -->
            <template v-if="getStage(order) === 1">
              <a
                :href="buildZaloUrl('Chào JPM Marine, tôi muốn hỏi về đơn mã #' + order.code)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-danger lux-action-btn"
              >
                <i class="bi bi-chat-dots me-1"></i> Liên hệ kỹ thuật
              </a>
            </template>

            <template v-else-if="getStage(order) === 2">
              <button
                v-if="order._type === 'quote' && order.token"
                type="button"
                class="btn btn-outline-secondary lux-action-btn"
                @click="handleRejectQuote(order)"
              >
                Từ chối
              </button>
              <button
                v-if="getDepositRequired(order) > 0"
                type="button"
                class="btn btn-danger lux-action-btn"
                @click="toggleVietQR(order)"
              >
                <i class="bi bi-qr-scan me-1"></i>
                {{ activeQrOrderCode === order.code ? 'Đóng mã QR' : `Quét QR cọc (${formatPrice(getDepositRequired(order))}₫)` }}
              </button>
              <button
                v-else-if="order._type === 'quote' && order.token"
                type="button"
                class="btn btn-danger lux-action-btn"
                @click="handleAcceptQuote(order)"
              >
                <i class="bi bi-check2-circle me-1"></i> Đồng ý chốt đơn
              </button>
            </template>

            <template v-else-if="getStage(order) === 3">
              <a
                :href="buildZaloUrl('Hỏi tiến độ chuẩn bị hàng cho đơn #' + order.code)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-outline-secondary lux-action-btn"
              >
                Hỏi tiến độ
              </a>
            </template>

            <template v-else-if="getStage(order) === 4">
              <a
                :href="buildZaloUrl('Hỏi tiến độ giao hàng cho đơn #' + order.code)"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-danger lux-action-btn"
              >
                <i class="bi bi-truck me-1"></i> Theo dõi giao hàng
              </a>
            </template>

            <template v-else-if="getStage(order) === 5">
              <button
                type="button"
                class="btn btn-danger lux-action-btn"
                @click="handleReorderAll(order)"
              >
                Mua lại đơn này
              </button>
            </template>
          </div>
        </div>
      </article>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL CHI TIẾT ĐƠN HÀNG 2 CỘT (TRÁI: SẢN PHẨM | PHẢI: KHÁCH & THANH TOÁN) -->
    <!-- ========================================================================= -->
    <div v-if="selectedOrderForModal" class="modal-backdrop fade show" style="z-index: 1050;" @click="closeProductModal"></div>
    <div v-if="selectedOrderForModal" class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" @click.self="closeProductModal">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content lux-modal-content">
          <!-- Header: Trạng thái nổi bật + Loại đơn & Mã đơn & Ngày đặt -->
          <div class="lux-modal-header">
            <div class="d-flex align-items-center flex-wrap gap-2">
              <span class="lux-status-indicator" :class="'status-stage-' + getStage(selectedOrderForModal)">
                <span class="status-dot"></span>
                <span>{{ getStatusText(selectedOrderForModal) }}</span>
              </span>
              <span class="lux-order-type">
                {{ selectedOrderForModal._type === 'quote' ? 'Phiếu báo giá:' : 'Đơn hàng:' }}
              </span>
              <span class="lux-order-code">#{{ selectedOrderForModal.code }}</span>
              <span class="lux-meta-divider" aria-hidden="true">|</span>
              <span class="lux-order-date">
                <i class="bi bi-calendar3 me-1"></i>{{ formatDate(selectedOrderForModal.created_at) }}
              </span>
            </div>
            <button type="button" class="btn-close" aria-label="Đóng" @click="closeProductModal"></button>
          </div>

          <!-- Nội dung 2 cột trên nền slate dịu: Trái (Sản phẩm) | Phải (Thông tin nhận hàng + Thanh toán) -->
          <div class="row g-3 lux-modal-split">
            <!-- CỘT TRÁI: CHI TIẾT ĐƠN HÀNG (DANH SÁCH SẢN PHẨM CUỘN ĐỘC LẬP + TICK CHỌN) -->
            <div class="col-lg-7 lux-modal-col-main">
              <div class="lux-info-card p-0 d-flex flex-column h-100 overflow-hidden">
                <div class="lux-card-head-sub d-flex align-items-center justify-content-between gap-2 flex-wrap">
                  <div class="lux-info-heading mb-0">
                    Chi tiết đơn ({{ modalProducts.length }} sản phẩm)
                  </div>

                  <label class="lux-select-all-label">
                    <input
                      type="checkbox"
                      class="form-check-input lux-check-input m-0"
                      :checked="isAllModalItemsSelected"
                      :disabled="modalProducts.length === 0"
                      @change="toggleSelectAllModalItems"
                    />
                    <span>Chọn tất cả</span>
                  </label>
                </div>

                <div v-if="modalProducts.length > 4" class="lux-modal-search-bar">
                  <div class="lux-modal-search w-100">
                    <i class="bi bi-search"></i>
                    <input
                      v-model.trim="modalSearchQuery"
                      type="text"
                      placeholder="Tìm theo tên hoặc mã sản phẩm..."
                    />
                  </div>
                </div>

                <div class="lux-modal-body">
                  <div v-if="filteredModalProducts.length === 0" class="text-center py-5 text-muted small">
                    Không tìm thấy sản phẩm phù hợp.
                  </div>

                  <div
                    v-for="prod in filteredModalProducts"
                    :key="prod._uid"
                    class="lux-modal-item"
                    :class="{ 'is-selected': selectedModalItemIds.includes(prod._uid) }"
                    @click="toggleModalItemSelection(prod._uid)"
                  >
                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                      <input
                        type="checkbox"
                        class="form-check-input lux-check-input m-0 flex-shrink-0"
                        :value="prod._uid"
                        v-model="selectedModalItemIds"
                        :aria-label="'Chọn ' + prod.name"
                        @click.stop
                      />

                      <div class="lux-modal-thumb">
                        <img v-if="prod.image" :src="getImageUrl(prod.image)" :alt="prod.name">
                        <i v-else class="bi bi-box-seam"></i>
                      </div>

                      <div class="min-w-0 flex-grow-1">
                        <div
                          class="lux-modal-prod-name"
                          title="Xem chi tiết sản phẩm"
                          @click.stop="goToProductDetail(prod)"
                        >
                          <span class="text-truncate">{{ prod.name }}</span>
                          <i class="bi bi-arrow-up-right lux-ext-icon flex-shrink-0"></i>
                        </div>
                        <div class="lux-item-sub mt-1">
                          <span class="lux-sub-label">Mã:</span>
                          <span class="lux-sku-code">{{ prod.code || 'N/A' }}</span>
                          <span class="lux-meta-divider" aria-hidden="true">|</span>
                          <span class="lux-sub-label">SL:</span>
                          <strong class="lux-sub-qty">{{ prod.qty }}</strong>
                        </div>
                      </div>
                    </div>

                    <div class="lux-modal-item-right">
                      <span v-if="prod.price > 0" class="lux-item-unit-price">
                        {{ formatPrice(prod.price) }} ₫
                      </span>
                      <span v-else class="lux-price-pending">Chờ báo giá</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CỘT PHẢI: THÔNG TIN KHÁCH HÀNG & CHI TIẾT THANH TOÁN -->
            <div class="col-lg-5 lux-modal-col-side">
              <!-- Khối 1: Thông tin người nhận -->
              <div class="lux-info-card p-0 overflow-hidden">
                <div class="lux-card-head-sub">
                  <div class="lux-info-heading mb-0">Thông tin nhận hàng</div>
                </div>

                <div class="lux-info-body">
                  <div class="lux-info-list">
                    <div class="lux-info-row">
                      <span class="lux-info-label">Người nhận</span>
                      <span class="lux-info-val">{{ getOrderCustomerName(selectedOrderForModal) }}</span>
                    </div>
                    <div class="lux-info-row">
                      <span class="lux-info-label">Điện thoại</span>
                      <span class="lux-info-val">{{ getOrderCustomerPhone(selectedOrderForModal) }}</span>
                    </div>
                    <div class="lux-info-row" v-if="getOrderCustomerEmail(selectedOrderForModal)">
                      <span class="lux-info-label">Email</span>
                      <span class="lux-info-val text-break">{{ getOrderCustomerEmail(selectedOrderForModal) }}</span>
                    </div>
                    <div class="lux-info-row">
                      <span class="lux-info-label">Địa chỉ</span>
                      <span class="lux-info-val">{{ getOrderAddress(selectedOrderForModal) || 'Chưa cập nhật' }}</span>
                    </div>
                  </div>

                  <div class="lux-note-box mt-3" v-if="selectedOrderForModal.ghi_chu_khach_hang">
                    <span class="lux-note-label">Ghi chú:</span>
                    <span class="lux-note-text">{{ selectedOrderForModal.ghi_chu_khach_hang }}</span>
                  </div>
                </div>
              </div>

              <!-- Khối 2: Tổng kết tài chính -->
              <div class="lux-modal-summary p-0 overflow-hidden mb-0">
                <div class="lux-card-head-sub">
                  <div class="lux-info-heading mb-0">Chi tiết thanh toán</div>
                </div>

                <div class="lux-info-body pt-2">
                  <div class="lux-sum-row align-items-start">
                  <span class="text-muted">Tạm tính tiền hàng</span>
                  <div class="text-end">
                    <template v-if="getItemSubtotal(selectedOrderForModal) > 0">
                      <div class="fw-semibold text-navy tabular-nums">
                        {{ formatPrice(getItemSubtotal(selectedOrderForModal)) }} ₫
                      </div>
                      <div
                        v-if="getUnquotedCount(selectedOrderForModal) > 0 && getStage(selectedOrderForModal) === 1"
                        class="lux-metric-note mt-1"
                      >
                        (+{{ getUnquotedCount(selectedOrderForModal) }} món chờ giá)
                      </div>
                    </template>
                    <span v-else class="lux-metric-muted">Chờ báo giá</span>
                  </div>
                </div>

                <div class="lux-sum-row">
                  <span class="text-muted">Cước vận chuyển</span>
                  <span class="fw-medium text-navy text-end">
                    <template v-if="getStage(selectedOrderForModal) === 1 && Number(selectedOrderForModal.phi_van_chuyen || 0) <= 0">
                      <span class="lux-metric-muted">Chờ báo giá</span>
                    </template>
                    <template v-else-if="selectedOrderForModal.khach_tu_tra_cuoc">Khách tự trả chành xe</template>
                    <template v-else-if="Number(selectedOrderForModal.phi_van_chuyen) > 0">{{ formatPrice(selectedOrderForModal.phi_van_chuyen) }} ₫</template>
                    <template v-else><span class="text-success">Miễn phí</span></template>
                  </span>
                </div>

                <div class="lux-sum-row" v-if="getDepositRequired(selectedOrderForModal) > 0">
                  <span class="text-muted">Tiền cọc yêu cầu</span>
                  <span class="fw-semibold text-navy text-end">
                    {{ formatPrice(getDepositRequired(selectedOrderForModal)) }} ₫
                    <span
                      class="lux-deposit-state ms-1"
                      :class="getDepositStatus(selectedOrderForModal) === 'paid' ? 'is-paid' : 'is-unpaid'"
                    >
                      {{ getDepositStatus(selectedOrderForModal) === 'paid' ? 'Đã cọc' : 'Chưa cọc' }}
                    </span>
                  </span>
                </div>

                <div class="lux-sum-row lux-sum-total">
                  <span>
                    {{ getGrandTotal(selectedOrderForModal) > 0 && (getStage(selectedOrderForModal) >= 2 || getDepositRequired(selectedOrderForModal) > 0) ? 'Còn lại thanh toán (COD)' : 'Tổng giá trị đơn hàng' }}
                  </span>
                  <span class="lux-total-num text-end">
                    <template v-if="getGrandTotal(selectedOrderForModal) !== null && getGrandTotal(selectedOrderForModal) > 0">
                      {{ formatPrice(getStage(selectedOrderForModal) >= 2 || getDepositRequired(selectedOrderForModal) > 0 ? getRemainingAmount(selectedOrderForModal) : getGrandTotal(selectedOrderForModal)) }} ₫
                    </template>
                    <template v-else>
                      <span class="lux-metric-muted fw-normal">Chờ hoàn tất báo giá</span>
                    </template>
                  </span>
                </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer: Nút Bảng báo giá bên trái | Đóng & nút Thêm vào giỏ (chỉ hiện khi đã tick chọn) bên phải -->
          <div class="lux-modal-footer" v-if="selectedOrderForModal">
            <div class="lux-modal-actions">
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <router-link
                  v-if="selectedOrderForModal.token && getStage(selectedOrderForModal) >= 2"
                  :to="'/bao-gia/' + selectedOrderForModal.token"
                  class="btn btn-outline-secondary lux-action-btn"
                  @click="closeProductModal"
                >
                  Bảng báo giá
                </router-link>
                <span v-if="selectedModalItemIds.length > 0" class="text-muted small fw-medium">
                  Đã chọn <strong class="text-navy">{{ selectedModalItemIds.length }}</strong> sản phẩm
                </span>
              </div>

              <div class="d-flex align-items-center gap-2 ms-auto">
                <button type="button" class="btn btn-outline-secondary lux-action-btn px-4" @click="closeProductModal">
                  Đóng
                </button>
                <button
                  v-if="selectedModalItemIds.length > 0"
                  type="button"
                  class="btn btn-danger lux-action-btn"
                  @click="handleAddSelectedToCart"
                >
                  <i class="bi bi-cart-plus me-1"></i>
                  Thêm vào giỏ ({{ selectedModalItemIds.length }})
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount, watch, inject } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/authStore";
import { useCartStore } from "../../../stores/cartStore";
import Swal from "sweetalert2";
import { createToaster } from "@meforma/vue-toaster";
import axiosClient from "../../../api/axiosClient";
import { useQuery, useQueryClient } from "@tanstack/vue-query";
import { useSiteSettings } from "../../../composables/useSiteSettings";
import { getImageUrl } from "../../../utils/imageUrl";

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const queryClient = useQueryClient();
const toaster = createToaster({ position: "top-right", duration: 2500 });
const { settings } = useSiteSettings();

const buildZaloUrl = (text = '') => {
  const base = (settings.value?.social_zalo || '').trim() || `https://zalo.me/${String(settings.value?.company_hotline || '').replace(/[^0-9]/g, '')}`;
  if (!text) return base;
  const sep = base.includes('?') ? '&' : '?';
  return `${base}${sep}text=${encodeURIComponent(text)}`;
};

// ===================== DATA FETCHING =====================
const { data: quotesData, isLoading: isLoadingQuotes } = useQuery({
  queryKey: ["clientQuotesHistory"],
  queryFn: async () => await axiosClient.get("/client/history/quotes"),
  staleTime: 30 * 1000,
});
const quotes = computed(() => quotesData.value || []);

const { data: ordersData, isLoading: isLoadingOrders } = useQuery({
  queryKey: ["clientOrdersHistory"],
  queryFn: async () => await axiosClient.get("/client/history/orders"),
  staleTime: 30 * 1000,
});
const orders = computed(() => ordersData.value || []);

const isLoading = computed(() => isLoadingQuotes.value || isLoadingOrders.value);
// Chuyển sang dùng inline skeleton có sẵn trong trang, không kích hoạt preloader toàn trang gây giật

// ===================== TAB STATE (SHOPEE STYLE) =====================
const currentTab = ref('all'); // 'all' | '1' | '2' | '3' | '4' | '-1'
const activeQrOrderCode = ref(null);
const vietQrData = ref(null);
const isLoadingQr = ref(false);
const vietQrError = ref('');
const pollingTimer = ref(null);
const selectedOrderForModal = ref(null);

const allOrders = computed(() => {
  const orderQuoteCodes = new Set();
  (orders.value || []).forEach(o => {
    if (o.ghi_chu_khach_hang) {
      const m = o.ghi_chu_khach_hang.match(/YCBG-[^\s|]+/);
      if (m) orderQuoteCodes.add(m[0]);
    }
  });

  const qList = (quotes.value || [])
    .filter(q => {
      // Khi báo giá đã chuyển thành đơn hàng xử lý (status >= 2), chỉ hiển thị đơn hàng để tránh trùng
      if (Number(q.trang_thai) >= 2 && orderQuoteCodes.has(q.ma_yeu_cau)) {
        return false;
      }
      return true;
    })
    .map(q => ({
      ...q,
      _type: 'quote',
      code: q.ma_yeu_cau,
      sortDate: new Date(q.created_at).getTime()
    }));

  const oList = (orders.value || []).map(o => ({
    ...o,
    _type: 'order',
    code: o.ma_don_hang,
    sortDate: new Date(o.created_at).getTime()
  }));

  return [...qList, ...oList].sort((a, b) => b.sortDate - a.sortDate);
});

const displayedOrders = computed(() => {
  if (currentTab.value === 'all') return allOrders.value;
  return allOrders.value.filter(order => String(getStage(order)) === currentTab.value);
});

const countByStage = (stageNum) => {
  return allOrders.value.filter(o => getStage(o) === stageNum).length;
};

// ===================== LOGIC TÍNH BƯỚC =====================
const getStage = (order) => {
  if (!order) return 1;

  if (order._type === 'quote') {
    const st = Number(order.trang_thai);
    if (st === 0) return 1; // 1. Đợi Báo Giá
    if (st === 1) return 2; // 2. Báo Giá & Cọc
    if (st === 2) return 3; // 3. Đang Xử Lý / Gom hàng
    if (st === 3) return -1; // Đã Hủy
    if (st === 4) return 5; // 5. Hoàn Thành
    return 1;
  } else {
    const st = Number(order.trang_thai);
    const paySt = Number(order.trang_thai_thanh_toan);
    const daThu = Number(order.da_thu || 0);
    const depositReq = Number(order.tien_coc_yeu_cau || 0);

    if (st === 4) return -1; // Đã Hủy
    if (st === 3) return 5;  // 5. Hoàn Thành
    if (st === 2) return 4;  // 4. Đang Giao Hàng
    if (st === 1) return 3;  // 3. Đang Xử Lý / Gom Hàng

    if (paySt >= 1 || (depositReq > 0 && daThu >= depositReq)) {
      return 3;
    }
    if (depositReq > 0 && daThu === 0) {
      return 2;
    }
    if (st === 0 && paySt === 0) {
      return 2; // Chờ thanh toán / xác nhận
    }
    return 3;
  }
};

// Phân loại đơn ở Giai đoạn 1 (Chờ báo giá)
// 1 = Toàn bộ sản phẩm có giá
// 2 = Vừa có giá vừa liên hệ (hỗn hợp)
// 3 = Toàn bộ sản phẩm liên hệ
const getOrderPricingType = (order) => {
  const products = getItemProducts(order);
  if (products.length === 0) return 3;
  const pricedCount = products.filter(p => p.price > 0).length;
  if (pricedCount === products.length) return 1; // 100% có giá
  if (pricedCount > 0) return 2; // Hỗn hợp
  return 3; // 100% liên hệ
};

const getUnquotedCount = (order) => {
  return getItemProducts(order).filter(p => p.price <= 0).length;
};

const getPricedSubtotal = (order) => {
  return getItemProducts(order).filter(p => p.price > 0).reduce((sum, p) => sum + (p.price * p.qty), 0);
};

const getStatusText = (order) => {
  if (order._type === 'order') {
    const st = Number(order.trang_thai);
    const paySt = Number(order.trang_thai_thanh_toan);
    if (st === 4) return 'Đã hủy';
    if (st === 3) return 'Hoàn thành';
    if (st === 2) return 'Đang giao hàng';
    if (st === 1) return 'Đang xử lý';
    if (st === 0) return paySt === 0 ? 'Chờ đặt cọc' : 'Đã thanh toán';
  }
  const stage = getStage(order);
  switch (stage) {
    case 1:
      return 'Chờ báo giá';
    case 2:
      return 'Chờ đặt cọc';
    case 3:
      return 'Đang xử lý';
    case 4:
      return 'Đang giao hàng';
    case 5:
      return 'Hoàn thành';
    default:
      return 'Đã hủy';
  }
};

const getProcessNote = (order) => {
  const stage = getStage(order);
  switch (stage) {
    case 1:
      return 'JPM Marine đang kiểm tra giá phụ tùng & cước vận chuyển.';
    case 2:
      return 'Báo giá hiệu lực 72 giờ. Vui lòng đặt cọc để tiến hành xuất kho hoặc đặt hàng Nhật.';
    case 3:
      return getLogisticsStatus(order);
    case 4:
      return getLogisticsStatus(order);
    case 5:
      return 'Đơn hàng đã hoàn thành. Cảm ơn quý khách đã tin dùng phụ tùng JPM Marine.';
    default:
      return 'Yêu cầu đã đóng hoặc hết thời hạn hiệu lực.';
  }
};

const getOrderAddress = (order) => {
  if (!order) return '';
  const raw = order._type === 'quote'
    ? (order.dia_chi_giao_hang || '')
    : (order.dia_chi?.dia_chi_cu_the || order.dia_chi_text || order.dia_chi_giao_hang || '');
  return String(raw).replace(/\s*,\s*/g, ', ').replace(/\s+/g, ' ').trim();
};

const getOrderCustomerName = (order) => {
  if (!order) return 'Chưa cập nhật';
  return order.khach_hang_ten || order.khach_hang?.ho_ten || authStore.user?.name || 'Chưa cập nhật';
};

const getOrderCustomerPhone = (order) => {
  if (!order) return '-';
  return order.khach_hang_sdt || order.khach_hang?.so_dien_thoai || authStore.user?.so_dien_thoai || '-';
};

const getOrderCustomerEmail = (order) => {
  if (!order) return '';
  return order.khach_hang_email || order.khach_hang?.email || '';
};

const getLogisticsStatus = (order) => {
  const st = Number(order.trang_thai);
  if (st === 2) {
    const carrier = order.ten_don_vi_van_chuyen ? `qua ${order.ten_don_vi_van_chuyen}` : 'cho quý khách';
    const tracking = order.ma_van_don ? ` (Mã vận đơn: ${order.ma_van_don})` : '';
    return `Đang giao hàng ${carrier}${tracking}`;
  }
  if (st === 1) return 'Đang chuẩn bị & gom phụ tùng đóng kiện xuất kho';
  return 'Đã nhận cọc & đang điều phối hàng';
};

const getItemProducts = (order) => {
  if (!order) return [];
  const list = order.items || order.chi_tiet || [];
  return list.map((p, idx) => ({
    _uid: `${p.id || idx}-${idx}`,
    id: p.id_san_pham || p.id_phu_tung || p.san_pham_id || p.phu_tung_id || p.id,
    loai_san_pham: p.loai_san_pham || (p.slug || p.san_pham?.slug ? 'san_pham' : 'phu_tung'),
    name: p.ten_phu_tung || p.ten_san_pham || 'Phụ tùng máy thủy',
    code: p.ma_phu_tung || p.ma_san_pham || '',
    slug: p.slug || p.san_pham?.slug || '',
    image: p.hinh_anh || p.hinh_anh_chinh || p.san_pham?.hinh_anh_chinh || p.san_pham?.hinh_anh || null,
    qty: p.so_luong || 1,
    price: Number(p.don_gia || p.gia_ban || 0)
  }));
};

const hasUnquotedItems = (order) => {
  if (!order) return false;
  return getItemProducts(order).some(p => p.price <= 0);
};

// 1. Tiền hàng (Tạm tính)
const getItemSubtotal = (order) => {
  if (!order) return 0;
  const products = getItemProducts(order);
  const pricedSum = products.filter(p => p.price > 0).reduce((sum, p) => sum + (p.price * p.qty), 0);
  if (pricedSum > 0) return pricedSum;
  return Number(order.tong_tien || 0);
};

// 2. Cước vận chuyển
const getShippingFee = (order) => {
  if (!order) return 0;
  if (order.khach_tu_tra_cuoc) return 0;
  return Number(order.phi_van_chuyen || 0);
};

// 3. Tổng giá trị toàn bộ đơn hàng
const getGrandTotal = (order) => {
  if (!order) return 0;
  // Nếu là báo giá đang ở bước 1 và có món chưa định giá -> chưa có tổng
  if (getStage(order) === 1 && getOrderPricingType(order) !== 1) {
    return null;
  }
  const subtotal = getItemSubtotal(order);
  const ship = order.khach_tu_tra_cuoc ? 0 : Number(order.phi_van_chuyen || 0);
  return subtotal + ship;
};

// 4. Số tiền thực tế ĐÃ THU (chỉ lấy da_thu, KHÔNG lấy tien_coc_yeu_cau khi khách chưa cọc)
const getRealPaidAmount = (order) => {
  if (!order) return 0;
  if (order._type === 'quote') return 0; // Báo giá chưa thu tiền
  return Number(order.da_thu || 0);
};

// 5. Số tiền cọc yêu cầu
const getDepositRequired = (order) => {
  if (!order) return 0;
  return Number(order.tien_coc_yeu_cau || 0);
};

// 6. Tình trạng cọc: 'paid' (Đã cọc), 'unpaid' (Chưa cọc), 'waiting' (Chờ báo giá), 'none' (Không yêu cầu)
const getDepositStatus = (order) => {
  if (!order) return 'none';
  const dep = getDepositRequired(order);
  if (dep <= 0) {
    return getStage(order) === 1 ? 'waiting' : 'none';
  }
  const paid = getRealPaidAmount(order);
  if (paid >= dep) return 'paid';
  return 'unpaid';
};

// 7. Số tiền còn lại phải thanh toán khi nhận hàng
const getRemainingAmount = (order) => {
  if (!order) return 0;
  const grand = getGrandTotal(order);
  if (!grand || grand <= 0) return 0;
  const paid = getRealPaidAmount(order);
  return Math.max(0, grand - paid);
};

const calculateItemTotal = (order) => {
  if (!order) return 0;
  const total = getGrandTotal(order);
  if (total !== null) return total;
  return getItemSubtotal(order);
};

const calculateRemaining = (order) => {
  return getRemainingAmount(order);
};

// ===================== MODAL SẢN PHẨM & NAVIGATION =====================
const selectedModalItemIds = ref([]);
const modalSearchQuery = ref('');

const modalProducts = computed(() => getItemProducts(selectedOrderForModal.value));

const filteredModalProducts = computed(() => {
  const q = modalSearchQuery.value.toLowerCase();
  if (!q) return modalProducts.value;
  return modalProducts.value.filter(p =>
    (p.name || '').toLowerCase().includes(q) || (p.code || '').toLowerCase().includes(q)
  );
});

const isAllModalItemsSelected = computed(() => {
  return modalProducts.value.length > 0 && selectedModalItemIds.value.length === modalProducts.value.length;
});

const toggleSelectAllModalItems = () => {
  if (isAllModalItemsSelected.value) {
    selectedModalItemIds.value = [];
  } else {
    selectedModalItemIds.value = modalProducts.value.map(p => p._uid);
  }
};

const toggleModalItemSelection = (uid) => {
  const idx = selectedModalItemIds.value.indexOf(uid);
  if (idx >= 0) {
    selectedModalItemIds.value.splice(idx, 1);
  } else {
    selectedModalItemIds.value.push(uid);
  }
};

const openProductModal = (order) => {
  selectedOrderForModal.value = order;
  selectedModalItemIds.value = [];
  modalSearchQuery.value = '';
};

const closeProductModal = () => {
  selectedOrderForModal.value = null;
  selectedModalItemIds.value = [];
  modalSearchQuery.value = '';
};

const handleAddSelectedToCart = () => {
  const chosen = modalProducts.value.filter(p => selectedModalItemIds.value.includes(p._uid));
  if (!chosen.length) return;
  chosen.forEach((prod) => {
    cartStore.addItem({
      id: prod.id,
      loai_san_pham: prod.loai_san_pham || 'phu_tung',
      slug: prod.slug || null,
      ma_phu_tung: prod.code,
      ten_phu_tung: prod.name,
      hinh_anh: prod.image,
      gia_ban: prod.price > 0 ? prod.price : 0,
      is_gia_lien_he: prod.price <= 0,
    }, prod.qty || 1);
  });
  selectedModalItemIds.value = [];
  toaster.success(
    chosen.length === 1
      ? `Đã thêm "${chosen[0].name}" vào giỏ hàng`
      : `Đã thêm ${chosen.length} sản phẩm vào giỏ hàng`
  );
};

const goToProductDetail = (prod) => {
  closeProductModal();
  if (prod.slug) {
    router.push(`/chi-tiet-san-pham/${prod.slug}`);
  } else if (prod.code) {
    router.push(`/chi-tiet-phu-tung/${prod.code}`);
  } else if (prod.id) {
    router.push(`/chi-tiet-phu-tung/${prod.id}`);
  }
};

const handleAddToCart = (prod) => {
  cartStore.addItem({
    id: prod.id,
    loai_san_pham: prod.loai_san_pham || 'phu_tung',
    slug: prod.slug || null,
    ma_phu_tung: prod.code,
    ten_phu_tung: prod.name,
    hinh_anh: prod.image,
    gia_ban: prod.price > 0 ? prod.price : 0,
    is_gia_lien_he: prod.price <= 0
  }, prod.qty || 1);

  toaster.success(`Đã thêm "${prod.name}" vào giỏ hàng`);
};

const handleReorderAll = (order) => {
  const prods = getItemProducts(order);
  if (!prods.length) return;
  prods.forEach((prod) => {
    cartStore.addItem({
      id: prod.id,
      loai_san_pham: prod.loai_san_pham || 'phu_tung',
      slug: prod.slug || null,
      ma_phu_tung: prod.code,
      ten_phu_tung: prod.name,
      hinh_anh: prod.image,
      gia_ban: prod.price > 0 ? prod.price : 0,
      is_gia_lien_he: prod.price <= 0,
    }, prod.qty || 1);
  });
  closeProductModal();
  toaster.success(`Đã thêm ${prods.length} phụ tùng vào giỏ hàng`);
};

// ===================== VIETQR & ACTIONS =====================
const loadVietQR = async (order) => {
  vietQrData.value = null;
  vietQrError.value = '';
  isLoadingQr.value = true;
  try {
    const endpoint = order._type === 'quote'
      ? `/public/vietqr/bao-gia/${order.code}`
      : `/public/vietqr/don-hang/${order.code}`;
    const res = await axiosClient.get(endpoint);
    const payload = res?.qr_url ? res : res?.data;
    if (payload?.qr_url && payload?.account_no) {
      vietQrData.value = payload;
    } else {
      vietQrError.value = 'Hệ thống chưa cấu hình số tài khoản VietQR. Vui lòng liên hệ Zalo JPM Marine.';
    }
  } catch (e) {
    vietQrError.value = 'Không thể kết nối máy chủ để lấy mã VietQR. Vui lòng thử lại.';
  } finally {
    isLoadingQr.value = false;
  }
};

const toggleVietQR = async (order) => {
  if (activeQrOrderCode.value === order.code) {
    activeQrOrderCode.value = null;
    vietQrData.value = null;
    vietQrError.value = '';
  } else {
    activeQrOrderCode.value = order.code;
    await loadVietQR(order);
  }
};

const copyText = (text, msg) => {
  if (!text) return;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text);
  }
  toaster.success(msg || 'Đã sao chép!');
};

const confirmManualPaid = async (order) => {
  const depositAmt = Number(order.tien_coc_yeu_cau || 0);
  const transferContent = vietQrData.value?.transfer_content || ('COC ' + order.code);
  
  const result = await Swal.fire({
    title: 'Xác nhận đã chuyển khoản cọc?',
    html: `Bạn đã chuyển cọc số tiền <b>${formatPrice(depositAmt)}₫</b> với nội dung <code>${transferContent}</code>?<br><small class="text-muted">Kỹ thuật viên JPM sẽ kiểm tra thông báo ngân hàng và cập nhật đơn ngay.</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Tôi đã chuyển cọc',
    cancelButtonText: 'Đóng'
  });

  if (result.isConfirmed) {
    if (order._type === 'quote' && order.token) {
      try {
        await axiosClient.post(`/public/yeu-cau-bao-gia/${order.token}/action`, { action: 'accept' });
      } catch (_) {}
    }

    activeQrOrderCode.value = null;
    vietQrData.value = null;

    const msg = `Chào JPM Marine, tôi vừa chuyển cọc ${formatPrice(depositAmt)}₫ cho đơn #${order.code}. Nhờ kỹ thuật viên kiểm tra ngân hàng và lên đơn giúp tôi.`;

    Swal.fire({
      icon: 'success',
      title: 'Đã tiếp nhận thông tin cọc',
      text: `JPM Marine đã ghi nhận thông báo chuyển cọc cho đơn #${order.code}. Kỹ thuật viên sẽ đối soát ngân hàng và cập nhật tiến độ ngay.`,
      showCancelButton: true,
      confirmButtonText: '<i class="bi bi-chat-dots me-1"></i> Gửi biên lai qua Zalo',
      cancelButtonText: 'Đóng'
    }).then((res) => {
      if (res.isConfirmed) {
        window.open(buildZaloUrl(msg), '_blank', 'noopener,noreferrer');
      }
    });

    queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
    queryClient.invalidateQueries({ queryKey: ["clientOrdersHistory"] });
  }
};

const handleAcceptQuote = async (order) => {
  const result = await Swal.fire({
    title: 'Đồng ý chốt đơn?',
    text: 'Bạn đồng ý với mức báo giá này và chốt đặt hàng?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý chốt đơn',
    cancelButtonText: 'Đóng'
  });

  if (result.isConfirmed && order.token) {
    try {
      await axiosClient.post(`/public/yeu-cau-bao-gia/${order.token}/action`, { action: 'accept' });
      queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
      queryClient.invalidateQueries({ queryKey: ["clientOrdersHistory"] });
      Swal.fire({ icon: 'success', title: 'Đã chốt đơn thành công!' });
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Lỗi', text: e.response?.data?.message || 'Không thể chốt đơn lúc này.' });
    }
  }
};

const handleRejectQuote = async (order) => {
  const result = await Swal.fire({
    title: 'Từ Chối Báo Giá?',
    text: 'Bạn chắc chắn muốn từ chối báo giá này?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Từ chối',
    cancelButtonText: 'Đóng',
    confirmButtonColor: '#f82249'
  });

  if (result.isConfirmed && order.token) {
    try {
      await axiosClient.post(`/public/yeu-cau-bao-gia/${order.token}/action`, { action: 'reject' });
      queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
      Swal.fire({ icon: 'success', title: 'Đã từ chối đơn' });
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể từ chối lúc này.' });
    }
  }
};

const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('vi-VN').format(price);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<style scoped>
.lux-orders-wrapper {
  width: 100%;
  font-family: var(--default-font, 'Inter', sans-serif);
}

.text-navy {
  color: var(--jpm-color-navy-dark, #0A1128);
}

.tabular-nums {
  font-variant-numeric: tabular-nums;
}

/* ========================================================================= */
/* 1. THANH TAB LỌC TRẠNG THÁI (SẠCH, CHỈ 1 TAB ACTIVE CÓ MÀU ĐỎ) */
/* ========================================================================= */
.lux-tabs-bar {
  display: flex;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04);
  overflow-x: auto;
  white-space: nowrap;
}

.lux-tab-btn {
  flex: 1;
  min-width: 108px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 15px 10px;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  transition: color 0.18s ease, border-color 0.18s ease;
}

.lux-tab-btn:hover {
  color: var(--jpm-color-navy-dark, #0A1128);
}

.lux-tab-btn.active {
  color: var(--jpm-color-red-marine, #F82249);
  border-bottom-color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
}

.lux-tab-count {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: var(--jpm-radius-badge, 6px);
  background-color: #f1f5f9;
  color: #475569;
  line-height: 1.4;
}

.lux-tab-btn.active .lux-tab-count {
  background-color: rgba(248, 34, 73, 0.1);
  color: var(--jpm-color-red-marine, #F82249);
}

/* ========================================================================= */
/* 2. EMPTY STATE */
/* ========================================================================= */
.lux-empty-state {
  background-color: #ffffff;
  min-height: 360px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 16px;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04);
}

.empty-icon-wrap {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  font-size: 1.75rem;
}

.order-empty-heading {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.order-empty-desc {
  font-size: 0.875rem;
  max-width: 420px;
  line-height: 1.6;
}

/* ========================================================================= */
/* 3. ORDER CARD (MINIMALIST LUXURY - CHUẨN DESIGN SYSTEM 2026) */
/* ========================================================================= */
.lux-order-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04);
  overflow: hidden;
  transition: border-color 0.2s ease;
}

.lux-order-card:hover {
  border-color: #cbd5e1;
}

/* Header 1 dòng gọn: Loại đơn + Mã đơn | Ngày đặt bên trái | Trạng thái bên phải */
.lux-card-head {
  padding: 13px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  border-bottom: 1px solid #f1f5f9;
}

.lux-head-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 0;
  flex-wrap: wrap;
}

.lux-order-type {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--jpm-neutral-500, #64748B);
  flex-shrink: 0;
}

.lux-order-code {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-variant-numeric: tabular-nums;
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  letter-spacing: 0.1px;
  flex-shrink: 0;
}

.lux-meta-divider {
  color: #cbd5e1;
  font-size: 0.78rem;
  user-select: none;
  margin: 0 2px;
  flex-shrink: 0;
}

.lux-meta-dot {
  color: #cbd5e1;
  user-select: none;
  margin: 0;
  flex-shrink: 0;
}

.lux-order-date {
  font-size: 0.8125rem;
  color: var(--jpm-neutral-500, #64748B);
  font-variant-numeric: tabular-nums;
  flex-shrink: 0;
}

.lux-order-date strong {
  color: var(--jpm-neutral-700, #334155);
  font-weight: 600;
}

/* Body: Sản phẩm đại diện căn giữa trục ngang 100% */
.lux-card-body {
  padding: 16px 20px;
  cursor: pointer;
  transition: background-color 0.18s ease;
}

.lux-card-body:hover {
  background-color: #fafbfc;
}

.lux-product-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.lux-item-thumb {
  width: 58px;
  height: 58px;
  background-color: #ffffff;
  border: 1px solid var(--jpm-neutral-200, #E2E8F0);
  border-radius: var(--jpm-radius-control, 8px);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 1.35rem;
  flex-shrink: 0;
  overflow: hidden;
  padding: 4px;
}

.lux-item-thumb img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.lux-item-info {
  flex: 1;
  min-width: 0;
}

.lux-item-name {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--jpm-color-navy-dark, #0A1128);
  line-height: 1.35;
}

.lux-item-sub {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
  font-size: 0.8125rem;
  color: var(--jpm-neutral-500, #64748B);
  margin-top: 4px;
}

.lux-sub-label {
  color: var(--jpm-neutral-500, #64748B);
  font-weight: 400;
}

/* Mã sản phẩm chữ xanh đồng bộ 100% với ProductCard, VatTuThuongMua, GarageChiTietMay */
.lux-sku-code {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-variant-numeric: tabular-nums;
  color: var(--jpm-color-blue-electric, #1D4ED8);
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.1px;
}

.lux-sub-qty {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 600;
  font-variant-numeric: tabular-nums;
}

.lux-more-items-inline {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.8125rem;
  font-weight: 600;
}

.lux-item-price {
  text-align: right;
  flex-shrink: 0;
  padding-left: 12px;
}

/* Đơn giá sản phẩm đại diện: Deep Navy điềm đạm, nhường màu Đỏ cho Tổng tạm tính */
.lux-item-unit-price {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  font-variant-numeric: tabular-nums;
}

/* Giá Tổng / Tạm tính của đơn: Điểm nhấn tài chính màu Đỏ Marine */
.lux-price-value {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--jpm-color-red-marine, #F82249);
  font-variant-numeric: tabular-nums;
}

.lux-price-pending {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--jpm-neutral-500, #64748B);
}

/* ========================================================================= */
/* 4. DẢI TỔNG KẾT TÀI CHÍNH MỎNG 1 DÒNG (CĂN PHẢI GỌN GÀNG)                 */
/* ========================================================================= */
.lux-summary-strip {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 11px 20px;
  background-color: #f8fafc;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}

.lux-summary-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  gap: 2px;
  width: 100%;
  text-align: right;
}

.lux-fin-inline {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  justify-content: flex-end;
  gap: 6px 10px;
}

.lux-fin-item {
  display: inline-flex;
  align-items: baseline;
  gap: 6px;
  font-size: 0.825rem;
}

.lux-metric-label {
  color: var(--jpm-neutral-500, #64748B);
  font-weight: 400;
}

.lux-metric-val {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-variant-numeric: tabular-nums;
  display: inline-flex;
  align-items: baseline;
  gap: 6px;
}

.lux-total-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--jpm-neutral-700, #334155);
}

.lux-total-val {
  display: inline-flex;
  align-items: baseline;
}

.lux-metric-note {
  font-size: 0.76rem;
  color: var(--jpm-neutral-500, #64748B);
  font-style: italic;
  font-weight: 400;
}

.lux-metric-status {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-size: 0.825rem;
  font-weight: 600;
}

@media (max-width: 767.98px) {
  .lux-fin-inline {
    justify-content: flex-start;
  }
  .lux-summary-right {
    align-items: flex-start;
    text-align: left;
  }
}

.lux-deposit-state {
  font-size: 0.72rem;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: var(--jpm-radius-badge, 6px);
}
.lux-deposit-state.is-paid {
  background: #dcfce7;
  color: #15803d;
}
.lux-deposit-state.is-unpaid {
  background: #ffe4e6;
  color: #be123c;
}

/* ========================================================================= */
/* 5. FOOTER THẺ ĐƠN: GHI CHÚ TRÁI — HÀNH ĐỘNG PHẢI */
/* ========================================================================= */
.lux-card-footer {
  padding: 13px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background-color: #ffffff;
}

.lux-footer-note {
  font-size: 0.8rem;
  color: #64748b;
  max-width: 48%;
  line-height: 1.45;
}

.lux-footer-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-left: auto;
}

.lux-action-btn {
  height: 38px;
  padding: 0 16px !important;
  font-size: 0.84rem !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}

@media (max-width: 768px) {
  .lux-footer-note {
    max-width: 100%;
  }
  .lux-footer-actions {
    width: 100%;
    justify-content: flex-end;
  }
}

/* ========================================================================= */
/* 6. VIETQR DEPOSIT BOX */
/* ========================================================================= */
.qr-deposit-box {
  padding: 16px 20px;
  background-color: #fffdfd;
  border-top: 1px dashed rgba(248, 34, 73, 0.25);
}

.qr-inner {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.qr-img-col {
  width: 175px;
}

.qr-info-col {
  flex: 1;
  min-width: 260px;
  background-color: #f8fafc;
  padding: 14px 16px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #e2e8f0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.82rem;
  padding: 4px 0;
  border-bottom: 1px solid #f1f5f9;
}

.info-row:last-of-type {
  border-bottom: none;
}

.btn-copy {
  margin-left: 8px;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #334155;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-copy:hover {
  background-color: #f1f5f9;
}

/* ========================================================================= */
/* 7. MODAL CHI TIẾT ĐƠN HÀNG 2 CỘT (MINIMALIST LUXURY) */
/* ========================================================================= */
.lux-modal-content {
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  box-shadow: 0 24px 60px rgba(10, 17, 40, 0.16);
  overflow: hidden;
  background: #ffffff;
}

.lux-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #ffffff;
}

.lux-modal-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0a1128;
  letter-spacing: -0.01em;
}

.lux-modal-split {
  background: #f8fafc;
  padding: 18px 20px;
}

.lux-modal-col-main {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.lux-modal-col-side {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.lux-info-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
}

.lux-card-head-sub {
  padding: 12px 16px;
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9;
}

.lux-info-heading {
  font-family: var(--heading-font, 'Inter', sans-serif);
  font-size: 0.9rem;
  font-weight: 700;
  text-transform: none;
  letter-spacing: -0.01em;
  color: var(--jpm-color-navy-dark, #0A1128);
}

.lux-info-body {
  padding: 14px 16px;
}

.lux-info-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.lux-info-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  font-size: 0.84rem;
  line-height: 1.45;
}

.lux-info-label {
  color: #64748b;
  flex-shrink: 0;
  min-width: 78px;
}

.lux-info-val {
  color: #0a1128;
  font-weight: 600;
  text-align: right;
  word-break: break-word;
}

.lux-note-box {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: var(--jpm-radius-badge, 6px);
  padding: 8px 10px;
  font-size: 0.8125rem;
  line-height: 1.45;
  text-align: left;
  word-break: break-word;
}

.lux-note-label {
  color: #64748b;
  font-weight: 600;
  margin-right: 6px;
}

.lux-note-text {
  color: #475569;
}

.lux-modal-search-bar {
  padding: 10px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.lux-select-all-label {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.8125rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  user-select: none;
  margin: 0;
}

.lux-check-input {
  width: 17px;
  height: 17px;
  border-radius: 5px;
  border-color: #cbd5e1;
  cursor: pointer;
  box-shadow: none !important;
}

.lux-check-input:checked {
  background-color: var(--jpm-color-red-marine, #f82249);
  border-color: var(--jpm-color-red-marine, #f82249);
}

.lux-modal-search {
  position: relative;
  display: flex;
  align-items: center;
  min-width: 190px;
}

.lux-modal-search i {
  position: absolute;
  left: 10px;
  font-size: 0.8rem;
  color: #94a3b8;
  pointer-events: none;
}

.lux-modal-search input {
  width: 100%;
  height: 32px;
  padding: 0 10px 0 30px;
  font-size: 0.8125rem;
  border: 1px solid #cbd5e1;
  border-radius: var(--jpm-radius-control, 8px);
  background: #ffffff;
  color: #0a1128;
  outline: none;
}

.lux-modal-search input:focus {
  border-color: var(--jpm-color-blue-electric, #1D4ED8);
}

.lux-modal-body {
  max-height: 410px;
  overflow-y: auto;
  flex: 1;
}

.lux-modal-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 13px 16px;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.lux-modal-item:hover {
  background-color: #fafbfc;
}

.lux-modal-item.is-selected {
  background-color: rgba(248, 34, 73, 0.03);
}

.lux-modal-thumb {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 1.2rem;
  flex-shrink: 0;
  overflow: hidden;
}

.lux-modal-thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 3px;
}

.lux-modal-prod-name {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  max-width: 100%;
  font-size: 0.9rem;
  font-weight: 600;
  color: #0a1128;
  transition: color 0.15s ease;
}

.lux-modal-prod-name:hover {
  color: var(--jpm-color-blue-electric, #1D4ED8);
}

.lux-ext-icon {
  font-size: 0.72rem;
  color: #94a3b8;
}

.lux-modal-item-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.lux-modal-footer {
  padding: 14px 24px;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}

.lux-modal-summary {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  padding: 14px 16px;
}

@media (max-width: 991.98px) {
  .lux-modal-split {
    padding: 14px;
  }
}

.lux-sum-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  font-size: 0.84rem;
  padding: 5px 0;
}

.lux-sum-total {
  margin-top: 6px;
  padding-top: 10px;
  border-top: 1px solid #f1f5f9;
  font-weight: 700;
  color: #0a1128;
}

.lux-total-num {
  font-size: 1rem;
  font-weight: 700;
  color: var(--jpm-color-red-marine, #f82249);
  font-variant-numeric: tabular-nums;
}

.lux-modal-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
}
</style>
