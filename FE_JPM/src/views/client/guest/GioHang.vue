<template>
  <div class="cart-page-bg main-page">
    
    <!-- ========================================================
         HEADER SECTION (Khôi phục nguyên bản 2 hôm trước)
         ======================================================== -->
    <section id="cart" class="section pb-5 inner-page-section">
      <div class="container">
        
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-12 d-flex flex-wrap justify-content-between align-items-end gap-3" data-aos="fade-down">
            <div>
              <h2 class="jpm-page-title mb-1">Giỏ Hàng <span style="color: var(--jpm-color-red-marine, #F82249);">Báo Giá</span></h2>
              <p class="text-muted mb-0">Kiểm tra phụ tùng và gửi yêu cầu để nhận mức chiết khấu tốt nhất từ JPM Marine.</p>
            </div>
            <router-link to="/san-pham" class="btn-back-diagram" title="Tiếp tục mua sắm">
              <i class="bi bi-arrow-left"></i>
              <span>Tiếp tục mua sắm</span>
            </router-link>
          </div>
        </div>
        
        <!-- TRƯỜNG HỢP 1: GIỎ HÀNG CÓ SẢN PHẨM -->
        <div class="row gy-4" v-if="cartItems.length > 0">
          
          <!-- CỘT TRÁI (COL-LG-7 COL-XL-8): DANH SÁCH PHỤ TÙNG & QUY TRÌNH TIẾP NHẬN -->
          <div class="col-lg-7 col-xl-8">
            
            <!-- 1. Thẻ Bảng Danh Sách Phụ Tùng Đã Chọn (Card chuẩn 12px bo góc theo Trang Chủ) -->
            <div class="card catalog-cart-card mb-4" data-aos="fade-up">
              <div class="card-header bg-white px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center">
                  <h5 class="mb-0 jpm-card-title">Danh Sách Phụ Tùng Đã Chọn</h5>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="badge-item-count">{{ cartItems.length }} phụ tùng</span>
                  <button v-if="selectedItems.length > 0" class="btn btn-outline-danger btn-sm btn-rounded-8" @click="removeSelectedItems">
                    <i class="bi bi-trash3 me-1"></i> Xóa đã chọn ({{ selectedItems.length }})
                  </button>
                </div>
              </div>
              
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0 custom-cart-table">
                    <thead>
                      <tr>
                        <th class="text-center th-check">
                          <input class="form-check-input custom-checkbox" type="checkbox" v-model="selectAll" title="Chọn tất cả">
                        </th>
                        <th class="text-center th-thumb">Ảnh</th>
                        <th class="text-start">Mã & Tên Phụ Tùng</th>
                        <th class="text-center th-price">Đơn Giá</th>
                        <th class="text-center th-qty">Số Lượng</th>
                        <th class="text-center th-action">Thao Tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in cartItems" :key="item.ma_phu_tung" class="table-row-hover">
                        <!-- Cột Checkbox (căn lề thẳng hàng với Card Header px-4) -->
                        <td class="text-center th-check">
                          <input class="form-check-input custom-checkbox" type="checkbox" :value="item.ma_phu_tung" v-model="selectedItems">
                        </td>
                        <!-- Cột Ảnh / Thumbnail (8px bo góc chuẩn Trang Chủ) -->
                        <td class="text-center th-thumb">
                          <div class="cart-item-thumb">
                            <img v-if="item.hinh_anh" :src="getImageUrl(item.hinh_anh)" :alt="item.ten_phu_tung">
                            <i v-else class="bi bi-image text-muted fs-4"></i>
                          </div>
                        </td>
                        <!-- Cột Mã & Tên Phụ Tùng -->
                        <td>
                          <div>
                            <span class="part-code">{{ item.ma_phu_tung }}</span>
                          </div>
                          <div class="part-name mt-1">{{ item.ten_phu_tung }}</div>
                        </td>
                        <!-- Cột Đơn Giá -->
                        <td class="text-center th-price">
                          <span v-if="item.is_gia_lien_he || !item.gia_ban || parseFloat(item.gia_ban) <= 0" class="part-price-quote">
                            Liên hệ
                          </span>
                          <span v-else class="part-price-val">
                            {{ formatPrice(item.gia_ban) }}
                          </span>
                        </td>
                        <!-- Cột Số Lượng Tương Tác Cơ Khí (8px bo góc chuẩn nút Trang Chủ) -->
                        <td class="text-center th-qty">
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
                        <!-- Cột Nút Xóa Từng Món (8px bo góc) -->
                        <td class="text-center th-action">
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

          </div>

          <!-- CỘT PHẢI (COL-LG-5 COL-XL-4): SIDEBAR TỔNG KẾT & FORM ĐẶT HÀNG (STICKY) -->
          <div class="col-lg-5 col-xl-4">
            <div class="card catalog-cart-card sticky-sidebar" data-aos="fade-up" data-aos-delay="150">
              <div class="card-header cart-sidebar-header bg-white px-4 py-3 border-bottom d-flex align-items-center" style="min-height: 60px;">
                <h5 class="mb-0 jpm-card-title">Thông tin đặt hàng & báo giá</h5>
              </div>
              
              <div class="card-body p-4">
                
                <!-- 1. TỔNG QUAN TÀI CHÍNH -->
                <div class="summary-section mb-4 pb-3 border-bottom">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="summary-label">Số lượng đã chọn:</span>
                    <strong class="summary-val">{{ totalQuantity }} món</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-baseline pt-1">
                    <span class="summary-total-label">Tạm tính tiền hàng:</span>
                    <div class="text-end">
                      <strong class="summary-price-accent fs-5 fw-bold" v-if="selectedItems.length === 0">0 ₫</strong>
                      <strong class="summary-price-accent fs-5 fw-bold" v-else-if="hasPricedItems">{{ formatPrice(pricedTotalAmount) }}</strong>
                      <strong class="summary-price-accent fs-5 fw-bold" v-else>Liên hệ báo giá</strong>
                      <div v-if="isMixedCart && selectedItems.length > 0" class="small text-muted fst-italic mt-1">
                        (+ {{ contactItemCount }} phụ tùng chờ tư vấn giá)
                      </div>
                    </div>
                  </div>
                  
                  <!-- Thông báo quy trình vận chuyển & cọc -->
                  <div class="b2b-notice-box mt-3">
                    <i class="bi bi-truck flex-shrink-0"></i>
                    <span>Cước vận chuyển chành xe và tiền đặt cọc sẽ được Kỹ thuật viên JPM liên hệ báo giá chính xác sau khi tiếp nhận đơn.</span>
                  </div>
                </div>

                <!-- 2. THÔNG TIN GIAO HÀNG & BÁO GIÁ -->
                <form novalidate @submit.prevent="submitQuotation">
                  
                  <div class="form-partition-header d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">Thông tin nhận hàng</span>
                  </div>

                  <!-- Trường hợp 2A: Khách vãng lai (Guest) -->
                  <div v-if="!isAuthenticated">
                    <!-- HÀNG 2 CỘT GỌN GÀNG: HỌ TÊN + SỐ ĐIỆN THOẠI / ZALO -->
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label field-label">Họ và tên người nhận<span class="text-danger">*</span></label>
                        <input type="text" class="form-control custom-form-input" v-model="checkoutForm.name" placeholder="Nhập họ và tên...">
                      </div>
                      <div class="col-6">
                        <label class="form-label field-label">Số điện thoại / Zalo<span class="text-danger">*</span></label>
                        <input type="tel" class="form-control custom-form-input" v-model="checkoutForm.phone" placeholder="Nhập SĐT / Zalo...">
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label field-label">Địa chỉ nhận hàng / Chành xe<span class="text-danger">*</span></label>
                      <textarea class="form-control custom-form-input custom-textarea" v-model="checkoutForm.address" rows="2" placeholder="Số nhà, đường, tỉnh/thành phố hoặc tên bến chành xe..."></textarea>
                    </div>
                  </div>

                  <!-- Trường hợp 2B: Khách đã đăng nhập (Member có sổ địa chỉ) -->
                  <div v-else class="mb-3">
                    <!-- Trạng thái 1: ĐÃ CHỌN ĐỊA CHỈ -->
                    <div v-if="selectedAddress" class="address-preview-card p-3">
                      <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                          <strong class="addr-recipient-name">{{ selectedAddress.ten_nguoi_nhan }}</strong>
                          <span v-if="selectedAddress.la_mac_dinh" class="addr-default-badge">Mặc định</span>
                        </div>
                        <button type="button" class="btn btn-link p-0 text-decoration-none address-change-link flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addressModal" @click="modalState = 'list'">
                          Thay đổi <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                      </div>
                      <div class="addr-meta-row mb-1">
                        <i class="bi bi-telephone"></i>
                        <span>{{ selectedAddress.so_dien_thoai }}</span>
                      </div>
                      <div class="addr-meta-row addr-location-row">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ selectedAddress.dia_chi_cu_the }}</span>
                      </div>
                    </div>

                    <!-- Trạng thái 2: CHƯA CÓ ĐỊA CHỈ -->
                    <div v-else class="no-address-box p-3 text-center">
                      <i class="bi bi-geo-alt fs-4 mb-1 d-block text-secondary"></i>
                      <p class="text-muted small mb-2">Bạn chưa có địa chỉ nào trong Sổ địa chỉ.</p>
                      <button type="button" class="btn btn-outline-danger btn-sm btn-rounded-8" data-bs-toggle="modal" data-bs-target="#addressModal" @click.prevent="openAddAddress">
                        <i class="bi bi-plus-lg me-1"></i> Thêm địa chỉ ngay
                      </button>
                    </div>
                  </div>

                  <!-- Email nhận báo giá / hóa đơn (Dùng chung cho cả Khách vãng lai & Khách đã đăng nhập) -->
                  <div class="mb-3">
                    <label class="form-label field-label">
                      <span>Email nhận báo giá / hóa đơn</span>
                      <span class="field-optional-tag">(Không bắt buộc)</span>
                    </label>
                    <input type="email" class="form-control custom-form-input" v-model="checkoutForm.email" placeholder="Nhập email nhận báo giá hoặc hóa đơn...">
                  </div>

                  <!-- Ghi chú chung (Gọn gàng 2 dòng) -->
                  <div class="mb-4">
                    <label class="form-label field-label">
                      <span>Ghi chú đơn hàng</span>
                      <span class="field-optional-tag">(Không bắt buộc)</span>
                    </label>
                    <textarea class="form-control custom-form-input custom-textarea" v-model="checkoutForm.note" rows="2" placeholder="Ghi chú số khung, chành xe nhận, yêu cầu hóa đơn..."></textarea>
                  </div>

                  <!-- 3. HÀNH ĐỘNG & BẢO ĐẢM -->
                  <button
                    type="submit"
                    class="btn btn-danger btn-jpm-submit w-100 d-flex justify-content-center align-items-center gap-2"
                    :disabled="isSubmitting || selectedItems.length === 0"
                  >
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm"></span>
                    <template v-else>
                      <span class="fw-bold">{{ buttonText }}</span>
                      <i v-if="selectedItems.length > 0" class="bi bi-arrow-right"></i>
                    </template>
                  </button>
                  
                  <div class="security-guarantee-text text-center mt-3 mb-0 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-shield-fill-check text-success fs-6"></i>
                    <span>Cam kết bảo mật thông tin & chính hãng 100%</span>
                  </div>
                </form>

              </div>
            </div>
          </div>

        </div>

        <!-- Empty Cart (Chuẩn Client JPM Design System) -->
        <div class="empty-cart-container text-center py-5" v-else data-aos="fade-up">
          <div class="empty-cart-illustration mb-3">
            <img 
              src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" 
              alt="Giỏ hàng trống" 
              class="empty-cart-img"
              @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-flex'"
            >
            <div class="empty-icon-fallback" style="display: none; align-items: center; justify-content: center;">
              <i class="bi bi-cart-x" style="font-size: 6.5rem; color: var(--jpm-neutral-400, #94A3B8);"></i>
            </div>
          </div>
          <h3 class="empty-cart-title mb-2">Giỏ hàng của bạn đang trống</h3>
          <p class="empty-cart-desc mx-auto mb-4">
            Hãy tìm kiếm phụ tùng bạn cần và thêm vào giỏ hàng nhé!
          </p>
          <router-link 
            to="/san-pham" 
            class="btn btn-empty-cart-action text-decoration-none"
          >
            <span>Khám phá phụ tùng máy thủy</span>
            <i class="bi bi-arrow-right"></i>
          </router-link>
        </div>

      </div>
    </section>

    <!-- ========================================================
         MODAL SỔ ĐỊA CHỈ GIAO HÀNG (Chuẩn Bootstrap 5 & Bo góc 12px)
         ======================================================== -->
    <Teleport to="body">
      <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content custom-modal-content">
            <div class="modal-header border-bottom px-4 py-3">
              <h5 class="modal-title fw-bold text-dark fs-5" id="addressModalLabel">
                {{ modalState === 'list' ? 'Sổ địa chỉ giao hàng' : (editingAddressId ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới') }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <!-- Danh sách địa chỉ đã lưu -->
              <div v-if="modalState === 'list'" class="address-list">
                <div v-if="addresses.length === 0" class="text-center py-4 text-muted small">
                  Chưa có địa chỉ nào được lưu.
                </div>
                <div v-for="addr in addresses" :key="addr.id" class="address-item p-3 mb-3 border position-relative d-flex align-items-start gap-3" :class="{'selected-addr': selectedAddressId === addr.id}" style="cursor: pointer;" @click="selectedAddressId = addr.id">
                  <div class="radio-wrap mt-1 flex-shrink-0">
                    <input class="form-check-input custom-radio" type="radio" :name="'address_radio'" :value="addr.id" v-model="selectedAddressId">
                  </div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                      <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="fw-bold text-dark">{{ addr.ten_nguoi_nhan }}</span>
                        <span v-if="addr.la_mac_dinh" class="addr-default-badge">Mặc định</span>
                      </div>
                      <a href="#" class="text-decoration-none small fw-semibold flex-shrink-0" style="color: var(--jpm-color-red-marine, #F82249);" @click.stop.prevent="openEditAddress(addr)">Sửa</a>
                    </div>
                    <div class="addr-meta-row mb-1">
                      <i class="bi bi-telephone"></i>
                      <span>{{ addr.so_dien_thoai }}</span>
                    </div>
                    <div class="addr-meta-row">
                      <i class="bi bi-geo-alt"></i>
                      <span>{{ addr.dia_chi_cu_the }}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Form thêm / sửa địa chỉ mới -->
              <div v-else class="address-form-inline">
                <form @submit.prevent="saveAddress">
                  <div class="mb-3">
                    <label class="form-label field-label">Họ và tên người nhận<span class="text-danger">*</span></label>
                    <input type="text" class="form-control custom-form-input" v-model="addressForm.ten_nguoi_nhan" required placeholder="Nhập họ và tên người nhận...">
                  </div>
                  <div class="mb-3">
                    <label class="form-label field-label">Số điện thoại / Zalo<span class="text-danger">*</span></label>
                    <input type="tel" class="form-control custom-form-input" v-model="addressForm.so_dien_thoai" required placeholder="Nhập SĐT / Zalo...">
                  </div>
                  <div class="mb-3">
                    <label class="form-label field-label">Địa chỉ nhận hàng / Chành xe<span class="text-danger">*</span></label>
                    <textarea class="form-control custom-form-input custom-textarea" v-model="addressForm.dia_chi_cu_the" required rows="3" placeholder="Số nhà, đường, tỉnh/thành phố hoặc tên bến chành xe..."></textarea>
                  </div>
                  <div class="mb-3 modal-check-row">
                    <input type="checkbox" class="form-check-input custom-checkbox modal-checkbox-input" id="laMacDinh" v-model="addressForm.la_mac_dinh">
                    <label class="form-check-label small fw-medium text-secondary cursor-pointer mb-0" for="laMacDinh">Đặt làm địa chỉ nhận hàng mặc định</label>
                  </div>
                  <div class="d-flex justify-content-end gap-2 pt-3 mt-4 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 btn-rounded-8" @click="modalState = 'list'">Hủy</button>
                    <button type="submit" class="btn btn-danger px-4 btn-rounded-8" :disabled="isSavingAddress">
                      <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-2"></span>
                      Lưu địa chỉ
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 d-flex justify-content-between align-items-center gap-2" v-if="modalState === 'list'">
              <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 btn-rounded-8" @click.prevent="openAddAddress">
                <i class="bi bi-plus-lg"></i>
                <span>Thêm địa chỉ mới</span>
              </button>
              <button type="button" class="btn btn-danger px-4 btn-rounded-8" data-bs-dismiss="modal">
                Xác nhận
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL XÁC NHẬN ĐÃ GỬI YÊU CẦU BÁO GIÁ (JPM MINIMALIST LUXURY) -->
      <div
        v-if="orderConfirmModal.show"
        class="lux-confirm-backdrop"
        role="dialog"
        aria-modal="true"
        aria-labelledby="luxConfirmTitle"
        @click.self="closeConfirmModal"
      >
        <div class="lux-confirm-card">
          <button
            type="button"
            class="lux-confirm-close"
            aria-label="Đóng"
            @click="closeConfirmModal"
          >
            <i class="bi bi-x-lg"></i>
          </button>

          <div class="lux-confirm-head">
            <div class="lux-confirm-icon">
              <i class="bi bi-check-lg"></i>
            </div>
            <h4 id="luxConfirmTitle" class="lux-confirm-title">Đã tiếp nhận yêu cầu báo giá</h4>
            <p class="lux-confirm-sub">
              Kỹ thuật viên JPM Marine sẽ kiểm tra mã phụ tùng, cước vận chuyển và gửi bảng báo giá chi tiết qua Zalo cho bạn.
            </p>
          </div>

          <div class="lux-confirm-code-box">
            <div class="min-w-0">
              <span class="lux-confirm-code-label">MÃ YÊU CẦU</span>
              <div class="lux-confirm-code-val">#{{ orderConfirmModal.code }}</div>
            </div>
            <button
              type="button"
              class="lux-copy-code-btn"
              :class="{ copied: orderConfirmModal.copied }"
              @click="copyConfirmCode"
            >
              <i :class="orderConfirmModal.copied ? 'bi bi-check2' : 'bi bi-copy'"></i>
              <span>{{ orderConfirmModal.copied ? 'Đã chép' : 'Sao chép' }}</span>
            </button>
          </div>

          <div class="lux-confirm-steps">
            <div class="lux-step is-done">
              <span class="lux-step-dot"></span>
              <div class="lux-step-text">
                <strong>Đã ghi nhận yêu cầu</strong>
                <span>{{ orderConfirmModal.itemCount }} sản phẩm đã được chuyển tới bộ phận kỹ thuật</span>
              </div>
            </div>
            <div class="lux-step is-next">
              <span class="lux-step-dot"></span>
              <div class="lux-step-text">
                <strong>Kiểm tra mã OEM &amp; cước chành xe</strong>
                <span>Xác nhận tồn kho, thời gian giao và mức đặt cọc (nếu có)</span>
              </div>
            </div>
            <div class="lux-step">
              <span class="lux-step-dot"></span>
              <div class="lux-step-text">
                <strong>Gửi link báo giá qua Zalo</strong>
                <span>Bạn duyệt báo giá và theo dõi tiến độ trực tiếp trên hệ thống</span>
              </div>
            </div>
          </div>

          <div class="lux-confirm-actions">
            <button
              type="button"
              class="btn btn-outline-secondary lux-confirm-btn"
              @click="openConfirmZalo"
            >
              <i class="bi bi-chat-dots"></i>
              <span>Nhắn Zalo kỹ thuật</span>
            </button>
            <button
              type="button"
              class="btn btn-danger lux-confirm-btn"
              @click="goToConfirmTarget"
            >
              <span>{{ orderConfirmModal.token ? 'Theo dõi báo giá' : (isAuthenticated ? 'Xem lịch sử đơn' : 'Về trang chủ') }}</span>
              <i class="bi bi-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script>
import { useCartStore } from '../../../stores/cartStore'
import { useSiteSettings } from '../../../composables/useSiteSettings'
import axiosClient from '../../../api/axiosClient'
import { getImageUrl as resolveImageUrl } from '../../../utils/imageUrl'
import Swal from 'sweetalert2'

export default {
  name: 'GioHang',
  setup() {
    const cartStore = useCartStore()
    const { settings: siteSettings } = useSiteSettings()
    return { cartStore, siteSettings }
  },
  data() {
    return {
      // Thông tin checkout cho khách vãng lai (Guest)
      checkoutForm: {
        name: '',
        phone: '',
        email: '',
        note: '',
        address: ''
      },
      selectedItems: [], // Danh sách mã phụ tùng được tick chọn
      hasAutoSelected: false,
      isSubmitting: false,
      pendingOrderId: '',
      orderConfirmModal: {
        show: false,
        code: '',
        token: null,
        itemCount: 0,
        copied: false
      },

      // Thông tin khách đã đăng nhập (Member)
      isAuthenticated: false,
      userProfile: null,
      addresses: [],
      selectedAddressId: null,
      modalState: 'list', // 'list' | 'form'
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
      return this.selectedItems.length === 0 ? 'Chọn sản phẩm để báo giá' : 'Gửi yêu cầu báo giá'
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
  watch: {
    cartItems: {
      immediate: true,
      handler(items) {
        if (!this.hasAutoSelected && Array.isArray(items) && items.length > 0) {
          this.selectedItems = items.map(item => item.ma_phu_tung)
          this.hasAutoSelected = true
        }
      }
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
      return resolveImageUrl(path, 'https://placehold.co/400x300/f8f9fa/a1acb8?text=JPM+Marine')
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
        this.cartStore.removeItems(this.selectedItems)
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
        Swal.fire({
          icon: 'warning',
          title: 'Chưa chọn sản phẩm',
          text: 'Vui lòng tích chọn ít nhất 1 sản phẩm trong giỏ hàng để gửi yêu cầu báo giá!',
          confirmButtonColor: '#f82249',
          confirmButtonText: 'Đã hiểu'
        })
        return
      }

      if (!this.isAuthenticated) {
        if (!this.checkoutForm.name || !this.checkoutForm.name.trim()) {
          Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập Họ và tên người nhận!',
            confirmButtonColor: '#f82249',
            confirmButtonText: 'Đã hiểu'
          })
          return
        }
        if (!this.checkoutForm.phone || !this.checkoutForm.phone.trim()) {
          Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập Số điện thoại / Zalo để nhận báo giá!',
            confirmButtonColor: '#f82249',
            confirmButtonText: 'Đã hiểu'
          })
          return
        }
        if (!this.checkoutForm.address || !this.checkoutForm.address.trim()) {
          Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập Địa chỉ nhận hàng hoặc tên Chành Xe quen thuộc!',
            confirmButtonColor: '#f82249',
            confirmButtonText: 'Đã hiểu'
          })
          return
        }
      } else {
        if (!this.selectedAddressId) {
          Swal.fire({
            icon: 'warning',
            title: 'Chưa chọn địa chỉ',
            text: 'Vui lòng chọn hoặc thêm địa chỉ nhận hàng trong Sổ địa chỉ!',
            confirmButtonColor: '#f82249',
            confirmButtonText: 'Đã hiểu'
          })
          return
        }
      }

      const selectedCartItems = this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung))
      
      let finalName = (this.checkoutForm.name || '').trim()
      let finalPhone = (this.checkoutForm.phone || '').trim()
      let finalAddress = (this.checkoutForm.address || '').trim()

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
        khach_hang_email: (this.checkoutForm.email || '').trim() || null,
        ghi_chu_khach_hang: (this.checkoutForm.note || '').trim() || null,
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
          this.cartStore.removeItems(this.selectedItems)
          this.selectedItems = []
          this.isSubmitting = false
          return
        }

        const submittedCount = selectedCartItems.reduce((sum, item) => sum + (item.quantity || 1), 0)
        const res = await axiosClient.post('/public/yeu-cau-bao-gia', payload)
        if (res.success || (res.data && res.data.success)) {
          const responseData = res.success ? res.data : res.data.data
          this.pendingOrderId = responseData.code || responseData.ma_yeu_cau || ('YCBG-' + Date.now())
          const token = responseData.token || null

          this.cartStore.removeItems(this.selectedItems)
          this.selectedItems = []

          this.orderConfirmModal = {
            show: true,
            code: this.pendingOrderId,
            token,
            itemCount: submittedCount || selectedCartItems.length,
            copied: false
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Đã có lỗi xảy ra. Vui lòng thử lại!'
        Swal.fire({ icon: 'error', title: 'Gửi yêu cầu thất bại', text: msg, confirmButtonColor: '#f82249' })
      } finally {
        this.isSubmitting = false
      }
    },
    buildZaloUrl(message = '') {
      const rawZalo = this.siteSettings?.social_zalo || ''
      const digits = (rawZalo.match(/\d{8,15}/)?.[0]) || (this.siteSettings?.company_hotline || '0909123456').replace(/\D/g, '')
      const base = rawZalo.startsWith('http') ? rawZalo.split('?')[0] : `https://zalo.me/${digits}`
      return message ? `${base}?text=${encodeURIComponent(message)}` : base
    },
    async copyConfirmCode() {
      const text = this.orderConfirmModal.code
      if (!text) return
      try {
        if (navigator.clipboard) {
          await navigator.clipboard.writeText(text)
        }
        this.orderConfirmModal.copied = true
        setTimeout(() => {
          this.orderConfirmModal.copied = false
        }, 2000)
      } catch (_) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `Mã yêu cầu: #${text}`,
          showConfirmButton: false,
          timer: 2000
        })
      }
    },
    openConfirmZalo() {
      const chatMsg = `Chào JPM Marine, tôi vừa gửi yêu cầu báo giá #${this.orderConfirmModal.code}. Nhờ kỹ thuật viên kiểm tra và báo giá giúp tôi.`
      window.open(this.buildZaloUrl(chatMsg), '_blank', 'noopener,noreferrer')
    },
    goToConfirmTarget() {
      const token = this.orderConfirmModal.token
      this.orderConfirmModal.show = false
      if (token) {
        this.$router.push(`/bao-gia/${token}`)
      } else if (this.isAuthenticated) {
        this.$router.push('/lich-su-don-hang')
      } else {
        this.$router.push('/')
      }
    },
    closeConfirmModal() {
      this.orderConfirmModal.show = false
    }
  }
}
</script>

<style scoped>
/* ===============================================================
   CART PAGE DESIGN SYSTEM — UNIFIED JPM MARINE DNA
   Tokens: Cards 12px (Chuẩn Trang Chủ), Inputs/Buttons 8px, Deep Navy & Marine Red
   =============================================================== */
.cart-page-bg {
  min-height: 100vh;
  background-color: #f8fafc; /* Nền xám Slate cao cấp đồng bộ home-section-slate ở Trang Chủ */
  font-family: var(--default-font, 'Roboto', sans-serif);
  color: var(--default-color, #212529);
}

/* ===== HEADER TITLE (ĐỒNG BỘ DESIGN SYSTEM 2026) ===== */
.jpm-page-title {
  font-family: var(--heading-font, 'Raleway', 'Montserrat', sans-serif);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--heading-color, #0A1128);
  letter-spacing: -0.3px;
}

/* ===== SHARED 12PX CARDS (CHUẨN PRODUCT-CARD VÀ FILTER CONSOLE TRANG CHỦ) ===== */
.catalog-cart-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid #e2e8f0 !important;
  background: #ffffff;
  box-shadow: 0 4px 16px rgba(10, 17, 40, 0.04) !important;
  overflow: hidden;
}

.jpm-card-title {
  font-family: var(--heading-font, 'Raleway', 'Montserrat', sans-serif);
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--jpm-color-navy-deep, #0A1128);
  letter-spacing: -0.2px;
}

.badge-item-count {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: var(--jpm-radius-badge, 6px);
  display: inline-flex;
  align-items: center;
  letter-spacing: 0.3px;
}

/* Utility bo góc 8px đồng bộ Trang Chủ */
.btn-rounded-8 {
  border-radius: var(--jpm-radius-control, 8px) !important;
}

/* ===== TABLE STYLING (ĐỒNG BỘ 100% VỚI SƠ ĐỒ PHỤ TÙNG & THƯƠNG HIỆU) ===== */
.custom-cart-table {
  width: 100%;
}

.custom-cart-table thead th {
  background-color: #f8fafc !important;
  color: #0A1128 !important;
  font-family: var(--heading-font, var(--jpm-font-family));
  font-size: 0.88rem;
  font-weight: 600;
  letter-spacing: 0.2px;
  text-transform: none;
  border-top: none !important;
  border-bottom: 1px solid #e2e8f0 !important;
  padding-top: 13px;
  padding-bottom: 13px;
  vertical-align: middle;
}

.custom-cart-table tbody td {
  padding: 14px 12px;
  border-bottom: 1px solid #edf2f7;
  vertical-align: middle;
  background-color: #ffffff !important;
  transition: background-color 0.15s ease;
}

.custom-cart-table tbody tr:nth-child(odd) td,
.custom-cart-table tbody tr:nth-child(even) td {
  background-color: #ffffff !important;
}

/* Căn lề trái thẳng trục hoàn hảo với padding px-4 (24px) của Card Header */
.th-check {
  width: 56px;
  padding-left: 24px !important;
  padding-right: 12px !important;
}

/* Cột Ảnh & Ref: độ rộng vừa vặn 100px, không bị bẻ gãy 2 dòng text */
.th-thumb {
  width: 100px;
  min-width: 100px;
  white-space: nowrap !important;
}

.th-price {
  width: 150px;
  white-space: nowrap !important;
}

.th-qty {
  width: 140px;
  white-space: nowrap !important;
}

/* Cột Xóa: căn lề phải thẳng trục với px-4 (24px) của Card Header */
.th-action {
  width: 72px;
  padding-right: 24px !important;
  padding-left: 12px !important;
  white-space: nowrap !important;
}

.table-row-hover {
  transition: all 0.15s ease;
}

.table-row-hover:hover td {
  background-color: #f1f5f9 !important;
  --bs-table-accent-bg: transparent !important;
}

.custom-checkbox {
  width: 17px;
  height: 17px;
  border-color: #cbd5e1;
  border-radius: var(--jpm-radius-badge, 6px);
  cursor: pointer;
  margin: 0 auto;
  vertical-align: middle;
  transition: all 0.15s ease-in-out;
}

.custom-checkbox:checked {
  background-color: var(--jpm-color-red-marine, #F82249);
  border-color: var(--jpm-color-red-marine, #F82249);
}

.custom-checkbox:focus {
  border-color: var(--jpm-color-red-marine, #F82249);
  box-shadow: 0 0 0 2px rgba(248, 34, 73, 0.2);
}

.cart-item-thumb {
  width: 60px;
  height: 60px;
  border-radius: var(--jpm-radius-control, 8px); /* Bo góc 8px chuẩn thumbnail Trang Chủ */
  border: 1px solid #e2e8f0;
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
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-weight: 700;
  color: var(--jpm-color-navy-deep, #0A1128);
  font-size: 0.95rem;
}

.part-name {
  color: #475569;
  font-size: 0.85rem;
  line-height: 1.4;
  font-weight: 400;
}

.part-price-val {
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 700;
  font-size: 0.95rem;
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-variant-numeric: lining-nums;
  white-space: nowrap;
}

.part-price-quote {
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 700;
  font-size: 0.9rem;
  font-family: var(--default-font, 'Roboto', sans-serif);
  background: transparent;
  padding: 0;
  white-space: nowrap;
}

/* Quantity Selector: 32px height, 8px radius */
.qty-selector {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  overflow: hidden;
  height: 32px;
  width: 104px;
}

.qty-btn {
  background: #f8fafc;
  border: none;
  color: #475569;
  width: 32px;
  height: 100%;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  cursor: pointer;
}

.qty-btn:hover {
  background-color: #e2e8f0;
  color: #0A1128;
}

.qty-input {
  width: 40px;
  height: 100%;
  border: none;
  text-align: center;
  font-weight: 700;
  color: #0A1128;
  background: transparent;
  font-size: 0.9rem;
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-variant-numeric: lining-nums;
}

.qty-input:focus {
  outline: none;
}

/* Nút xóa: Luôn đỏ outline chuẩn, không hiệu ứng chuyển màu rườm rà */
.btn-delete-item {
  width: 32px;
  height: 32px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid var(--jpm-color-red-marine, #F82249);
  background-color: #ffffff;
  color: var(--jpm-color-red-marine, #F82249);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-delete-item:hover {
  background-color: var(--jpm-color-red-marine, #F82249);
  color: #ffffff;
  border-color: var(--jpm-color-red-marine, #F82249);
}

/* ===== REASSURANCE / WORKFLOW TRUST BOX ===== */
/* ===== RIGHT COLUMN / CHECKOUT SIDEBAR ===== */
.sticky-sidebar {
  position: sticky;
  top: 95px;
}

/* Card chính: Nổi khối cao cấp 12px bo góc, bóng mờ dịu mắt */
.catalog-cart-card {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid #e2e8f0 !important;
  background: #ffffff;
  box-shadow: 0 8px 24px -4px rgba(10, 17, 40, 0.07), 0 2px 6px -2px rgba(10, 17, 40, 0.04) !important;
  overflow: hidden;
}

.cart-sidebar-header {
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9 !important;
}

/* PHÂN VÙNG 1: KHỐI TÀI CHÍNH */
.summary-label {
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
}

.summary-val {
  color: var(--jpm-color-navy-deep, #0A1128);
  font-size: 0.95rem;
  font-weight: 600;
}

.summary-total-label {
  color: var(--jpm-color-navy-deep, #0A1128);
  font-size: 0.9rem;
  font-weight: 600;
}

.summary-zero-amount {
  color: var(--jpm-color-red-marine, #F82249);
  font-size: 1.25rem;
  font-weight: 700;
}

.summary-price-accent {
  color: var(--jpm-color-red-marine, #F82249);
  font-size: 1.25rem;
  letter-spacing: -0.3px;
  font-weight: 700;
}

/* THÔNG BÁO QUY TRÌNH CHÀNH XE & CỌC (SKY BLUE ALERT) */
.b2b-notice-box {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  color: #0369a1;
  font-size: 0.775rem;
  line-height: 1.45;
  padding: 9px 12px;
  border-radius: 8px;
}

.b2b-notice-box i {
  color: #0284c7 !important;
  font-size: 1rem;
  line-height: 1;
  margin-top: 2px;
}

/* PHÂN VÙNG 2: THÔNG TIN GIAO NHẬN (FORM) */
.form-partition-header {
  padding-bottom: 0;
}

.field-label {
  display: inline-flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 5px;
  font-size: 0.8125rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 6px;
}

.field-optional-tag {
  font-size: 0.75rem;
  font-weight: 400;
  color: #64748b;
}

/* Custom Input Form: 8px bo góc chuẩn Trang Chủ, nền trắng sạch, focus viền đỏ marine */
.custom-form-input {
  border-radius: var(--jpm-radius-control, 8px) !important;
  border: 1px solid #cbd5e1;
  font-size: 0.85rem;
  padding: 8px 12px;
  color: var(--jpm-color-navy-deep, #0A1128);
  background-color: #ffffff;
  transition: all 0.2s ease;
}

.custom-form-input:not(textarea) {
  height: 40px;
}

.custom-form-input:hover {
  border-color: #94a3b8;
}

.custom-form-input:focus {
  border-color: var(--jpm-color-red-marine, #F82249) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12) !important;
  outline: none;
}

.custom-textarea {
  resize: vertical;
  min-height: 60px;
  line-height: 1.45;
}

/* ===== ĐỊA CHỈ GIAO HÀNG (TỐI GIẢN, GỌN GÀNG, CHUẨN CHECKOUT) ===== */
.address-preview-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
}

.addr-recipient-name {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--jpm-color-navy-dark, #0A1128);
  line-height: 1.3;
}

.addr-default-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: var(--jpm-radius-badge, 6px);
  background-color: rgba(248, 34, 73, 0.08);
  color: var(--jpm-color-red-marine, #F82249);
  font-size: 0.7rem;
  font-weight: 600;
  line-height: 1.3;
}

.addr-meta-row {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.825rem;
  color: #475569;
  line-height: 1.45;
}

.addr-meta-row i {
  font-size: 0.85rem;
  color: #64748b;
  margin-top: 2px;
  flex-shrink: 0;
}

.address-change-link {
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
  font-size: 0.82rem;
  line-height: 1.3;
}

.address-change-link:hover {
  text-decoration: underline !important;
  transform: none !important;
}

.no-address-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
}

.btn-outline-danger {
  border-radius: var(--jpm-radius-control, 8px) !important;
}

.no-address-box .btn-outline-danger {
  background-color: #ffffff;
  color: var(--jpm-color-red-marine, #F82249);
  border: 1px solid var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
  padding: 6px 16px;
  transition: all 0.2s ease;
}

.no-address-box .btn-outline-danger {
  font-family: var(--heading-font, 'Raleway', 'Montserrat', var(--default-font)) !important;
  font-weight: 600 !important;
  border-radius: var(--jpm-radius-control, 8px) !important;
  box-shadow: none !important;
  transition: all 0.2s ease !important;
}

.no-address-box .btn-outline-danger:hover {
  background-color: var(--jpm-color-red-marine, #F82249) !important;
  border-color: var(--jpm-color-red-marine, #F82249) !important;
  color: #ffffff !important;
  transform: translateY(-1px) !important;
  box-shadow: none !important;
}

.no-address-box .btn-outline-danger:active {
  transform: translateY(0) !important;
  box-shadow: none !important;
}

/* PHÂN VÙNG 3: NÚT GỬI BÁO GIÁ & BẢO ĐẢM */
.btn-jpm-submit {
  height: 48px;
  border-radius: var(--jpm-radius-control, 8px) !important;
  font-weight: 600;
  font-size: 0.95rem;
  letter-spacing: 0.2px;
  transition: all 0.2s ease;
  box-shadow: none !important;
}

.btn-jpm-submit:hover:not(:disabled) {
  transform: translateY(-1px) !important;
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  box-shadow: none !important;
}

.btn-jpm-submit:active:not(:disabled) {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
}

.btn-jpm-submit:focus {
  box-shadow: none !important;
}

.btn-jpm-submit:disabled {
  background-color: #f1f5f9 !important;
  border-color: #e2e8f0 !important;
  color: #94a3b8 !important;
  box-shadow: none !important;
  cursor: not-allowed;
}

.security-guarantee-text {
  font-size: 0.775rem;
  color: #64748b;
}

/* ===== EMPTY CART STATE CHUẨN CLIENT JPM ===== */
.empty-cart-container {
  padding: 3.5rem 1rem 5rem;
}

.empty-cart-illustration {
  display: flex;
  justify-content: center;
  align-items: center;
}

.empty-cart-img {
  width: 175px;
  max-width: 100%;
  height: auto;
  object-fit: contain;
  opacity: 0.92;
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
}

.empty-cart-container:hover .empty-cart-img {
  transform: translateY(-4px) scale(1.03);
  opacity: 1;
}

.empty-cart-title {
  font-family: var(--heading-font, 'Raleway', sans-serif);
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--heading-color, #0A1128);
  letter-spacing: -0.3px;
  line-height: 1.35;
}

.empty-cart-desc {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.95rem;
  color: var(--jpm-neutral-500, #64748B);
  max-width: 460px;
  line-height: 1.6;
}

.btn-empty-cart-action {
  font-family: var(--default-font, 'Roboto', sans-serif);
  font-size: 0.95rem;
  font-weight: 600;
  padding: 12px 28px;
  border-radius: var(--jpm-radius-control, 8px) !important;
  background-color: var(--jpm-color-red-marine, #F82249) !important;
  border: 1px solid var(--jpm-color-red-marine, #F82249) !important;
  color: #ffffff !important;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: none;
}

.btn-empty-cart-action:hover {
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  color: #ffffff !important;
  transform: translateY(-1px) !important;
  box-shadow: none !important;
}

.btn-empty-cart-action:active {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
}

.btn-empty-cart-action i {
  transition: transform 0.2s ease;
  font-size: 1.05rem;
}

.btn-empty-cart-action:hover i {
  transform: translateX(4px);
}

/* ===== ADDRESS MODAL ===== */
.custom-modal-content {
  border-radius: var(--jpm-radius-card, 12px) !important;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 45px -10px rgba(10, 17, 40, 0.2);
}

.address-item {
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  background: #ffffff;
  transition: all 0.15s ease;
}

.address-item:last-child {
  margin-bottom: 0 !important;
}

.address-item:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.address-item.selected-addr {
  border-color: var(--jpm-color-red-marine, #F82249) !important;
  background: rgba(248, 34, 73, 0.02);
}

.custom-radio {
  width: 18px;
  height: 18px;
  border-color: #cbd5e1;
  border-radius: 50% !important;
  margin: 0 !important;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}

.custom-radio:checked {
  background-color: var(--jpm-color-red-marine, #F82249);
  border-color: var(--jpm-color-red-marine, #F82249);
}

.custom-radio:focus {
  border-color: var(--jpm-color-red-marine, #F82249);
  box-shadow: 0 0 0 2px rgba(248, 34, 73, 0.2);
}

.modal-check-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-left: 0;
}

.modal-checkbox-input {
  margin: 0 !important;
  flex-shrink: 0;
}

.cursor-pointer {
  cursor: pointer;
}

/* ===== LUXURY ORDER CONFIRMATION MODAL ===== */
.lux-confirm-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1090;
  background: rgba(10, 17, 40, 0.5);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: luxConfirmFade 0.2s ease-out;
}

.lux-confirm-card {
  position: relative;
  width: 100%;
  max-width: 472px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-card, 12px);
  padding: 30px 28px 26px;
  box-shadow: 0 24px 60px -12px rgba(10, 17, 40, 0.24), 0 4px 16px rgba(10, 17, 40, 0.06);
  font-family: var(--default-font, 'Inter', sans-serif);
  animation: luxConfirmPop 0.24s cubic-bezier(0.16, 1, 0.3, 1);
}

.lux-confirm-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background-color 0.18s ease, color 0.18s ease;
}

.lux-confirm-close:hover {
  background: #f1f5f9;
  color: #0a1128;
}

.lux-confirm-head {
  text-align: center;
  margin-bottom: 20px;
}

.lux-confirm-icon {
  width: 50px;
  height: 50px;
  margin: 0 auto 14px;
  border-radius: 50%;
  background: #ecfdf5;
  border: 1px solid #d1fae5;
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.55rem;
}

.lux-confirm-title {
  margin: 0 0 8px;
  font-size: 1.2rem;
  font-weight: 700;
  color: #0a1128;
  letter-spacing: -0.015em;
  line-height: 1.3;
}

.lux-confirm-sub {
  margin: 0;
  font-size: 0.875rem;
  color: #64748b;
  line-height: 1.55;
}

.lux-confirm-code-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--jpm-radius-control, 8px);
  margin-bottom: 18px;
}

.lux-confirm-code-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #64748b;
  margin-bottom: 2px;
}

.lux-confirm-code-val {
  font-family: 'Roboto Mono', monospace;
  font-size: 1.08rem;
  font-weight: 700;
  color: #0a1128;
  letter-spacing: -0.01em;
  word-break: break-all;
}

.lux-copy-code-btn {
  flex-shrink: 0;
  height: 32px;
  padding: 0 12px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #334155;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
}

.lux-copy-code-btn:hover {
  border-color: #0a1128;
  color: #0a1128;
}

.lux-copy-code-btn.copied {
  border-color: #10b981;
  background: #ecfdf5;
  color: #047857;
}

.lux-confirm-steps {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px 0 20px;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 20px;
}

.lux-step {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.lux-step-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
  background: #cbd5e1;
}

.lux-step.is-done .lux-step-dot {
  background: #059669;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.14);
}

.lux-step.is-next .lux-step-dot {
  background: #ffffff;
  border: 2px solid #0a1128;
}

.lux-step-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.lux-step-text strong {
  font-size: 0.84rem;
  font-weight: 600;
  color: #0a1128;
  line-height: 1.35;
}

.lux-step-text span {
  font-size: 0.78rem;
  color: #64748b;
  line-height: 1.45;
}

.lux-confirm-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.lux-confirm-btn {
  height: 42px;
  padding: 0 16px;
  border-radius: 8px !important;
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  box-shadow: none !important;
  transition: all 0.2s ease;
}

.lux-confirm-btn.btn-outline-secondary {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  color: #334155 !important;
}

.lux-confirm-btn.btn-outline-secondary:hover {
  background: #f8fafc !important;
  border-color: #94a3b8 !important;
  color: #0a1128 !important;
}

.lux-confirm-btn.btn-danger {
  background: #f82249 !important;
  border: 1px solid #f82249 !important;
  color: #ffffff !important;
}

.lux-confirm-btn.btn-danger:hover {
  background: var(--jpm-color-red-hover, #D9163A) !important;
  border-color: var(--jpm-color-red-hover, #D9163A) !important;
  transform: translateY(-1px);
}

@keyframes luxConfirmFade {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes luxConfirmPop {
  from {
    opacity: 0;
    transform: translateY(10px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@media (max-width: 520px) {
  .lux-confirm-card {
    padding: 24px 20px 20px;
  }

  .lux-confirm-actions {
    grid-template-columns: 1fr;
  }
}
</style>
