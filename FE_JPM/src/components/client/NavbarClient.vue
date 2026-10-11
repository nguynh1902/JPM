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
        <span style="font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 600; letter-spacing: 6px; color: white; line-height: 1; margin-left: 6px;">JPM</span>
      </router-link>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li>
            <router-link to="/" exact-active-class="active">TRANG CHỦ</router-link>
          </li>
          
          <!-- CỬA HÀNG -->
          <li class="dropdown">
            <router-link to="/san-pham" :class="{ active: $route.path.startsWith('/san-pham') }">
              CỬA HÀNG
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
            <a href="#" @click.prevent :class="{ active: $route.path.includes('/thuong-hieu') }">THƯƠNG HIỆU</a>
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
            <router-link to="/tra-cuu" >TRA CỨU</router-link>
          </li>

          <!-- TIN TỨC -->
          <li>
            <router-link to="/tin-tuc" >TIN TỨC</router-link>
          </li>

          <li>
            <router-link to="/ho-tro" >HỖ TRỢ</router-link>
          </li>

          <!-- SMART OMNISEARCH -->
          <li class="omnisearch-item position-relative ms-xl-2 me-xl-1">
            <div class="omnisearch-wrapper" :class="{ 'is-focused': isSearchFocused, 'has-query': searchQuery.length > 0 }">
              <i class="bi bi-search search-icon"></i>
              <input 
                ref="omniInput"
                type="text" 
                v-model="searchQuery" 
                class="omnisearch-input" 
                placeholder="Tra mã OEM, Model máy... (vd: 8M0142205)"
                @focus="isSearchFocused = true"
                @blur="onSearchBlur"
                @keydown.enter.prevent="handleEnter"
                @keydown.esc="closeSearch"
              />
              <button v-if="searchQuery" class="clear-btn" @click="clearSearch" type="button" title="Xóa">
                <i class="bi bi-x-circle-fill"></i>
              </button>
              <span v-else class="shortcut-badge d-none d-xxl-inline-block">/</span>
            </div>

            <!-- SMART SUGGESTION DROPDOWN POPUP -->
            <div 
              v-if="isSearchFocused && (searchQuery.length >= 2 || searchLoading)" 
              class="omnisearch-dropdown shadow-lg"
              @mousedown.prevent
            >
              <!-- SPINNER -->
              <div v-if="searchLoading" class="p-3 text-center text-muted">
                <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                <span class="small fw-semibold">Đang tìm dữ liệu...</span>
              </div>

              <template v-else>
                <!-- 1. DIRECT EXACT MATCH -->
                <div v-if="searchResults.exact_match" class="omni-section exact-match-section p-2 bg-danger-subtle border-bottom">
                  <div class="d-flex align-items-center justify-content-between mb-1 px-1">
                    <span class="exact-badge text-danger fw-bold small">
                      <i class="bi bi-bullseye me-1"></i>KHỚP CHÍNH XÁC 100%
                    </span>
                    <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 0.65rem;">Enter để mở ngay</span>
                  </div>
                  <a 
                    :href="searchResults.exact_match.url" 
                    @click.prevent="goToUrl(searchResults.exact_match.url)"
                    class="omni-item exact-item d-flex align-items-center justify-content-between p-2 rounded bg-white text-decoration-none shadow-sm"
                  >
                    <div>
                      <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                        {{ searchResults.exact_match.title }}
                      </div>
                      <div class="small text-muted mt-1">
                        <span class="badge bg-danger-subtle text-danger me-2">{{ searchResults.exact_match.type === 'phu_tung' ? 'PHỤ TÙNG' : 'MODEL MÁY' }}</span>
                        {{ searchResults.exact_match.brand }}
                        <span v-if="searchResults.exact_match.price" class="ms-2 fw-bold text-danger">
                          {{ formatCurrency(searchResults.exact_match.price) }}
                        </span>
                      </div>
                    </div>
                    <span class="btn btn-sm btn-danger px-3 py-1 fw-bold rounded-pill text-nowrap" style="font-size: 0.78rem;">
                      Mở ngay <i class="bi bi-arrow-right"></i>
                    </span>
                  </a>
                </div>

                <!-- 2. MODEL ĐỘNG CƠ & SƠ ĐỒ -->
                <div v-if="searchResults.models && searchResults.models.length > 0" class="omni-section border-bottom">
                  <div class="omni-section-title px-3 pt-2 pb-1 d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                      <i class="bi bi-diagram-3-fill me-1 text-danger"></i> MODEL & SƠ ĐỒ MÁY THỦY
                    </span>
                    <span class="badge rounded-pill bg-white border text-muted" style="font-size: 0.7rem;">{{ searchResults.models.length }}</span>
                  </div>
                  <div class="omni-list">
                    <a 
                      v-for="m in searchResults.models" 
                      :key="'model-' + m.id" 
                      :href="m.url"
                      @click.prevent="goToUrl(m.url)"
                      class="omni-item px-3 py-2 d-flex align-items-center justify-content-between text-decoration-none border-bottom-subtle"
                    >
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light border text-dark fw-bold font-monospace">{{ m.code }}</span>
                        <span class="fw-semibold text-dark text-truncate" style="max-width: 250px; font-size: 0.85rem;">{{ m.name }}</span>
                      </div>
                      <div class="text-end text-muted small text-nowrap ms-2">
                        <span class="badge bg-secondary-subtle text-secondary me-1">{{ m.brand }}</span>
                        <span v-if="m.year">{{ m.year }}</span>
                      </div>
                    </a>
                  </div>
                </div>

                <!-- 3. PHỤ TÙNG OEM / TRA MÃ -->
                <div v-if="searchResults.phu_tungs && searchResults.phu_tungs.length > 0" class="omni-section border-bottom">
                  <div class="omni-section-title px-3 pt-2 pb-1 d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                      <i class="bi bi-gear-wide-connected me-1 text-danger"></i> PHỤ TÙNG OEM TRA MÃ
                    </span>
                    <span class="badge rounded-pill bg-white border text-muted" style="font-size: 0.7rem;">{{ searchResults.phu_tungs.length }}</span>
                  </div>
                  <div class="omni-list">
                    <a 
                      v-for="p in searchResults.phu_tungs" 
                      :key="'part-' + p.id" 
                      :href="p.url"
                      @click.prevent="goToUrl(p.url)"
                      class="omni-item px-3 py-2 d-flex align-items-center gap-2 text-decoration-none border-bottom-subtle"
                    >
                      <img 
                        :src="p.image || 'https://placehold.co/60x60/f8f9fa/a1acb8?text=JPM'" 
                        class="omni-thumb rounded object-fit-cover" 
                        alt=""
                        style="width: 38px; height: 38px;"
                      />
                      <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-2">
                          <span class="fw-bold text-dark font-monospace" style="font-size: 0.88rem;">{{ p.code }}</span>
                          <span class="badge bg-light border text-secondary" style="font-size: 0.65rem;">{{ p.brand }}</span>
                        </div>
                        <div class="text-muted small text-truncate" style="max-width: 260px; font-size: 0.8rem;">{{ p.name }}</div>
                      </div>
                      <div class="text-end text-nowrap ms-2">
                        <span v-if="p.is_contact_price" class="text-danger fw-bold small">Liên hệ</span>
                        <span v-else class="text-danger fw-bold small">{{ formatCurrency(p.price) }}</span>
                      </div>
                    </a>
                  </div>
                </div>

                <!-- 4. SẢN PHẨM PHỔ THÔNG / CMS -->
                <div v-if="searchResults.san_phams && searchResults.san_phams.length > 0" class="omni-section border-bottom">
                  <div class="omni-section-title px-3 pt-2 pb-1 d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                      <i class="bi bi-box-seam me-1 text-danger"></i> SẢN PHẨM & VẬT TƯ
                    </span>
                    <span class="badge rounded-pill bg-white border text-muted" style="font-size: 0.7rem;">{{ searchResults.san_phams.length }}</span>
                  </div>
                  <div class="omni-list">
                    <a 
                      v-for="s in searchResults.san_phams" 
                      :key="'sp-' + s.id" 
                      :href="s.url"
                      @click.prevent="goToUrl(s.url)"
                      class="omni-item px-3 py-2 d-flex align-items-center gap-2 text-decoration-none border-bottom-subtle"
                    >
                      <img 
                        :src="s.image || 'https://placehold.co/60x60/f8f9fa/a1acb8?text=JPM'" 
                        class="omni-thumb rounded object-fit-cover" 
                        alt=""
                        style="width: 38px; height: 38px;"
                      />
                      <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-dark small text-truncate" style="max-width: 260px; font-size: 0.82rem;">{{ s.name }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ s.category }}</small>
                      </div>
                      <div class="text-end text-nowrap ms-2">
                        <span class="text-danger fw-bold small">{{ formatCurrency(s.sale_price || s.price) }}</span>
                      </div>
                    </a>
                  </div>
                </div>

                <!-- EMPTY STATE -->
                <div 
                  v-if="!searchResults.exact_match && (!searchResults.models || searchResults.models.length === 0) && (!searchResults.phu_tungs || searchResults.phu_tungs.length === 0) && (!searchResults.san_phams || searchResults.san_phams.length === 0)" 
                  class="p-4 text-center text-muted"
                >
                  <i class="bi bi-search text-muted fs-4 d-block mb-1 opacity-50"></i>
                  <div class="fw-semibold small">Không tìm thấy mã hoặc tên khớp "{{ searchQuery }}"</div>
                  <div class="small mt-1 text-muted" style="font-size: 0.78rem;">Nhấn Enter để tìm kiếm mở rộng trên toàn hệ thống</div>
                </div>

                <!-- FOOTER -->
                <div class="omni-footer p-2 bg-light text-center">
                  <a 
                    :href="'/tra-cuu?keyword=' + encodeURIComponent(searchQuery)" 
                    @click.prevent="goToSearchPage"
                    class="btn btn-sm btn-link text-decoration-none text-dark fw-bold w-100"
                    style="font-size: 0.8rem;"
                  >
                    Xem tất cả kết quả cho "<span class="text-danger">{{ searchQuery }}</span>" <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </template>
            </div>
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
                <span class="fw-bold text-dark text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Thông báo của bạn</span>
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
                <router-link to="/tai-khoan" class="d-block text-center py-2 bg-light text-primary fw-bold text-decoration-none" style="font-size: 0.8rem; letter-spacing: 1px;">
                  XEM TẤT CẢ
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
      searchQuery: '',
      searchResults: {
        exact_match: null,
        models: [],
        phu_tungs: [],
        san_phams: []
      },
      searchLoading: false,
      isSearchFocused: false,
      searchDebounceTimer: null,
    };
  },
  async mounted() {
    window.addEventListener('keydown', this.handleGlobalShortcut);
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
  beforeUnmount() {
    window.removeEventListener('keydown', this.handleGlobalShortcut);
    clearTimeout(this.searchDebounceTimer);
  },
  watch: {
    isLoggedIn(newVal) {
      if (newVal) this.fetchNotifications();
      else { this.notifications = []; this.unreadCount = 0; }
    },
    searchQuery() {
      this.onSearchInput();
    },
    $route() {
      this.closeSearch();
    }
  },
  methods: {
    handleGlobalShortcut(e) {
      if ((e.key === '/' || (e.ctrlKey && e.key.toLowerCase() === 'k')) && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) {
        e.preventDefault();
        this.$refs.omniInput?.focus();
        this.isSearchFocused = true;
      }
    },
    onSearchInput() {
      clearTimeout(this.searchDebounceTimer);
      const q = this.searchQuery.trim();
      if (q.length < 2) {
        this.searchResults = { exact_match: null, models: [], phu_tungs: [], san_phams: [] };
        this.searchLoading = false;
        return;
      }
      this.searchLoading = true;
      this.searchDebounceTimer = setTimeout(async () => {
        try {
          const res = await axiosClient.get('/public/quick-search', {
            params: { q }
          });
          if (res.data?.success) {
            this.searchResults = res.data.data;
          }
        } catch (e) {
          console.warn('Lỗi Omnisearch:', e);
        } finally {
          this.searchLoading = false;
        }
      }, 250);
    },
    handleEnter() {
      const q = this.searchQuery.trim();
      if (!q) return;

      if (this.searchResults.exact_match?.url) {
        this.goToUrl(this.searchResults.exact_match.url);
        return;
      }

      if (this.searchResults.models && this.searchResults.models.length > 0) {
        this.goToUrl(this.searchResults.models[0].url);
        return;
      }

      if (this.searchResults.phu_tungs && this.searchResults.phu_tungs.length > 0) {
        this.goToUrl(this.searchResults.phu_tungs[0].url);
        return;
      }

      this.goToSearchPage();
    },
    goToUrl(url) {
      this.closeSearch();
      this.$router.push(url);
    },
    goToSearchPage() {
      const q = this.searchQuery.trim();
      this.closeSearch();
      if (q) {
        this.$router.push({ path: '/tra-cuu', query: { keyword: q } });
      } else {
        this.$router.push('/tra-cuu');
      }
    },
    clearSearch() {
      this.searchQuery = '';
      this.searchResults = { exact_match: null, models: [], phu_tungs: [], san_phams: [] };
      this.$refs.omniInput?.focus();
    },
    closeSearch() {
      this.isSearchFocused = false;
    },
    onSearchBlur() {
      setTimeout(() => {
        this.isSearchFocused = false;
      }, 250);
    },
    formatCurrency(val) {
      if (!val) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
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

/* ===== OMNISEARCH SMART SEARCH BAR ===== */
.omnisearch-item {
  display: flex;
  align-items: center;
}

.omnisearch-wrapper {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 4px 12px;
  width: 230px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.omnisearch-wrapper.is-focused,
.omnisearch-wrapper.has-query {
  width: 320px;
  background: rgba(255, 255, 255, 0.16);
  border-color: #f82249;
  box-shadow: 0 0 12px rgba(248, 34, 73, 0.35);
}

.omnisearch-wrapper .search-icon {
  color: rgba(255, 255, 255, 0.65);
  font-size: 0.82rem;
  flex-shrink: 0;
}

.omnisearch-input {
  background: transparent;
  border: none;
  outline: none;
  color: #ffffff;
  font-size: 0.82rem;
  width: 100%;
  margin-left: 6px;
  padding: 2px 0;
}

.omnisearch-input::placeholder {
  color: rgba(255, 255, 255, 0.55);
  font-size: 0.78rem;
}

.shortcut-badge {
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 3px;
  padding: 0 5px;
  font-size: 0.68rem;
  color: rgba(255, 255, 255, 0.55);
  line-height: 1.4;
}

.clear-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  padding: 0;
  font-size: 0.85rem;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.clear-btn:hover {
  color: #ffffff;
}

.omnisearch-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  left: 0;
  width: 450px;
  max-width: 92vw;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
  max-height: 480px;
  overflow-y: auto;
  z-index: 9999;
  animation: omniFadeIn 0.2s ease;
}

@keyframes omniFadeIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.omni-item {
  transition: background-color 0.15s ease;
}

.omni-item:hover {
  background-color: #f8fafc;
}

.border-bottom-subtle {
  border-bottom: 1px solid #f1f5f9;
}

@media (max-width: 1200px) {
  .omnisearch-wrapper {
    width: 180px;
  }
  .omnisearch-wrapper.is-focused,
  .omnisearch-wrapper.has-query {
    width: 240px;
  }
}
</style>




















