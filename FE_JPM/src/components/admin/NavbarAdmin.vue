<template>
  <!-- Menu Admin (Phẳng trực quan - Click chuyển trang ngay) -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-3 position-relative" style="height: auto; padding-left: 1rem; padding-right: 1rem;">
      <router-link to="/admin/ho-so" class="d-flex align-items-center w-100 text-decoration-none p-2 rounded user-profile-box" title="Xem Hồ sơ cá nhân">
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

        <!-- 3. Submenu Group (Dự phòng mở rộng) -->
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
            <template v-for="(child, childIndex) in item.children" :key="childIndex">
              <li 
                class="menu-item" 
                v-if="child.roles.includes(currentUserRole)"
                :class="{ 'active': $route.path === child.path }"
              >
                <router-link :to="child.path" class="menu-link">
                  <div>{{ child.title }}</div>
                </router-link>
              </li>
            </template>
          </ul>
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
      // Menu phẳng trực quan cho Admin:
      // Các mục được hiển thị trực tiếp dưới Thẻ tên nhóm (Header),
      // bấm vào là chuyển trang ngay lập tức mà không cần qua dropdown accordion
      menus: [
        {
          title: "Dashboard",
          icon: "bx-home-circle",
          path: "/admin/dashboard",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Cấu Hình Hệ Thống",
          icon: "bx-cog",
          path: "/admin/cau-hinh-he-thong",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Quản Trị & Nhân Sự",
          type: "header",
          roles: ["ADMIN"]
        },
        {
          title: "Khách Hàng",
          icon: "bx-group",
          path: "/admin/khach-hang",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Nhân Viên",
          icon: "bx-user-pin",
          path: "/admin/nhan-vien",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Phân Quyền",
          icon: "bx-shield-quarter",
          path: "/admin/phan-quyen",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Lịch Sử Hoạt Động",
          icon: "bx-history",
          path: "/admin/lich-su-hoat-dong",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Động Cơ & Phụ Tùng",
          type: "header",
          roles: ["ADMIN", "STAFF"]
        },
        {
          title: "Thương Hiệu",
          icon: "bx-purchase-tag-alt",
          path: "/admin/thuong-hieu",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Loại Động Cơ",
          icon: "bx-cube",
          path: "/admin/loai-dong-co",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Model Động Cơ",
          icon: "bx-layer",
          path: "/admin/dong-co-model",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Nhóm Phụ Tùng",
          icon: "bx-category",
          path: "/admin/nhom-phu-tung",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Danh Sách Phụ Tùng",
          icon: "bx-wrench",
          path: "/admin/phu-tung",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Sơ Đồ Phụ Tùng",
          icon: "bx-sitemap",
          path: "/admin/so-do-phu-tung",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Mã Thay Thế",
          icon: "bx-git-compare",
          path: "/admin/ma-thay-the",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Cửa Hàng & Bán Hàng",
          type: "header",
          roles: ["ADMIN", "STAFF"]
        },
        {
          title: "Danh Mục Sản Phẩm",
          icon: "bx-collection",
          path: "/admin/danh-muc-san-pham",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Danh Sách Sản Phẩm",
          icon: "bx-cart",
          path: "/admin/san-pham",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Kinh Doanh & Vận Hành",
          type: "header",
          roles: ["ADMIN", "STAFF"]
        },
        {
          title: "Yêu Cầu & Đơn Khách",
          icon: "bx-receipt",
          path: "/admin/don-hang",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Gom Đơn & PO",
          icon: "bx-archive-out",
          path: "/admin/don-nhap-hang",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Danh Sách Kho",
          icon: "bx-store-alt",
          path: "/admin/danh-sach-kho",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Tồn Kho",
          icon: "bx-building-house",
          path: "/admin/ton-kho",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Xuất / Nhập / Tồn",
          icon: "bx-transfer-alt",
          path: "/admin/the-kho",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Nội Dung & CMS",
          type: "header",
          roles: ["ADMIN"]
        },
        {
          title: "Chuyên Mục",
          icon: "bx-folder",
          path: "/admin/chuyen-muc",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Tin Tức",
          icon: "bx-news",
          path: "/admin/tin-tuc",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "FAQ",
          icon: "bx-help-circle",
          path: "/admin/faq",
          roles: ["ADMIN"],
          type: "item"
        }
      ]
    };
  },
  computed: {
    currentUserRole() {
      const store = useAuthStore();
      return store.user && store.user.roles && store.user.roles.length > 0 ? store.user.roles[0].toUpperCase() : 'STAFF';
    },
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
      if (!store.user || !store.user.roles) return 'admin';
      return store.user.roles[0];
    },
    displayRoleName() {
      const store = useAuthStore();
      if (!store.user || !store.user.roles || store.user.roles.length === 0) return 'Quản Trị Viên';
      const role = store.user.roles[0];
      if (role.toLowerCase() === 'admin') return 'Quản Trị Viên';
      if (role.toLowerCase() === 'staff') return 'Nhân Viên';
      return role;
    },
    // Chỉ trả về những Menu mà user có quyền truy cập
    filteredMenus() {
      return this.menus.reduce((result, menu) => {
        // Nếu mục có roles quy định và user không có quyền -> Bỏ qua
        if (menu.roles && !menu.roles.includes(this.currentUserRole)) return result;

        if (menu.type === 'submenu') {
          const visibleChildren = menu.children ? menu.children.filter(child => !child.roles || child.roles.includes(this.currentUserRole)) : [];
          if (visibleChildren.length > 0) {
            result.push({ ...menu, children: visibleChildren });
          }
        } else {
          result.push(menu);
        }
        return result;
      }, []);
    }
  },
  methods: {
    // Kiểm tra xem có bất kỳ menu con nào đang được active không
    isSubmenuActive(children) {
      if (!children) return false;
      return children.some(child => this.$route.path === child.path);
    }
  }
};
</script>
