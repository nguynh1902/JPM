<template>
  <div class="container-fluid min-vh-100 p-0 overflow-x-hidden position-relative d-flex flex-column">
    
    <!-- ================= LAYER 1: HÌNH ẢNH NỀN CỐ ĐỊNH (NGỌN SÓNG BIỂN NGỌC) ================= -->
    <div
      class="position-absolute w-100 h-100 z-0"
      style="
        background-image: url('https://images.unsplash.com/photo-1502680390469-be75c86b636f?q=80&w=1600&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
      "
    ></div>
    
    <!-- ================= LAYER 2: GRADIENT BẢO VỆ CHỮ ================= -->
    <div class="position-absolute w-100 h-100 z-1" style="background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%); pointer-events: none;"></div>

    <!-- ================= LAYER 3: BỐ CỤC CHIA CỘT ================= -->
    <div class="row g-0 flex-grow-1 min-vh-100 position-relative z-2 justify-content-end">
      
      <!-- CỘT TRÁI: TYPOGRAPHY (Original) -->
      <div class="col d-none d-lg-flex flex-column p-5" style="padding-left: 8% !important;">
        
        <!-- Logo -->
        <div class="d-flex justify-content-between align-items-start w-100 pe-5 mt-2">
          <router-link to="/" class="text-decoration-none d-inline-flex flex-column align-items-center justify-content-center">
            <svg width="46" height="30" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 6px;">
              <path d="M14.5 1 L14.5 19 L2 19 Z" fill="white" />
              <path d="M17.5 1 L30 19 L17.5 19 Z" fill="white" />
            </svg>
            <span style="font-size: 16px; font-weight: 800; letter-spacing: 2px; color: white; line-height: 1;">JPM</span>
          </router-link>

          <router-link to="/" class="text-white text-decoration-none hover-lift d-flex align-items-center gap-2 mt-2" style="font-size: 0.85rem; opacity: 1; font-weight: 500;">
            <i class="bi bi-arrow-left"></i> Trang chủ
          </router-link>
        </div>

        <div class="mt-auto mb-auto pb-5">
          <!-- BADGE -->
          <div class="d-inline-flex align-items-center mb-4" style="border-left: 3px solid var(--jpm-color-red-marine, #F82249); padding-left: 14px;">
            <span style="color: rgba(255, 255, 255, 0.9); font-size: 14px; font-weight: 500; line-height: 1;">Đơn vị uy tín hàng đầu Việt Nam</span>
          </div>
          
          <!-- HEADLINE -->
          <h1 data-aos="fade-up" data-aos-delay="100" style="font-size: clamp(32px, 3.2vw, 44px); font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 24px; text-shadow: 0 4px 24px rgba(0, 0, 0, 0.5);">
            Hệ Thống Phụ Tùng<br />
            <span style="color: var(--jpm-color-red-marine, #F82249);">Tàu Thuyền</span>
          </h1>
          
          <!-- PARAGRAPH -->
          <p data-aos="fade-up" data-aos-delay="150" style="font-size: 15px; color: rgba(255, 255, 255, 0.85); line-height: 1.7; margin-bottom: 32px; max-width: 460px; font-weight: 400;">
            Cung cấp phụ tùng chính hãng cho JPM Marine, Yamaha, Suzuki, Honda Marine và nhiều thương hiệu uy tín khác.
          </p>
          
          <!-- BULLETS -->
          <ul data-aos="fade-up" data-aos-delay="200" class="list-unstyled d-flex flex-column gap-3" style="font-size: 15px; color: rgba(255, 255, 255, 0.85); font-weight: 500; margin: 0;">
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle fs-5" style="color: var(--jpm-color-red-marine, #F82249);"></i> Phụ tùng chính hãng 100%
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle fs-5" style="color: var(--jpm-color-red-marine, #F82249);"></i> Giao hàng toàn quốc nhanh chóng
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle fs-5" style="color: var(--jpm-color-red-marine, #F82249);"></i> Hỗ trợ kỹ thuật 24/7
            </li>
          </ul>
        </div>
      </div>

      <!-- CỘT PHẢI: BỐ CỤC CHUẨN GLASSMORPHISM DEEP NAVY -->
      <div class="col-12 col-md-7 col-lg-4 d-flex flex-column align-items-center justify-content-center min-vh-100 auth-panel">
        <div class="w-100 p-4 p-xl-5">
          <!-- Mobile Logo & Back to Home -->
          <div class="d-flex d-lg-none justify-content-between align-items-center mb-4 pb-3 border-bottom border-white border-opacity-10">
            <router-link to="/" class="text-decoration-none d-inline-flex align-items-center gap-2">
              <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.5 1 L14.5 19 L2 19 Z" fill="white" />
                <path d="M17.5 1 L30 19 L17.5 19 Z" fill="white" />
              </svg>
              <span style="font-size: 15px; font-weight: 800; letter-spacing: 2px; color: white;">JPM</span>
            </router-link>
            <router-link to="/" class="text-white text-decoration-none small d-inline-flex align-items-center gap-1 opacity-75">
              <i class="bi bi-arrow-left"></i> Trang chủ
            </router-link>
          </div>
          
          <transition name="fade" mode="out-in">
            <!-- FORM ĐĂNG NHẬP -->
            <div v-if="activeForm === 'login'" key="login">
              <h2 class="text-white mb-3 auth-heading">Đăng Nhập</h2>
              <p class="mb-4 auth-subtext">Chưa có tài khoản? <a href="#" @click.prevent="switchToRegister" class="text-decoration-none ms-1" style="color: var(--jpm-color-red-marine, #F82249); font-weight: 600;">Đăng ký ngay</a>
              </p>

              <form @submit.prevent="handleLogin">
                <div class="mb-3">
                  <label class="form-label text-white auth-label">Số điện thoại / Email</label>
                  <input type="text" class="form-control minimal-input" placeholder="Nhập số điện thoại hoặc email" v-model="loginForm.login_id" required />
                </div>

                <div class="mb-3">
                  <label class="form-label text-white auth-label">Mật khẩu</label>
                  <div class="position-relative">
                    <input :type="showPw ? 'text' : 'password'" class="form-control minimal-input pe-5" placeholder="Nhập mật khẩu" v-model="loginForm.password" required />
                    <i class="bi position-absolute top-50 end-0 translate-middle-y text-white auth-pw-icon" :class="showPw ? 'bi-eye' : 'bi-eye-slash'" @click="showPw = !showPw"></i>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                  <div class="form-check m-0 ps-0 d-flex align-items-center gap-2">
                    <input class="form-check-input minimal-checkbox m-0" type="checkbox" id="rememberMe">
                    <label class="form-check-label text-white m-0" for="rememberMe" style="font-size: 0.85rem; font-weight: 400; opacity: 0.8;">Ghi nhớ</label>
                  </div>
                  <a href="#" @click.prevent="switchToForgot" class="text-white text-decoration-none hover-lift" style="font-size: 0.85rem; font-weight: 400; opacity: 0.85;">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn w-100 text-white fw-semibold hover-lift-btn auth-submit-btn" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Đăng nhập
                </button>
              </form>
            </div>

            <!-- FORM ĐĂNG KÝ -->
            <div v-else-if="activeForm === 'register'" key="register">
              <h2 class="text-white mb-3 auth-heading">Tạo Tài Khoản</h2>
              <p class="mb-4 auth-subtext">Đã có tài khoản? <a href="#" @click.prevent="switchToLogin" class="text-decoration-none ms-1" style="color: var(--jpm-color-red-marine, #F82249); font-weight: 600;">Đăng nhập ngay</a>
              </p>

              <div v-if="authStore.error" class="alert alert-danger py-2 small mb-3">{{ authStore.error }}</div>

              <form @submit.prevent="handleRegister">
                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label text-white auth-label">Họ</label>
                    <input type="text" class="form-control minimal-input" placeholder="Nhập họ của bạn" v-model="registerForm.ho" required />
                  </div>
                  <div class="col-6">
                    <label class="form-label text-white auth-label">Tên</label>
                    <input type="text" class="form-control minimal-input" placeholder="Nhập tên của bạn" v-model="registerForm.ten" required />
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white auth-label">Email</label>
                  <input type="email" class="form-control minimal-input" placeholder="Nhập địa chỉ email" v-model="registerForm.email" required />
                </div>

                <div class="mb-3">
                  <label class="form-label text-white auth-label">Số điện thoại</label>
                  <input type="tel" class="form-control minimal-input" placeholder="Nhập số điện thoại" v-model="registerForm.phone" required />
                </div>

                <div class="mb-3">
                  <label class="form-label text-white auth-label">Mật khẩu</label>
                  <div class="position-relative">
                    <input :type="showRegPw ? 'text' : 'password'" class="form-control minimal-input pe-5" placeholder="Tối thiểu 8 ký tự" v-model="registerForm.password" required />
                    <i class="bi position-absolute top-50 end-0 translate-middle-y text-white auth-pw-icon" :class="showRegPw ? 'bi-eye' : 'bi-eye-slash'" @click="showRegPw = !showRegPw"></i>
                  </div>
                </div>

                <div class="form-check mb-4 mt-3 ps-0 d-flex align-items-center gap-2">
                  <input class="form-check-input minimal-checkbox m-0" type="checkbox" id="terms" required>
                  <label class="form-check-label text-white m-0" for="terms" style="font-size: 0.85rem; font-weight: 400; opacity: 0.8;">
                    Tôi đồng ý với <router-link to="/ho-tro" target="_blank" class="text-white text-decoration-underline" style="font-weight: 500;">Điều khoản & Chính sách</router-link>
                  </label>
                </div>

                <button type="submit" class="btn w-100 text-white fw-semibold hover-lift-btn auth-submit-btn" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Tạo tài khoản
                </button>
              </form>
            </div>
            <!-- FORM QUÊN MẬT KHẨU -->
            <div v-else-if="activeForm === 'forgot'" key="forgot">
              <h2 class="text-white mb-3 auth-heading">Quên Mật Khẩu?</h2>
              <p class="mb-4 auth-subtext">Vui lòng nhập địa chỉ email liên kết với tài khoản của bạn để nhận liên kết khôi phục.
              </p>

              <form @submit.prevent="handleForgot">
                <div class="mb-3">
                  <label class="form-label text-white auth-label">Địa chỉ Email</label>
                  <input type="email" class="form-control minimal-input" placeholder="Nhập địa chỉ email của bạn" v-model="forgotForm.email" required />
                </div>

                <button type="submit" class="btn w-100 text-white fw-semibold hover-lift-btn auth-submit-btn mb-4 mt-2" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Gửi liên kết khôi phục
                </button>
              </form>

              <div class="text-center mt-3 pt-2">
                <a href="#" @click.prevent="switchToLogin" class="text-white text-decoration-none hover-lift d-inline-flex align-items-center gap-2" style="font-weight: 400; font-size: 0.88rem; opacity: 0.85;">
                  <i class="bi bi-arrow-left"></i> Quay lại Đăng nhập
                </a>
              </div>
            </div>
            <!-- FORM ĐẶT LẠI MẬT KHẨU -->
            <div v-else-if="activeForm === 'reset'" key="reset">
              <h2 class="text-white mb-3 auth-heading">Mật Khẩu Mới</h2>
              <p class="mb-4 auth-subtext">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.
              </p>

              <form @submit.prevent="handleReset">
                <div class="mb-3">
                  <label class="form-label text-white auth-label">Mật khẩu mới</label>
                  <div class="position-relative">
                    <input :type="showResetPw ? 'text' : 'password'" class="form-control minimal-input pe-5" placeholder="Tối thiểu 8 ký tự" v-model="resetForm.password" required />
                    <i class="bi position-absolute top-50 end-0 translate-middle-y text-white auth-pw-icon" :class="showResetPw ? 'bi-eye' : 'bi-eye-slash'" @click="showResetPw = !showResetPw"></i>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-white auth-label">Xác nhận mật khẩu</label>
                  <div class="position-relative">
                    <input :type="showResetConfirmPw ? 'text' : 'password'" class="form-control minimal-input pe-5" placeholder="Nhập lại mật khẩu mới" v-model="resetForm.password_confirmation" required />
                    <i class="bi position-absolute top-50 end-0 translate-middle-y text-white auth-pw-icon" :class="showResetConfirmPw ? 'bi-eye' : 'bi-eye-slash'" @click="showResetConfirmPw = !showResetConfirmPw"></i>
                  </div>
                </div>

                <button type="submit" class="btn w-100 text-white fw-semibold hover-lift-btn auth-submit-btn mb-4 mt-2" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Đổi mật khẩu
                </button>
              </form>

              <div class="text-center mt-2">
                <a href="#" @click.prevent="switchToLogin" class="text-white text-decoration-none hover-lift d-inline-flex align-items-center gap-2" style="font-weight: 400; font-size: 0.88rem; opacity: 0.85;">
                  <i class="bi bi-arrow-left"></i> Quay lại Đăng nhập
                </a>
              </div>
            </div>
          </transition>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { useAuthStore } from "../../stores/authStore";
import { useCartStore } from "../../stores/cartStore";
import axiosClient from "../../api/axiosClient";
import Swal from "sweetalert2";

export default {
  name: 'XacThuc',
  data() {
    return {
      activeForm: 'login', // 'login', 'register', 'forgot', 'reset'
      localLoading: false,
      showPw: false,
      showRegPw: false,
      
      loginForm: { login_id: '', password: '' },
      
      registerForm: {
        ho: '', ten: '', email: '', phone: '', so_dien_thoai: '', password: '', password_confirmation: '', mat_khau: '', mat_khau_confirmation: '', role: 'user'
      },

      forgotEmail: '',
      forgotForm: { email: '' },
      resetForm: { password: '', password_confirmation: '' },
      showResetPw: false,
      showResetConfirmPw: false
    }
  },
  computed: {
    authStore() {
      return useAuthStore();
    },
    isLoading() {
      return this.authStore.loading || this.localLoading;
    }
  },
  watch: {
    '$route.path'() {
      this.syncFormFromRoute();
    },
    '$route.query.mode'() {
      this.syncFormFromRoute();
    }
  },
  mounted() {
    this.syncFormFromRoute();
  },
  methods: {
    syncFormFromRoute() {
      const path = this.$route.path || '';
      if (path.includes('dang-ky')) {
        this.activeForm = 'register';
      } else if (path.includes('quen-mat-khau')) {
        this.activeForm = 'forgot';
      } else if (path.includes('dat-lai-mat-khau') || this.$route.query.mode === 'reset') {
        this.activeForm = 'reset';
      } else {
        this.activeForm = 'login';
      }
      if (this.authStore) {
        this.authStore.error = null;
      }
    },
    switchToRegister() {
      this.authStore.error = null;
      this.activeForm = 'register';
    },
    switchToLogin() {
      this.authStore.error = null;
      this.activeForm = 'login';
    },
    switchToForgot() {
      this.authStore.error = null;
      this.activeForm = 'forgot';
    },
    notify(type, msg) {
      if (this.$toast && typeof this.$toast[type] === 'function') {
        this.$toast[type](msg);
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: type === 'error' ? 'error' : 'success',
          title: msg,
          showConfirmButton: false,
          timer: 3000,
        });
      }
    },
    async handleForgot() {
      const email = this.forgotEmail || this.forgotForm.email;
      this.localLoading = true;
      try {
        const res = await axiosClient.post('/forgot-password', { email });
        const msg = res?.message || "Nếu email tồn tại trong hệ thống, liên kết khôi phục đã được gửi.";
        this.notify('success', msg);
      } catch (err) {
        const msg = err.response?.data?.message || "Không thể gửi yêu cầu khôi phục mật khẩu, vui lòng kiểm tra lại email!";
        this.notify('error', msg);
      } finally {
        this.localLoading = false;
      }
    },
    async handleReset() {
      if (this.resetForm.password !== this.resetForm.password_confirmation) {
        this.notify('error', "Mật khẩu xác nhận không khớp!");
        return;
      }
      this.localLoading = true;
      try {
        const res = await axiosClient.post('/reset-password', {
          ...this.resetForm,
          token: this.$route.query.token,
          email: this.$route.query.email
        });
        const msg = res?.message || "Đổi mật khẩu thành công!";
        this.notify('success', msg);
        this.activeForm = 'login';
        this.$router.push('/dang-nhap');
      } catch (err) {
        const msg = err.response?.data?.message || "Đặt lại mật khẩu thất bại, liên kết có thể đã hết hạn!";
        this.notify('error', msg);
      } finally {
        this.localLoading = false;
      }
    },
    async handleLogin() {
      const authStore = useAuthStore();
      const success = await authStore.login(this.loginForm);
      if (success) {
        const cartStore = useCartStore();
        await cartStore.fetchFromCloud();

        this.notify('success', "Đăng nhập thành công!");

        if (authStore.isStaffOrAdmin) {
          this.$router.push('/portal/dashboard');
        } else {
          this.$router.push('/');
        }
      } else {
        const msg = authStore.error || "Đăng nhập thất bại, vui lòng kiểm tra lại thông tin!";
        this.notify('error', msg);
      }
    },
    async handleRegister() {
      this.registerForm.password_confirmation = this.registerForm.password;
      this.registerForm.so_dien_thoai = this.registerForm.phone || this.registerForm.so_dien_thoai;
      this.registerForm.mat_khau = this.registerForm.password;
      this.registerForm.mat_khau_confirmation = this.registerForm.password;
      const authStore = useAuthStore();
      const success = await authStore.register(this.registerForm);
      if (success) {
        this.notify('success', "Đăng ký tài khoản thành công!");
        if (authStore.isLoggedIn) {
          const cartStore = useCartStore();
          await cartStore.fetchFromCloud();
          this.$router.push('/');
        } else {
          this.activeForm = 'login';
          this.$router.push('/dang-nhap');
        }
      } else {
        const msg = authStore.error || "Đăng ký thất bại, vui lòng kiểm tra lại thông tin!";
        this.notify('error', msg);
      }
    }
  }
}
</script>

<style scoped>
.auth-panel {
  background: rgba(10, 17, 40, 0.58);
  backdrop-filter: blur(20px);
  border-left: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: -15px 0 50px rgba(10, 17, 40, 0.6);
  width: 100%;
  max-width: 480px;
}
@media (min-width: 768px) {
  .auth-panel {
    min-width: 420px;
  }
}

.auth-label {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 0.875rem;
  letter-spacing: normal;
  text-transform: none;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.95);
  margin-bottom: 0.45rem;
  display: block;
}
.auth-submit-btn {
  background-color: var(--jpm-color-red-marine, #F82249) !important;
  border: 1px solid var(--jpm-color-red-marine, #F82249) !important;
  height: 44px !important;
  min-height: 44px !important;
  max-height: 44px !important;
  padding: 0 16px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-family: var(--default-font, 'Inter', sans-serif) !important;
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  letter-spacing: normal !important;
  text-transform: none !important;
  border-radius: 8px !important;
  box-shadow: none !important;
  transition: all 0.2s ease !important;
  width: 100% !important;
  color: #ffffff !important;
  cursor: pointer !important;
}
.auth-submit-btn:hover:not(:disabled) {
  background-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  border-color: var(--jpm-btn-red-hover, #FF3B5F) !important;
  transform: translateY(-1px) !important;
  box-shadow: none !important;
}
.auth-submit-btn:active:not(:disabled) {
  background-color: var(--jpm-btn-red-active, #D9163A) !important;
  border-color: var(--jpm-btn-red-active, #D9163A) !important;
  transform: translateY(0) !important;
  box-shadow: none !important;
  outline: none !important;
}
.auth-submit-btn:focus {
  box-shadow: none !important;
  outline: none !important;
}
.auth-pw-icon {
  position: absolute !important;
  right: 14px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  opacity: 0.65;
  cursor: pointer;
  font-size: 1.15rem;
  padding: 0 !important;
  line-height: 1 !important;
  z-index: 5 !important;
  transition: opacity 0.2s ease, color 0.2s ease;
}
.auth-pw-icon:hover {
  opacity: 1;
  color: var(--jpm-color-red-marine, #F82249) !important;
}
.auth-divider {
  border-color: rgba(255, 255, 255, 0.15);
}
.auth-heading {
  font-family: var(--default-font, 'Inter', sans-serif);
  font-size: 28px;
  font-weight: 700;
  letter-spacing: -0.02em;
  text-transform: none;
}
.auth-subtext {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.88rem;
  letter-spacing: normal;
  font-weight: 400;
}

/* Transition form */
.fade-enter-active, .fade-leave-active { transition: opacity 0.35s ease, transform 0.35s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(4px); }

/* ORIGINAL GLASSMORPHISM INPUTS */
.minimal-input,
input.minimal-input,
.form-control.minimal-input {
  display: block !important;
  width: 100% !important;
  height: 44px !important;
  min-height: 44px !important;
  max-height: 44px !important;
  line-height: 42px !important;
  padding: 0 16px !important;
  box-sizing: border-box !important;
  background: rgba(255, 255, 255, 0.08) !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  border-radius: 8px !important;
  color: #ffffff !important;
  font-family: var(--default-font, 'Inter', sans-serif) !important;
  font-size: 0.92rem !important;
  font-weight: 400 !important;
  box-shadow: none !important;
  transition: all 0.2s ease !important;
}

.minimal-input.pe-5,
input.minimal-input.pe-5,
.form-control.minimal-input.pe-5 {
  padding-right: 44px !important;
}

.minimal-input:focus,
input.minimal-input:focus,
.form-control.minimal-input:focus {
  border-color: var(--jpm-color-red-marine, #F82249) !important;
  background: rgba(248, 34, 73, 0.08) !important;
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.2) !important;
  outline: none !important;
  color: #ffffff !important;
}

.minimal-input::placeholder,
input.minimal-input::placeholder,
.form-control.minimal-input::placeholder {
  color: rgba(255, 255, 255, 0.5) !important;
  font-weight: 400 !important;
  opacity: 1 !important;
  transform: none !important;
}

.minimal-input:focus::placeholder,
input.minimal-input:focus::placeholder,
.form-control.minimal-input:focus::placeholder {
  transform: none !important;
}

/* FIX AUTOFILL */
.minimal-input:-webkit-autofill,
.minimal-input:-webkit-autofill:hover, 
.minimal-input:-webkit-autofill:focus, 
.minimal-input:-webkit-autofill:active {
  -webkit-text-fill-color: white !important;
  transition: background-color 50000s ease-in-out 0s !important;
  caret-color: white;
}

/* Minimal Checkbox */
.minimal-checkbox {
  background-color: rgba(255, 255, 255, 0.08) !important;
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  border-radius: 4px !important;
  cursor: pointer;
  width: 17px !important;
  height: 17px !important;
  min-width: 17px !important;
  margin: 0 !important;
  transition: all 0.2s ease;
}
.minimal-checkbox:checked {
  background-color: var(--jpm-color-red-marine, #F82249) !important;
  border-color: var(--jpm-color-red-marine, #F82249) !important;
}
.minimal-checkbox:focus {
  box-shadow: 0 0 0 2px rgba(248, 34, 73, 0.25) !important;
  border-color: var(--jpm-color-red-marine, #F82249) !important;
}

.hover-lift {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.hover-lift:hover {
  transform: translateX(-4px);
  opacity: 1 !important;
}

/* Button Hover Effects */
.hover-lift-btn {
  transition: transform 0.2s ease, background-color 0.2s ease;
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
}
.hover-lift-btn:focus, .hover-lift-btn:active {
  outline: none !important;
  box-shadow: none !important;
}
.hover-lift-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: none !important;
}
.hover-lift-btn:active:not(:disabled) {
  transform: translateY(0px);
  box-shadow: none !important;
}

.hover-bg-google {
  transition: all 0.25s ease;
  border-radius: 8px !important;
}
.hover-bg-google:hover {
  background: rgba(255, 255, 255, 0.12) !important;
  border-color: rgba(255, 255, 255, 0.35) !important;
  transform: translateY(-1px) !important;
  box-shadow: none !important;
}
.hover-bg-google:active {
  transform: translateY(0) !important;
  box-shadow: none !important;
}
</style>




































