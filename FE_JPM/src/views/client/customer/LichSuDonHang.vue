<template>
<div class="flex-grow-1 d-flex flex-column">
    <div class="card border-0 flex-grow-1" style="box-shadow: var(--shadow-sm); border-radius: 12px;">
              <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0 w-100 shopee-tabs d-flex justify-content-between" id="historyTab" role="tablist">
                  <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link custom-tab active w-100 py-3" id="quotes-tab" data-bs-toggle="tab" data-bs-target="#quotes" type="button" role="tab" @click="activeTab = 'quotes'">YÊU CẦU BÁO GIÁ</button>
                  </li>
                  <li class="nav-item flex-fill text-center" role="presentation">
                    <button class="nav-link custom-tab w-100 py-3" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" @click="activeTab = 'orders'">ĐƠN HÀNG CỦA TÔI</button>
                  </li>
                </ul>
              </div>

              <div class="card-body p-4 bg-light">
                <div class="tab-content" id="historyTabContent">
                
                  <!-- TAB: YÊU CẦU BÁO GIÁ -->
                  <div class="tab-pane fade show active" id="quotes" role="tabpanel">
                    
                                        <!-- Skeletons for Quotes -->
                    <div class="d-flex flex-column gap-4" v-if="isLoadingQuotes">
                      <div class="card border-0 shadow-sm rounded-3 overflow-hidden placeholder-glow" v-for="i in 2" :key="i">
                        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between">
                          <span class="placeholder col-4 rounded bg-secondary"></span>
                          <span class="placeholder col-2 rounded bg-secondary"></span>
                        </div>
                        <div class="card-body p-3">
                          <div class="d-flex gap-3">
                            <div class="placeholder bg-secondary rounded" style="width: 60px; height: 60px;"></div>
                            <div class="flex-grow-1">
                              <span class="placeholder col-6 mb-2 rounded bg-secondary"></span><br>
                              <span class="placeholder col-3 rounded bg-secondary"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Empty State -->
                    <div class="text-center py-5" v-else-if="quotes.length === 0">
                      <i class="bi bi-file-earmark-text text-muted" style="font-size: 3.5rem; opacity: 0.35;"></i>
                      <h6 class="mt-3 mb-1" style="color: var(--heading-color); font-weight: 700;">Chưa có yêu cầu báo giá</h6>
                      <p class="text-muted mb-0" style="font-size: 14px;">Bạn chưa thực hiện gửi yêu cầu báo giá nào.</p>
                    </div>

                    <!-- Quote List -->
                    <div class="d-flex flex-column gap-4" v-else>
                      <div v-for="quote in quotes" :key="quote.id" class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                          <div>
                            <strong style="color: var(--heading-color);">Mã: {{ quote.ma_yeu_cau }}</strong>
                            <span class="text-muted mx-2">|</span>
                            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ formatDate(quote.created_at) }}</span>
                          </div>
                          <span class="badge text-uppercase px-3 py-2" :class="getQuoteStatusClass(quote.trang_thai)" style="font-size: 11px; letter-spacing: 0.5px;">
                            {{ getQuoteStatusText(quote.trang_thai) }}
                          </span>
                        </div>
                        
                        <div class="card-body p-0">
                          <div class="list-group list-group-flush">
                            <div v-for="(item, idx) in quote.chi_tiet" :key="idx" class="list-group-item p-3 border-bottom d-flex align-items-center gap-3">
                              <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-gear-fill text-secondary fs-3"></i>
                              </div>
                              <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">{{ item.ten_phu_tung }}</h6>
                                <div class="text-muted small">
                                  <span>SL: {{ item.so_luong }}</span>
                                  <span v-if="item.ma_phu_tung" class="mx-2 text-light-subtle">|</span>
                                  <span v-if="item.ma_phu_tung">Mã OEM: {{ item.ma_phu_tung }}</span>
                                </div>
                              </div>
                              <div class="text-end" v-if="quote.trang_thai == 1 && item.don_gia > 0">
                                <span class="fw-bold text-danger">{{ formatPrice(item.don_gia) }}đ</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center border-top">
                          <div class="text-muted small" style="max-width: 50%;">
                            <i class="bi bi-chat-left-text me-1"></i> Ghi chú: {{ quote.ghi_chu_khach_hang || 'Không có ghi chú' }}
                          </div>
                          <div class="d-flex align-items-center gap-3">
                            <div v-if="quote.trang_thai == 1 && quote.tong_tien > 0" class="text-end">
                              <span class="text-muted small me-1">Tổng báo giá:</span>
                              <strong class="text-danger fs-6">{{ formatPrice(quote.tong_tien + (quote.khach_tu_tra_cuoc ? 0 : (quote.phi_van_chuyen || 0))) }}đ</strong>
                            </div>
                            <router-link v-if="quote.token" :to="'/tra-cuu-bao-gia/' + quote.token" class="btn btn-sm btn-outline-primary fw-bold px-3">
                              Xem & Chốt Đơn
                            </router-link>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- TAB: ĐƠN HÀNG -->
                  <div class="tab-pane fade" id="orders" role="tabpanel">
                    
                                        <!-- Skeletons for Orders -->
                    <div class="d-flex flex-column gap-4" v-if="isLoadingOrders">
                      <div class="card border-0 shadow-sm rounded-3 overflow-hidden placeholder-glow" v-for="i in 2" :key="i">
                        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between">
                          <span class="placeholder col-4 rounded bg-secondary"></span>
                          <span class="placeholder col-2 rounded bg-secondary"></span>
                        </div>
                        <div class="card-body p-3">
                          <div class="d-flex gap-3">
                            <div class="placeholder bg-secondary rounded" style="width: 60px; height: 60px;"></div>
                            <div class="flex-grow-1">
                              <span class="placeholder col-6 mb-2 rounded bg-secondary"></span><br>
                              <span class="placeholder col-3 rounded bg-secondary"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Empty State -->
                    <div class="text-center py-5" v-else-if="orders.length === 0">
                      <i class="bi bi-box-seam text-muted" style="font-size: 3.5rem; opacity: 0.35;"></i>
                      <h6 class="mt-3 mb-1" style="color: var(--heading-color); font-weight: 700;">Chưa có đơn hàng nào</h6>
                      <p class="text-muted mb-0" style="font-size: 14px;">Bạn chưa thực hiện giao dịch mua hàng nào.</p>
                    </div>

                    <!-- Order List -->
                    <div class="d-flex flex-column gap-4" v-else>
                      <div v-for="order in orders" :key="order.id" class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                          <div>
                            <strong style="color: var(--heading-color);">Đơn hàng: {{ order.ma_don_hang }}</strong>
                            <span class="text-muted mx-2">|</span>
                            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ formatDate(order.created_at) }}</span>
                          </div>
                          <span class="badge text-uppercase px-3 py-2" :class="getOrderStatusClass(order.trang_thai)" style="font-size: 11px; letter-spacing: 0.5px;">
                            {{ getOrderStatusText(order.trang_thai) }}
                          </span>
                        </div>
                        
                        <div class="card-body p-0">
                          <div class="list-group list-group-flush">
                            <div v-for="(item, idx) in order.items" :key="idx" class="list-group-item p-3 border-bottom d-flex align-items-center gap-3">
                              <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-box text-secondary fs-3"></i>
                              </div>
                              <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">Sản phẩm #{{ item.id_san_pham }}</h6>
                                <div class="text-muted small">SL: {{ item.so_luong }} <span class="mx-2 text-light-subtle">|</span> Đơn giá: {{ formatPrice(item.don_gia) }}đ</div>
                              </div>
                              <div class="text-end">
                                <span class="fw-bold text-dark">{{ formatPrice(item.thanh_tien) }}đ</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="card-footer bg-white p-3 d-flex flex-column gap-3 border-top">
                          <div class="d-flex justify-content-between align-items-center">
                            <span class="badge text-uppercase px-2 py-1" :class="getPaymentStatusClass(order.trang_thai_thanh_toan)" style="font-size: 10px;">
                              {{ getPaymentStatusText(order.trang_thai_thanh_toan) }}
                            </span>
                            <div class="text-end">
                              <div v-if="order.khach_tu_tra_cuoc" class="text-muted small mb-1">Phí vận chuyển: Người nhận trả cước</div>
                              <div v-else-if="order.phi_van_chuyen > 0" class="text-muted small mb-1">Phí vận chuyển: {{ formatPrice(order.phi_van_chuyen) }}đ</div>
                              
                              <span class="text-muted small me-2">Cần thanh toán:</span>
                              <strong class="text-danger fs-5">{{ formatPrice(order.tong_tien + (order.khach_tu_tra_cuoc ? 0 : (order.phi_van_chuyen || 0)) - (order.da_thu || 0)) }}đ</strong>
                            </div>
                          </div>

                          <!-- QR CODE CHO THANH TOÁN CỌC HOẶC THANH TOÁN NỐT -->
                          <div v-if="order.trang_thai_thanh_toan === 0 && (order.tien_coc_yeu_cau > 0 || order.tong_tien > 0)" class="alert alert-info d-flex align-items-center justify-content-between mb-0 py-2 px-3 border-0" style="background-color: rgba(3, 195, 236, 0.08);">
                            <div>
                              <strong class="text-dark fs-6 d-block mb-1">
                                {{ (order.da_thu || 0) == 0 && order.tien_coc_yeu_cau > 0 ? 'Vui lòng thanh toán Tiền Cọc' : 'Vui lòng thanh toán Đơn Hàng' }}
                              </strong>
                              <span class="text-muted small">Quét mã QR để chuyển khoản nhanh</span>
                            </div>
                            <button class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" @click="showPaymentQR(order)" data-bs-toggle="modal" data-bs-target="#paymentModal">
                              <i class="bi bi-qr-code-scan me-1"></i> Thanh Toán Ngay
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          
    <!-- PAYMENT QR MODAL -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold text-dark w-100 text-center">Thanh Toán Chuyển Khoản</h5>
            <button type="button" class="btn-close position-absolute" style="right: 15px; top: 15px;" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center pt-2 pb-4">
            <p class="text-muted small mb-3">Vui lòng sử dụng App Ngân hàng để quét mã QR bên dưới.</p>
            
            <div class="bg-light p-3 rounded-3 d-inline-block mb-3 border">
              <img v-if="selectedOrderForPayment" :src="getVietQRUrl(selectedOrderForPayment)" alt="VietQR" class="img-fluid rounded" style="max-width: 250px;">
            </div>
            
            <div v-if="selectedOrderForPayment" class="text-start bg-light p-3 rounded-3" style="font-size: 14px;">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Ngân hàng:</span>
                <span class="fw-bold text-dark">MB Bank (Demo)</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Số tài khoản:</span>
                <span class="fw-bold text-dark">0123456789</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Chủ tài khoản:</span>
                <span class="fw-bold text-dark">CTY TNHH JPM</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Số tiền:</span>
                <span class="fw-bold text-danger">{{ formatPrice(getPaymentAmount(selectedOrderForPayment)) }}đ</span>
              </div>
              <div class="d-flex justify-content-between border-top pt-2 mt-2">
                <span class="text-muted">Nội dung CK:</span>
                <span class="fw-bold text-primary">{{ selectedOrderForPayment.ma_don_hang }}</span>
              </div>
            </div>
            
            <button class="btn btn-success w-100 mt-4 fw-bold py-2 shadow-sm" data-bs-dismiss="modal" @click="confirmPaid">
              Tôi đã chuyển khoản thành công
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, inject } from "vue";
import { useAuthStore } from "../../../stores/authStore";

import Swal from "sweetalert2";
import axiosClient from "../../../api/axiosClient";
import { useQuery } from "@tanstack/vue-query";

const authStore = useAuthStore();

// ===================== QUOTES DATA =====================
const { data: quotesData, isLoading: isLoadingQuotes } = useQuery({
  queryKey: ["clientQuotesHistory"],
  queryFn: async () => await axiosClient.get("/client/history/quotes"),
  staleTime: 5 * 60 * 1000,
});
const quotes = computed(() => quotesData.value || []);

// ===================== ORDERS DATA =====================
const { data: ordersData, isLoading: isLoadingOrders } = useQuery({
  queryKey: ["clientOrdersHistory"],
  queryFn: async () => await axiosClient.get("/client/history/orders"),
  staleTime: 5 * 60 * 1000,
});
const orders = computed(() => ordersData.value || []);

// ===================== PAGE LOADING =====================
const setPageLoading = inject('setPageLoading', () => {});
const isPageLoading = computed(() => isLoadingQuotes.value || isLoadingOrders.value);
watch(isPageLoading, (val) => setPageLoading(val), { immediate: true });

// ===================== UTILS & FORMATTERS =====================
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getQuoteStatusText = (status) => {
  switch(Number(status)) {
    case 0: return 'Đang chờ báo giá';
    case 1: return 'Đã có báo giá';
    case 2: return 'Đã từ chối';
    case 3: return 'Đã chốt thành đơn';
    default: return 'Không xác định';
  }
};
const getQuoteStatusClass = (status) => {
  switch(Number(status)) {
    case 0: return 'bg-pastel-warning text-warning';
    case 1: return 'bg-pastel-success text-success';
    case 2: return 'bg-pastel-danger text-danger';
    case 3: return 'bg-pastel-info text-info';
    default: return 'bg-light text-secondary';
  }
};

const getOrderStatusText = (status) => {
  switch(Number(status)) {
    case 0: return 'Chờ xác nhận';
    case 1: return 'Đã xác nhận';
    case 2: return 'Đang vận chuyển';
    case 3: return 'Hoàn thành';
    case 4: return 'Đã hủy';
    default: return 'Không xác định';
  }
};
const getOrderStatusClass = (status) => {
  switch(Number(status)) {
    case 0: return 'bg-pastel-warning text-warning';
    case 1: return 'bg-pastel-info text-info';
    case 2: return 'bg-pastel-primary text-primary';
    case 3: return 'bg-pastel-success text-success';
    case 4: return 'bg-pastel-danger text-danger';
    default: return 'bg-light text-secondary';
  }
};

const getPaymentStatusText = (status) => {
  return Number(status) === 1 ? 'Đã thanh toán' : 'Chưa thanh toán';
};
const getPaymentStatusClass = (status) => {
  return Number(status) === 1 ? 'bg-success text-white' : 'bg-secondary text-white';
};


// ===================== PAYMENT LOGIC =====================
const selectedOrderForPayment = ref(null);

const getPaymentAmount = (order) => {
  if (!order) return 0;
  const total = order.tong_tien + (order.khach_tu_tra_cuoc ? 0 : (order.phi_van_chuyen || 0));
  // Nếu chưa cọc và có yêu cầu cọc -> Cần cọc
  if ((order.da_thu || 0) == 0 && order.tien_coc_yeu_cau > 0) {
    return order.tien_coc_yeu_cau;
  }
  // Còn lại thanh toán nốt
  return total - (order.da_thu || 0);
};

const getVietQRUrl = (order) => {
  if (!order) return '';
  const amount = getPaymentAmount(order);
  const content = order.ma_don_hang;
  // Replace with actual Bank ID and Account Number (e.g. MB, VCB)
  const bankId = "970422"; // MB Bank bin code example
  const accountNo = "0123456789"; 
  const template = "print"; 
  return `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.png?amount=${amount}&addInfo=${content}&accountName=JPM`;
};

const showPaymentQR = (order) => {
  selectedOrderForPayment.value = order;
};

const confirmPaid = () => {
  Swal.fire({
    icon: 'success',
    title: 'Đã ghi nhận',
    text: 'Cảm ơn bạn đã thanh toán. Chúng tôi sẽ kiểm tra đối soát và cập nhật trạng thái đơn hàng trong thời gian sớm nhất!',
    confirmButtonColor: '#28a745'
  });
};

// ===================== LOGOUT =====================
const logout = async () => {
  try {
    await authStore.logout();
    window.location.href = "/dang-nhap";
  } catch {
    console.error("Lỗi đăng xuất");
  }
};
</script>

<style scoped>
.active-sidebar-link {
  color: var(--accent-color) !important;
  background-color: rgba(239, 35, 60, 0.04) !important;
  border-left: 3px solid var(--accent-color) !important;
}
.list-group-item-action:hover {
  background-color: rgba(0, 0, 0, 0.02);
  color: var(--heading-color) !important;
}

/* Custom Tabs Styling */
.custom-tab {
  color: #555;
  font-weight: 500;
  font-size: 16px;
  border: none !important;
  border-bottom: 2px solid transparent !important;
  border-radius: 0 !important;
  background: white !important;
  transition: all 0.2s ease-in-out;
}
.custom-tab:hover {
  color: var(--accent-color);
}
.custom-tab.active {
  color: var(--accent-color) !important;
  border-bottom: 2px solid var(--accent-color) !important;
  background: white !important;
}

/* Custom Badge Colors to match project styles */
.bg-pastel-warning { background-color: rgba(255, 171, 0, 0.1); }
.bg-pastel-success { background-color: rgba(113, 221, 55, 0.1); }
.bg-pastel-danger { background-color: rgba(255, 62, 29, 0.1); }
.bg-pastel-info { background-color: rgba(3, 195, 236, 0.1); }
.bg-pastel-primary { background-color: rgba(105, 108, 255, 0.1); }
</style>










