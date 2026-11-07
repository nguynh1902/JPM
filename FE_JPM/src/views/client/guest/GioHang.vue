<template>
  <main class="cart-page-bg main-page">
    
    <!-- ========================================================
         PAGE HEADER & BREADCRUMB BANNER
         ======================================================== -->
    <section class="cart-header-banner">
      <div class="container">
        <!-- Breadcrumb chuẩn hệ thống -->
        <nav aria-label="breadcrumb" class="custom-breadcrumb mb-3" data-aos="fade-down">
          <ol class="breadcrumb mb-0 align-items-center">
            <li class="breadcrumb-item">
              <router-link to="/" class="breadcrumb-link">
                <i class="bi bi-house-door me-1"></i> Trang chủ
              </router-link>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
              Giỏ hàng báo giá
            </li>
          </ol>
        </nav>

        <!-- Cart Title & Trust Badges -->
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3" data-aos="fade-up">
          <div>
            <span class="cart-eyebrow">
              <i class="bi bi-patch-check-fill me-1"></i> QUY TRÌNH BÁO GIÁ MINH BẠCH
            </span>
            <h1 class="cart-page-title mb-2">
              Giỏ Hàng <span class="cart-title-accent">Báo Giá</span>
            </h1>
            <p class="cart-page-subtitle mb-0">
              Kiểm tra phụ tùng, số lượng và gửi yêu cầu để nhận mức chiết khấu đại lý tốt nhất từ JPM Marine.
            </p>
          </div>

          <!-- Trust Badges (Desktop) -->
          <div class="cart-header-trust d-none d-lg-flex align-items-center gap-3">
            <div class="trust-card">
              <div class="trust-icon-wrap text-success">
                <i class="bi bi-patch-check"></i>
              </div>
              <div>
                <div class="trust-title">Chuẩn Mã OEM</div>
                <div class="trust-sub">100% Phụ tùng Nhật</div>
              </div>
            </div>
            <div class="trust-card">
              <div class="trust-icon-wrap text-danger">
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <div class="trust-title">Chành Xe Toàn Quốc</div>
                <div class="trust-sub">Giao nhanh bến cảng</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================================
         MAIN CART AREA
         ======================================================== -->
    <section class="cart-content-section">
      <div class="container">
        
        <div class="row gy-4" v-if="cartItems.length > 0">
          
          <!-- LEFT COLUMN: CART ITEMS & TRUST SECTION -->
          <div class="col-lg-7 col-xl-8">
            
            <!-- 1. Danh sách sản phẩm table card -->
            <div class="card catalog-cart-card border-0 mb-4" data-aos="fade-up">
              <div class="card-header bg-white px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                  <h5 class="mb-0 jpm-card-title">Danh Sách Phụ Tùng Đã Chọn</h5>
                  <span class="badge-item-count">{{ cartItems.length }} phụ tùng</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <button v-if="selectedItems.length > 0" class="btn btn-outline-danger btn-sm rounded-8" @click="removeSelectedItems">
                    <i class="bi bi-trash3 me-1"></i> Xóa đã chọn ({{ selectedItems.length }})
                  </button>
                </div>
              </div>
              
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0 custom-cart-table">
                    <thead>
                      <tr>
                        <th class="text-center th-check" style="width: 46px;">
                          <input class="form-check-input custom-checkbox" type="checkbox" v-model="selectAll" title="Chọn tất cả">
                        </th>
                        <th class="text-center" style="width: 80px;">ẢNH / REF</th>
                        <th>MÃ & TÊN PHỤ TÙNG</th>
                        <th class="text-center" style="width: 140px;">ĐƠN GIÁ</th>
                        <th class="text-center" style="width: 130px;">SỐ LƯỢNG</th>
                        <th class="text-center" style="width: 70px;">XÓA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in cartItems" :key="item.ma_phu_tung" class="table-row-hover">
                        <td class="text-center th-check">
                          <input class="form-check-input custom-checkbox" type="checkbox" :value="item.ma_phu_tung" v-model="selectedItems">
                        </td>
                        <td class="text-center">
                          <div class="cart-item-thumb">
                            <img v-if="item.hinh_anh" :src="getImageUrl(item.hinh_anh)" :alt="item.ten_phu_tung">
                            <i v-else class="bi bi-image text-muted fs-4"></i>
                          </div>
                        </td>
                        <td>
                          <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="part-code">{{ item.ma_phu_tung }}</span>
                            <span 
                              v-if="item.loai_san_pham" 
                              class="badge rounded-pill d-inline-flex align-items-center gap-1" 
                              :class="item.loai_san_pham === 'san_pham' ? 'badge-san-pham' : 'badge-phu-tung'"
                            >
                              <i :class="item.loai_san_pham === 'san_pham' ? 'bi bi-box-seam' : 'bi bi-tools'" style="font-size: 9.5px;"></i>
                              {{ item.loai_san_pham === 'san_pham' ? 'Sản phẩm' : 'Phụ tùng' }}
                            </span>
                          </div>
                          <div class="part-name mt-1">{{ item.ten_phu_tung }}</div>
                        </td>
                        <td class="text-center">
                          <span v-if="item.is_gia_lien_he || !item.gia_ban || parseFloat(item.gia_ban) <= 0" class="part-price-quote">Liên hệ</span>
                          <span v-else class="part-price-val">{{ formatPrice(item.gia_ban) }}</span>
                        </td>
                        <td class="text-center">
                          <div class="qty-selector d-inline-flex align-items-center mx-auto">
                            <button type="button" class="qty-btn" @click="updateQuantity(item, -1)" title="Giảm số lượng">
                              <i class="bi bi-dash"></i>
                            </button>
                            <input type="text" class="qty-input" v-model="item.quantity" readonly>
                            <button type="button" class="qty-btn" @click="updateQuantity(item, 1)" title="Tăng số lượng">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </td>
                        <td class="text-center">
                          <button class="btn btn-delete-item" @click="removeItem(item)" title="Xóa phụ tùng này">
                            <i class="bi bi-trash3"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- 2. Quy trình & Cam kết (Cân bằng chiều cao bố cục 2 cột, không để khoảng trống khi cuộn) -->
            <div class="card catalog-cart-card border-0 mb-4" data-aos="fade-up" data-aos-delay="100">
              <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                  <h6 class="workflow-title mb-0">
                    <i class="bi bi-arrow-repeat text-danger me-2"></i>Quy Trình Tiếp Nhận & Xử Lý Đơn Hàng Tại JPM Marine
                  </h6>
                  <span class="badge rounded-pill bg-light text-secondary px-3 py-1 small">Cam kết minh bạch</span>
                </div>
                
                <div class="row g-3 pt-1">
                  <div class="col-md-4">
                    <div class="workflow-step">
                      <div class="step-num">01</div>
                      <div class="step-content">
                        <div class="step-heading">Kiểm Tra Mã OEM</div>
                        <div class="step-desc">Đội ngũ kỹ thuật đối chiếu chính xác mã phụ tùng với sơ đồ rã máy động cơ của bạn.</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="workflow-step">
                      <div class="step-num">02</div>
                      <div class="step-content">
                        <div class="step-heading">Báo Tồn Kho & Cước</div>
                        <div class="step-desc">Kiểm tra tồn kho kho Nhật/Việt Nam, tính cước chành xe và gửi thông báo qua Zalo.</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="workflow-step">
                      <div class="step-num">03</div>
                      <div class="step-content">
                        <div class="step-heading">Giao Hàng Chành Xe</div>
                        <div class="step-desc">Đóng gói an toàn chống va đập máy thủy, giao hỏa tốc đến chành xe hoặc bến cảng.</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-headset text-danger fs-5"></i>
                    <span>Cần hỗ trợ tra mã máy gấp? Hotline / Zalo: <strong class="text-dark">0123 456 789</strong></span>
                  </div>
                  <div class="text-success fw-medium">
                    <i class="bi bi-clock-history me-1"></i> Phản hồi trong 15-30 phút trong giờ làm việc
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- RIGHT COLUMN: CHECKOUT SIDEBAR -->
          <div class="col-lg-5 col-xl-4">
            <div class="card catalog-cart-card border-0 sticky-sidebar" data-aos="fade-up" data-aos-delay="150">
              <div class="card-header bg-white px-4 py-3 border-bottom d-flex align-items-center" style="min-height: 64px;">
                <h5 class="mb-0 jpm-card-title">Thông Tin Đặt Hàng & Báo Giá</h5>
              </div>
              
              <div class="card-body p-4">
                
                <!-- Bảng tóm tắt tài chính -->
                <div class="summary-box mb-4 p-3 rounded-8">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-medium">Số lượng đã chọn:</span>
                    <strong class="text-dark">{{ totalQuantity }} món</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                    <span><i class="bi bi-truck me-1"></i> Cước vận chuyển:</span>
                    <span v-if="isB2B" class="text-success fw-bold">Ưu đãi đại lý B2B</span>
                    <span v-else class="text-secondary fw-medium">Báo cước theo chành xe</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                    <span><i class="bi bi-wallet2 me-1"></i> Tiền đặt cọc:</span>
                    <span class="text-secondary fw-medium">Chờ KTV xác nhận tồn</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-baseline pt-2 mt-2 border-top">
                    <span class="text-dark fw-bold">Tạm tính tiền hàng:</span>
                    <div class="text-end">
                      <strong class="text-muted fs-5 fw-bold" v-if="selectedItems.length === 0">0 ₫</strong>
                      <strong class="summary-price-accent fs-5 fw-bold" v-else-if="hasPricedItems">{{ formatPrice(pricedTotalAmount) }}</strong>
                      <strong class="summary-price-accent fs-5 fw-bold" v-else>Liên hệ báo giá</strong>
                      <div v-if="isMixedCart && selectedItems.length > 0" class="small text-muted fst-italic mt-1">
                        (+ {{ contactItemCount }} phụ tùng chờ tư vấn giá)
                      </div>
                    </div>
                  </div>
                  
                  <!-- Hướng dẫn quy trình xác nhận -->
                  <div class="b2b-notice-box mt-3">
                    <i class="bi bi-info-circle-fill text-danger me-1 flex-shrink-0 mt-0"></i>
                    <span>Sau khi nhận đơn, Kỹ thuật viên JPM sẽ kiểm tra tồn kho, tính cước vận chuyển và gửi link báo giá kèm mã VietQR qua Zalo để quý khách xác nhận.</span>
                  </div>
                </div>

                <!-- Form Giao Hàng & Báo Giá -->
                <form @submit.prevent="submitQuotation">
                  
                  <!-- If Not Authenticated (GUEST) -->
                  <div v-if="!isAuthenticated">
                    <div class="mb-3">
                      <label class="form-label field-label">Họ và tên <span class="text-danger">*</span></label>
                      <input type="text" class="form-control custom-form-input" v-model="checkoutForm.name" required placeholder="Nhập tên của bạn...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label field-label">Số điện thoại / Zalo <span class="text-danger">*</span></label>
                      <input type="tel" class="form-control custom-form-input" v-model="checkoutForm.phone" required placeholder="Nhập số điện thoại nhận báo giá...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label field-label">Email (Tuỳ chọn)</label>
                      <input type="email" class="form-control custom-form-input" v-model="checkoutForm.email" placeholder="Nhập email nhận hóa đơn...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label field-label">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                      <textarea class="form-control custom-form-input custom-textarea" v-model="checkoutForm.address" required rows="2" placeholder="Số nhà, Tỉnh/Thành phố hoặc Tên Chành Xe quen thuộc"></textarea>
                    </div>
                  </div>

                  <!-- If Authenticated (MEMBER) -->
                  <div v-else class="mb-4">
                    <label class="form-label field-label mb-2">Thông tin & Địa chỉ giao hàng</label>
                    
                    <!-- Has selected address -->
                    <div v-if="selectedAddress" class="address-preview-card p-3 rounded-8 position-relative">
                      <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-danger fs-5 mt-n1 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                          <div class="fw-bold text-dark">{{ selectedAddress.ten_nguoi_nhan }}</div>
                          <div class="text-muted small mt-1">
                            <i class="bi bi-telephone-fill me-1"></i> {{ selectedAddress.so_dien_thoai }}
                          </div>
                          <div class="small text-secondary mt-1" style="line-height: 1.4;">
                            {{ selectedAddress.dia_chi_cu_the }}
                          </div>
                        </div>
                      </div>
                      <div class="mt-2 text-end pt-2 border-top" v-if="addresses.length > 0">
                        <button type="button" class="btn btn-link p-0 text-decoration-none address-change-link" data-bs-toggle="modal" data-bs-target="#addressModal" @click="modalState = 'list'">
                          Thay đổi địa chỉ <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                      </div>
                    </div>

                    <!-- No address -->
                    <div v-else class="no-address-box p-3 rounded-8 text-center">
                      <i class="bi bi-geo-alt text-muted fs-3 mb-2 d-block"></i>
                      <p class="text-muted small mb-2">Bạn chưa có địa chỉ nào trong Sổ địa chỉ.</p>
                      <button type="button" class="btn btn-outline-danger btn-sm rounded-8" data-bs-toggle="modal" data-bs-target="#addressModal" @click.prevent="openAddAddress">
                        <i class="bi bi-plus-lg me-1"></i> Thêm địa chỉ ngay
                      </button>
                    </div>
                  </div>

                  <!-- Note Input (Common) -->
                  <div class="mb-4">
                    <label class="form-label field-label">Ghi chú (Tuỳ chọn)</label>
                    <textarea class="form-control custom-form-input custom-textarea" v-model="checkoutForm.note" rows="2" placeholder="Ghi chú số khung, chành xe nhận, yêu cầu hóa đơn..."></textarea>
                  </div>

                  <!-- Submit Button -->
                  <button type="submit" class="btn btn-jpm-submit w-100 d-flex justify-content-center align-items-center gap-2" :disabled="isSubmitting || selectedItems.length === 0">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm"></span>
                    <span v-else class="fw-bold">{{ buttonText }}</span>
                    <i class="bi bi-arrow-right" v-if="!isSubmitting && selectedItems.length > 0"></i>
                  </button>
                  
                  <div class="security-guarantee-text text-center mt-3 mb-0">
                    <i class="bi bi-shield-check text-success me-1"></i> Cam kết bảo mật thông tin & chính hãng 100%
                  </div>
                </form>

              </div>
            </div>
          </div>

        </div>

        <!-- Empty Cart -->
        <div class="empty-cart-card card border-0 p-5 text-center" v-else data-aos="fade-up">
          <div class="empty-icon-wrap mx-auto mb-3">
            <i class="bi bi-cart-x"></i>
          </div>
          <h3 class="fw-bold text-dark mb-2">Giỏ Hàng Báo Giá Đang Trống</h3>
          <p class="text-muted mb-4 fs-6" style="max-width: 480px; margin: 0 auto;">
            Bạn chưa thêm linh kiện hoặc phụ tùng nào vào giỏ hàng. Hãy tìm kiếm phụ tùng qua sơ đồ rã máy hoặc catalog thương hiệu.
          </p>
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <router-link to="/san-pham" class="btn btn-jpm-submit px-4 text-decoration-none">
              Khám Phá Cửa Hàng Phụ Tùng
            </router-link>
            <router-link to="/thuong-hieu/yamaha" class="btn btn-outline-secondary rounded-8 px-4 text-decoration-none">
              Tra Cứu Theo Thương Hiệu
            </router-link>
          </div>
        </div>

      </div>
    </section>

    <!-- ========================================================
         ADDRESS MODAL (TELEPORTED TO BODY)
         ======================================================== -->
    <Teleport to="body">
      <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content custom-modal-content">
            <div class="modal-header border-bottom px-4 py-3">
              <h5 class="modal-title fw-bold text-dark fs-5" id="addressModalLabel">
                {{ modalState === 'list' ? 'Sổ Địa Chỉ Giao Hàng' : (editingAddressId ? 'Cập Nhật Địa Chỉ' : 'Thêm Địa Chỉ Mới') }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <!-- Danh sách địa chỉ -->
              <div v-if="modalState === 'list'" class="address-list">
                <div v-if="addresses.length === 0" class="text-center py-4 text-muted small">
                  Chưa có địa chỉ nào được lưu.
                </div>
                <div v-for="addr in addresses" :key="addr.id" class="address-item p-3 mb-3 border rounded-8 position-relative d-flex align-items-start gap-3" :class="{'selected-addr': selectedAddressId === addr.id}" style="cursor: pointer;" @click="selectedAddressId = addr.id">
                  <div class="radio-wrap mt-1">
                    <input class="form-check-input custom-checkbox" type="radio" :name="'address_radio'" :value="addr.id" v-model="selectedAddressId">
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fw-bold text-dark">{{ addr.ten_nguoi_nhan }}</span>
                      <a href="#" class="text-decoration-none small text-primary fw-medium" @click.stop.prevent="openEditAddress(addr)">Sửa</a>
                    </div>
                    <div class="text-muted small mb-1">(+84) {{ addr.so_dien_thoai }}</div>
                    <div class="small text-secondary" style="line-height: 1.4;">{{ addr.dia_chi_cu_the }}</div>
                    <span v-if="addr.la_mac_dinh" class="badge bg-danger-subtle text-danger mt-2 rounded-pill px-2 py-1 small">Mặc định</span>
                  </div>
                </div>
              </div>
              
              <!-- Form thêm / sửa địa chỉ -->
              <div v-else class="address-form-inline">
                <form @submit.prevent="saveAddress">
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Họ và tên người nhận <span class="text-danger">*</span></label>
                    <input type="text" class="form-control custom-form-input" v-model="addressForm.ten_nguoi_nhan" required placeholder="Tên người nhận hàng...">
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control custom-form-input" v-model="addressForm.so_dien_thoai" required placeholder="Số điện thoại...">
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Địa chỉ cụ thể / Chành xe <span class="text-danger">*</span></label>
                    <textarea class="form-control custom-form-input" v-model="addressForm.dia_chi_cu_the" required rows="2" placeholder="Số nhà, đường, xã/phường, tỉnh/thành phố hoặc bến chành xe..."></textarea>
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input custom-checkbox" id="laMacDinh" v-model="addressForm.la_mac_dinh">
                    <label class="form-check-label small ms-2 cursor-pointer" for="laMacDinh">Đặt làm địa chỉ nhận hàng mặc định</label>
                  </div>
                  <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light rounded-8 flex-grow-1" @click="modalState = 'list'">Hủy</button>
                    <button type="submit" class="btn btn-jpm-submit rounded-8 flex-grow-1" :disabled="isSavingAddress">
                      <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-2"></span>
                      Lưu địa chỉ
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal-footer border-top px-4 py-3" v-if="modalState === 'list'">
              <button type="button" class="btn btn-jpm-submit w-100 py-2 d-flex justify-content-center align-items-center gap-2" @click.prevent="openAddAddress">
                <i class="bi bi-plus-lg"></i> Thêm Địa Chỉ Mới
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

  </main>
</template>

<script>
import { useCartStore } from '../../../stores/cartStore'
import axiosClient from '../../../api/axiosClient'
import Swal from 'sweetalert2'

export default {
  name: 'GioHang',
  setup() {
    const cartStore = useCartStore()
    return { cartStore }
  },
  data() {
    return {
      checkoutForm: {
        name: '',
        phone: '',
        email: '',
        note: '',
        address: ''
      },
      selectedItems: [],
      isSubmitting: false,
      pendingOrderId: '',

      isAuthenticated: false,
      userProfile: null,
      addresses: [],
      selectedAddressId: null,
      modalState: 'list',
      editingAddressId: null,
      addressForm: {
        ten_nguoi_nhan: '',
        so_dien_thoai: '',
        dia_chi_cu_the: '',
        la_mac_dinh: false
      },
      isSavingAddress: false
    }
  },
  computed: {
    cartItems() {
      return this.cartStore.items || []
    },
    selectedCartItems() {
      return this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung))
    },
    totalQuantity() {
      return this.selectedCartItems.reduce((total, item) => total + item.quantity, 0)
    },
    checkoutType() {
      return 'quote'
    },
    hasContactItems() {
      return this.selectedCartItems.some(item => item.is_gia_lien_he || !item.gia_ban || parseFloat(item.gia_ban) <= 0)
    },
    hasPricedItems() {
      return this.selectedCartItems.some(item => !item.is_gia_lien_he && item.gia_ban && parseFloat(item.gia_ban) > 0)
    },
    isMixedCart() {
      return this.hasContactItems && this.hasPricedItems
    },
    contactItemCount() {
      return this.selectedCartItems.filter(item => item.is_gia_lien_he || !item.gia_ban || parseFloat(item.gia_ban) <= 0).length
    },
    pricedTotalAmount() {
      return this.selectedCartItems.reduce((sum, item) => {
        const price = (item.is_gia_lien_he || !item.gia_ban) ? 0 : parseFloat(item.gia_ban)
        return sum + (price * item.quantity)
      }, 0)
    },
    buttonText() {
      if (this.selectedItems.length === 0) return 'Vui lòng chọn sản phẩm'
      return 'Gửi Yêu Cầu Báo Giá'
    },
    totalAmount() {
      if (this.selectedItems.length === 0) return this.formatPrice(0)
      if (!this.hasPricedItems) return 'Liên hệ báo giá'
      return this.formatPrice(this.pricedTotalAmount)
    },
    selectedAddress() {
      return this.addresses.find(a => a.id === this.selectedAddressId) || null
    },
    selectAll: {
      get() {
        return this.cartItems.length > 0 && this.selectedItems.length === this.cartItems.length
      },
      set(value) {
        if (value) {
          this.selectedItems = this.cartItems.map(item => item.ma_phu_tung)
        } else {
          this.selectedItems = []
        }
      }
    },
    isB2B() {
      if (!this.isAuthenticated || !this.userProfile) return false
      const roles = this.userProfile.roles || []
      const hasB2BRole = Array.isArray(roles) && roles.some(r => {
        const s = typeof r === 'string' ? r.toLowerCase() : (r.name || '').toLowerCase()
        return s.includes('đại lý') || s.includes('b2b')
      })
      return hasB2BRole || Boolean(this.userProfile.ten_cong_ty)
    }
  },
  mounted() {
    try {
      const userStr = localStorage.getItem('auth_user')
      const token = localStorage.getItem('auth_token')
      if (userStr && token) {
        this.isAuthenticated = true
        const user = JSON.parse(userStr)
        this.userProfile = user
        
        this.checkoutForm.name = user.ho_ten || user.name || ''
        this.checkoutForm.phone = user.so_dien_thoai || user.phone || ''
        this.checkoutForm.email = user.email || ''

        this.fetchAddresses()
      }
    } catch (e) {
      console.warn('Cannot parse user info', e)
    }
  },
  methods: {
    getImageUrl(path) {
      if (!path) return 'https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM+Marine'
      if (path.startsWith('http')) return path
      if (path.startsWith('/storage/')) return 'http://localhost:8000' + path
      return 'http://localhost:8000/storage/' + path.replace(/^\/+/, '')
    },
    formatPrice(price) {
      if (!price) return '0 ₫'
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)
    },
    updateQuantity(item, amount) {
      const newQty = item.quantity + amount
      if (newQty < 1) return
      this.cartStore.updateQuantity(item.ma_phu_tung, newQty)
    },
    async removeItem(item) {
      const result = await Swal.fire({
        title: 'Xóa phụ tùng?',
        text: `"${item.ten_phu_tung}" sẽ bị xóa khỏi giỏ hàng.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f82249',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
      })
      if (result.isConfirmed) {
        this.cartStore.removeItem(item.ma_phu_tung)
        this.selectedItems = this.selectedItems.filter(id => id !== item.ma_phu_tung)
      }
    },
    async removeSelectedItems() {
      if (this.selectedItems.length === 0) return
      const result = await Swal.fire({
        title: `Xóa ${this.selectedItems.length} phụ tùng?`,
        text: 'Hành động này sẽ xóa các sản phẩm đã chọn khỏi giỏ.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f82249',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
      })
      if (result.isConfirmed) {
        this.selectedItems.forEach(maPhuTung => {
          this.cartStore.removeItem(maPhuTung)
        })
        this.selectedItems = []
      }
    },
    openAddAddress() {
      this.editingAddressId = null
      this.addressForm = { ten_nguoi_nhan: '', so_dien_thoai: '', dia_chi_cu_the: '', la_mac_dinh: this.addresses.length === 0 }
      this.modalState = 'form'
    },
    openEditAddress(addr) {
      this.editingAddressId = addr.id
      this.addressForm = { 
        ten_nguoi_nhan: addr.ten_nguoi_nhan, 
        so_dien_thoai: addr.so_dien_thoai, 
        dia_chi_cu_the: addr.dia_chi_cu_the, 
        la_mac_dinh: addr.la_mac_dinh === 1 
      }
      this.modalState = 'form'
    },
    async saveAddress() {
      this.isSavingAddress = true
      try {
        if (this.editingAddressId) {
          await axiosClient.put(`/client/profile/addresses/${this.editingAddressId}`, this.addressForm)
          Swal.fire({ icon: 'success', title: 'Thành công', text: 'Cập nhật địa chỉ thành công!', timer: 1500, showConfirmButton: false })
        } else {
          await axiosClient.post('/client/profile/addresses', this.addressForm)
          Swal.fire({ icon: 'success', title: 'Thành công', text: 'Thêm địa chỉ mới thành công!', timer: 1500, showConfirmButton: false })
        }
        await this.fetchAddresses()
        this.modalState = 'list'
      } catch (error) {
        console.error(error)
        Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể lưu địa chỉ. Vui lòng thử lại!', confirmButtonColor: '#f82249' })
      } finally {
        this.isSavingAddress = false
      }
    },
    async fetchAddresses() {
      try {
        const res = await axiosClient.get('/client/profile/addresses')
        this.addresses = Array.isArray(res) ? res : (res.data || [])
        if (this.addresses.length > 0 && !this.selectedAddressId) {
          const defaultAddr = this.addresses.find(a => a.la_mac_dinh) || this.addresses[0]
          this.selectedAddressId = defaultAddr.id
        }
      } catch (error) {
        console.error('Lỗi khi tải sổ địa chỉ', error)
      }
    },
    async submitQuotation() {
      if (this.selectedItems.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn sản phẩm', text: 'Vui lòng chọn ít nhất 1 sản phẩm!', confirmButtonColor: '#f82249' })
        return
      }

      if (!this.isAuthenticated) {
        if (!this.checkoutForm.name.trim() || !this.checkoutForm.phone.trim()) {
          Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng điền đầy đủ Họ Tên và Số điện thoại!', confirmButtonColor: '#f82249' })
          return
        }
        if (!this.checkoutForm.address.trim()) {
          Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng điền địa chỉ giao hàng!', confirmButtonColor: '#f82249' })
          return
        }
      } else {
        if (!this.selectedAddressId) {
          Swal.fire({ icon: 'warning', title: 'Thiếu địa chỉ', text: 'Vui lòng chọn hoặc thêm một địa chỉ giao hàng!', confirmButtonColor: '#f82249' })
          return
        }
      }

      const selectedCartItems = this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung))
      
      let finalName = this.checkoutForm.name.trim()
      let finalPhone = this.checkoutForm.phone.trim()
      let finalAddress = this.checkoutForm.address ? this.checkoutForm.address.trim() : null

      if (this.isAuthenticated && this.selectedAddress) {
        finalName = this.selectedAddress.ten_nguoi_nhan
        finalPhone = this.selectedAddress.so_dien_thoai
        finalAddress = this.selectedAddress.dia_chi_cu_the
      }

      const payload = {
        user_id: this.userProfile ? this.userProfile.id : null,
        loai_yeu_cau: this.checkoutType,
        loai_don: this.isB2B ? 'b2b' : 'b2c',
        khach_hang_ten: finalName,
        khach_hang_sdt: finalPhone,
        dia_chi_giao_hang: finalAddress,
        khach_hang_email: this.checkoutForm.email || null,
        ghi_chu_khach_hang: this.checkoutForm.note || null,
        chi_tiet: selectedCartItems.map(item => ({
          id_phu_tung:  item.id,
          id_san_pham:  item.id,
          loai_san_pham: item.loai_san_pham || (item.ma_san_pham && !item.ma_phu_tung ? 'san_pham' : 'phu_tung'),
          ten_phu_tung: item.ten_phu_tung,
          ma_phu_tung:  item.ma_phu_tung,
          so_luong:     item.quantity,
          gia_ban:      item.is_gia_lien_he ? 0 : item.gia_ban
        }))
      }

      this.isSubmitting = true
      try {
        if (!navigator.onLine) {
          const { saveDraft } = await import('../../../utils/offlineSyncService')
          await saveDraft(this.checkoutType, payload, '/public/yeu-cau-bao-gia')
          Swal.fire({
            icon: 'info',
            title: 'Đã lưu ngoại tuyến!',
            text: 'Hệ thống phát hiện mất kết nối mạng. Đơn hàng của bạn đã được lưu tạm và sẽ tự động gửi đi khi có mạng trở lại.',
            confirmButtonColor: '#f82249'
          })
          this.selectedItems.forEach(id => this.cartStore.removeItem(id))
          this.selectedItems = []
          this.isSubmitting = false
          return
        }

        const res = await axiosClient.post('/public/yeu-cau-bao-gia', payload)
        if (res.success || (res.data && res.data.success)) {
          const responseData = res.success ? res.data : res.data.data
          this.pendingOrderId = responseData.code || responseData.ma_yeu_cau || ('YCBG-' + Date.now())
          const token = responseData.token
          
          this.selectedItems.forEach(id => this.cartStore.removeItem(id))
          this.selectedItems = []
          
          const descText = `Hệ thống đã tiếp nhận đơn yêu cầu #${this.pendingOrderId}. Đội ngũ JPM Marine sẽ kiểm tra phụ tùng, tính cước vận chuyển và tiền cọc, sau đó gửi link báo giá qua Zalo cho Quý khách.`
          const chatMsg = `Chào JPM Marine, tôi vừa gửi đơn hàng #${this.pendingOrderId}. Nhờ Kỹ thuật viên kiểm tra và báo giá giúp tôi nhé!`
            
          Swal.fire({
            icon: 'success',
            title: 'Gửi Đơn Thành Công!',
            html: `
              <p class="text-muted mb-2">Mã Đơn / Yêu Cầu của bạn:</p>
              <div class="fw-bold fs-3 mb-3 text-primary">#${this.pendingOrderId}</div>
              <p style="font-size: 0.95rem;">${descText}</p>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-chat-dots me-1"></i> Nhắn Zalo Cho JPM',
            cancelButtonText: token ? 'Xem Trang Đợi Báo Giá' : (this.isAuthenticated ? 'Lịch Sử Đơn Hàng' : 'Theo Dõi Đơn Hàng'),
            confirmButtonColor: '#0068ff',
            cancelButtonColor: '#6c757d'
          }).then((result) => {
            if (result.isConfirmed) {
              const zaloPhone = '0123456789'
              window.open(`https://zalo.me/${zaloPhone}?text=${encodeURIComponent(chatMsg)}`, '_blank')
              if (token) {
                this.$router.push(`/bao-gia/${token}`)
              }
            } else if (token) {
              this.$router.push(`/bao-gia/${token}`)
            } else if (this.isAuthenticated) {
              this.$router.push('/lich-su-don-hang')
            } else {
              this.$router.push(`/tra-cuu-don-hang?code=${this.pendingOrderId}&phone=${finalPhone}`)
            }
          })
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Đã có lỗi xảy ra. Vui lòng thử lại!'
        Swal.fire({ icon: 'error', title: 'Gửi yêu cầu thất bại', text: msg, confirmButtonColor: '#f82249' })
      } finally {
        this.isSubmitting = false
      }
    }
  }
}
</script>

<style scoped>
/* ===============================================================
   CART PAGE DESIGN SYSTEM — UNIFIED JPM MARINE DNA
   Tokens: Cards 12px, Inputs/Buttons 8px, Deep Navy & Marine Red
   =============================================================== */
.cart-page-bg {
  min-height: 100vh;
  padding-bottom: 60px;
  background-color: #f8fafc;
  font-family: var(--default-font, system-ui, -apple-system, sans-serif);
  color: var(--default-color, #212529);
}

/* ===== BANNER HEADER ===== */
.cart-header-banner {
  padding-top: 100px;
  padding-bottom: 28px;
  background: #ffffff;
  border-bottom: 1px solid #edf2f7;
  margin-bottom: 28px;
}

.custom-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  content: ">";
  font-size: 1.15rem;
  line-height: 1;
  vertical-align: middle;
  color: #94a3b8;
  padding: 0 8px;
}

.breadcrumb-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.15s ease;
}

.breadcrumb-link:hover {
  color: var(--accent-color, #f82249);
}

.cart-eyebrow {
  display: inline-flex;
  align-items: center;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--accent-color, #f82249);
  background: rgba(248, 34, 73, 0.08);
  padding: 4px 10px;
  border-radius: 6px;
  margin-bottom: 8px;
}

.cart-page-title {
  font-size: 2rem;
  font-weight: 800;
  color: #0A1128;
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.cart-title-accent {
  color: var(--accent-color, #f82249);
}

.cart-page-subtitle {
  color: #64748b;
  font-size: 0.95rem;
  max-width: 650px;
}

.cart-header-trust {
  display: flex;
}

.trust-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f8fafc;
  border: 1px solid #edf2f7;
  border-radius: 10px;
  padding: 10px 16px;
}

.trust-icon-wrap {
  font-size: 1.5rem;
  line-height: 1;
}

.trust-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #0A1128;
}

.trust-sub {
  font-size: 0.75rem;
  color: #64748b;
}

/* ===== SHARED 12PX CARDS ===== */
.catalog-cart-card {
  border-radius: 12px !important;
  border: 1px solid #edf2f7 !important;
  background: #ffffff;
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  overflow: hidden;
}

.jpm-card-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0A1128;
  letter-spacing: -0.2px;
}

.badge-item-count {
  background: #fff1f2;
  color: #e11d48;
  border: 1px solid #fecdd3;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 999px;
}

.rounded-8 {
  border-radius: 8px !important;
}

/* ===== TABLE STYLING ===== */
.custom-cart-table {
  width: 100%;
}

.custom-cart-table thead th {
  background-color: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border-bottom: 1px solid #edf2f7;
  padding: 14px 16px;
}

.custom-cart-table tbody td {
  padding: 16px;
  border-bottom: 1px solid #edf2f7;
  vertical-align: middle;
}

.th-check {
  padding-left: 18px !important;
  padding-right: 12px !important;
}

.table-row-hover {
  transition: background-color 0.15s ease;
}

.table-row-hover:hover {
  background-color: #fbfcfe;
}

.custom-checkbox {
  width: 17px;
  height: 17px;
  border-color: #cbd5e1;
  border-radius: 4px;
  cursor: pointer;
  margin: 0 auto;
  vertical-align: middle;
  transition: all 0.15s ease-in-out;
}

.custom-checkbox:checked {
  background-color: var(--accent-color, #f82249);
  border-color: var(--accent-color, #f82249);
}

.custom-checkbox:focus {
  border-color: var(--accent-color, #f82249);
  box-shadow: 0 0 0 2px rgba(248, 34, 73, 0.2);
}

.cart-item-thumb {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  border: 1px solid #edf2f7;
  background: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
}

.cart-item-thumb img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.part-code {
  font-weight: 700;
  color: #0A1128;
  font-size: 0.95rem;
}

.part-name {
  color: #475569;
  font-size: 0.85rem;
  line-height: 1.4;
  font-weight: 400;
}

.part-price-val {
  color: var(--accent-color, #f82249);
  font-weight: 700;
  font-size: 0.95rem;
  white-space: nowrap;
}

.part-price-quote {
  color: var(--accent-color, #f82249);
  font-weight: 700;
  font-size: 0.9rem;
  background: rgba(248, 34, 73, 0.08);
  padding: 3px 10px;
  border-radius: 6px;
  white-space: nowrap;
}

.badge-san-pham {
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
}

.badge-phu-tung {
  background-color: #fff1f2;
  color: #e11d48;
  border: 1px solid #fecdd3;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
}

/* Quantity Selector: 38px height, 8px radius */
.qty-selector {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  height: 38px;
  width: 110px;
}

.qty-btn {
  background: #f8fafc;
  border: none;
  color: #334155;
  width: 34px;
  height: 100%;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  cursor: pointer;
}

.qty-btn:hover {
  background-color: #edf2f7;
  color: #0A1128;
}

.qty-input {
  width: 42px;
  height: 100%;
  border: none;
  text-align: center;
  font-weight: 700;
  color: #0A1128;
  background: transparent;
  font-size: 0.9rem;
}

.qty-input:focus {
  outline: none;
}

.btn-delete-item {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #fee2e2;
  background: #fff5f5;
  color: #ef4444;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  cursor: pointer;
}

.btn-delete-item:hover {
  background: #ef4444;
  color: #ffffff;
  border-color: #ef4444;
}

/* ===== REASSURANCE / WORKFLOW TRUST BOX ===== */
.workflow-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #0A1128;
}

.workflow-step {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #edf2f7;
  height: 100%;
}

.step-num {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--accent-color, #f82249);
  opacity: 0.85;
  line-height: 1;
}

.step-heading {
  font-size: 0.85rem;
  font-weight: 700;
  color: #0A1128;
  margin-bottom: 4px;
}

.step-desc {
  font-size: 0.78rem;
  color: #64748b;
  line-height: 1.4;
}

/* ===== RIGHT COLUMN / CHECKOUT SIDEBAR ===== */
.sticky-sidebar {
  position: sticky;
  top: 90px;
}

.summary-box {
  background: #f8fafc;
  border: 1px solid #edf2f7;
}

.summary-price-accent {
  color: var(--accent-color, #f82249);
}

.b2b-notice-box {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
  font-size: 0.78rem;
  line-height: 1.45;
  padding: 10px 12px;
  border-radius: 8px;
}

.b2b-notice-box i {
  color: #16a34a !important;
  font-size: 1rem;
}

.field-label {
  font-size: 0.85rem;
  font-weight: 700;
  color: #0A1128;
  margin-bottom: 6px;
}

.custom-form-input {
  border-radius: 8px !important;
  border: 1px solid #e2e8f0;
  font-size: 0.875rem;
  padding: 9px 13px;
  color: #0A1128;
  transition: all 0.2s ease;
}

.custom-form-input:focus {
  border-color: #f82249 !important;
  box-shadow: inset 0 0 0 1px #f82249, 0 2px 6px 0 rgba(248, 34, 73, 0.25) !important;
  outline: none;
}

.custom-textarea {
  resize: vertical;
  min-height: 64px;
}

/* Address Box in Sidebar */
.address-preview-card {
  background: #f8fafc;
  border: 1px solid #edf2f7;
}

.address-change-link {
  color: var(--accent-color, #f82249);
  font-weight: 600;
  font-size: 0.82rem;
}

.address-change-link:hover {
  text-decoration: underline !important;
}

.no-address-box {
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
}

/* Submit Button */
.btn-jpm-submit {
  height: 48px;
  background-color: var(--accent-color, #f82249);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(248, 34, 73, 0.25);
}

.btn-jpm-submit:hover:not(:disabled) {
  background-color: #d91438;
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(248, 34, 73, 0.35);
}

.btn-jpm-submit:disabled {
  background-color: #e2e8f0;
  color: #94a3b8;
  box-shadow: none;
  cursor: not-allowed;
}

.security-guarantee-text {
  font-size: 0.8rem;
  color: #64748b;
}

/* ===== EMPTY CART STATE ===== */
.empty-cart-card {
  border-radius: 12px !important;
  background: #ffffff;
  box-shadow: 0 4px 20px rgba(10, 17, 40, 0.04);
}

.empty-icon-wrap {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #fff1f2;
  color: var(--accent-color, #f82249);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
}

/* ===== ADDRESS MODAL ===== */
.custom-modal-content {
  border-radius: 12px;
  border: 1px solid #edf2f7;
  box-shadow: 0 20px 45px -10px rgba(10, 17, 40, 0.2);
}

.address-item {
  border: 1px solid #e2e8f0;
  background: #ffffff;
  transition: all 0.15s ease;
}

.address-item:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.address-item.selected-addr {
  border-color: var(--accent-color, #f82249) !important;
  background: rgba(248, 34, 73, 0.02);
}

.cursor-pointer {
  cursor: pointer;
}
</style>
