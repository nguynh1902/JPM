<template>
  <main class="cart-page-bg main-page">
    
    <section id="cart" class="section" style="padding-top: 110px;">
      <div class="container">
        
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-12" data-aos="fade-down">
            <h2 class="jpm-page-title">Giỏ Hàng <span class="text-danger">Báo Giá</span></h2>
            <p class="text-muted">Kiểm tra phụ tùng và gửi yêu cầu để nhận mức chiết khấu tốt nhất từ JPM Marine.</p>
          </div>
        </div>

        <div class="row gy-5" v-if="cartItems.length > 0">
          
          <!-- LEFT COLUMN: CART ITEMS -->
          <div class="col-lg-7 col-xl-8" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
              <div class="card-header bg-white px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="height: 76px;">
                <div class="d-flex align-items-center">
                  <h4 class="mb-0 jpm-card-title">Danh Sách Sản Phẩm</h4>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                  <span class="badge rounded-pill px-3 py-2 text-nowrap" style="background-color: #fde6ea; color: #f82249; font-weight: 700;">{{ cartItems.length }} Sản phẩm</span>
                  <button v-if="selectedItems.length > 0" class="btn btn-outline-jpm px-3 text-nowrap" style="padding: 4px 12px; font-size: 0.85rem;" @click="removeSelectedItems">
                    <i class="bi bi-trash3 me-1"></i> Xóa ({{ selectedItems.length }})
                  </button>
                </div>
              </div>
              
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table align-middle mb-0">
                    <thead class="bg-white">
                      <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th class="text-center border-0" style="width: 50px; padding-left: 1.5rem;">
                          <input class="form-check-input custom-checkbox" type="checkbox" v-model="selectAll" style="width: 20px; height: 20px; cursor: pointer;">
                        </th>
                        <th class="text-center text-uppercase border-0" style="width: 80px; font-size: 0.85rem; letter-spacing: 0.5px;">REF</th>
                        <th class="text-uppercase border-0" style="font-size: 0.85rem; letter-spacing: 0.5px;">MÃ & TÊN</th>
                        <th class="text-center text-uppercase border-0" style="width: 150px; font-size: 0.85rem; letter-spacing: 0.5px;">GIÁ BÁN</th>
                        <th class="text-center text-uppercase border-0" style="width: 150px; font-size: 0.85rem; letter-spacing: 0.5px;">SỐ LƯỢNG</th>
                        <th class="text-center text-uppercase border-0" style="width: 100px; font-size: 0.85rem; letter-spacing: 0.5px;">THAO TÁC</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="cartItems.length === 0">
                        <td colspan="6" class="text-center py-4 text-muted">Giỏ hàng trống.</td>
                      </tr>
                      <tr v-for="item in cartItems" :key="item.ma_phu_tung" class="table-row-hover" style="transition: all 0.2s ease;">
                        <td class="text-center" style="padding-left: 1.5rem;">
                          <input class="form-check-input custom-checkbox" type="checkbox" :value="item.ma_phu_tung" v-model="selectedItems" style="width: 20px; height: 20px; cursor: pointer;">
                        </td>
                        <td class="text-center">
                          <div class="d-inline-flex justify-content-center align-items-center border rounded bg-white" style="width: 60px; height: 60px;">
                            <img v-if="item.hinh_anh" :src="getImageUrl(item.hinh_anh)" style="max-height: 40px; max-width: 50px; object-fit: contain;" :alt="item.ten_phu_tung">
                            <i v-else class="bi bi-image text-muted fs-4"></i>
                          </div>
                        </td>
                        <td>
                          <div class="fw-bold" style="color: var(--heading-color); font-size: 0.95rem;">{{ item.ma_phu_tung }}</div>
                          <div class="text-muted small fw-medium mt-1">{{ item.ten_phu_tung }}</div>
                        </td>
                        <td class="text-center">
                          <span v-if="item.is_gia_lien_he" class="text-danger fw-bold" style="font-size: 0.95rem;">Liên hệ</span>
                          <span v-else class="fw-bold text-danger text-nowrap" style="font-size: 0.95rem;">{{ formatPrice(item.gia_ban) }}</span>
                        </td>
                        <td class="text-center">
                          <div class="qty-selector d-inline-flex align-items-center mx-auto" style="width: 110px;">
                            <button class="qty-btn" @click="updateQuantity(item, -1)"><i class="bi bi-dash"></i></button>
                            <input type="text" class="qty-input w-100 text-center border-0 bg-transparent fw-bold" v-model="item.quantity" readonly>
                            <button class="qty-btn" @click="updateQuantity(item, 1)"><i class="bi bi-plus"></i></button>
                          </div>
                        </td>
                        <td class="text-center">
                          <button class="btn btn-outline-danger btn-sm rounded px-2 py-1 fw-bold text-nowrap d-inline-flex align-items-center justify-content-center shadow-none" @click="removeItem(item)" title="Xóa">
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

          <div class="col-lg-5 col-xl-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card sticky-sidebar">
              <div class="card-header bg-white px-4 border-bottom d-flex align-items-center" style="height: 76px;">
                <h4 class="mb-0 jpm-card-title">{{ checkoutType === 'quote' ? 'Yêu Cầu Báo Giá' : 'Thông Tin Đặt Hàng' }}</h4>
              </div>
              
              <div class="card-body p-4">
                
                <!-- Summary -->
                <div class="summary-box mb-4">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fw-medium">Tổng sản phẩm:</span>
                    <strong class="text-dark">{{ totalQuantity }}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <span class="text-muted fw-medium">Tạm tính:</span>
                    <strong class="text-danger fs-5 fw-bold">{{ totalAmount }}</strong>
                  </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitQuotation">
                  
                  <!-- If Not Authenticated (GUEST) -->
                  <div v-if="!isAuthenticated">
                    <div class="mb-3">
                      <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Họ và tên <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" v-model="checkoutForm.name" required placeholder="Nhập tên của bạn...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Số điện thoại / Zalo <span class="text-danger">*</span></label>
                      <input type="tel" class="form-control" v-model="checkoutForm.phone" required placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Email (Tuỳ chọn)</label>
                      <input type="email" class="form-control" v-model="checkoutForm.email" placeholder="Nhập email...">
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                      <textarea class="form-control" v-model="checkoutForm.address" required rows="2" placeholder="Nhập địa chỉ nhận hàng (Hoặc điền Tỉnh/Thành phố để báo cước vận chuyển)"></textarea>
                    </div>
                  </div>

                  <!-- If Authenticated (MEMBER) -->
                  <div v-else class="mb-4">
                    <label class="form-label fw-bold text-dark mb-3" style="font-size: 0.9rem;">Thông tin & Địa chỉ giao hàng</label>
                    <div v-if="selectedAddress" class="border rounded p-3 bg-light position-relative">
                      <div class="fw-bold mb-1">{{ selectedAddress.ten_nguoi_nhan }}</div>
                      <div class="text-muted small mb-2"><i class="bi bi-telephone-fill me-1"></i> {{ selectedAddress.so_dien_thoai }}</div>
                      <div class="small" style="line-height: 1.4;">{{ selectedAddress.dia_chi_cu_the }}</div>
                      <!-- Option to change address -->
                      <div class="mt-3 text-end" v-if="addresses.length > 0">
                        <a href="#" class="text-decoration-none fw-bold" style="color: var(--accent-color); font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#addressModal" @click="modalState = 'list'">Thay đổi</a>
                      </div>
                    </div>
                    <div v-else class="alert alert-warning small p-3">
                      Bạn chưa có địa chỉ nào trong Sổ địa chỉ.
                      <a href="#" class="alert-link fw-bold d-block mt-2" data-bs-toggle="modal" data-bs-target="#addressModal" @click.prevent="openAddAddress">Thêm địa chỉ ngay</a>
                    </div>
                  </div>

                  <!-- Note Input (Common) -->
                  <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Ghi chú (Tuỳ chọn)</label>
                    <textarea class="form-control" v-model="checkoutForm.note" rows="3" style="min-height: 80px;" placeholder="Yêu cầu hóa đơn, giao gấp..."></textarea>
                  </div>

                  <!-- Submit Button -->
                  <button type="submit" class="btn btn-jpm w-100 py-3 d-flex justify-content-center align-items-center" :disabled="isSubmitting">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                    <span v-else class="fw-bold" style="letter-spacing: 0;">{{ checkoutType === 'quote' ? 'NHẬN BÁO GIÁ NGAY' : 'ĐẶT HÀNG NGAY' }}</span>
                    <i class="bi bi-arrow-right ms-2" v-if="!isSubmitting"></i>
                  </button>
                  
                  <p class="security-text text-center mt-3 mb-0 text-muted" style="font-size: 0.85rem;">
                    <i class="bi bi-shield-check text-success me-1"></i> Thông tin được bảo mật hoàn toàn
                  </p>
                </form>

              </div>
            </div>
          </div>

        </div>

        <!-- Empty Cart -->
        <div class="empty-cart-container text-center py-5" v-else data-aos="fade-up">
          <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="120" alt="Empty Cart" class="mb-4 opacity-50">
          <h3 class="fw-bold text-dark mb-3">Giỏ hàng của bạn đang trống</h3>
          <p class="text-muted mb-4 fs-5">Hãy tìm kiếm phụ tùng bạn cần và thêm vào giỏ hàng nhé!</p>
          <router-link to="/" class="submit-btn d-inline-block px-5 text-decoration-none">
            Khám phá phụ tùng
          </router-link>
        </div>

      </div>
    </section>


  </main>

  <!-- Address Modal — Teleport to body để thoát khỏi CSS stacking context của parent transforms -->
  <Teleport to="body">
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
          <div class="modal-header border-bottom-0 pb-0 mt-2">
            <h5 class="modal-title fw-bold text-dark" id="addressModalLabel">Địa Chỉ Của Tôi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="modalState === 'list'" class="address-list">
              <div v-for="addr in addresses" :key="addr.id" class="address-item p-3 mb-3 border rounded position-relative d-flex align-items-start gap-3" :class="{'border-danger': selectedAddressId === addr.id}" style="cursor: pointer;" @click="selectedAddressId = addr.id">
                <div class="radio-wrap mt-1">
                  <input class="form-check-input" type="radio" :name="'address_radio'" :value="addr.id" v-model="selectedAddressId" style="transform: scale(1.2);">
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark">{{ addr.ten_nguoi_nhan }}</span>
                    <a href="#" class="text-decoration-none" style="color: #0d6efd; font-size: 0.85rem;" @click.prevent="openEditAddress(addr)">Cập nhật</a>
                  </div>
                  <div class="text-muted small mb-1">(+84) {{ addr.so_dien_thoai }}</div>
                  <div class="small text-secondary" style="line-height: 1.4;">{{ addr.dia_chi_cu_the }}</div>
                  <span v-if="addr.la_mac_dinh" class="badge text-danger mt-2" style="background: rgba(248,34,73,0.1);">Mặc định</span>
                </div>
              </div>
            </div>
            
            <div v-else class="address-form-inline">
              <form @submit.prevent="saveAddress">
                <div class="mb-3">
                  <label class="form-label small fw-bold text-dark">Họ và tên <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-sm" v-model="addressForm.ten_nguoi_nhan" required placeholder="Tên người nhận">
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-bold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-sm" v-model="addressForm.so_dien_thoai" required placeholder="SĐT liên hệ">
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-bold text-dark">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                  <textarea class="form-control form-control-sm" v-model="addressForm.dia_chi_cu_the" required rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố..."></textarea>
                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="laMacDinh" v-model="addressForm.la_mac_dinh">
                  <label class="form-check-label small" for="laMacDinh">Đặt làm địa chỉ mặc định</label>
                </div>
                <div class="d-flex gap-2 mt-4">
                  <button type="button" class="btn btn-light flex-grow-1" style="font-size: 0.9rem;" @click="modalState = 'list'">Hủy</button>
                  <button type="submit" class="btn btn-jpm flex-grow-1" style="font-size: 0.9rem;" :disabled="isSavingAddress">
                    <span v-if="isSavingAddress" class="spinner-border spinner-border-sm me-2"></span>
                    Lưu địa chỉ
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0 mb-2" v-if="modalState === 'list'">
            <a href="#" class="btn btn-jpm w-100 py-2 d-flex justify-content-center align-items-center gap-2" @click.prevent="openAddAddress">
              <i class="bi bi-plus-lg"></i> Thêm Địa Chỉ Mới
            </a>
          </div>
        </div>
      </div>
    </div>
  </Teleport>

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
      showSuccessModal: false,
      isSubmitting: false,
      pendingOrderId: '',

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
      return this.cartStore.items
    },
    totalQuantity() {
      return this.cartItems
        .filter(item => this.selectedItems.includes(item.ma_phu_tung))
        .reduce((total, item) => total + item.quantity, 0)
    },
    checkoutType() {
      if (this.selectedItems.length === 0) return 'order';
      const selectedCartItems = this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung));
      const hasContact = selectedCartItems.some(item => item.is_gia_lien_he || !item.gia_ban || parseFloat(item.gia_ban) <= 0);
      return hasContact ? 'quote' : 'order';
    },
    totalAmount() {
      if (this.selectedItems.length === 0) return this.formatPrice(0);
        if (this.checkoutType === 'quote') return 'Liên hệ báo giá';
      const selectedCartItems = this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung));
      const total = selectedCartItems.reduce((sum, item) => sum + (item.gia_ban * item.quantity), 0);
      return this.formatPrice(total);
    },
    selectedAddress() {
      return this.addresses.find(a => a.id === this.selectedAddressId) || null;
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
    }
  },
  mounted() {
      this.selectedItems = []
    
    try {
      const userStr = localStorage.getItem('auth_user');
      const token = localStorage.getItem('auth_token');
      if (userStr && token) {
        this.isAuthenticated = true;
        const user = JSON.parse(userStr);
        this.userProfile = user;
        
        // Fallback info from profile in case they have no address
        this.checkoutForm.name = user.ho_ten || user.name || '';
        this.checkoutForm.phone = user.so_dien_thoai || user.phone || '';
        this.checkoutForm.email = user.email || '';

        this.fetchAddresses();
      }
    } catch (e) {
      console.warn('Cannot parse user info');
    }
  },
  methods: {
    goToProfile() {
      const modalElement = document.getElementById('addressModal');
      if (modalElement) {
        // Remove backdrop manually if needed, or use bootstrap instance
        const bsModal = window.bootstrap.Modal.getInstance(modalElement);
        if (bsModal) {
          bsModal.hide();
        }
        // Force cleanup backdrop just in case
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
      }
      this.$router.push('/tai-khoan');
    },
    openAddAddress() {
      this.editingAddressId = null;
      this.addressForm = { ten_nguoi_nhan: '', so_dien_thoai: '', dia_chi_cu_the: '', la_mac_dinh: this.addresses.length === 0 };
      this.modalState = 'form';
    },
    openEditAddress(addr) {
      this.editingAddressId = addr.id;
      this.addressForm = { 
        ten_nguoi_nhan: addr.ten_nguoi_nhan, 
        so_dien_thoai: addr.so_dien_thoai, 
        dia_chi_cu_the: addr.dia_chi_cu_the, 
        la_mac_dinh: addr.la_mac_dinh === 1 
      };
      this.modalState = 'form';
    },
    async saveAddress() {
      this.isSavingAddress = true;
      try {
        if (this.editingAddressId) {
          await axiosClient.put(`/client/profile/addresses/${this.editingAddressId}`, this.addressForm);
          Swal.fire({ icon: 'success', title: 'Thành công', text: 'Cập nhật địa chỉ thành công!', timer: 1500, showConfirmButton: false });
        } else {
          await axiosClient.post('/client/profile/addresses', this.addressForm);
          Swal.fire({ icon: 'success', title: 'Thành công', text: 'Thêm địa chỉ mới thành công!', timer: 1500, showConfirmButton: false });
        }
        await this.fetchAddresses();
        this.modalState = 'list';
      } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không thể lưu địa chỉ. Vui lòng thử lại!' });
      } finally {
        this.isSavingAddress = false;
      }
    },
    async fetchAddresses() {
      try {
        const res = await axiosClient.get('/client/profile/addresses');
        this.addresses = Array.isArray(res) ? res : (res.data || []);
        if (this.addresses.length > 0) {
          const defaultAddr = this.addresses.find(a => a.la_mac_dinh) || this.addresses[0];
          this.selectedAddressId = defaultAddr.id;
        }
      } catch (error) {
        console.error('Lỗi khi tải sổ địa chỉ', error);
      }
    },
    formatPrice(price) {
      if (!price) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    },
    updateQuantity(item, amount) {
      const newQty = item.quantity + amount
      this.cartStore.updateQuantity(item.ma_phu_tung, newQty)
    },

    async removeItem(item) {
      const result = await Swal.fire({
        title: 'Xóa sản phẩm?',
        text: `"${item.ten_phu_tung}" sẽ bị xóa khỏi giỏ hàng.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--accent-color)',
        cancelButtonColor: '#aaa',
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
        title: `Xóa ${this.selectedItems.length} sản phẩm?`,
        text: 'Hành động này không thể hoàn tác.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--accent-color)',
        cancelButtonColor: '#aaa',
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

    async submitQuotation() {
      if (this.selectedItems.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Chưa chọn sản phẩm', text: 'Vui lòng chọn ít nhất 1 sản phẩm!', confirmButtonColor: 'var(--accent-color)' })
        return
      }

      if (!this.isAuthenticated) {
        if (!this.checkoutForm.name.trim() || !this.checkoutForm.phone.trim()) {
          Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng điền đầy đủ Họ Tên và Số điện thoại!', confirmButtonColor: 'var(--accent-color)' })
          return
        }
        if (!this.checkoutForm.address.trim()) {
          Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng điền địa chỉ giao hàng!', confirmButtonColor: 'var(--accent-color)' })
          return
        }
      } else {
        if (!this.selectedAddressId) {
          Swal.fire({ icon: 'warning', title: 'Thiếu địa chỉ', text: 'Vui lòng chọn hoặc thêm một địa chỉ giao hàng!', confirmButtonColor: 'var(--accent-color)' })
          return
        }
      }

      const selectedCartItems = this.cartItems.filter(item => this.selectedItems.includes(item.ma_phu_tung))
      
      let finalName = this.checkoutForm.name.trim();
      let finalPhone = this.checkoutForm.phone.trim();
      let finalAddress = this.checkoutForm.address ? this.checkoutForm.address.trim() : null;

      if (this.isAuthenticated && this.selectedAddress) {
        finalName = this.selectedAddress.ten_nguoi_nhan;
        finalPhone = this.selectedAddress.so_dien_thoai;
        finalAddress = this.selectedAddress.dia_chi_cu_the;
      }

      const payload = {
        user_id: this.userProfile ? this.userProfile.id : null,
        loai_yeu_cau: this.checkoutType, // 'quote' or 'order'
        khach_hang_ten: finalName,
        khach_hang_sdt: finalPhone,
        dia_chi_giao_hang: finalAddress,
        khach_hang_email: this.checkoutForm.email || null,
        ghi_chu_khach_hang: this.checkoutForm.note || null,
        chi_tiet: selectedCartItems.map(item => ({
          id_phu_tung:  item.id,
          ten_phu_tung: item.ten_phu_tung,
          ma_phu_tung:  item.ma_phu_tung,
          so_luong:     item.quantity,
          gia_ban:      item.is_gia_lien_he ? 0 : item.gia_ban
        }))
      }

      this.isSubmitting = true
      try {
        if (!navigator.onLine) {
          // Lưu vào IndexedDB nếu mất mạng
          import('../../../utils/offlineSyncService').then(async ({ saveDraft }) => {
             await saveDraft(this.checkoutType, payload, '/public/yeu-cau-bao-gia');
             Swal.fire({
                icon: 'info',
                title: 'Đã lưu ngoại tuyến!',
                text: 'Hệ thống phát hiện mất kết nối mạng. Đơn hàng của bạn đã được lưu tạm và sẽ tự động gửi đi khi có mạng trở lại.',
                confirmButtonColor: 'var(--accent-color)'
             });
             this.selectedItems.forEach(id => {
                this.cartStore.removeItem(id)
             });
             this.selectedItems = [];
             this.isSubmitting = false;
          });
          return;
        }

        const res = await axiosClient.post('/public/yeu-cau-bao-gia', payload)
        if (res.success || (res.data && res.data.success)) {
          const responseData = res.success ? res.data : res.data.data;
          this.pendingOrderId = responseData.ma_yeu_cau
          this.showSuccessModal = true
          
          this.selectedItems.forEach(id => {
            this.cartStore.removeItem(id)
          })
          this.selectedItems = []
          
          const isOrder = this.checkoutType === 'order';
          const titleType = isOrder ? 'Mã đơn hàng' : 'Mã yêu cầu báo giá';
          const descText = isOrder 
            ? 'Hệ thống đã tiếp nhận đơn hàng của bạn. Bộ phận vận hành sẽ liên hệ trong ít phút để giao hàng.' 
            : 'Hệ thống đã tiếp nhận yêu cầu. Hãy chat với chúng tôi qua Zalo kèm mã đơn để nhận báo giá lập tức.';
          const chatMsg = isOrder 
            ? `Chào JPM Marine, tôi vừa đặt đơn hàng mã ${this.pendingOrderId}.` 
            : `Chào JPM Marine, tôi muốn nhận báo giá cho mã ${this.pendingOrderId}.`;
            
          Swal.fire({
            icon: 'success',
            title: 'Thành Công!',
            html: `
              <p class="text-muted mb-2">${titleType} của bạn:</p>
              <div class="fw-bold fs-3 mb-3" style="color: #059669;">${this.pendingOrderId}</div>
              <p style="font-size: 0.95rem;">${descText}</p>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-chat-dots me-1"></i> Chat Zalo Ngay',
            cancelButtonText: 'Quay về giỏ hàng',
            confirmButtonColor: '#0068ff', // Zalo Color
            cancelButtonColor: '#6c757d'
          }).then((result) => {
            if (result.isConfirmed) {
              const zaloPhone = '0123456789'
              const message = chatMsg;
              window.open(`https://zalo.me/${zaloPhone}?text=${encodeURIComponent(message)}`, '_blank')
            }
          })
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Đã có lỗi xảy ra. Vui lòng thử lại!'
        Swal.fire({ icon: 'error', title: 'Gửi yêu cầu thất bại', text: msg, confirmButtonColor: 'var(--accent-color)' })
      } finally {
        if (navigator.onLine) {
            this.isSubmitting = false
        }
      }
    }
  }
}
</script>


<style scoped>
/* =========================================
   MODERN PREMIUM CART CSS (USING TEMPLATE VARIABLES)
========================================== */
.cart-page-bg {
  min-height: 100vh;
  padding-bottom: 50px;
  font-family: var(--default-font);
  color: var(--default-color);
}

.cart-page-title {
  font-weight: 700;
  color: var(--heading-color);
  letter-spacing: -0.5px;
}

/* ================= LEFT COLUMN: CART ================= */
.cart-card {
  background: var(--surface-color);
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(14, 27, 77, 0.04);
  overflow: hidden;
  border: 1px solid rgba(0,0,0,0.05);
}

.cart-header {
  padding: 12px 24px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  background-color: var(--surface-color);
  min-height: 70px;
}
.cart-header h5 {
  color: var(--heading-color);
  font-size: 1.1rem;
}

.bg-soft-danger {
  background-color: color-mix(in srgb, var(--accent-color), transparent 90%);
  color: var(--accent-color) !important;
}

.cart-body {
  padding: 0 24px;
}

.cart-item {
  padding: 16px 0;
  border-bottom: 1px solid rgba(0,0,0,0.03);
}

.item-img-wrapper {
  width: 80px;
  height: 80px;
  background: var(--surface-color);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(0,0,0,0.05);
  flex-shrink: 0;
  padding: 8px;
}
.item-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.item-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--heading-color);
  line-height: 1.4;
}

.oem-badge {
  background: #f2f2f3;
  color: var(--default-color);
  border: 1px solid rgba(0,0,0,0.05);
  font-weight: 500;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 4px;
}

.qty-selector {
  background: var(--surface-color);
  border: 1px solid rgba(0,0,0,0.1);
  border-radius: 6px;
  overflow: hidden;
  height: 36px;
}
.qty-btn {
  background: #f2f2f3;
  border: none;
  color: var(--default-color);
  width: 32px;
  height: 100%;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.qty-btn:hover {
  background: rgba(0,0,0,0.05);
  color: var(--heading-color);
}
.qty-input {
  width: 40px;
  height: 100%;
  border: none;
  text-align: center;
  font-weight: 600;
  color: var(--heading-color);
  background: transparent;
}
.qty-input:focus {
  outline: none;
}

.btn-delete {
  background: transparent;
  color: color-mix(in srgb, var(--default-color), transparent 40%);
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.btn-delete:hover {
  background: color-mix(in srgb, var(--accent-color), transparent 90%);
  color: var(--accent-color);
}

.continue-shopping {
  color: color-mix(in srgb, var(--default-color), transparent 20%);
  font-weight: 500;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: color 0.2s;
  font-size: 0.95rem;
}
.continue-shopping:hover {
  color: var(--heading-color);
}

/* ================= MODAL ================= */
.success-checkmark {
  width: 60px;
  height: 60px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}
.success-checkmark i {
  font-size: 32px;
  color: #fff;
}
.order-id-box {
  display: inline-block;
  background: #f2f2f3;
  color: var(--heading-color);
  font-size: 1.5rem;
  font-weight: 700;
  padding: 8px 24px;
  border-radius: 6px;
  border: 1px dashed rgba(0,0,0,0.2);
  letter-spacing: 1px;
}
.zalo-btn {
  background: #0068ff;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 104, 255, 0.2);
}
.zalo-btn:hover {
  background: #0056cc;
  transform: translateY(-1px);
}
.cursor-pointer {
  cursor: pointer;
}




</style>




