import { createRouter, createWebHistory } from "vue-router";
import Swal from 'sweetalert2';

const routes = [
  // CLIENT - GUEST (Public)
  {
    path: "/",
    component: () => import("../views/client/guest/TrangChu.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/gio-hang",
    component: () => import("../views/client/guest/GioHang.vue"),
    meta: { layout: "client" },
  },

      {
      path: "/san-pham",
      component: () => import("../views/client/guest/SanPham.vue"),
      meta: { layout: "client" },
    },
    {
      path: "/chi-tiet-san-pham/:slug",
      component: () => import("../views/client/guest/ChiTietSanPham.vue"),
      meta: { layout: "client" },
    },
    {
      path: "/tin-tuc",
    component: () => import("../views/client/guest/TinTuc.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/tin-tuc/:id",
    component: () => import("../views/client/guest/ChiTietTinTuc.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/danh-sach-phu-tung",
    redirect: "/tra-cuu",
  },
  {
    path: "/so-do-phu-tung",
    redirect: "/tra-cuu",
  },
  {
    path: "/thuong-hieu",
    redirect: "/thuong-hieu/YAM",
  },
  {
    path: "/gioi-thieu",
    redirect: "/ho-tro",
  },
  {
    path: "/he-thong-dai-ly",
    redirect: "/ho-tro",
  },
  {
    path: "/ho-tro",
    component: () => import("../views/client/guest/HoTro.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/bao-gia/:token",
    alias: ["/tra-cuu-bao-gia/:token"],
    component: () => import("../views/client/guest/TraCuuBaoGia.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/tra-cuu-don-hang",
    component: () => import("../views/client/guest/TraCuuDonHang.vue"),
    meta: { layout: "client" },
  },
    { path: "/tra-cuu",
    component: () => import("../views/client/guest/KetQuaTraCuu.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/thuong-hieu/:brand",
    name: "ThuongHieu",
    component: () => import("../views/client/guest/ThuongHieu.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/thuong-hieu/:brand/:loai/:nam/:model/so-do",
    name: "SoDoPhuTung",
    component: () => import("../views/client/guest/SoDoPhuTung.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/so-do/:model",
    name: "SoDoPhuTungNgan",
    component: () => import("../views/client/guest/SoDoPhuTung.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/chi-tiet-phu-tung/:code",
    component: () => import("../views/client/guest/ChiTietPhuTung.vue"),
    meta: { layout: "client" },
  },

  // CLIENT - CUSTOMER (Requires Auth)
  {
    path: "/_account",
    component: () => import("../views/client/customer/LayoutTaiKhoan.vue"),
    meta: { layout: "client", requiresAuth: true },
    children: [
      { path: "/tai-khoan", component: () => import("../views/client/customer/TaiKhoan.vue") },
      { path: "/garage", component: () => import("../views/client/customer/Garage.vue") },
      { path: "/garage/:id", component: () => import("../views/client/customer/GarageChiTietMay.vue") },
      { path: "/vat-tu-thuong-mua", component: () => import("../views/client/customer/VatTuThuongMua.vue") },
      { path: "/lich-su-don-hang", component: () => import("../views/client/customer/LichSuDonHang.vue") }
    ]
  },

  // AUTHENTICATION
  {
    path: "/dang-nhap",
    component: () => import("../views/auth/XacThuc.vue"),
    meta: { layout: "auth" },
  },
  {
    path: "/dang-ky",
    component: () => import("../views/auth/XacThuc.vue"),
    meta: { layout: "auth" },
  },
  {
    path: "/quen-mat-khau",
    component: () => import("../views/auth/XacThuc.vue"),
    meta: { layout: "auth" },
  },
  {
    path: "/dat-lai-mat-khau",
    component: () => import("../views/auth/XacThuc.vue"),
    meta: { layout: "auth" },
  },

  // PORTAL (CỔNG QUẢN TRỊ & VẬN HÀNH NỘI BỘ THỐNG NHẤT)
  {
    path: "/portal",
    redirect: "/portal/dashboard",
  },
  {
    path: "/portal/dashboard",
    component: () => import("../views/admin/Dashboard/DashboardAdmin.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },
  // ================= PORTAL ROUTES (Chuẩn Kebab-case, Phân quyền RBAC) =================
  // BÁN HÀNG & ĐƠN HÀNG
  {
    path: "/portal/don-hang",
    name: "PortalDonHang",
    component: () => import("../views/admin/BanHang/DonHang.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Đơn hàng" },
  },
  {
    path: "/portal/thanh-toan",
    name: "PortalThanhToan",
    redirect: "/portal/don-hang",
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Đơn hàng" },
  },

  // QUẢN LÝ KHO
  {
    path: "/portal/danh-sach-kho",
    alias: ["/portal/kho"],
    name: "PortalKho",
    component: () => import("../views/admin/Kho/Kho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Danh sách kho" },
  },
  {
    path: "/portal/ton-kho",
    name: "PortalTonKho",
    component: () => import("../views/admin/Kho/TonKho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Tồn kho" },
  },
  {
    path: "/portal/the-kho",
    name: "PortalTheKho",
    component: () => import("../views/admin/Kho/TheKho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Thẻ kho" },
  },

  // MÁY THỦY & ĐỘNG CƠ
  {
    path: "/portal/loai-dong-co",
    name: "PortalLoaiDongCo",
    component: () => import("../views/admin/MayThuy/LoaiDongCo.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Động cơ & Thương hiệu" },
  },
  {
    path: "/portal/thuong-hieu",
    name: "PortalThuongHieu",
    component: () => import("../views/admin/MayThuy/ThuongHieu.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Động cơ & Thương hiệu" },
  },
  {
    path: "/portal/dong-co-model",
    name: "PortalDongCoModel",
    component: () => import("../views/admin/MayThuy/DongCoModel.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Động cơ & Thương hiệu" },
  },

  // SẢN PHẨM PHỔ THÔNG
  {
    path: "/portal/danh-muc-san-pham",
    name: "PortalDanhMucSanPham",
    component: () => import("../views/admin/SanPham/DanhMucSanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Sản phẩm" },
  },
  {
    path: "/portal/san-pham",
    name: "PortalSanPham",
    component: () => import("../views/admin/SanPham/SanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Sản phẩm" },
  },

  // PHỤ TÙNG & LINH KIỆN
  {
    path: "/portal/nhom-phu-tung",
    name: "PortalNhomPhuTung",
    component: () => import("../views/admin/PhuTung/NhomPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/portal/phu-tung",
    name: "PortalPhuTung",
    component: () => import("../views/admin/PhuTung/PhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/portal/cap-nhat-linh-kien",
    name: "PortalCapNhatLinhKien",
    redirect: "/portal/phu-tung",
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/portal/so-do-phu-tung",
    name: "PortalSoDoPhuTung",
    component: () => import("../views/admin/PhuTung/SoDoPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/portal/ma-thay-the",
    name: "PortalMaThayThe",
    component: () => import("../views/admin/LinhKien/MaThayThe.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },

  // KHÁCH HÀNG & CRM
  {
    path: "/portal/khach-hang",
    name: "PortalKhachHang",
    component: () => import("../views/admin/KhachHang/KhachHang.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Khách hàng" },
  },
  {
    path: "/portal/lich-su-mua",
    name: "PortalLichSuMua",
    redirect: "/portal/don-hang",
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Khách hàng" },
  },

  // NHÂN SỰ & PHÂN QUYỀN
  {
    path: "/portal/nhan-vien",
    name: "PortalNhanVien",
    component: () => import("../views/admin/NhanSu/NhanVien.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Nhân sự" },
  },
  {
    path: "/portal/phan-quyen",
    name: "PortalPhanQuyen",
    component: () => import("../views/admin/NhanSu/PhanQuyen.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Vai trò" },
  },

  // NỘI DUNG & TIN TỨC
  {
    path: "/portal/chuyen-muc",
    name: "PortalChuyenMuc",
    component: () => import("../views/admin/NoiDung/ChuyenMuc.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Tin tức & CMS" },
  },
  {
    path: "/portal/tin-tuc",
    name: "PortalTinTuc",
    component: () => import("../views/admin/NoiDung/TinTuc.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Tin tức & CMS" },
  },
  {
    path: "/portal/faq",
    name: "PortalFaq",
    component: () => import("../views/admin/NoiDung/Faq.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Tin tức & CMS" },
  },

  // HỆ THỐNG
  {
    path: "/portal/cau-hinh-he-thong",
    name: "PortalCauHinhHeThong",
    component: () => import("../views/admin/HeThong/CauHinhHeThong.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Cấu hình chung" },
  },
  {
    path: "/portal/lich-su-hoat-dong",
    name: "PortalLichSuHoatDong",
    component: () => import("../views/admin/HeThong/LichSuHoatDong.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Cấu hình chung" },
  },
  {
    path: "/portal/thong-bao",
    name: "PortalThongBao",
    component: () => import("../views/admin/HeThong/DanhSachThongBao.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },

  // TÀI KHOẢN
  {
    path: "/portal/ho-so",
    name: "PortalHoSo",
    component: () => import("../views/admin/TaiKhoan/HoSo.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },
  {
    path: "/portal/doi-mat-khau",
    name: "PortalDoiMatKhau",
    component: () => import("../views/admin/TaiKhoan/DoiMatKhau.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },

  // ================= TỰ ĐỘNG CHUYỂN HƯỚNG TỪ ADMIN / STAFF SANG PORTAL (BACKWARD COMPATIBILITY) =================
  { path: "/admin", redirect: "/portal/dashboard" },
  { path: "/admin/dashboard", redirect: "/portal/dashboard" },
  { path: "/admin/don-hang", redirect: "/portal/don-hang" },
  { path: "/admin/thanh-toan", redirect: "/portal/thanh-toan" },
  { path: "/admin/danh-sach-kho", redirect: "/portal/danh-sach-kho" },
  { path: "/admin/kho", redirect: "/portal/danh-sach-kho" },
  { path: "/admin/ton-kho", redirect: "/portal/ton-kho" },
  { path: "/admin/the-kho", redirect: "/portal/the-kho" },
  { path: "/admin/loai-dong-co", redirect: "/portal/loai-dong-co" },
  { path: "/admin/thuong-hieu", redirect: "/portal/thuong-hieu" },
  { path: "/admin/dong-co-model", redirect: "/portal/dong-co-model" },
  { path: "/admin/danh-muc-san-pham", redirect: "/portal/danh-muc-san-pham" },
  { path: "/admin/san-pham", redirect: "/portal/san-pham" },
  { path: "/admin/nhom-phu-tung", redirect: "/portal/nhom-phu-tung" },
  { path: "/admin/phu-tung", redirect: "/portal/phu-tung" },
  { path: "/admin/cap-nhat-linh-kien", redirect: "/portal/cap-nhat-linh-kien" },
  { path: "/admin/so-do-phu-tung", redirect: "/portal/so-do-phu-tung" },
  { path: "/admin/ma-thay-the", redirect: "/portal/ma-thay-the" },
  { path: "/admin/khach-hang", redirect: "/portal/khach-hang" },
  { path: "/admin/lich-su-mua", redirect: "/portal/lich-su-mua" },
  { path: "/admin/nhan-vien", redirect: "/portal/nhan-vien" },
  { path: "/admin/phan-quyen", redirect: "/portal/phan-quyen" },
  { path: "/admin/chuyen-muc", redirect: "/portal/chuyen-muc" },
  { path: "/admin/tin-tuc", redirect: "/portal/tin-tuc" },
  { path: "/admin/faq", redirect: "/portal/faq" },
  { path: "/admin/cau-hinh-he-thong", redirect: "/portal/cau-hinh-he-thong" },
  { path: "/admin/lich-su-hoat-dong", redirect: "/portal/lich-su-hoat-dong" },
  { path: "/admin/thong-bao", redirect: "/portal/thong-bao" },
  { path: "/admin/ho-so", redirect: "/portal/ho-so" },
  { path: "/admin/doi-mat-khau", redirect: "/portal/doi-mat-khau" },

  // Cũ hơn (Legacy admin path redirects)
  { path: "/admin/khachhang/khachhang", redirect: "/portal/khach-hang" },
  { path: "/admin/khachhang/lichsumua", redirect: "/portal/lich-su-mua" },
  { path: "/admin/banhang/donhang", redirect: "/portal/don-hang" },
  { path: "/admin/banhang/thanhtoan", redirect: "/portal/thanh-toan" },
  { path: "/admin/kho/kho", redirect: "/portal/danh-sach-kho" },
  { path: "/admin/kho/tonkho", redirect: "/portal/ton-kho" },
  { path: "/admin/kho/thekho", redirect: "/portal/the-kho" },
  { path: "/admin/maythuy/thuonghieu", redirect: "/portal/thuong-hieu" },
  { path: "/admin/maythuy/loaidongco", redirect: "/portal/loai-dong-co" },
  { path: "/admin/maythuy/model", redirect: "/portal/dong-co-model" },
  { path: "/admin/sanpham/danhmucsanpham", redirect: "/portal/danh-muc-san-pham" },
  { path: "/admin/sanpham/sanpham", redirect: "/portal/san-pham" },
  { path: "/admin/phutung/nhomphutung", redirect: "/portal/nhom-phu-tung" },
  { path: "/admin/phutung/phutung", redirect: "/portal/phu-tung" },
  { path: "/admin/phutung/sodophutung", redirect: "/portal/so-do-phu-tung" },
  { path: "/admin/linhkien/capnhat", redirect: "/portal/cap-nhat-linh-kien" },
  { path: "/admin/linhkien/mathaythe", redirect: "/portal/ma-thay-the" },
  { path: "/admin/nhansu/nhanvien", redirect: "/portal/nhan-vien" },
  { path: "/admin/nhansu/phanquyen", redirect: "/portal/phan-quyen" },
  { path: "/admin/noidung/chuyenmuc", redirect: "/portal/chuyen-muc" },
  { path: "/admin/noidung/tintuc", redirect: "/portal/tin-tuc" },
  { path: "/admin/noidung/faq", redirect: "/portal/faq" },
  { path: "/admin/caidat/cauhinhhethong", redirect: "/portal/cau-hinh-he-thong" },
  { path: "/admin/lichsu/hoatdong", redirect: "/portal/lich-su-hoat-dong" },
  { path: "/admin/hethong/thongbao", redirect: "/portal/thong-bao" },
  { path: "/admin/taikhoan/hoso", redirect: "/portal/ho-so" },
  { path: "/admin/taikhoan/doimatkhau", redirect: "/portal/doi-mat-khau" },

  // Chuyển hướng từ Staff sang Portal
  { path: "/staff", redirect: "/portal/dashboard" },
  { path: "/staff/dashboard", redirect: "/portal/dashboard" },
  { path: "/staff/don-hang", redirect: "/portal/don-hang" },
  { path: "/staff/khach-hang", redirect: "/portal/khach-hang" },
  { path: "/staff/phu-tung", redirect: "/portal/phu-tung" },
  { path: "/staff/so-do-phu-tung", redirect: "/portal/so-do-phu-tung" },
  { path: "/staff/dong-co-model", redirect: "/portal/dong-co-model" },
  { path: "/staff/san-pham", redirect: "/portal/san-pham" },
  { path: "/staff/ton-kho", redirect: "/portal/ton-kho" },
  { path: "/staff/the-kho", redirect: "/portal/the-kho" },
  { path: "/staff/ho-so", redirect: "/portal/ho-so" },
  { path: "/staff/doi-mat-khau", redirect: "/portal/doi-mat-khau" },
];

import { useAuthStore } from "../stores/authStore";

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    if (to.hash) {
      return { el: to.hash, behavior: "smooth" };
    }
    return { top: 0 };
  },
});

const PROTOTYPE_MODE = false; // Set false to enable Auth Guard

router.beforeEach((to, from, next) => {
  // Lấy store auth
  const authStore = useAuthStore();

  // Giai đoạn UI Prototype: Bỏ qua Auth Guard để Review UI
  if (PROTOTYPE_MODE) {
    return next();
  }

  // Chặn đăng nhập nếu đã đăng nhập rồi: Nhân sự vào Portal Dashboard, Khách vào Trang chủ
  if (to.path === "/dang-nhap" && authStore.isLoggedIn) {
    if (authStore.isStaffOrAdmin) return next("/portal/dashboard");
    return next("/");
  }

  // Kiểm tra Route có yêu cầu Auth không
  if (to.meta.requiresAuth) {
    if (!authStore.isLoggedIn) {
      // Chưa đăng nhập, đẩy về trang đăng nhập
      return next("/dang-nhap");
    }

    // Nếu người dùng cố truy cập khu vực Backoffice (/portal/... hoặc /admin/...)
    if (to.path.startsWith('/portal') || to.path.startsWith('/admin')) {
      // Khách hàng (Customer) bị chặn không cho vào Backoffice
      if (!authStore.isStaffOrAdmin) {
        Swal.fire({
          icon: 'error',
          title: 'Truy cập bị từ chối',
          text: 'Tài khoản Khách hàng không có quyền truy cập vào cổng quản trị nội bộ.',
          confirmButtonColor: '#ff3e1d',
          confirmButtonText: 'Đã hiểu'
        });
        return next("/");
      }

      // Kiểm tra quyền hạn cụ thể của Route (nếu Route có khai báo meta.permission)
      // Super Admin luôn được bypass
      if (to.meta.permission && !authStore.isAdmin) {
        if (!authStore.hasPermission(to.meta.permission)) {
          Swal.fire({
            icon: 'warning',
            title: 'Quyền truy cập bị giới hạn',
            text: `Bạn cần quyền "${to.meta.permission}" để truy cập trang này. Vui lòng liên hệ Quản trị viên.`,
            confirmButtonColor: '#696cff',
            confirmButtonText: 'Quay lại Bảng điều khiển'
          });
          return next("/portal/dashboard");
        }
      }
    }

    // Kiểm tra phân quyền roles (nếu Route có khai báo meta.roles)
    if (to.meta.roles && to.meta.roles.length > 0) {
      const hasRole = to.meta.roles.some((role) => authStore.hasRole(role));
      if (!hasRole && !authStore.isAdmin) {
        Swal.fire({
          icon: 'error',
          title: 'Truy cập bị từ chối',
          text: 'Tài khoản của bạn không có quyền truy cập vào khu vực này.',
          confirmButtonColor: '#ff3e1d',
          confirmButtonText: 'Đã hiểu'
        });
        return next("/");
      }
    }

    // Đủ điều kiện truy cập
    next();
  } else {
    // Route public (không yêu cầu Auth)
    next();
  }
});

export default router;














