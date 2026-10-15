<template>
  <header
    id="header"
    class="header d-flex align-items-center fixed-top"
    :class="{ 'header-inner-page': $route.path !== '/' }"
  >
    <div class="container position-relative d-flex align-items-center w-100 h-100">
      <router-link to="/" class="logo d-flex flex-column align-items-center justify-content-center me-auto h-100 text-decoration-none">
        <svg width="42" height="26" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 2px;">
          <path d="M14.5 1 L14.5 19 L2 19 Z" fill="white" />
          <path d="M17.5 1 L30 19 L17.5 19 Z" fill="white" />
        </svg>
        <span style="font-family: var(--heading-font); font-size: 14px; font-weight: 700; letter-spacing: 4px; color: white; line-height: 1; margin-left: 6px;">JPM</span>
      </router-link>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li>
            <router-link to="/" exact-active-class="active">Trang chủ</router-link>
          </li>
          
          <!-- CỬA HÀNG -->
          <li class="dropdown">
            <router-link to="/san-pham" :class="{ active: $route.path.startsWith('/san-pham') }">
              Cửa hàng
            </router-link>
            <ul class="store-dropdown">
                <li v-for="cat in navCategories" :key="cat.id">
                  <router-link :to="'/san-pham?danh_muc=' + cat.slug" :class="{ active: $route.query.danh_muc === cat.slug }">
                    {{ cat.ten_danh_muc }}
                  </router-link>
                </li>
                <li v-if="hasMoreCategories">
                  <router-link to="/san-pham"  class="fw-bold d-flex align-items-center" style="font-size: 24px; line-height: 1; justify-content: center; letter-spacing: 2px;">
                    ...
                  </router-link>
                </li>
              </ul>
          </li>

          <!-- THƯƠNG HIỆU -->
          <li class="dropdown">
            <a href="#" @click.prevent :class="{ active: $route.path.includes('/thuong-hieu') }">Thương hiệu</a>
            <ul class="store-dropdown">
              <li v-for="brand in navBrands" :key="brand.id">
                <router-link :to="'/thuong-hieu/' + brand.code.toLowerCase()" >
                  {{ brand.name }}
                </router-link>
              </li>
              <li v-if="hasMoreBrands">
                <router-link to="/thuong-hieu"  class="fw-bold d-flex align-items-center" style="font-size: 24px; line-height: 1; justify-content: center; letter-spacing: 2px;">
                  ...
                </router-link>
              </li>
            </ul>
          </li>

          <!-- TRA CỨU -->
          <li>
            <router-link to="/tra-cuu" >Tra cứu</router-link>
          </li>

          <!-- TIN TỨC -->
          <li>
            <router-link to="/tin-tuc" >Tin tức</router-link>
          </li>

          <li>
            <router-link to="/ho-tro" >Hỗ trợ</router-link>
          </li>

          <!-- Divider -->
          <li class="d-none d-xl-block"><span style="color: rgba(255,255,255,0.2); margin: 0 10px;">|</span></li>

          <!-- THÔNG BÁO -->
          <li class="dropdown ms-2" v-if="isLoggedIn">
            <a href="#" class="custom-nav-icon" @mouseenter="markAsRead">
              <span class="position-relative d-inline-block">
                <i class="bi bi-bell fs-5" style="line-height: 1"></i>
                <span v-if="unreadCount > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 2px 4px; margin-top: 2px; margin-left: -2px;">
                  {{ unreadCount }}
                </span>
              </span>
            </a>
            <ul class="user-menu-dropdown notif-dropdown dropdown-menu-end">
              <li class="px-3 py-2 d-flex justify-content-between align-items-center bg-light border-bottom">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;">Thông báo của bạn</span>
                <span v-if="unreadCount > 0" class="badge bg-danger rounded-pill" style="font-size: 0.7rem;">{{ unreadCount }} mới</span>
              </li>
              
              <template v-if="notifications.length > 0">
                <li v-for="notif in notifications" :key="notif.id" class="notif-item" :class="{ 'unread': !notif.read_at }">
                  <router-link :to="notif.data.url || '#'" class="d-flex align-items-start px-3 py-3 text-decoration-none">
                    <div class="notif-icon me-3" :class="'bg-pastel-' + (notif.data.color || 'primary')">
                      <i :class="'bi ' + (notif.data.icon || 'bi-bell-fill')"></i>
                    </div>
                    <div class="notif-content flex-grow-1">
                      <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.85rem; letter-spacing: 0;">{{ notif.data.title }}</h6>
                      <p class="mb-1 text-secondary" style="font-size: 0.8rem; line-height: 1.4; text-transform: none; letter-spacing: 0;">{{ notif.data.body }}</p>
                      <small class="text-muted" style="font-size: 0.7rem; text-transform: none; letter-spacing: 0;">{{ new Date(notif.created_at).toLocaleDateString('vi-VN') }}</small>
                    </div>
                    <div v-if="!notif.read_at" class="notif-indicator"></div>
                  </router-link>
                </li>
              </template>
              <li v-else class="text-center py-4 text-muted" style="text-transform: none; letter-spacing: 0;">
                <i class="bi bi-bell-slash fs-3 mb-2 d-block"></i>
                <span style="font-size: 0.85rem;">Không có thông báo nào</span>
              </li>

              <li>
                <router-link to="/tai-khoan" class="d-block text-center py-2 bg-light text-primary fw-bold text-decoration-none" style="font-size: 0.85rem;">
                  Xem tất cả
                </router-link>
              </li>
            </ul>
          </li>
          <!-- Tra cứu đơn hàng (Guest) -->
          <li class="ms-2">
            <router-link to="/tra-cuu-don-hang" class="custom-nav-icon" title="Tra cứu đơn hàng">
              <i class="bi bi-box-seam fs-5" style="line-height: 1"></i>
            </router-link>
          </li>

          <!-- Giỏ hàng -->
          <li class="ms-2">
            <router-link to="/gio-hang" class="custom-nav-icon">
              <span class="position-relative d-inline-block">
                <i class="bi bi-cart2 fs-5" style="line-height: 1"></i>
                <span v-if="cartStore.totalItems > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 2px 4px; margin-top: 2px; margin-left: -2px;">{{ cartStore.totalItems }}</span>
              </span>
            </router-link>
          </li>

          <!-- Tài khoản -->
            <li class="dropdown d-flex align-items-center ms-3 position-relative">
              <template v-if="!isLoggedIn">
                <router-link to="/dang-nhap" class="custom-nav-icon">
                  <i class="bi bi-person fs-5"></i>
                </router-link>
              </template>
              <template v-else>
                <a href="#" class="custom-nav-icon" style="display: flex; align-items: center;">
                  <div class="user-avatar">
                    <img v-if="userAvatar" :src="userAvatar" alt="Avatar" class="w-100 h-100 rounded-circle object-fit-cover">
                    <i v-else class="bi bi-person-fill fs-6"></i>
                  </div>
                </a>
                <ul class="user-menu-dropdown dropdown-menu-end">
                  <li><router-link to="/tai-khoan" class="um-item"><i class="bi bi-person-circle"></i> Quản lý tài khoản</router-link></li>
                  <li><router-link to="/garage" class="um-item"><i class="bi bi-tools"></i> Garage</router-link></li>
                  <li><router-link to="/lich-su-don-hang" class="um-item"><i class="bi bi-receipt"></i> Lịch sử giao dịch</router-link></li>
                  <li><a href="#" class="um-item" @click.prevent="logout"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                </ul>
              </template>
            </li>

        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
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
      notifications: [],
      unreadCount: 0,
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
  watch: {
    isLoggedIn(newVal) {
      if (newVal) this.fetchNotifications();
      else { this.notifications = []; this.unreadCount = 0; }
    }
  },
  methods: {
    async fetchNotifications() {
      try {
        const res = await axiosClient.get('/client/notifications');
        this.notifications = res.data || [];
        this.unreadCount = res.unread_count || 0;
      } catch (e) {}
    },
    async markAsRead() {
      if (this.unreadCount === 0) return;
      try {
        await axiosClient.post('/client/notifications/read');
        this.unreadCount = 0;
        this.notifications.forEach(n => n.read_at = new Date());
      } catch (e) {}
    },
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

/* Custom Nav Icons (Cart) */
.navmenu .custom-nav-icon::before {
  display: none !important;
}
.navmenu .custom-nav-icon:hover,
.navmenu .custom-nav-icon:hover i {
  color: #fff !important;
}

/* ===== USER NAV TRIGGER ===== */
.user-nav-item > a.user-nav-trigger::before {
  display: none !important;
}
.user-nav-trigger {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 5px 12px 5px 5px !important;
  border-radius: 50px !important;
  border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
  transition: all 0.25s ease !important;
  text-decoration: none !important;
  background: transparent !important;
}
.user-nav-trigger:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  border-color: rgba(255, 255, 255, 0.45) !important;
}

/* Avatar circle */
.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f82249, #c0143a);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.user-avatar i {
  color: #fff !important;
  font-size: 14px !important;
  margin: 0 !important;
}

/* Text info next to avatar */
.user-nav-info {
  display: flex;
  flex-direction: column;
  line-height: 1.25;
}
.user-nav-label {
  font-size: 10px !important;
  color: rgba(255, 255, 255, 0.55) !important;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500 !important;
}
.user-nav-name {
  font-size: 13px !important;
  color: #fff !important;
  font-weight: 600 !important;
}

/* Chevron */
.user-nav-chevron {
  font-size: 10px !important;
  color: rgba(255, 255, 255, 0.55) !important;
  transition: transform 0.25s ease;
  margin-left: 2px;
}
.user-nav-item:hover .user-nav-chevron {
  transform: rotate(180deg);
}

/* User menu dropdown */
.navmenu .dropdown ul.user-menu-dropdown {
  right: 0 !important;
  left: auto !important;
  right: 0 !important;
  left: auto !important;
  min-width: 0 !important;
  width: max-content !important;
}
.navmenu .dropdown ul.user-menu-dropdown li {
  min-width: 0 !important;
}
.navmenu .dropdown ul.user-menu-dropdown .um-item {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  gap: 8px !important;
  padding: 11px 14px !important;
}
.navmenu .dropdown ul.user-menu-dropdown .um-item i {
  font-size: 14px !important;
  color: inherit !important;
  opacity: 0.6;
  margin: 0 !important;
  flex-shrink: 0;
}
.navmenu .dropdown ul.user-menu-dropdown .um-item:hover i {
  opacity: 1 !important;
}

/* Notifications */
.notif-dropdown {
  width: 340px !important;
  max-width: 100vw !important;
  padding: 0 !important;
  border-radius: 8px !important;
  overflow: hidden !important;
  border: 1px solid rgba(0,0,0,0.05) !important;
}
.notif-item {
  border-bottom: 1px solid rgba(0,0,0,0.04);
  transition: all 0.2s ease;
  position: relative;
}
.notif-item:hover {
  background-color: rgba(0,0,0,0.02);
}
.notif-item.unread {
  background-color: rgba(248, 34, 73, 0.03);
}
.notif-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.notif-icon i {
  font-size: 1rem;
}
.bg-pastel-primary { background: #e0e8ff; color: #3b5998; }
.bg-pastel-success { background: #dcfce7; color: #16a34a; }
.bg-pastel-danger { background: #fee2e2; color: #dc2626; }
.notif-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--accent-color, #f82249);
  margin-top: 6px;
}
</style>




















