<template>
  <!-- Menu Nhân Viên (Staff Sidebar - Chuẩn 100% Sneat Admin) -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-3 position-relative" style="height: auto; padding-left: 1rem; padding-right: 1rem;">
      <router-link to="/staff/ho-so" class="d-flex align-items-center w-100 text-decoration-none" title="Xem Hồ sơ cá nhân">
        <div class="avatar avatar-online me-3" style="width: 45px; height: 45px; min-width: 45px;">
          <img :src="userAvatar" alt="user-avatar" class="w-100 h-100 rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid #fff;" />
        </div>
        <div class="d-flex flex-column align-items-start overflow-hidden">
          <span class="fw-bold text-body text-truncate w-100" style="font-size: 15px; line-height: 1.2;">{{ userName }}</span>
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

        <!-- 2. Single Menu Item -->
        <li 
          class="menu-item" 
          v-else-if="item.type === 'item'" 
          :class="{ 'active': $route.path === item.path || ($route.path.startsWith(item.path) && item.path !== '/staff') }"
        >
          <router-link :to="item.path" class="menu-link">
            <i class="menu-icon tf-icons bx" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </router-link>
        </li>

        <!-- 3. Submenu Group (Dropdown) -->
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
      // Menu Nhân viên kế thừa 100% cấu trúc, tên mục và icon từ NavbarAdmin
      // Chỉ giữ lại những nghiệp vụ nhân viên được phân công vận hành
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
          title: "CRM & Nhân Sự",
          icon: "bx-group",
          type: "submenu",
          children: [
            { title: "Khách Hàng", path: "/staff/khach-hang" }
          ]
        },
        {
          title: "Dữ Liệu Nền Tảng",
          type: "header"
        },
        {
          title: "Động Cơ & Phụ Tùng",
          icon: "bx-category",
          type: "submenu",
          children: [
            { title: "Model Động Cơ", path: "/staff/dong-co-model" },
            { title: "Danh Sách Phụ Tùng", path: "/staff/phu-tung" },
            { title: "Sơ Đồ Phụ Tùng", path: "/staff/so-do-phu-tung" }
          ]
        },
        {
          title: "Cửa Hàng",
          icon: "bx-cart",
          type: "submenu",
          children: [
            { title: "Danh Sách Sản Phẩm", path: "/staff/san-pham" }
          ]
        },
        {
          title: "Kinh Doanh & Vận Hành",
          type: "header"
        },
        {
          title: "Quản Lý Đơn Hàng",
          icon: "bx-receipt",
          type: "submenu",
          children: [
            { title: "Yêu Cầu & Đơn Khách", path: "/staff/don-hang" }
          ]
        },
        {
          title: "Quản Lý Kho",
          icon: "bx-building-house",
          type: "submenu",
          children: [
            { title: "Tồn Kho", path: "/staff/ton-kho" },
            { title: "Xuất / Nhập / Tồn", path: "/staff/the-kho" }
          ]
        },
        {
          title: "Tài Khoản",
          type: "header"
        },
        {
          title: "Tài Khoản Cá Nhân",
          icon: "bx-user-circle",
          type: "submenu",
          children: [
            { title: "Hồ Sơ", path: "/staff/ho-so" },
            { title: "Đổi Mật Khẩu", path: "/staff/doi-mat-khau" }
          ]
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
