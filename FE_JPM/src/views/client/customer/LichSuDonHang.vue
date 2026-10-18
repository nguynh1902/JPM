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
          <div class="head-left">
            <span class="type-tag" :class="order._type === 'quote' ? 'quote-tag' : 'order-tag'">
              {{ order._type === 'quote' ? 'BÁO GIÁ' : 'ĐƠN HÀNG' }}
            </span>
            <span class="order-id">#{{ order.code }}</span>
            <span class="order-date text-muted">{{ formatDate(order.created_at) }}</span>
          </div>

          <div class="head-right status-text">
            {{ getStatusText(order) }}
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
          <div class="item-thumb">
            <i class="bi bi-gear-wide-connected"></i>
          </div>
          
          <div class="item-details">
            <div class="item-name">
              {{ getItemProducts(order)[0]?.name || 'Phụ tùng máy thủy' }}
            </div>
            <div class="item-oem text-muted">
              Mã OEM: {{ getItemProducts(order)[0]?.code || 'N/A' }}
            </div>
            <div class="item-qty mt-1">
              Số lượng: <strong>x{{ getItemProducts(order)[0]?.qty || 1 }}</strong>
              <span v-if="getItemProducts(order).length > 1" class="text-danger ms-2 fw-medium">
                (+{{ getItemProducts(order).length - 1 }} phụ tùng khác)
              </span>
            </div>
          </div>

          <!-- Đơn giá: Nếu là chờ báo giá thì hiển thị 'Liên hệ' -->
          <div class="item-price text-end">
            <template v-if="getStage(order) === 1">
              <span class="price-contact">Liên hệ</span>
            </template>
            <template v-else-if="getItemProducts(order)[0]?.price > 0">
              <span class="price-num">{{ formatPrice(getItemProducts(order)[0]?.price) }}₫</span>
            </template>
            <template v-else>
              <span class="price-contact">Liên hệ</span>
            </template>

            <div class="view-detail-hint">
              <span>Xem chi tiết</span>
              <i class="bi bi-chevron-right ms-1"></i>
            </div>
          </div>
        </div>

        <!-- Khung VietQR cọc nếu bấm mở (Ở bước 2) -->
        <div v-if="activeQrOrderCode === order.code" class="qr-deposit-box">
          <div class="qr-inner">
            <div class="text-center qr-img-col">
              <img :src="getVietQrImageUrl(order)" alt="VietQR" class="img-fluid rounded border p-1" style="max-width: 190px;">
              <div class="mt-2 text-muted small d-flex align-items-center justify-content-center gap-1">
                <span class="spinner-grow spinner-grow-sm text-danger me-1"></span>
                <span>Chờ thanh toán...</span>
              </div>
            </div>
            <div class="qr-info-col">
              <div class="info-row">
                <span class="text-muted">Ngân hàng:</span>
                <strong>MB Bank (Quân Đội)</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tài khoản:</span>
                <div>
                  <strong class="font-monospace">0123456789</strong>
                  <button type="button" class="btn-copy" @click="copyText('0123456789', 'Đã copy STK')">Copy</button>
                </div>
              </div>
              <div class="info-row">
                <span class="text-muted">Chủ tài khoản:</span>
                <strong>CTY TNHH JPM MARINE</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Số tiền cọc:</span>
                <strong class="text-danger fs-6">{{ formatPrice(order.tien_coc_yeu_cau || 0) }}₫</strong>
              </div>
              <div class="info-row">
                <span class="text-muted">Nội dung CK:</span>
                <div>
                  <strong class="font-monospace text-danger">{{ order.code }}</strong>
                  <button type="button" class="btn-copy" @click="copyText(order.code, 'Đã copy nội dung')">Copy</button>
                </div>
              </div>
              <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-success" @click="confirmManualPaid">
                  <i class="bi bi-check-lg me-1"></i> Tôi đã chuyển khoản cọc
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Footer: Tổng tiền & Nút thao tác -->
        <div class="card-bottom">
          <div class="price-section">
            <span class="label">
              {{ getStage(order) === 2 ? 'Tiền cọc yêu cầu:' : (getStage(order) === 3 ? 'Còn lại khi nhận hàng:' : 'Thành tiền:') }}
            </span>
            <span class="total-amount">
              <!-- NẾU LÀ ĐỢI BÁO GIÁ THÌ GIÁ PHẢI LÀ 'LIÊN HỆ' -->
              <template v-if="getStage(order) === 1">
                <span class="price-contact-lg">Liên hệ</span>
              </template>
              <template v-else-if="getStage(order) === 2">
                {{ formatPrice(order.tien_coc_yeu_cau || 0) }}₫
              </template>
              <template v-else-if="getStage(order) === 3">
                {{ formatPrice(calculateRemaining(order)) }}₫
              </template>
              <template v-else>
                {{ formatPrice(calculateItemTotal(order)) }}₫
              </template>
            </span>
          </div>

          <div class="action-buttons">
            <!-- Nút xem sản phẩm (Mở Modal) -->
            <button type="button" class="btn-shopee btn-shopee-outline" @click="openProductModal(order)">
              <i class="bi bi-list-ul me-1"></i> Xem Sản Phẩm ({{ getItemProducts(order).length }})
            </button>

            <!-- Bước 1: Liên hệ hỗ trợ -->
            <template v-if="getStage(order) === 1">
              <a :href="'https://zalo.me/0123456789?text=' + encodeURIComponent('Chào JPM, tôi muốn hỏi giá yêu cầu mã #' + order.code)" target="_blank" class="btn-shopee btn-shopee-outline">
                Chat Kỹ Thuật
              </a>
            </template>

            <!-- Bước 2: Từ chối hoặc Đặt cọc -->
            <template v-else-if="getStage(order) === 2">
              <button type="button" class="btn-shopee btn-shopee-outline" @click="handleRejectQuote(order)">
                Từ chối
              </button>
              <button type="button" class="btn-shopee btn-shopee-primary" @click="toggleVietQR(order)">
                {{ activeQrOrderCode === order.code ? 'Đóng QR' : 'Đặt Cọc VietQR' }}
              </button>
            </template>

            <!-- Bước 3: Xem chi tiết / Chờ giao -->
            <template v-else-if="getStage(order) === 3">
              <span class="text-muted small me-2">Đã cọc: {{ formatPrice(order.da_thu || order.tien_coc_yeu_cau || 0) }}₫</span>
              <a :href="'https://zalo.me/0123456789?text=' + encodeURIComponent('Hỏi tiến độ đơn #' + order.code)" target="_blank" class="btn-shopee btn-shopee-outline">
                Tra Cứu Vận Chuyển
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
                  <div class="modal-thumb">
                    <i class="bi bi-gear-wide-connected text-secondary fs-4"></i>
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
                    <template v-if="getStage(selectedOrderForModal) === 1">
                      <span class="price-contact">Liên hệ</span>
                    </template>
                    <template v-else-if="prod.price > 0">
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

          <!-- Modal Footer: Tổng tiền + Đóng -->
          <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
            <div class="small">
              <span class="text-muted me-1">Tổng cộng:</span>
              <strong v-if="getStage(selectedOrderForModal) === 1" class="text-danger fs-6">Liên hệ</strong>
              <strong v-else-if="getStage(selectedOrderForModal) === 2" class="text-danger fs-6">
                {{ formatPrice(selectedOrderForModal.tien_coc_yeu_cau || 0) }}₫ (Cọc) / {{ formatPrice(calculateItemTotal(selectedOrderForModal)) }}₫
              </strong>
              <strong v-else-if="getStage(selectedOrderForModal) === 3" class="text-danger fs-6">
                {{ formatPrice(calculateRemaining(selectedOrderForModal)) }}₫ (Còn lại)
              </strong>
              <strong v-else class="text-danger fs-6">
                {{ formatPrice(calculateItemTotal(selectedOrderForModal)) }}₫
              </strong>
            </div>
            <button type="button" class="btn btn-secondary btn-sm px-3" @click="closeProductModal">Đóng</button>
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
const setPageLoading = inject('setPageLoading', () => {});
watch(isLoading, (val) => setPageLoading(val), { immediate: true });

// ===================== TAB STATE (SHOPEE STYLE) =====================
const currentTab = ref('all'); // 'all' | '1' | '2' | '3' | '4' | '-1'
const activeQrOrderCode = ref(null);
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
    return 3;
  }
};

const getStatusText = (order) => {
  const stage = getStage(order);
  switch (stage) {
    case 1:
      return 'CHỜ ADMIN BÁO GIÁ';
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
  if (st === 2) return 'Đang giao chành xe liên tỉnh đến quý khách';
  if (st === 1) return 'Đã đặt hàng đối tác Nhật Bản';
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
    image: p.hinh_anh || p.hinh_anh_chinh || p.san_pham?.hinh_anh || null,
    qty: p.so_luong || 1,
    price: Number(p.don_gia || p.gia_ban || 0)
  }));
};

const calculateItemTotal = (order) => {
  if (!order) return 0;
  const subtotal = getItemProducts(order).reduce((sum, p) => sum + (p.price * p.qty), 0);
  const ship = order.khach_tu_tra_cuoc ? 0 : Number(order.phi_van_chuyen || 0);
  return subtotal + ship;
};

const calculateRemaining = (order) => {
  if (!order) return 0;
  const total = calculateItemTotal(order);
  const paid = Number(order.da_thu || order.tien_coc_yeu_cau || 0);
  return Math.max(0, total - paid);
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
const toggleVietQR = (order) => {
  if (activeQrOrderCode === order.code) {
    activeQrOrderCode.value = null;
    stopPolling();
  } else {
    activeQrOrderCode.value = order.code;
    startPolling(order);
  }
};

const getVietQrImageUrl = (order) => {
  if (!order) return '';
  const bankId = "970422"; // MB Bank
  const accountNo = "0123456789";
  const amount = Number(order.tien_coc_yeu_cau || 0);
  const addInfo = order.code || 'COC JPM';
  const template = "print";
  return `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${addInfo}&accountName=CTY TNHH JPM MARINE`;
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

const confirmManualPaid = () => {
  Swal.fire({
    icon: 'success',
    title: 'Đã Ghi Nhận Đặt Cọc!',
    text: 'Cảm ơn quý khách. Đơn hàng đang được cập nhật sang giai đoạn vận chuyển.',
    confirmButtonColor: '#f82249'
  });
  queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
  queryClient.invalidateQueries({ queryKey: ["clientOrdersHistory"] });
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

const startPolling = (order) => {
  stopPolling();
  if (!order) return;

  const type = order._type === 'quote' ? 'quote' : 'order';
  const code = order.code;

  pollingTimer.value = setInterval(async () => {
    try {
      const res = await axiosClient.get(`/public/vietqr/check-status/${type}/${code}`);
      if (res.data && res.data.success) {
        if (res.data.is_deposit_paid || res.data.is_paid || res.data.quote_status === 2) {
          stopPolling();
          activeQrOrderCode.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Đã Nhận Tiền Cọc!',
            text: 'Đơn hàng đã được chuyển sang giai đoạn vận chuyển.',
            confirmButtonColor: '#f82249'
          });
          queryClient.invalidateQueries({ queryKey: ["clientQuotesHistory"] });
          queryClient.invalidateQueries({ queryKey: ["clientOrdersHistory"] });
        }
      }
    } catch (e) {}
  }, 4000);
};

const stopPolling = () => {
  if (pollingTimer.value) {
    clearInterval(pollingTimer.value);
    pollingTimer.value = null;
  }
};

onBeforeUnmount(() => {
  stopPolling();
});

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

.head-right.status-text {
  font-size: 13px;
  font-weight: 600;
  color: var(--accent-color, #f82249);
  text-transform: uppercase;
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

/* Card Bottom */
.card-bottom {
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background-color: #ffffff;
}

.price-section {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.price-section .label {
  font-size: 14px;
  color: rgba(0, 0, 0, 0.8);
}

.price-section .total-amount {
  font-size: 20px;
  font-weight: 700;
  color: var(--accent-color, #f82249);
}

.price-contact-lg {
  font-size: 18px;
  font-weight: 700;
  color: var(--accent-color, #f82249);
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 10px;
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
