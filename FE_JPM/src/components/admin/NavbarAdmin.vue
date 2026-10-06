<template>
  <!-- Menu -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo py-3 position-relative" style="height: auto; padding-left: 1rem; padding-right: 1rem;">
      <router-link to="/admin/taikhoan/hoso" class="d-flex align-items-center w-100 text-decoration-none" title="Xem Hồ sơ cá nhân">
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
      <template v-for="(item, index) in filteredMenus" :key="index">
        <!-- 1. Header (Label) -->
        <li class="menu-header small text-uppercase" v-if="item.type === 'header'">
          <span class="menu-header-text">{{ item.title }}</span>
        </li>

        <!-- 2. Single Menu Item -->
        <li 
          class="menu-item" 
          v-else-if="item.type === 'item'" 
          :class="{ 'active': $route.path === item.path || $route.path.startsWith(item.path) && item.path !== '/admin' }"
        >
          <router-link :to="item.path" class="menu-link">
            <i class="menu-icon tf-icons bx" :class="item.icon"></i>
            <div>{{ item.title }}</div>
          </router-link>
        </li>

        <!-- 3. Submenu Group -->
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
                :class="{ 'active': $route.path.includes(child.path) }"
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
      isProfileOpen: false,
      // Giả lập quyền của người dùng hiện tại (Lấy từ localStorage/Pinia)
      // Mặc định là ADMIN để hiển thị UI trong quá trình phát triển
      

      // Cấu hình toàn bộ Menu dựa trên mảng Dữ Liệu (Data-driven)
      menus: [
        {
          title: "Dashboard",
          icon: "bx-home-circle",
          path: "/admin/dashboard",
          roles: ["ADMIN", "STAFF"],
          type: "item"
        },
        {
          title: "Quản Trị & Nhân Sự",
          type: "header",
          roles: ["ADMIN"]
        },
        {
          title: "CRM & Nhân Sự",
          icon: "bx-group",
          type: "submenu",
          roles: ["ADMIN", "STAFF"],
          children: [
            { title: "Khách Hàng", path: "/admin/khachhang/khachhang", roles: ["ADMIN", "STAFF"] },
            { title: "Nhân Viên", path: "/admin/nhansu/nhanvien", roles: ["ADMIN"] },
            { title: "Phân Quyền", path: "/admin/nhansu/phanquyen", roles: ["ADMIN"] }
          ]
        },
        {
          title: "Lịch Sử Hoạt Động",
          icon: "bx-history",
          path: "/admin/lichsu/hoatdong",
          roles: ["ADMIN"],
          type: "item"
        },
        {
          title: "Dữ Liệu Nền Tảng",
          type: "header",
          roles: ["ADMIN", "STAFF"]
        },
        {
          title: "Động Cơ & Phụ Tùng",
          icon: "bx-category",
          type: "submenu",
          roles: ["ADMIN", "STAFF"],
          children: [
            { title: "Thương Hiệu", path: "/admin/maythuy/thuonghieu", roles: ["ADMIN"] },
            { title: "Loại Động Cơ", path: "/admin/maythuy/loaidongco", roles: ["ADMIN"] },
            { title: "Model Động Cơ", path: "/admin/maythuy/model", roles: ["ADMIN", "STAFF"] },
            { title: "Nhóm Phụ Tùng", path: "/admin/phutung/nhomphutung", roles: ["ADMIN"] },
            { title: "Danh Sách Phụ Tùng", path: "/admin/phutung/phutung", roles: ["ADMIN", "STAFF"] },
            { title: "Sơ Đồ Phụ Tùng", path: "/admin/phutung/sodophutung", roles: ["ADMIN", "STAFF"] },
            { title: "Mã Thay Thế", path: "/admin/linhkien/mathaythe", roles: ["ADMIN"] }
          ]
        },
        {
          title: "Cửa Hàng",
          icon: "bx-cart",
          type: "submenu",
          roles: ["ADMIN", "STAFF"],
          children: [
            { title: "Danh Mục Sản Phẩm", path: "/admin/sanpham/danhmucsanpham", roles: ["ADMIN"] },
            { title: "Danh Sách Sản Phẩm", path: "/admin/sanpham/sanpham", roles: ["ADMIN", "STAFF"] }
          ]
        },
        {
          title: "Kinh Doanh & Vận Hành",
          type: "header",
          roles: ["ADMIN", "STAFF"]
        },
        {
          title: "Quản Lý Đơn Hàng",
          icon: "bx-receipt",
          type: "submenu",
          roles: ["ADMIN", "STAFF"],
          children: [
            { title: "Yêu Cầu & Đơn Khách", path: "/admin/banhang/donhang", roles: ["ADMIN", "STAFF"] },
            { title: "Gom Đơn & PO", path: "/admin/vanhanh/po", roles: ["ADMIN"] }
          ]
        },
        {
          title: "Quản Lý Kho",
          icon: "bx-building-house",
          type: "submenu",
          roles: ["ADMIN", "STAFF"],
          children: [
            { title: "Danh Sách Kho", path: "/admin/kho/kho", roles: ["ADMIN"] },
            { title: "Tồn Kho", path: "/admin/kho/tonkho", roles: ["ADMIN", "STAFF"] },
            { title: "Xuất / Nhập / Tồn", path: "/admin/kho/thekho", roles: ["ADMIN"] },
            { title: "Trạm Cross-Docking", path: "/admin/kho/cross-docking", roles: ["ADMIN"] }
          ]
        },
        {
          title: "Nội Dung",
          icon: "bx-news",
          type: "submenu",
          roles: ["ADMIN"],
          children: [
            { title: "Chuyên Mục", path: "/admin/noidung/chuyenmuc", roles: ["ADMIN"] },
            { title: "Tin Tức", path: "/admin/noidung/tintuc", roles: ["ADMIN"] },
            { title: "FAQ", path: "/admin/noidung/faq", roles: ["ADMIN"] }
          ]
        }
      ]
    };
  },
  computed: {
    currentUserRole() {
      const store = useAuthStore();
      return store.user && store.user.roles && store.user.roles.length > 0 ? store.user.roles[0].toUpperCase() : 'STAFF';
    },
    
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  unmounted() {
    document.removeEventListener('click', this.handleClickOutside);
  },userName() {
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
    // Chỉ trả về những Menu mà user có quyền truy cập
    // Nâng cấp: Tự động gộp menu con thành menu đơn nếu chỉ còn 1 mục
    filteredMenus() {
      return this.menus.reduce((result, menu) => {
        // Nếu menu cha không có quyền -> Bỏ qua
        if (!menu.roles.includes(this.currentUserRole)) return result;

        if (menu.type === 'submenu') {
          // Lọc các menu con có quyền
          const visibleChildren = menu.children.filter(child => child.roles.includes(this.currentUserRole));
          
          if (visibleChildren.length === 0) {
            // Nếu không có menu con nào được phép xem -> Ẩn luôn menu cha
            return result;
          } else {
            // Vẫn giữ nguyên dạng dropdown (submenu) cho dù có 1 hay nhiều con
            // Đảm bảo cấu trúc UI của Nhân viên hoàn toàn GIỐNG Admin
            result.push({
              ...menu,
              children: visibleChildren
            });
          }
        } else {
          // Nếu là header hoặc item thông thường thì cho qua
          result.push(menu);
        }
        
        return result;
      }, []);
    }
  },
    methods: {
    async logout() {
      const store = useAuthStore();
      await store.logout();
    },
    toggleProfile() {
      this.isProfileOpen = !this.isProfileOpen;
    },
    handleClickOutside(event) {
      // close if clicked outside
      const el = this.$el.querySelector('.app-brand');
      if (el && !el.contains(event.target)) {
        this.isProfileOpen = false;
      }
    },
    // Kiểm tra xem có bất kỳ menu con nào đang được active không
    isSubmenuActive(children) {
      if (!children) return false;
      return children.some(child => this.$route.path.includes(child.path));
    }
  }
};
</script>

<style>
</style>







