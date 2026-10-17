<template>
  <!-- Menu Nhân Viên (Staff Sidebar - Chuẩn 100% Sneat Admin) -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-3 position-relative" style="height: auto; padding-left: 1rem; padding-right: 1rem;">
      <router-link to="/staff/ho-so" class="d-flex align-items-center w-100 text-decoration-none p-2 rounded user-profile-box" title="Xem Hồ sơ cá nhân">
        <div class="avatar avatar-online me-3" style="width: 44px; height: 44px; min-width: 44px;">
          <img :src="userAvatar" alt="user-avatar" class="w-100 h-100 rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid #e7e7ff;" />
        </div>
        <div class="d-flex flex-column align-items-start overflow-hidden">
          <span class="fw-bold text-dark text-truncate w-100" style="font-size: 15px; line-height: 1.2;">{{ userName }}</span>
          <small class="text-muted" style="font-size: 12px;">{{ roleName }}</small>
        </div>
      </router-link>
      
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none position-absolute" style="right: -10px; top: 15px; z-index: 99;">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <template v-for="(item, index) in menus" :key="index">
        <!-- 1. Header (Label nhóm) -->
        <li class="menu-header small text-uppercase" v-if="item.type === 'header'">
          <span class="menu-header-text">{{ item.title }}</span>
        </li>

        <!-- 2. Single Menu Item (Kích vào là qua page luôn) -->
        <li 
          class="menu-item" 
          v-else-if="item.type === 'item'" 
          :class="{ 'active': $route.path === item.path || ($route.path.startsWith(item.path + '/') && item.path !== '/staff') }"
        >
          <router-link :to="item.path" class="menu-link">
            <i class="menu-icon tf-icons bx" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </router-link>
        </li>

        <!-- 3. Submenu Group (Dự phòng) -->
        <li 
          class="menu-item" 
          v-else-if="item.type === 'submenu'" 
          :class="{ 'active open': isSubmenuActive(item.children) }"
        >
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </a>
          <ul class="menu-sub">
            <li 
              class="menu-item" 
              v-for="(child, childIndex) in item.children" 
              :key="childIndex"
              :class="{ 'active': $route.path === child.path }"
            >
              <router-link :to="child.path" class="menu-link">
                <div>{{ child.title }}</div>
              </router-link>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </aside>
</template>

<script>
import { useAuthStore } from '../../stores/authStore';

export default {
  name: "NavbarStaff",
  data() {
    return {
      // Menu phẳng trực quan cho Nhân Viên:
      // Các mục được hiển thị trực tiếp dưới Thẻ tên nhóm (Header),
      // bấm vào là chuyển trang ngay lập tức mà không cần qua dropdown accordion
      menus: [
        {
          title: "Dashboard",
          icon: "bx-home-circle",
          path: "/staff/dashboard",
          type: "item"
        },
        {
          title: "Quản Trị & Nhân Sự",
          type: "header"
        },
        {
          title: "Khách Hàng",
          icon: "bx-group",
          path: "/staff/khach-hang",
          type: "item"
        },
        {
          title: "Dữ Liệu Nền Tảng",
          type: "header"
        },
        {
          title: "Model Động Cơ",
          icon: "bx-layer",
          path: "/staff/dong-co-model",
          type: "item"
        },
        {
          title: "Danh Sách Phụ Tùng",
          icon: "bx-cog",
          path: "/staff/phu-tung",
          type: "item"
        },
        {
          title: "Sơ Đồ Phụ Tùng",
          icon: "bx-sitemap",
          path: "/staff/so-do-phu-tung",
          type: "item"
        },
        {
          title: "Danh Sách Sản Phẩm",
          icon: "bx-cart",
          path: "/staff/san-pham",
          type: "item"
        },
        {
          title: "Kinh Doanh & Vận Hành",
          type: "header"
        },
        {
          title: "Yêu Cầu & Đơn Khách",
          icon: "bx-receipt",
          path: "/staff/don-hang",
          type: "item"
        },
        {
          title: "Tồn Kho",
          icon: "bx-building-house",
          path: "/staff/ton-kho",
          type: "item"
        },
        {
          title: "Xuất / Nhập / Tồn",
          icon: "bx-transfer-alt",
          path: "/staff/the-kho",
          type: "item"
        }
      ]
    };
  },
  computed: {
    userName() {
      const store = useAuthStore();
      return store.user ? store.user.name : 'Đang tải...';
    },
    userAvatar() {
      const store = useAuthStore();
      if (store.user && store.user.avatar) {
        let path = store.user.avatar;
        if (path.startsWith('http')) return path;
        return `http://localhost:8000${path}`;
      }
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.userName || 'U')}&background=e7e7ff&color=696cff`;
    },
    roleName() {
      const store = useAuthStore();
      if (!store.user || !store.user.roles || store.user.roles.length === 0) return 'Nhân viên';
      return store.user.roles[0];
    },
    displayRoleName() {
      const store = useAuthStore();
      if (!store.user || !store.user.roles || store.user.roles.length === 0) return 'Nhân Viên';
      const role = store.user.roles[0];
      if (role.toLowerCase() === 'admin') return 'Quản Trị Viên';
      if (role.toLowerCase() === 'staff') return 'Nhân Viên';
      return role;
    }
  },
  methods: {
    isSubmenuActive(children) {
      if (!children) return false;
      return children.some(child => this.$route.path === child.path);
    }
  }
};
</script>
