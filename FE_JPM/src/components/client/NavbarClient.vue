<template>
  <header
    id="header"
    class="header d-flex align-items-center fixed-top"
    :class="{ 'header-inner-page': $route.path !== '/' }"
  >
    <div class="container header-container position-relative d-flex align-items-center w-100 h-100">
      <!-- Vùng 1 (Trái): Logo JPM -->
      <router-link to="/" class="logo d-flex flex-column align-items-center justify-content-center me-auto me-xl-0 h-100 text-decoration-none">
        <svg width="42" height="26" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 2px;">
          <path d="M14.5 1 L14.5 19 L2 19 Z" fill="white" />
          <path d="M17.5 1 L30 19 L17.5 19 Z" fill="white" />
        </svg>
        <span style="font-family: var(--heading-font); font-size: 14px; font-weight: 700; letter-spacing: 4px; color: white; line-height: 1; margin-left: 6px;">JPM</span>
      </router-link>

      <!-- Vùng 2 (Giữa): Menu điều hướng chính -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li>
            <router-link to="/" exact-active-class="active">Trang chủ</router-link>
          </li>

          <!-- CỬA HÀNG -->
          <li class="dropdown">
            <router-link to="/san-pham" :class="{ active: $route.path.startsWith('/san-pham') }">
              <span>Cửa hàng</span>
              <i class="bi bi-chevron-down nav-chevron toggle-dropdown"></i>
            </router-link>
            <ul class="store-dropdown">
              <li v-for="cat in navCategories" :key="cat.id">
                <router-link :to="'/san-pham?danh_muc=' + cat.slug" :class="{ active: $route.query.danh_muc === cat.slug }">
                  {{ cat.ten_danh_muc }}
                </router-link>
              </li>
              <li v-if="hasMoreCategories">
                <router-link to="/san-pham" class="store-dropdown-more-link">
                  <span>Xem tất cả danh mục</span>
                  <i class="bi bi-arrow-right"></i>
                </router-link>
              </li>
            </ul>
          </li>

          <!-- THƯƠNG HIỆU -->
          <li class="dropdown">
            <a href="#" @click.prevent :class="{ active: $route.path.includes('/thuong-hieu') }">
              <span>Thương hiệu</span>
              <i class="bi bi-chevron-down nav-chevron toggle-dropdown"></i>
            </a>
            <ul class="store-dropdown">
              <li v-for="brand in navBrands" :key="brand.id">
                <router-link :to="'/thuong-hieu/' + brand.code.toLowerCase()" :class="{ active: $route.path === '/thuong-hieu/' + brand.code.toLowerCase() }">
                  {{ brand.name }}
                </router-link>
              </li>
              <li v-if="hasMoreBrands">
                <router-link to="/thuong-hieu" class="store-dropdown-more-link">
                  <span>Xem tất cả thương hiệu</span>
                  <i class="bi bi-arrow-right"></i>
                </router-link>
              </li>
            </ul>
          </li>

          <!-- TRA CỨU -->
          <li>
            <router-link to="/tra-cuu" :class="{ active: $route.path.startsWith('/tra-cuu') }">Tra cứu</router-link>
          </li>

          <!-- TIN TỨC -->
          <li>
            <router-link to="/tin-tuc" :class="{ active: $route.path.startsWith('/tin-tuc') }">Tin tức</router-link>
          </li>

          <!-- HỖ TRỢ -->
          <li>
            <router-link to="/ho-tro" :class="{ active: $route.path.startsWith('/ho-tro') }">Hỗ trợ</router-link>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- Vùng 3 (Phải): Cụm tiện ích Tra cứu đơn / Giỏ hàng / Tài khoản -->
      <div class="header-actions">
        <!-- Tra cứu đơn hàng -->
        <router-link to="/tra-cuu-don-hang" class="header-icon-btn" title="Tra cứu đơn hàng">
          <i class="bi bi-box-seam fs-5" style="line-height: 1"></i>
        </router-link>

        <!-- Giỏ hàng -->
        <router-link to="/gio-hang" class="header-icon-btn" title="Giỏ hàng">
          <span class="position-relative d-inline-block">
            <i class="bi bi-cart2 fs-5" style="line-height: 1"></i>
            <span
              v-if="cartStore.totalItems > 0"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size: 0.6rem; padding: 2px 4px; margin-top: 2px; margin-left: -2px;"
            >{{ cartStore.totalItems }}</span>
          </span>
        </router-link>

        <!-- Tài khoản -->
        <template v-if="!isLoggedIn">
          <router-link to="/dang-nhap" class="header-icon-btn" title="Đăng nhập">
            <i class="bi bi-person fs-5" style="line-height: 1"></i>
          </router-link>
        </template>
        <div v-else class="header-dropdown ms-1">
          <a href="#" @click.prevent class="header-avatar-btn" title="Tài khoản">
            <div class="user-avatar" :class="{ 'user-avatar-fallback': !userAvatar }">
              <img v-if="userAvatar" :src="userAvatar" alt="Avatar" class="w-100 h-100 rounded-circle object-fit-cover">
              <i v-else class="bi bi-person-fill fs-6"></i>
            </div>
          </a>

          <div class="header-popover user-popover">
            <router-link to="/tai-khoan" class="um-item">
              <i class="bi bi-person-circle"></i>
              <span>Quản lý tài khoản</span>
            </router-link>
            <router-link to="/garage" class="um-item">
              <i class="bi bi-tools"></i>
              <span>Garage</span>
            </router-link>
            <router-link to="/lich-su-don-hang" class="um-item">
              <i class="bi bi-receipt"></i>
              <span>Đơn hàng & Báo giá</span>
            </router-link>
            <div class="um-divider"></div>
            <a href="#" class="um-item um-item-logout" @click.prevent="logout">
              <i class="bi bi-box-arrow-right"></i>
              <span>Đăng xuất</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { useAuthStore } from '../../stores/authStore'
import axiosClient from '../../api/axiosClient'
import { useCartStore } from '../../stores/cartStore'

export default {
  name: "NavbarClient",
  setup() {
    const cartStore = useCartStore()
    return { cartStore }
  },
  computed: {
    authStore() {
      return useAuthStore();
    },
    isLoggedIn() {
      return this.authStore.isLoggedIn;
    },
    userName() {
      return this.authStore.user?.name || this.authStore.user?.ten || "Tài khoản";
    },
    userAvatar() {
      if (this.authStore.user?.avatar) {
        return this.authStore.user.avatar;
      }
      if (this.authStore.user?.name) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.authStore.user.name)}&background=f82249&color=fff&size=120`;
      }
      return null;
    }
  },
  data() {
    return {
      navCategories: [],
      hasMoreCategories: false,
      navBrands: [],
      hasMoreBrands: false,
    };
  },
  async mounted() {
    try {
      const res = await axiosClient.get('/public/danh-muc-san-pham');
      const allCats = res.data?.data || res.data || [];
      this.navCategories = allCats.slice(0, 17);
      this.hasMoreCategories = allCats.length > 17;
        
      const resBrands = await axiosClient.get('/public/thuong-hieu?_t=' + new Date().getTime());
      const allBrands = resBrands.data?.data || resBrands.data || [];
      this.navBrands = allBrands.slice(0, 17);
      this.hasMoreBrands = allBrands.length > 17;
    } catch (e) {
      console.warn('Không tải được danh mục navbar:', e);
    }
  },
  methods: {
    async logout() {
      await this.authStore.logout();
      this.$router.push('/dang-nhap');
    },
  },
};
</script>

<style scoped>
/* Fix header background for inner pages */
.header.header-inner-page {
  --background-color: rgba(1, 8, 33, 1);
}
.scrolled .header.header-inner-page {
  --background-color: rgba(1, 8, 33, 0.82);
}

/* Ensure navbar links are immediately responsive to clicks */
.navmenu a {
  cursor: pointer;
  position: relative;
  z-index: 5;
}

/* Inactive dropdowns must not intercept pointer events */
.navmenu .dropdown ul {
  pointer-events: none;
}
.navmenu .dropdown:hover > ul {
  pointer-events: auto !important;
}

/* ===== RIGHT UTILITY ZONE (.header-actions) ===== */
.header-actions {
  display: flex;
  align-items: center;
  gap: 24px;
}

.header-icon-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 2px;
  color: var(--nav-color, rgba(255, 255, 255, 0.85));
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s ease;
}

.header-icon-btn:hover,
.header-icon-btn:hover i,
.header-dropdown:hover > .header-icon-btn,
.header-dropdown:hover > .header-icon-btn i {
  color: #ffffff !important;
}

/* Avatar Trigger (Original 40x40 borderless circle) */
.header-avatar-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-avatar.user-avatar-fallback {
  background: linear-gradient(135deg, #f82249, #c0143a);
}

.user-avatar i {
  color: #fff !important;
  font-size: 14px !important;
  margin: 0 !important;
}

/* ===== SHARED POPOVER CONTAINER (12px Luxury Surface) ===== */
.header-dropdown {
  position: relative;
  display: flex;
  align-items: center;
}

.header-popover {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(15, 23, 42, 0.08);
  box-shadow: 0 20px 45px -10px rgba(10, 17, 40, 0.18), 0 4px 12px rgba(10, 17, 40, 0.06);
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  transform: translateY(6px);
  transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
  z-index: 1000;
}

/* Invisible hover bridge */
.header-popover::before {
  content: "";
  position: absolute;
  top: -14px;
  left: 0;
  right: 0;
  height: 14px;
  background: transparent;
}

.header-dropdown:hover > .header-popover {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
  transform: translateY(0);
}

/* ===== USER ACCOUNT POPOVER ===== */
.user-popover {
  min-width: 215px;
  padding: 6px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.um-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  font-family: var(--default-font);
  font-size: 0.875rem;
  font-weight: 400;
  color: #334155;
  text-decoration: none;
  white-space: nowrap;
  transition: background-color 0.18s ease, color 0.18s ease;
}

.um-item i {
  font-size: 1rem;
  line-height: 1;
  color: #64748b;
  flex-shrink: 0;
  transition: color 0.18s ease;
}

.um-item:hover {
  background-color: #f8fafc;
  color: var(--accent-color, #f82249);
}

.um-item:hover i {
  color: var(--accent-color, #f82249);
}

.um-divider {
  height: 1px;
  background-color: #f1f5f9;
  margin: 4px 6px;
}
</style>




















