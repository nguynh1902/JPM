<template>
  <div class="container-fluid vh-100 p-0 overflow-hidden font-sans position-relative">
    
    <!-- ================= LAYER 1: HÌNH ẢNH FULL MÀN HÌNH ================= -->
    <div class="position-absolute w-100 h-100 z-0">
      <div
        v-for="(img, idx) in bgImages"
        :key="idx"
        class="position-absolute w-100 h-100"
        :style="{
          backgroundImage: 'url(' + img + ')',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          transition: 'opacity 1.5s ease-in-out',
          opacity: idx === activeImgIndex ? 1 : 0
        }"
      ></div>
    </div>
    
    <!-- ================= LAYER 2: GRADIENT BẢO VỆ CHỮ ================= -->
    <div class="position-absolute w-100 h-100 z-1" style="background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%); pointer-events: none;"></div>

    <!-- ================= LAYER 3: BỐ CỤC CHIA CỘT ================= -->
    <div class="row g-0 h-100 position-relative z-2 justify-content-end">
      
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
          <div class="d-inline-flex align-items-center mb-4" style="border-left: 3px solid var(--accent-color); padding-left: 14px;">
            <span style="color: rgba(255, 255, 255, 0.9); font-size: 14px; font-weight: 500; line-height: 1;">Đơn vị uy tín hàng đầu Việt Nam</span>
          </div>
          
          <!-- HEADLINE -->
          <h1 data-aos="fade-up" data-aos-delay="100" style="font-family: var(--heading-font); font-size: clamp(32px, 3.2vw, 44px); font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 24px; text-shadow: 0 4px 24px rgba(0, 0, 0, 0.5);">
            Hệ Thống Phụ Tùng<br />
            <span style="color: var(--accent-color);">Tàu Thuyền</span>
          </h1>
          
          <!-- PARAGRAPH -->
          <p data-aos="fade-up" data-aos-delay="150" style="font-size: 15px; color: rgba(255, 255, 255, 0.85); line-height: 1.7; margin-bottom: 32px; max-width: 460px; font-weight: 400;">
            Cung cấp phụ tùng chính hãng cho JPM Marine, Yamaha, Suzuki, Honda Marine và nhiều thương hiệu uy tín khác.
          </p>
          
          <!-- BULLETS -->
          <ul data-aos="fade-up" data-aos-delay="200" class="list-unstyled d-flex flex-column gap-3" style="font-size: 15px; color: rgba(255, 255, 255, 0.85); font-weight: 500; margin: 0;">
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle text-danger fs-5"></i> Phụ tùng chính hãng 100%
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle text-danger fs-5"></i> Giao hàng toàn quốc nhanh chóng
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="bi bi-check2-circle text-danger fs-5"></i> Hỗ trợ kỹ thuật 24/7
            </li>
          </ul>
        </div>
      </div>

      <!-- CỘT PHẢI: BỐ CỤC CŨ (SLIM COLUMN) NHƯNG VỚI STYLE MINIMAL INPUT MỚI -->
      <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center justify-content-center h-100" style="background: rgba(10, 17, 40, 0.55); backdrop-filter: blur(20px); border-left: 1px solid rgba(255,255,255,0.05); box-shadow: -15px 0 50px rgba(10, 17, 40, 0.6); min-width: 420px; max-width: 480px;">
        <div class="w-100 p-4 p-xl-5">
          
          <transition name="fade" mode="out-in">
            <!-- FORM ĐĂNG NHẬP -->
            <div v-if="activeForm === 'login'" key="login">
              <h2 class="text-white mb-3 auth-heading">Đăng Nhập</h2>
              <p class="mb-4 auth-subtext">Chưa có tài khoản? <a href="#" @click.prevent="switchToRegister" class="text-decoration-none ms-1" style="color: var(--accent-color); font-weight: 500;">Đăng ký ngay</a>
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

                <button type="submit" class="btn w-100 text-white fw-bold hover-lift-btn auth-submit-btn" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Đăng nhập
                </button>
              </form>

              <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1 m-0 auth-divider">
                <span class="px-3" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; font-weight: 400;">Hoặc tiếp tục với</span>
                <hr class="flex-grow-1 m-0 auth-divider">
              </div>

              <button type="button" class="btn w-100 text-white d-flex justify-content-center align-items-center gap-2 hover-bg-google" style="padding: 11px; font-size: 0.9rem; font-weight: 500; border: 1px solid rgba(255,255,255,0.18); background: rgba(255,255,255,0.03); border-radius: 6px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G" style="width: 18px;">
                Google
              </button>
            </div>

            <!-- FORM ĐĂNG KÝ -->
            <div v-else-if="activeForm === 'register'" key="register">
              <h2 class="text-white mb-3 auth-heading">Tạo Tài Khoản</h2>
              <p class="mb-4 auth-subtext">Đã có tài khoản? <a href="#" @click.prevent="switchToLogin" class="text-decoration-none ms-1" style="color: var(--accent-color); font-weight: 500;">Đăng nhập ngay</a>
              </p>

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
                    Tôi đồng ý với <a href="#" class="text-white text-decoration-none" style="font-weight: 500;">Điều khoản & Chính sách</a>
                  </label>
                </div>

                <button type="submit" class="btn w-100 text-white fw-bold hover-lift-btn auth-submit-btn" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Tạo tài khoản
                </button>
              </form>
              
              <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1 m-0 auth-divider">
                <span class="px-3" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; font-weight: 400;">Hoặc tiếp tục với</span>
                <hr class="flex-grow-1 m-0 auth-divider">
              </div>

              <button type="button" class="btn w-100 text-white d-flex justify-content-center align-items-center gap-2 hover-bg-google" style="padding: 11px; font-size: 0.9rem; font-weight: 500; border: 1px solid rgba(255,255,255,0.18); background: rgba(255,255,255,0.03); border-radius: 6px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G" style="width: 18px;">
                Google
              </button>
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

                <button type="submit" class="btn w-100 text-white fw-bold hover-lift-btn auth-submit-btn mb-4 mt-2" :disabled="isLoading">
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

                <button type="submit" class="btn w-100 text-white fw-bold hover-lift-btn auth-submit-btn mb-4 mt-2" :disabled="isLoading">
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

export default {
  name: 'XacThuc',
  data() {
    return {
      activeForm: 'login', // 'login', 'register', 'forgot'
      showPw: false,
      showRegPw: false,
      
      loginForm: { login_id: '', password: '' },
      
      registerForm: {
        ho: '', ten: '', email: '', so_dien_thoai: '', mat_khau: '', mat_khau_confirmation: '', role: 'user'
      },

            forgotForm: { email: '' },
      resetForm: { password: '', password_confirmation: '' },
      showResetPw: false,
      showResetConfirmPw: false,
      
      bgImages: [
        "https://images.unsplash.com/photo-1502680390469-be75c86b636f?q=80&w=1400&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?q=80&w=1400&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1505118380757-91f5f5632de0?q=80&w=1400&auto=format&fit=crop"
      ],
      activeImgIndex: 0,
      timer: null
    }
  },
  computed: {
    isLoading() {
      const authStore = useAuthStore();
      return authStore.loading;
    }
  },
  mounted() {
    if (this.$route.query.mode === 'reset' || this.$route.path.includes('dat-lai-mat-khau')) {
      this.activeForm = 'reset';
    }
    this.timer = setInterval(() => {
      this.activeImgIndex = (this.activeImgIndex + 1) % this.bgImages.length;
    }, 5000);
  },
  beforeUnmount() {
    if (this.timer) clearInterval(this.timer);
  },
  methods: {
    switchToRegister() { this.activeForm = 'register'; },
    switchToLogin() { this.activeForm = 'login'; },
    switchToForgot() { this.activeForm = 'forgot'; },
    async handleForgot() {
      alert("Đã gửi liên kết khôi phục đến email: " + this.forgotForm.email);
    },
    async handleReset() {
      if (this.resetForm.mat_khau !== this.resetForm.mat_khau_confirmation) {
        alert("Mật khẩu xác nhận không khớp!");
        return;
      }
      alert("Đổi mật khẩu thành công!");
      this.activeForm = 'login';
    },
    async handleLogin() {
      const authStore = useAuthStore();
      const success = await authStore.login(this.loginForm);
      if (success) {
        const cartStore = useCartStore();
        await cartStore.fetchFromCloud();

        if (authStore.isStaffOrAdmin) {
          this.$router.push('/admin/dashboard');
        } else {
          this.$router.push('/');
        }
      } else {
        alert(authStore.error || "Đăng nhập thất bại");
      }
    },
    async handleRegister() {
      this.registerForm.password_confirmation = this.registerForm.password;
      const authStore = useAuthStore();
      await authStore.register(this.registerForm);
    }
  }
}
</script>

<style scoped>

.auth-label {
  font-size: 0.85rem;
  letter-spacing: normal;
  text-transform: none;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 0.35rem;
}
.auth-submit-btn {
  background-color: var(--accent-color);
  padding: 12px;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: normal;
  text-transform: none;
  border-radius: 6px;
  box-shadow: 0 4px 15px rgba(239, 35, 60, 0.2);
  border: none;
  transition: all 0.3s ease;
}
.auth-submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(239, 35, 60, 0.3);
}
.auth-pw-icon {
  opacity: 0.5;
  cursor: pointer;
  font-size: 1.1rem;
  padding-right: 12px;
  transition: opacity 0.3s ease;
}
.auth-pw-icon:hover {
  opacity: 0.9;
}
.auth-divider {
  border-color: rgba(255,255,255,0.15);
}
.auth-heading {
  font-family: var(--heading-font);
  font-size: 26px;
  font-weight: 700;
  letter-spacing: normal;
  text-transform: none;
}
.auth-subtext {
  color: rgba(255,255,255,0.65);
  font-size: 0.88rem;
  letter-spacing: normal;
  font-weight: 400;
}

/* Transisiton form */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
    /* ORIGINAL GLASSMORPHISM INPUTS */
    .minimal-input {
      background: rgba(255, 255, 255, 0.08) !important;
      border: 1px solid rgba(255, 255, 255, 0.2) !important;
      border-radius: 6px !important;
      color: #fff !important;
      padding: 10px 14px !important;
      font-size: 0.92rem;
      font-weight: 400;
      box-shadow: none !important;
      transition: all 0.2s ease;
    }
    
    .minimal-input:focus {
      border-color: var(--accent-color) !important;
      background: rgba(239, 35, 60, 0.05) !important;
      box-shadow: 0 0 0 3px rgba(239, 35, 60, 0.2) !important;
    }
    
    .minimal-input::placeholder {
      color: rgba(255, 255, 255, 0.45) !important;
    }
    
    /* FIX AUTOFILL */
    .minimal-input:-webkit-autofill,
    .minimal-input:-webkit-autofill:hover, 
    .minimal-input:-webkit-autofill:focus, 
    .minimal-input:-webkit-autofill:active{
        -webkit-text-fill-color: white !important;
        transition: background-color 50000s ease-in-out 0s !important;
        caret-color: white;
    }

/* Minimal Checkbox */
.minimal-checkbox {
  background-color: transparent;
  border: 1px solid rgba(255,255,255,0.5);
  border-radius: 2px;
  cursor: pointer;
  width: 15px;
  height: 15px;
  margin-left: 0 !important;
}
.minimal-checkbox:checked {
  background-color: #fff;
  border-color: #fff;
}
.minimal-checkbox:focus {
  box-shadow: none;
  border-color: #fff;
}

.hover-lift {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.hover-lift:hover {
  transform: translateX(-5px);
  opacity: 1 !important;
}

/* Button Hover Effects */
.hover-lift-btn {
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease, filter 0.3s ease;
  border: none !important;
  outline: none !important;
}
.hover-lift-btn:focus, .hover-lift-btn:active {
  outline: none !important;
  box-shadow: 0 4px 15px rgba(239, 35, 60, 0.2) !important;
}
.hover-lift-btn:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(239, 35, 60, 0.5) !important;
  filter: brightness(1.1);
}
.hover-lift-btn:active:not(:disabled) {
  transform: translateY(0px);
  box-shadow: 0 4px 10px rgba(239, 35, 60, 0.3) !important;
}

.hover-bg-google {
  transition: all 0.3s ease;
}
.hover-bg-google:hover {
  background: rgba(255, 255, 255, 0.15) !important;
  border-color: rgba(255, 255, 255, 0.4) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
</style>




































