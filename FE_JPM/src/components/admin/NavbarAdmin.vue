<template>
  <!-- Menu Admin / Backoffice Hợp Nhất (Phân quyền động theo RBAC) -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-3 position-relative" style="height: auto; padding-left: 1rem; padding-right: 1rem;">
      <router-link to="/admin/ho-so" class="d-flex align-items-center w-100 text-decoration-none p-2 rounded user-profile-box" title="Xem Hồ sơ cá nhân">
        <div class="avatar avatar-online me-3" style="width: 44px; height: 44px; min-width: 44px;">
          <img :src="userAvatar" alt="user-avatar" class="w-100 h-100 rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid #e7e7ff;" />
        </div>
        <div class="d-flex flex-column align-items-start overflow-hidden">
          <span class="fw-bold text-dark text-truncate w-100" style="font-size: 15px; line-height: 1.2;">{{ userName }}</span>
          <small class="text-primary fw-semibold" style="font-size: 11.5px;">{{ displayRoleName }}</small>
        </div>
      </router-link>
      
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none position-absolute" style="right: -10px; top: 15px; z-index: 99;">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <template v-for="(item, index) in filteredMenus" :key="index">
        <!-- 1. Header (Label nhóm) -->
        <li class="menu-header small text-uppercase" v-if="item.type === 'header'">
          <span class="menu-header-text">{{ item.title }}</span>
        </li>

        <!-- 2. Single Menu Item (Kích vào là qua page luôn) -->
        <li 
          class="menu-item" 
          v-else-if="item.type === 'item'" 
          :class="{ 'active': $route.path === item.path || ($route.path.startsWith(item.path + '/') && item.path !== '/admin') }"
        >
          <router-link :to="item.path" class="menu-link">
            <i class="menu-icon tf-icons bx" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </router-link>
        </li>
      </template>
    </ul>
  </aside>
  <!-- / Menu -->
</template>

<script>
import { useAuthStore } from '../../stores/authStore';

export default {
  name: "NavbarAdmin",
  data() {
    return {
      // Menu Backoffice Hợp nhất: Tự động phân quyền động theo Roles & Permissions
      menus: [
        {
          title: "Dashboard",
          icon: "bx-home-circle",
          path: "/admin/dashboard",
          type: "item"
          // Không yêu cầu permission -> Mọi nhân sự nội bộ đều truy cập được
        },
        {
          title: "Cấu Hình Hệ Thống",
          icon: "bx-cog",
          path: "/admin/cau-hinh-he-thong",
          permission: "Cấu hình chung",
          type: "item"
        },
        {
          title: "Quản Trị & Nhân Sự",
          type: "header"
        },
        {
          title: "Khách Hàng",
          icon: "bx-group",
          path: "/admin/khach-hang",
          permission: "Xem Khách hàng",
          type: "item"
        },
        {
          title: "Nhân Viên",
          icon: "bx-user-pin",
          path: "/admin/nhan-vien",
          permission: "Quản lý Nhân sự",
          type: "item"
        },
        {
          title: "Phân Quyền",
          icon: "bx-shield-quarter",
          path: "/admin/phan-quyen",
          permission: "Quản lý Vai trò",
          type: "item"
        },
        {
          title: "Lịch Sử Hoạt Động",
          icon: "bx-history",
          path: "/admin/lich-su-hoat-dong",
          permission: "Cấu hình chung",
          type: "item"
        },
        {
          title: "Động Cơ & Phụ Tùng",
          type: "header"
        },
        {
          title: "Thương Hiệu",
          icon: "bx-purchase-tag-alt",
          path: "/admin/thuong-hieu",
          permission: "Quản lý Model & Động cơ",
          type: "item"
        },
        {
          title: "Loại Động Cơ",
          icon: "bx-cube",
          path: "/admin/loai-dong-co",
          permission: "Quản lý Model & Động cơ",
          type: "item"
        },
        {
          title: "Model Động Cơ",
          icon: "bx-layer",
          path: "/admin/dong-co-model",
          permission: "Quản lý Model & Động cơ",
          type: "item"
        },
        {
          title: "Nhóm Phụ Tùng",
          icon: "bx-category",
          path: "/admin/nhom-phu-tung",
          permission: "Xem Phụ tùng",
          type: "item"
        },
        {
          title: "Danh Sách Phụ Tùng",
          icon: "bx-wrench",
          path: "/admin/phu-tung",
          permission: "Xem Phụ tùng",
          type: "item"
        },
        {
          title: "Sơ Đồ Phụ Tùng",
          icon: "bx-sitemap",
          path: "/admin/so-do-phu-tung",
          permission: "Xem Phụ tùng",
          type: "item"
        },
        {
          title: "Mã Thay Thế",
          icon: "bx-git-compare",
          path: "/admin/ma-thay-the",
          permission: "Xem Phụ tùng",
          type: "item"
        },
        {
          title: "Cửa Hàng & Bán Hàng",
          type: "header"
        },
        {
          title: "Danh Mục Sản Phẩm",
          icon: "bx-collection",
          path: "/admin/danh-muc-san-pham",
          permission: "Quản lý Sản phẩm",
          type: "item"
        },
        {
          title: "Danh Sách Sản Phẩm",
          icon: "bx-cart",
          path: "/admin/san-pham",
          permission: "Quản lý Sản phẩm",
          type: "item"
        },
        {
          title: "Kinh Doanh & Vận Hành",
          type: "header"
        },
        {
          title: "Yêu Cầu & Đơn Khách",
          icon: "bx-receipt",
          path: "/admin/don-hang",
          permission: "Xem Đơn hàng",
          type: "item"
        },
        {
          title: "Gom Đơn & PO",
          icon: "bx-archive-out",
          path: "/admin/don-nhap-hang",
          permission: "Quản lý Đơn nhập PO",
          type: "item"
        },
        {
          title: "Danh Sách Kho",
          icon: "bx-store-alt",
          path: "/admin/danh-sach-kho",
          permission: "Quản lý Danh sách kho",
          type: "item"
        },
        {
          title: "Tồn Kho",
          icon: "bx-building-house",
          path: "/admin/ton-kho",
          permission: "Xem Tồn kho",
          type: "item"
        },
        {
          title: "Xuất / Nhập / Tồn",
          icon: "bx-transfer-alt",
          path: "/admin/the-kho",
          permission: "Quản lý Thẻ kho",
          type: "item"
        },
        {
          title: "Nội Dung & CMS",
          type: "header"
        },
        {
          title: "Chuyên Mục",
          icon: "bx-folder",
          path: "/admin/chuyen-muc",
          permission: "Quản lý Tin tức & CMS",
          type: "item"
        },
        {
          title: "Tin Tức",
          icon: "bx-news",
          path: "/admin/tin-tuc",
          permission: "Quản lý Tin tức & CMS",
          type: "item"
        },
        {
          title: "FAQ",
          icon: "bx-help-circle",
          path: "/admin/faq",
          permission: "Quản lý Tin tức & CMS",
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
    displayRoleName() {
      const store = useAuthStore();
      if (!store.user || !store.user.roles || store.user.roles.length === 0) return 'Nhân Sự';
      const role = store.user.roles[0];
      const rLower = String(role).toLowerCase();
      if (rLower === 'admin' || rLower === 'super admin') return 'Quản Trị Viên';
      if (rLower === 'staff') return 'Nhân Viên';
      return role;
    },
    // Thuật toán lọc menu động theo Roles & Permissions
    filteredMenus() {
      const authStore = useAuthStore();
      const isAdmin = authStore.isAdmin;

      // Bước 1: Duyệt qua tất cả các mục, kiểm tra quyền từng item
      const visibleItems = this.menus.map(item => {
        if (item.type === 'header') return item;

        // Super Admin luôn thấy 100% tất cả các mục
        if (isAdmin) return item;

        // Nếu item không yêu cầu permission cụ thể (ví dụ Dashboard)
        if (!item.permission && !item.permissions) {
          return item;
        }

        // Kiểm tra permission đơn
        if (item.permission && authStore.hasPermission(item.permission)) {
          return item;
        }

        // Kiểm tra permission mảng
        if (item.permissions && authStore.hasAnyPermission(item.permissions)) {
          return item;
        }

        return null;
      });

      // Bước 2: Tự động loại bỏ các Header mồ côi (Header không có item nào bên dưới)
      const result = [];
      let currentHeader = null;
      let currentHeaderHasItems = false;

      for (let i = 0; i < visibleItems.length; i++) {
        const item = visibleItems[i];
        if (!item) continue;

        if (item.type === 'header') {
          currentHeader = item;
          currentHeaderHasItems = false;
        } else {
          // Là menu item
          if (currentHeader && !currentHeaderHasItems) {
            result.push(currentHeader);
            currentHeaderHasItems = true;
          }
          result.push(item);
        }
      }

      return result;
    }
  }
};
</script>
