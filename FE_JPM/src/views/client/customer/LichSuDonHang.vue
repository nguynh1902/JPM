<template>
  <div class="shopee-orders-wrapper">
    
    <!-- ========================================================================= -->
    <!-- THANH TAB ĐƠN HÀNG CHUẨN SHOPEE (FLAT, MINIMALIST, KHÔNG MÀU MÈ) -->
    <!-- ========================================================================= -->
    <div class="shopee-tabs-container">
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === 'all' }" 
        @click="currentTab = 'all'"
      >
        Tất cả
      </div>
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === '1' }" 
        @click="currentTab = '1'"
      >
        Đợi báo giá <span v-if="countByStage(1) > 0" class="tab-badge">({{ countByStage(1) }})</span>
      </div>
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === '2' }" 
        @click="currentTab = '2'"
      >
        Báo giá & Cọc <span v-if="countByStage(2) > 0" class="tab-badge">({{ countByStage(2) }})</span>
      </div>
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === '3' }" 
        @click="currentTab = '3'"
      >
        Vận chuyển <span v-if="countByStage(3) > 0" class="tab-badge">({{ countByStage(3) }})</span>
      </div>
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === '4' }" 
        @click="currentTab = '4'"
      >
        Hoàn thành
      </div>
      <div 
        class="shopee-tab-item" 
        :class="{ active: currentTab === '-1' }" 
        @click="currentTab = '-1'"
      >
        Đã hủy
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- NỘI DUNG ĐƠN HÀNG DẠNG THẺ ĐƠN CHUẨN SHOPEE -->
    <!-- ========================================================================= -->

    <!-- Loading Skeleton -->
    <div class="d-flex flex-column gap-3 mt-3" v-if="isLoading">
      <div class="shopee-order-card placeholder-glow p-4 bg-white" v-for="i in 2" :key="i">
        <div class="placeholder col-4 rounded bg-secondary mb-3"></div>
        <div class="d-flex gap-3">
          <div class="placeholder bg-secondary rounded" style="width: 80px; height: 80px;"></div>
          <div class="flex-grow-1">
            <span class="placeholder col-6 mb-2 rounded bg-secondary"></span><br>
            <span class="placeholder col-3 rounded bg-secondary"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State (Giống hệt ảnh Shopee) -->
    <div class="shopee-empty-state" v-else-if="displayedOrders.length === 0">
      <div class="empty-icon-box">
        <svg class="empty-clipboard-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="46" fill="#F4F8FA"/>
          <circle cx="28" cy="30" r="4" fill="#E8B86D" opacity="0.6"/>
          <circle cx="74" cy="48" r="3" fill="#E8B86D" opacity="0.6"/>
          <circle cx="34" cy="22" r="5" fill="#88D4D0" opacity="0.5"/>
          <rect x="30" y="24" width="40" height="52" rx="4" fill="#FFFFFF" stroke="#CCD5DD" stroke-width="2"/>
          <rect x="42" y="20" width="16" height="7" rx="3" fill="#5EAAAC"/>
          <line x1="38" y1="36" x2="62" y2="36" stroke="#E1E6EB" stroke-width="2.5" stroke-linecap="round"/>
          <line x1="38" y1="44" x2="58" y2="44" stroke="#E1E6EB" stroke-width="2.5" stroke-linecap="round"/>
          <line x1="38" y1="52" x2="62" y2="52" stroke="#E1E6EB" stroke-width="2.5" stroke-linecap="round"/>
          <line x1="38" y1="60" x2="52" y2="60" stroke="#E1E6EB" stroke-width="2.5" stroke-linecap="round"/>
          <path d="M60 68 L68 56 L72 60 L64 72 Z" fill="#4B9DA0"/>
          <path d="M58 70 L60 68 L64 72 L60 74 Z" fill="#E59866"/>
        </svg>
      </div>
      <p class="empty-text">Chưa có đơn hàng</p>
    </div>

    <!-- DANH SÁCH THẺ ĐƠN HÀNG -->
    <div class="d-flex flex-column gap-3 mt-3" v-else>
      <div 
        v-for="order in displayedOrders" 
        :key="order._type + order.id" 
        class="shopee-order-card"
      >
        <!-- Card Header: Tag + Mã đơn + Trạng thái -->
        <div class="card-head">
          <div class="head-left flex-wrap gap-2">
            <span class="type-tag" :class="order._type === 'quote' ? 'quote-tag' : 'order-tag'">
              {{ order._type === 'quote' ? 'BÁO GIÁ' : 'ĐƠN HÀNG' }}
            </span>
            <span class="order-id">#{{ order.code }}</span>
            <span class="order-date text-muted">{{ formatDate(order.created_at) }}</span>

            <!-- Tag phân loại hàng rõ ràng -->
            <template v-if="getStage(order) === 1">
              <span v-if="getOrderPricingType(order) === 1" class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle fw-medium" style="font-size: 11px;">
                <i class="bi bi-tag-fill me-1"></i> Có sẵn giá
              </span>
              <span v-else-if="getOrderPricingType(order) === 2" class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-medium" style="font-size: 11px;">
                <i class="bi bi-layers-half me-1"></i> Đơn hỗn hợp
              </span>
              <span v-else class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle fw-medium" style="font-size: 11px;">
                <i class="bi bi-chat-quote me-1"></i> Cần tra thông số
              </span>
            </template>
          </div>

          <div class="head-right">
            <span class="status-badge-pill" :class="'stage-status-' + getStage(order) + (getStage(order) === 1 ? '-p' + getOrderPricingType(order) : '')">
              <i v-if="getStage(order) === 1 && getOrderPricingType(order) === 1" class="bi bi-clock-history me-1"></i>
              <i v-else-if="getStage(order) === 1 && getOrderPricingType(order) === 2" class="bi bi-hourglass-split me-1"></i>
              <i v-else-if="getStage(order) === 1" class="bi bi-search me-1"></i>
              <i v-else-if="getStage(order) === 2" class="bi bi-qr-scan me-1"></i>
              <i v-else-if="getStage(order) === 3" class="bi bi-truck me-1"></i>
              <i v-else-if="getStage(order) === 4" class="bi bi-check-circle me-1"></i>
              <i v-else class="bi bi-x-circle me-1"></i>
              {{ getStatusText(order) }}
            </span>
          </div>
        </div>

        <!-- Banner thông báo vận chuyển (Khi ở bước 3) -->
        <div class="logistics-notice" v-if="getStage(order) === 3">
          <i class="bi bi-truck text-danger me-2"></i>
          <span>{{ getLogisticsStatus(order) }}</span>
        </div>

        <!-- THẺ ĐƠN TÓM TẮT: KÍCH VÀO MỞ MODAL XEM TẤT CẢ SẢN PHẨM -->
        <div 
          class="card-summary-row cursor-pointer" 
          @click="openProductModal(order)"
          title="Nhấp để xem danh sách chi tiết các sản phẩm trong đơn"
        >
          <div class="item-thumb rounded-3 overflow-hidden border">
            <img v-if="getItemProducts(order)[0]?.image" :src="getImageUrl(getItemProducts(order)[0].image)" :alt="getItemProducts(order)[0]?.name" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
            <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
              <i class="bi bi-box-seam text-secondary fs-4"></i>
            </div>
          </div>
          
          <div class="item-details">
            <div class="item-name fw-bold">
              {{ getItemProducts(order)[0]?.name || 'Phụ tùng máy thủy' }}
            </div>
            <div class="item-oem text-muted mt-1">
              <span>Mã OEM: <code class="text-secondary">{{ getItemProducts(order)[0]?.code || 'N/A' }}</code></span>
            </div>
            <div class="item-qty mt-1">
              Số lượng: <strong>x{{ getItemProducts(order)[0]?.qty || 1 }}</strong>
              <span v-if="getItemProducts(order).length > 1" class="text-danger ms-2 fw-medium">
                (+{{ getItemProducts(order).length - 1 }} phụ tùng khác)
              </span>
            </div>
          </div>

          <!-- Đơn giá: Hiển thị giá nếu có, nếu chưa có giá thì hiển thị 'Chờ tra giá' -->
          <div class="item-price text-end">
            <template v-if="getItemProducts(order)[0]?.price > 0">
              <span class="price-num">{{ formatPrice(getItemProducts(order)[0]?.price) }}₫</span>
            </template>
            <template v-else>
              <span class="badge bg-light text-secondary border fw-medium px-2 py-1">Chờ tra giá</span>
            </template>

            <div class="view-detail-hint mt-1">
              <span>Xem chi tiết</span>
              <i class="bi bi-chevron-right ms-1"></i>
            </div>
          </div>
        </div>

        <!-- Khung VietQR cọc nếu bấm mở (Ở bước 2) -->
        <div v-if="activeQrOrderCode === order.code" class="qr-deposit-box">
          <div class="qr-inner">
            <div class="text-center qr-img-col">
              <img :src="vietQrData?.qr_url || getVietQrImageUrl(order)" alt="VietQR" class="img-fluid rounded border p-1" style="max-width: 190px;">
              <div class="mt-2 text-muted small d-flex align-items-center justify-content-center gap-1">
                <span class="spinner-grow spinner-grow-sm text-danger me-1"></span>
                <span>Chờ thanh toán...</span>
              </div>
            </div>
            <div class="qr-info-col">
              <div class="info-row">
                <span class="text-muted">Ngân hàng:</span>
                <strong>{{ vietQrData ? `${vietQrData.bank_name} (${vietQrData.bank_id})` : 'VietinBank (ICB)' }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tài khoản:</span>
                <div>
                  <strong class="font-monospace">{{ vietQrData?.account_no || '108876543210' }}</strong>
                  <button type="button" class="btn-copy" @click="copyText(vietQrData?.account_no || '108876543210', 'Đã copy STK')">Copy</button>
                </div>
              </div>
              <div class="info-row">
                <span class="text-muted">Chủ tài khoản:</span>
                <strong>{{ vietQrData?.account_name || 'CONG TY PHU TUNG MAY THUY JPM' }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tiền cọc:</span>
                <strong class="text-danger fs-6">{{ vietQrData?.amount_formatted || (formatPrice(order.tien_coc_yeu_cau || 0) + '₫') }}</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Nội dung CK:</span>
                <div>
                  <strong class="font-monospace text-danger">{{ vietQrData?.transfer_content || order.code }}</strong>
                  <button type="button" class="btn-copy" @click="copyText(vietQrData?.transfer_content || order.code, 'Đã copy nội dung')">Copy</button>
                </div>
              </div>
              <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-success" @click="confirmManualPaid(order)">
                  <i class="bi bi-check-lg me-1"></i> Tôi đã chuyển khoản cọc
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- BẢNG PHÂN TÍCH TÀI CHÍNH & VẬN CHUYỂN CHUẨN DOANH NGHIỆP -->
        <div class="order-finance-card">
          <div class="finance-grid">
            <!-- 1. Tiền hàng tạm tính -->
            <div class="finance-col">
              <span class="finance-label">Tiền hàng (Tạm tính)</span>
              <div class="finance-value">
                <template v-if="getItemSubtotal(order) > 0">
                  <span class="fw-bold text-dark">{{ formatPrice(getItemSubtotal(order)) }}₫</span>
                  <span v-if="getOrderPricingType(order) === 2 && getStage(order) === 1" class="d-block text-warning small" style="font-size: 11px;">
                    (+ {{ getUnquotedCount(order) }} món chờ tra giá)
                  </span>
                </template>
                <template v-else>
                  <span class="badge bg-light text-secondary border">Đang định giá</span>
                </template>
              </div>
            </div>

            <!-- 2. Cước chành xe / vận chuyển -->
            <div class="finance-col">
              <span class="finance-label">Cước vận chuyển</span>
              <div class="finance-value">
                <template v-if="order.khach_tu_tra_cuoc">
                  <span class="badge bg-light text-dark border fw-medium" title="Khách tự thanh toán cước với nhà xe khi nhận hàng">
                    <i class="bi bi-truck me-1 text-primary"></i> Khách tự trả chành xe
                  </span>
                </template>
                <template v-else-if="Number(order.phi_van_chuyen) > 0">
                  <span class="fw-bold text-dark">{{ formatPrice(order.phi_van_chuyen) }}₫</span>
                  <span class="text-muted d-block small" style="font-size: 11px;">(Giao tận nơi/chành)</span>
                </template>
                <template v-else-if="getStage(order) === 1">
                  <span class="text-muted small fst-italic">Chờ Admin liên hệ xe</span>
                </template>
                <template v-else>
                  <span class="badge bg-success-subtle text-success border border-success-subtle">Miễn phí ship</span>
                </template>
              </div>
            </div>

            <!-- 3. Tổng giá trị đơn hàng -->
            <div class="finance-col">
              <span class="finance-label">Tổng đơn hàng</span>
              <div class="finance-value">
                <template v-if="getGrandTotal(order) !== null && getGrandTotal(order) > 0">
                  <span class="fw-bold text-danger fs-6">{{ formatPrice(getGrandTotal(order)) }}₫</span>
                  <span class="text-muted d-block small" style="font-size: 11px;">(Đã gồm tiền hàng + ship)</span>
                </template>
                <template v-else>
                  <span class="badge bg-light text-secondary border">Chờ hoàn tất giá</span>
                </template>
              </div>
            </div>

            <!-- 4. Tiền cọc yêu cầu & Tình trạng cọc -->
            <div class="finance-col finance-col-deposit">
              <span class="finance-label">Tiền cọc cam kết</span>
              <div class="finance-value">
                <template v-if="getDepositRequired(order) > 0">
                  <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-primary">{{ formatPrice(getDepositRequired(order)) }}₫</span>
                  </div>
                  <div class="mt-1">
                    <span v-if="getDepositStatus(order) === 'paid'" class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">
                      <i class="bi bi-check-circle-fill me-1"></i> Đã cọc {{ formatPrice(getRealPaidAmount(order)) }}₫
                    </span>
                    <span v-else class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 11px;">
                      <i class="bi bi-exclamation-circle-fill me-1"></i> Chưa thanh toán cọc
                    </span>
                  </div>
                </template>
                <template v-else-if="getStage(order) === 1">
                  <span class="text-muted small fst-italic">Admin đang ấn định mức cọc</span>
                </template>
                <template v-else>
                  <span class="text-muted small">Không yêu cầu cọc</span>
                </template>
              </div>
            </div>
          </div>

          <!-- DÒNG CÒN LẠI THANH TOÁN KHI NHẬN HÀNG (COD) -->
          <div v-if="getGrandTotal(order) > 0 && (getStage(order) >= 2 || getDepositRequired(order) > 0)" class="finance-remaining-bar">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2 text-secondary small">
                <i class="bi bi-shield-check text-success fs-6"></i>
                <span class="fw-medium">Còn lại thanh toán khi nhận hàng từ chành xe (COD):</span>
              </div>
              <div class="fs-6 fw-bold text-dark font-monospace">
                {{ formatPrice(getRemainingAmount(order)) }}₫
              </div>
            </div>
          </div>
        </div>

        <!-- Card Footer: Ghi chú quy trình & Nút thao tác -->
        <div class="card-bottom">
          <div class="process-hint-col">
            <!-- Thông điệp hướng dẫn theo giai đoạn -->
            <template v-if="getStage(order) === 1">
              <div v-if="getOrderPricingType(order) === 1" class="text-muted small d-flex align-items-center gap-1">
                <i class="bi bi-truck text-primary"></i>
                <span>Admin đang xác nhận cước vận chuyển (chành xe) & số tiền cọc cho quý khách.</span>
              </div>
              <div v-else-if="getOrderPricingType(order) === 2" class="text-muted small d-flex align-items-center gap-1">
                <i class="bi bi-hourglass-split text-warning"></i>
                <span>Kỹ thuật viên đang tra cứu giá phụ tùng đặc thù & cước chành xe liên tỉnh.</span>
              </div>
              <div v-else class="text-muted small d-flex align-items-center gap-1">
                <i class="bi bi-search text-secondary"></i>
                <span>Kỹ thuật viên đang đối soát thông số động cơ & tính cước chành xe.</span>
              </div>
            </template>

            <template v-else-if="getStage(order) === 2">
              <div class="text-danger small d-flex align-items-center gap-1 fw-medium">
                <i class="bi bi-clock-history"></i>
                <span>Báo giá hiệu lực 72 giờ. Quý khách vui lòng đặt cọc để hệ thống xuất kho hoặc đặt hàng từ Nhật.</span>
              </div>
            </template>

            <template v-else-if="getStage(order) === 3">
              <div class="text-primary small d-flex align-items-center gap-1 fw-medium">
                <i class="bi bi-box-seam"></i>
                <span>Đã ghi nhận cọc. Đang điều phối hàng và gửi qua chành xe liên tỉnh đến quý khách.</span>
              </div>
            </template>

            <template v-else-if="getStage(order) === 4">
              <div class="text-success small d-flex align-items-center gap-1 fw-medium">
                <i class="bi bi-check-circle-fill"></i>
                <span>Giao dịch hoàn tất thành công. Cảm ơn quý khách đã tin dùng phụ tùng JPM!</span>
              </div>
            </template>

            <template v-else>
              <div class="text-muted small d-flex align-items-center gap-1">
                <i class="bi bi-x-circle"></i>
                <span>Yêu cầu đã đóng hoặc hết thời hạn hiệu lực.</span>
              </div>
            </template>
          </div>

          <div class="action-buttons">
            <!-- Nút xem sản phẩm (Mở Modal) -->
            <button type="button" class="btn-shopee btn-shopee-outline" @click="openProductModal(order)">
              <i class="bi bi-list-ul me-1"></i> Xem Sản Phẩm ({{ getItemProducts(order).length }})
            </button>

            <!-- Nếu có token báo giá: Cho phép mở xem trang Báo giá chi tiết -->
            <router-link v-if="order.token" :to="'/bao-gia/' + order.token" class="btn-shopee btn-shopee-outline">
              <i class="bi bi-receipt me-1"></i> Bảng Báo Giá Chi Tiết
            </router-link>

            <!-- Bước 1: Liên hệ hỗ trợ -->
            <template v-if="getStage(order) === 1">
              <a :href="'https://zalo.me/0123456789?text=' + encodeURIComponent('Chào JPM, tôi muốn hỏi về đơn hàng mã #' + order.code)" target="_blank" class="btn-shopee btn-shopee-primary">
                <i class="bi bi-chat-dots me-1"></i> Chat Kỹ Thuật
              </a>
            </template>

            <!-- Bước 2: Từ chối hoặc Quét QR Cọc -->
            <template v-else-if="getStage(order) === 2">
              <button type="button" class="btn-shopee btn-shopee-outline text-danger" @click="handleRejectQuote(order)">
                Từ chối
              </button>
              <button 
                type="button" 
                class="btn-shopee btn-shopee-primary fw-bold"
                @click="toggleVietQR(order)"
              >
                <i class="bi bi-qr-scan me-1"></i> {{ activeQrOrderCode === order.code ? 'Đóng Mã QR' : `Quét QR Cọc (${formatPrice(getDepositRequired(order))}₫)` }}
              </button>
            </template>

            <!-- Bước 3: Xem chi tiết / Chờ giao -->
            <template v-else-if="getStage(order) === 3">
              <a :href="'https://zalo.me/0123456789?text=' + encodeURIComponent('Hỏi tiến độ vận chuyển chành xe cho đơn #' + order.code)" target="_blank" class="btn-shopee btn-shopee-outline">
                <i class="bi bi-truck me-1"></i> Tiến Độ Vận Chuyển
              </a>
            </template>

            <!-- Bước 4: Hoàn thành -->
            <template v-else-if="getStage(order) === 4">
              <router-link to="/san-pham" class="btn-shopee btn-shopee-primary">
                Mua Lại
              </router-link>
            </template>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL DANH SÁCH SẢN PHẨM CỦA ĐƠN HÀNG (KÍCH VÀO CHI TIẾT & CÓ THÊM GIỎ HÀNG) -->
    <!-- ========================================================================= -->
    <div v-if="selectedOrderForModal" class="modal-backdrop fade show" style="z-index: 1050;" @click="closeProductModal"></div>
    <div v-if="selectedOrderForModal" class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" @click.self="closeProductModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
          
          <!-- Modal Header -->
          <div class="modal-header border-bottom py-3 px-4 bg-white d-flex justify-content-between align-items-center">
            <div>
              <h5 class="modal-title fw-bold mb-1" style="color: var(--heading-color); font-size: 16px;">
                <i class="bi bi-box-seam text-danger me-2"></i>DANH SÁCH SẢN PHẨM - ĐƠN #{{ selectedOrderForModal.code }}
              </h5>
              <div class="text-muted small">
                Trạng thái: <strong class="text-danger text-uppercase">{{ getStatusText(selectedOrderForModal) }}</strong>
                <span class="mx-2">|</span>
                <span>Nhấp vào tên để xem chi tiết hoặc bấm icon thêm vào giỏ hàng</span>
              </div>
            </div>
            <button type="button" class="btn-close" @click="closeProductModal"></button>
          </div>

          <!-- Modal Body: Danh sách phụ tùng -->
          <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
            <div class="list-group list-group-flush">
              <div 
                v-for="(prod, idx) in getItemProducts(selectedOrderForModal)" 
                :key="idx"
                class="list-group-item p-3 d-flex align-items-center justify-content-between gap-3 border-bottom modal-item-row"
              >
                <!-- Bên trái: Hình ảnh + Tên (Click mở trang chi tiết) -->
                <div class="d-flex align-items-center gap-3 flex-grow-1" style="cursor: pointer;" @click="goToProductDetail(prod)" title="Bấm để xem trang chi tiết sản phẩm này">
                  <div class="modal-thumb rounded-2 overflow-hidden border">
                    <img v-if="prod.image" :src="getImageUrl(prod.image)" :alt="prod.name" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain; padding: 2px;">
                    <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                      <i class="bi bi-box-seam text-secondary fs-4"></i>
                    </div>
                  </div>
                  <div>
                    <div class="fw-bold text-dark hover-red d-flex align-items-center gap-1 product-link" style="font-size: 14.5px;">
                      <span>{{ prod.name }}</span>
                      <i class="bi bi-box-arrow-up-right small text-muted" style="font-size: 11px;"></i>
                    </div>
                    <div class="text-muted small mt-1">
                      <span>Mã OEM: <code class="text-secondary">{{ prod.code || 'N/A' }}</code></span>
                      <span class="mx-2">|</span>
                      <span>Số lượng: <strong class="text-dark">x{{ prod.qty }}</strong></span>
                    </div>
                  </div>
                </div>

                <!-- Bên phải: Giá (Liên hệ nếu bước 1) + Icon Thêm Giỏ Hàng -->
                <div class="d-flex align-items-center gap-3">
                  <div class="text-end">
                    <template v-if="prod.price > 0">
                      <span class="price-num">{{ formatPrice(prod.price) }}₫</span>
                    </template>
                    <template v-else>
                      <span class="price-contact">Liên hệ</span>
                    </template>
                  </div>

                  <!-- ICON / NÚT THÊM VÀO GIỎ HÀNG -->
                  <button 
                    type="button" 
                    class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1 px-3 py-1 btn-add-cart"
                    title="Thêm phụ tùng này vào giỏ hàng"
                    @click.stop="handleAddToCart(prod)"
                  >
                    <i class="bi bi-cart-plus fs-6"></i>
                    <span class="small fw-semibold">Thêm giỏ</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer: Bảng phân tích tài chính chi tiết chuẩn doanh nghiệp -->
          <div class="modal-footer bg-light p-3 d-flex flex-column gap-2" v-if="selectedOrderForModal">
            <div class="w-100 bg-white p-3 rounded-3 border">
              <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small">Tiền hàng (Tạm tính):</span>
                <span class="fw-bold text-dark">
                  <template v-if="getItemSubtotal(selectedOrderForModal) > 0">
                    {{ formatPrice(getItemSubtotal(selectedOrderForModal)) }}₫
                    <span v-if="getOrderPricingType(selectedOrderForModal) === 2 && getStage(selectedOrderForModal) === 1" class="text-warning-emphasis small fw-normal ms-1">
                      (+ {{ getUnquotedCount(selectedOrderForModal) }} món chờ tra giá)
                    </span>
                  </template>
                  <template v-else>
                    <span class="badge bg-light text-secondary border">Đang định giá</span>
                  </template>
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small">Cước vận chuyển:</span>
                <span class="fw-medium text-dark">
                  {{ selectedOrderForModal.khach_tu_tra_cuoc ? 'Khách tự trả chành xe' : (Number(selectedOrderForModal.phi_van_chuyen) > 0 ? formatPrice(selectedOrderForModal.phi_van_chuyen) + '₫' : 'Chờ xác nhận') }}
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-dark fw-bold small text-uppercase">Tổng Giá Trị Đơn Hàng:</span>
                <span class="fw-bold text-danger fs-6">
                  <template v-if="getGrandTotal(selectedOrderForModal) !== null && getGrandTotal(selectedOrderForModal) > 0">
                    {{ formatPrice(getGrandTotal(selectedOrderForModal)) }}₫
                  </template>
                  <template v-else>
                    <span class="text-secondary small">Chờ định giá xong</span>
                  </template>
                </span>
              </div>
              <div v-if="getDepositRequired(selectedOrderForModal) > 0" class="d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small">Tiền Cọc Cam Kết:</span>
                <div class="text-end">
                  <span class="fw-bold text-primary">{{ formatPrice(getDepositRequired(selectedOrderForModal)) }}₫</span>
                  <div class="small">
                    <span v-if="getDepositStatus(selectedOrderForModal) === 'paid'" class="text-success fw-semibold">
                      <i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán cọc
                    </span>
                    <span v-else class="text-danger fw-semibold">
                      <i class="bi bi-exclamation-circle-fill me-1"></i>Chưa thanh toán cọc
                    </span>
                  </div>
                </div>
              </div>
              <div v-if="getGrandTotal(selectedOrderForModal) > 0 && (getStage(selectedOrderForModal) >= 2 || getDepositRequired(selectedOrderForModal) > 0)" class="d-flex justify-content-between align-items-center pt-2">
                <span class="text-secondary fw-semibold small">Còn lại thu khi nhận hàng từ chành xe (COD):</span>
                <span class="fw-bold text-dark font-monospace fs-6">
                  {{ formatPrice(getRemainingAmount(selectedOrderForModal)) }}₫
                </span>
              </div>
            </div>

            <div class="w-100 d-flex justify-content-end gap-2 mt-1">
              <router-link v-if="selectedOrderForModal.token" :to="'/bao-gia/' + selectedOrderForModal.token" class="btn btn-outline-primary btn-sm px-3" @click="closeProductModal">
                <i class="bi bi-receipt me-1"></i> Bảng Báo Giá Chi Tiết
              </router-link>
              <button type="button" class="btn btn-secondary btn-sm px-3" @click="closeProductModal">Đóng</button>
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
import axiosClient from "../../../api/axiosClient";
import { useQuery, useQueryClient } from "@tanstack/vue-query";

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const queryClient = useQueryClient();

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
const pollingTimer = ref(null);
const selectedOrderForModal = ref(null);

const allOrders = computed(() => {
  const qList = (quotes.value || []).map(q => ({
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
    if (st === 2) return 3; // 3. Vận Chuyển
    if (st === 3) return -1; // Đã Hủy
    return 1;
  } else {
    const st = Number(order.trang_thai);
    const paySt = Number(order.trang_thai_thanh_toan);
    const daThu = Number(order.da_thu || 0);
    const depositReq = Number(order.tien_coc_yeu_cau || 0);

    if (st === 4) return -1; // Đã Hủy
    if (st === 3) return 4;  // 4. Hoàn Thành
    if (st === 1 || st === 2) return 3; // 3. Vận Chuyển

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

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  if (path.startsWith('/storage/')) return 'http://localhost:8000' + path;
  return 'http://localhost:8000/storage/' + path.replace(/^\/+/, '');
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
    if (st === 4) return 'ĐÃ HỦY';
    if (st === 3) return 'HOÀN THÀNH';
    if (st === 2) return 'ĐANG GIAO HÀNG';
    if (st === 1) return 'ĐANG CHUẨN BỊ XUẤT KHO';
    if (st === 0) return paySt === 0 ? 'CHỜ DUYỆT & CỌC' : 'ĐÃ THANH TOÁN';
  }
  const stage = getStage(order);
  switch (stage) {
    case 1: {
      const pType = getOrderPricingType(order);
      if (pType === 1) return 'CHỜ BÁO PHÍ SHIP & CỌC';
      if (pType === 2) return 'CHỜ BÁO GIÁ & PHÍ SHIP';
      return 'CHỜ KỸ THUẬT BÁO GIÁ';
    }
    case 2:
      return 'ĐÃ BÁO GIÁ - CHỜ ĐẶT CỌC';
    case 3:
      return 'ĐANG VẬN CHUYỂN';
    case 4:
      return 'HOÀN THÀNH';
    default:
      return 'ĐÃ HỦY';
  }
};

const getLogisticsStatus = (order) => {
  const st = Number(order.trang_thai);
  if (st === 2) {
    const carrier = order.ten_don_vi_van_chuyen ? `qua ${order.ten_don_vi_van_chuyen}` : 'cho quý khách';
    const tracking = order.ma_van_don ? ` (Mã vận đơn: ${order.ma_van_don})` : '';
    return `Đang giao hàng ${carrier}${tracking}`;
  }
  if (st === 1) return 'Đang chuẩn bị & Lên đơn nhà cung cấp Nhật Bản';
  return 'Đã nhận cọc & đang điều phối hàng';
};

const getItemProducts = (order) => {
  if (!order) return [];
  const list = order.items || order.chi_tiet || [];
  return list.map(p => ({
    id: p.id || p.phu_tung_id || p.san_pham_id,
    name: p.ten_phu_tung || p.ten_san_pham || 'Phụ tùng máy thủy',
    code: p.ma_phu_tung || p.ma_san_pham || '',
    slug: p.slug || p.san_pham?.slug || '',
    image: p.hinh_anh || p.hinh_anh_chinh || p.san_pham?.hinh_anh || p.san_pham?.hinh_anh_chinh || null,
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
const openProductModal = (order) => {
  selectedOrderForModal.value = order;
};

const closeProductModal = () => {
  selectedOrderForModal.value = null;
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
    ma_phu_tung: prod.code,
    ten_phu_tung: prod.name,
    hinh_anh: prod.image,
    gia_ban: prod.price > 0 ? prod.price : 0,
    is_gia_lien_he: prod.price <= 0
  }, prod.qty || 1);

  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: `Đã thêm "${prod.name}" vào giỏ hàng`,
    showConfirmButton: false,
    timer: 2000
  });
};

// ===================== VIETQR & ACTIONS =====================
const toggleVietQR = async (order) => {
  if (activeQrOrderCode.value === order.code) {
    activeQrOrderCode.value = null;
    vietQrData.value = null;
  } else {
    activeQrOrderCode.value = order.code;
    vietQrData.value = null;
    try {
      const endpoint = order._type === 'quote'
        ? `/public/vietqr/bao-gia/${order.code}`
        : `/public/vietqr/don-hang/${order.code}`;
      const res = await axiosClient.get(endpoint);
      const payload = res?.qr_url ? res : res?.data;
      if (payload && (res?.success || payload?.success)) {
        vietQrData.value = payload;
      }
    } catch (e) {
      // Fallback to static VietQR URL
    }
  }
};

const getVietQrImageUrl = (order) => {
  if (!order) return '';
  const bankId = "ICB"; // VietinBank (ICB)
  const accountNo = "108876543210";
  const amount = Number(order.tien_coc_yeu_cau || 0);
  const addInfo = order.code || 'COC JPM';
  const template = "compact2";
  return `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${encodeURIComponent(addInfo)}&accountName=${encodeURIComponent('CONG TY PHU TUNG MAY THUY JPM')}`;
};

const copyText = (text, msg) => {
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
};

const confirmManualPaid = async (order) => {
  const depositAmt = Number(order.tien_coc_yeu_cau || 0);
  const transferContent = vietQrData.value?.transfer_content || ('COC ' + order.code);
  
  const result = await Swal.fire({
    title: 'Xác Nhận Đã Chuyển Khoản Cọc?',
    html: `Quý khách đã chuyển cọc số tiền <b>${formatPrice(depositAmt)}₫</b> với nội dung <code>${transferContent}</code>?<br><small class="text-muted">Kỹ thuật viên JPM sẽ kiểm tra thông báo ngân hàng và cập nhật đơn ngay.</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Tôi đã chuyển cọc',
    cancelButtonText: 'Đóng',
    confirmButtonColor: '#16a34a'
  });

  if (result.isConfirmed) {
    if (order._type === 'quote' && order.token) {
      try {
        await axiosClient.post(`/public/yeu-cau-bao-gia/${order.token}/action`, { action: 'accept' });
      } catch (_) {}
    }

    activeQrOrderCode.value = null;
    vietQrData.value = null;

    const zaloPhone = '0123456789';
    const msg = `Chào JPM Marine, tôi vừa chuyển cọc ${formatPrice(depositAmt)}₫ cho đơn #${order.code}. Nhờ Kỹ thuật viên kiểm tra ngân hàng và lên đơn đặt hàng Nhật giúp tôi nhé!`;

    Swal.fire({
      icon: 'success',
      title: 'Đã Tiếp Nhận Thông Tin Cọc!',
      text: `JPM Marine đã ghi nhận thông báo chuyển cọc của Quý khách cho đơn #${order.code}. Kỹ thuật viên sẽ kiểm tra tài khoản ngân hàng và cập nhật tiến độ đặt hàng Nhật.`,
      showCancelButton: true,
      confirmButtonText: '<i class="bi bi-chat-dots me-1"></i> Gửi Biên Lai Qua Zalo',
      cancelButtonText: 'Đóng',
      confirmButtonColor: '#0068ff'
    }).then((res) => {
      if (res.isConfirmed) {
        window.open(`https://zalo.me/${zaloPhone}?text=${encodeURIComponent(msg)}`, '_blank');
      }
    });

    queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
    queryClient.invalidateQueries({ queryKey: ["clientOrdersHistory"] });
  }
};

const handleAcceptQuote = async (order) => {
  const result = await Swal.fire({
    title: 'Đồng Ý Chốt Đơn?',
    text: 'Bạn đồng ý với mức báo giá này và chốt đặt hàng?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý chốt đơn',
    cancelButtonText: 'Đóng',
    confirmButtonColor: '#16a34a'
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
.shopee-orders-wrapper {
  width: 100%;
}

.cursor-pointer {
  cursor: pointer;
}

.hover-red:hover {
  color: var(--accent-color, #f82249) !important;
}

/* ========================================================================= */
/* SHOPEE TAB BAR CHUẨN (GIỐNG HÌNH ẢNH) */
/* ========================================================================= */
.shopee-tabs-container {
  display: flex;
  background-color: #ffffff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.09);
  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.05);
  border-radius: 2px;
  overflow-x: auto;
  white-space: nowrap;
}

.shopee-tab-item {
  flex: 1;
  min-width: 110px;
  text-align: center;
  padding: 16px 8px;
  font-size: 14px;
  color: rgba(0, 0, 0, 0.8);
  cursor: pointer;
  user-select: none;
  transition: color 0.2s;
  border-bottom: 2px solid transparent;
}

.shopee-tab-item:hover {
  color: var(--accent-color, #f82249);
}

.shopee-tab-item.active {
  color: var(--accent-color, #f82249);
  border-bottom: 2px solid var(--accent-color, #f82249);
  font-weight: 500;
}

.tab-badge {
  font-size: 12px;
  opacity: 0.85;
}

/* ========================================================================= */
/* EMPTY STATE (GIỐNG HỆT ẢNH CHỤP SHOPEE) */
/* ========================================================================= */
.shopee-empty-state {
  background-color: #ffffff;
  min-height: 480px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 12px;
  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.05);
  border-radius: 2px;
}

.empty-clipboard-icon {
  width: 120px;
  height: 120px;
}

.empty-text {
  margin-top: 18px;
  font-size: 15px;
  color: rgba(0, 0, 0, 0.65);
}

/* ========================================================================= */
/* ORDER CARD CHUẨN SHOPEE */
/* ========================================================================= */
.shopee-order-card {
  background: #ffffff;
  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.05);
  border-radius: 2px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

/* Head */
.card-head {
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.head-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.type-tag {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 2px;
}
.quote-tag {
  background-color: #e0f2fe;
  color: #0369a1;
}
.order-tag {
  background-color: #fef3c7;
  color: #b45309;
}

.order-id {
  font-weight: 700;
  color: #222;
  font-size: 14px;
}

.order-date {
  font-size: 12px;
}

.status-badge-pill {
  display: inline-flex;
  align-items: center;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}
.stage-status-1-p1 {
  background-color: #e0f2fe;
  color: #0284c7;
  border: 1px solid rgba(2, 132, 199, 0.25);
}
.stage-status-1-p2 {
  background-color: #fef3c7;
  color: #d97706;
  border: 1px solid rgba(217, 119, 6, 0.25);
}
.stage-status-1-p3 {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid rgba(71, 85, 105, 0.25);
}
.stage-status-2 {
  background-color: #ffe4e6;
  color: #e11d48;
  border: 1px solid rgba(225, 29, 72, 0.25);
}
.stage-status-3 {
  background-color: #dbeafe;
  color: #2563eb;
  border: 1px solid rgba(37, 99, 235, 0.25);
}
.stage-status-4 {
  background-color: #dcfce7;
  color: #16a34a;
  border: 1px solid rgba(22, 163, 74, 0.25);
}
.stage-status--1 {
  background-color: #fee2e2;
  color: #dc2626;
  border: 1px solid rgba(220, 38, 38, 0.25);
}

/* Logistics Notice */
.logistics-notice {
  padding: 10px 20px;
  background-color: #fbfbfb;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  font-size: 13px;
  color: rgba(0, 0, 0, 0.7);
}

/* Card Summary Row */
.card-summary-row {
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  transition: background-color 0.2s;
}
.card-summary-row:hover {
  background-color: #fafbfc;
}

.item-thumb {
  width: 65px;
  height: 65px;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: #6c757d;
  flex-shrink: 0;
}

.item-details {
  flex-grow: 1;
}

.item-name {
  font-size: 14px;
  color: rgba(0, 0, 0, 0.87);
}

.item-oem {
  font-size: 12px;
}

.item-qty {
  font-size: 12px;
  color: rgba(0, 0, 0, 0.6);
}

.item-price {
  min-width: 120px;
}

.price-contact {
  font-size: 14px;
  font-weight: 700;
  color: var(--accent-color, #f82249);
}

.price-num {
  font-size: 14px;
  font-weight: 600;
  color: var(--accent-color, #f82249);
}

.view-detail-hint {
  font-size: 11.5px;
  color: #888;
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
.card-summary-row:hover .view-detail-hint {
  color: var(--accent-color, #f82249);
}

/* Order Finance Card - Enterprise Breakdown Bar */
.order-finance-card {
  margin: 12px 20px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

.finance-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  padding: 14px 18px;
}

@media (max-width: 991px) {
  .finance-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
}

@media (max-width: 575px) {
  .finance-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
}

.finance-col {
  display: flex;
  flex-direction: column;
}

.finance-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748b;
  margin-bottom: 5px;
  letter-spacing: 0.3px;
}

.finance-value {
  font-size: 14px;
}

.finance-remaining-bar {
  padding: 8px 18px;
  background-color: #f1f5f9;
  border-top: 1px dashed #cbd5e1;
}

/* Card Bottom */
.card-bottom {
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background-color: #ffffff;
}

.process-hint-col {
  max-width: 55%;
}

@media (max-width: 768px) {
  .process-hint-col {
    max-width: 100%;
  }
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-shopee {
  padding: 8px 16px;
  font-size: 13.5px;
  border-radius: 2px;
  text-decoration: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-shopee-primary {
  background-color: var(--accent-color, #f82249);
  color: #ffffff;
  border: 1px solid var(--accent-color, #f82249);
}
.btn-shopee-primary:hover {
  background-color: #d81438;
  color: #ffffff;
}

.btn-shopee-outline {
  background-color: #ffffff;
  color: #555555;
  border: 1px solid rgba(0, 0, 0, 0.15);
}
.btn-shopee-outline:hover {
  background-color: #f8f8f8;
  color: #222222;
}

/* QR Deposit Box */
.qr-deposit-box {
  padding: 16px 20px;
  background-color: #fffdfd;
  border-bottom: 1px dashed rgba(248, 34, 73, 0.3);
}

.qr-inner {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.qr-img-col {
  width: 140px;
}
.qr-img {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  padding: 4px;
}
.qr-subtext {
  font-size: 11px;
  margin-top: 4px;
}

.qr-info-col {
  flex: 1;
  min-width: 260px;
  background-color: #f8fafc;
  padding: 12px 16px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  padding: 3px 0;
}

.btn-copy {
  margin-left: 8px;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  font-size: 11px;
  padding: 1px 6px;
  border-radius: 2px;
  cursor: pointer;
}
.btn-copy:hover {
  background-color: #f1f5f9;
}

/* Modal Styling */
.modal-thumb {
  width: 50px;
  height: 50px;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-item-row:hover {
  background-color: #fcfcfc;
}

.product-link {
  transition: color 0.2s;
}

.btn-add-cart {
  transition: all 0.2s;
}
.btn-add-cart:hover {
  background-color: var(--accent-color, #f82249);
  color: #ffffff;
}
</style>
