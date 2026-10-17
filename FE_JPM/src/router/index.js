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
    path: "/ho-tro",
    component: () => import("../views/client/guest/HoTro.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/tra-cuu-bao-gia/:token", component: () => import("../views/client/guest/TraCuuBaoGia.vue"), meta: { layout: "client" } }, { path: "/tra-cuu-don-hang", component: () => import("../views/client/guest/TraCuuDonHang.vue"), meta: { layout: "client" } },
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

  // ADMIN
  {
    path: "/admin",
    redirect: "/admin/dashboard",
  },
  {
    path: "/admin/dashboard",
    component: () => import("../views/admin/Dashboard/DashboardAdmin.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },
  // ================= ADMIN ROUTES (Chuẩn Kebab-case, Phân quyền RBAC) =================
  // BÁN HÀNG & ĐƠN HÀNG
  {
    path: "/admin/don-hang",
    name: "AdminDonHang",
    component: () => import("../views/admin/BanHang/DonHang.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Đơn hàng" },
  },
  {
    path: "/admin/thanh-toan",
    name: "AdminThanhToan",
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Đơn hàng" },
  },

  // VẬN HÀNH & CHUỖI CUNG ỨNG (PO)
  {
    path: "/admin/don-nhap-hang",
    alias: ["/admin/po"],
    name: "AdminPO",
    component: () => import("../views/admin/VanHanh/PO.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Đơn nhập PO" },
  },

  // QUẢN LÝ KHO
  {
    path: "/admin/danh-sach-kho",
    alias: ["/admin/kho"],
    name: "AdminKho",
    component: () => import("../views/admin/Kho/Kho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Danh sách kho" },
  },
  {
    path: "/admin/ton-kho",
    name: "AdminTonKho",
    component: () => import("../views/admin/Kho/TonKho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Tồn kho" },
  },
  {
    path: "/admin/the-kho",
    name: "AdminTheKho",
    component: () => import("../views/admin/Kho/TheKho.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Thẻ kho" },
  },

  // MÁY THỦY & ĐỘNG CƠ
  {
    path: "/admin/loai-dong-co",
    name: "AdminLoaiDongCo",
    component: () => import("../views/admin/MayThuy/LoaiDongCo.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Model & Động cơ" },
  },
  {
    path: "/admin/thuong-hieu",
    name: "AdminThuongHieu",
    component: () => import("../views/admin/MayThuy/ThuongHieu.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Model & Động cơ" },
  },
  {
    path: "/admin/dong-co-model",
    name: "AdminDongCoModel",
    component: () => import("../views/admin/MayThuy/DongCoModel.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Model & Động cơ" },
  },

  // SẢN PHẨM PHỔ THÔNG
  {
    path: "/admin/danh-muc-san-pham",
    name: "AdminDanhMucSanPham",
    component: () => import("../views/admin/SanPham/DanhMucSanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Sản phẩm" },
  },
  {
    path: "/admin/san-pham",
    name: "AdminSanPham",
    component: () => import("../views/admin/SanPham/SanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Sản phẩm" },
  },

  // PHỤ TÙNG & LINH KIỆN
  {
    path: "/admin/nhom-phu-tung",
    name: "AdminNhomPhuTung",
    component: () => import("../views/admin/PhuTung/NhomPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/admin/phu-tung",
    name: "AdminPhuTung",
    component: () => import("../views/admin/PhuTung/PhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/admin/cap-nhat-linh-kien",
    name: "AdminCapNhatLinhKien",
    component: () => import("../views/admin/LinhKien/CapNhatLinhKien.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/admin/so-do-phu-tung",
    name: "AdminSoDoPhuTung",
    component: () => import("../views/admin/PhuTung/SoDoPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },
  {
    path: "/admin/ma-thay-the",
    name: "AdminMaThayThe",
    component: () => import("../views/admin/LinhKien/MaThayThe.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Phụ tùng" },
  },

  // KHÁCH HÀNG & CRM
  {
    path: "/admin/khach-hang",
    name: "AdminKhachHang",
    component: () => import("../views/admin/KhachHang/KhachHang.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Khách hàng" },
  },
  {
    path: "/admin/lich-su-mua",
    name: "AdminLichSuMua",
    meta: { layout: "admin", requiresAuth: true, permission: "Xem Khách hàng" },
  },

  // NHÂN SỰ & PHÂN QUYỀN
  {
    path: "/admin/nhan-vien",
    name: "AdminNhanVien",
    component: () => import("../views/admin/NhanSu/NhanVien.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Nhân sự" },
  },
  {
    path: "/admin/phan-quyen",
    name: "AdminPhanQuyen",
    component: () => import("../views/admin/NhanSu/PhanQuyen.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Vai trò" },
  },

  // NỘI DUNG & TIN TỨC
  {
    path: "/admin/chuyen-muc",
    name: "AdminChuyenMuc",
    component: () => import("../views/admin/NoiDung/ChuyenMuc.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Tin tức & CMS" },
  },
  {
    path: "/admin/tin-tuc",
    name: "AdminTinTuc",
    component: () => import("../views/admin/NoiDung/TinTuc.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Tin tức & CMS" },
  },
  {
    path: "/admin/faq",
    name: "AdminFaq",
    component: () => import("../views/admin/NoiDung/Faq.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Quản lý Tin tức & CMS" },
  },

  // HỆ THỐNG
  {
    path: "/admin/cau-hinh-he-thong",
    name: "AdminCauHinhHeThong",
    component: () => import("../views/admin/HeThong/CauHinhHeThong.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Cấu hình chung" },
  },
  {
    path: "/admin/lich-su-hoat-dong",
    name: "AdminLichSuHoatDong",
    component: () => import("../views/admin/HeThong/LichSuHoatDong.vue"),
    meta: { layout: "admin", requiresAuth: true, permission: "Cấu hình chung" },
  },
  {
    path: "/admin/thong-bao",
    name: "AdminThongBao",
    component: () => import("../views/admin/HeThong/DanhSachThongBao.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },

  // TÀI KHOẢN ADMIN
  {
    path: "/admin/ho-so",
    name: "AdminHoSo",
    component: () => import("../views/admin/TaiKhoan/HoSo.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },
  {
    path: "/admin/doi-mat-khau",
    name: "AdminDoiMatKhau",
    component: () => import("../views/admin/TaiKhoan/DoiMatKhau.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },

  // ================= TỰ ĐỘNG CHUYỂN HƯỚNG TỪ URL CŨ (BACKWARD COMPATIBILITY) =================
  { path: "/admin/khachhang/khachhang", redirect: "/admin/khach-hang" },
  { path: "/admin/khachhang/lichsumua", redirect: "/admin/lich-su-mua" },
  { path: "/admin/banhang/donhang", redirect: "/admin/don-hang" },
  { path: "/admin/banhang/thanhtoan", redirect: "/admin/thanh-toan" },
  { path: "/admin/vanhanh/po", redirect: "/admin/don-nhap-hang" },
  { path: "/admin/kho/kho", redirect: "/admin/danh-sach-kho" },
  { path: "/admin/kho/tonkho", redirect: "/admin/ton-kho" },
  { path: "/admin/kho/thekho", redirect: "/admin/the-kho" },
  { path: "/admin/maythuy/thuonghieu", redirect: "/admin/thuong-hieu" },
  { path: "/admin/maythuy/loaidongco", redirect: "/admin/loai-dong-co" },
  { path: "/admin/maythuy/model", redirect: "/admin/dong-co-model" },
  { path: "/admin/sanpham/danhmucsanpham", redirect: "/admin/danh-muc-san-pham" },
  { path: "/admin/sanpham/sanpham", redirect: "/admin/san-pham" },
  { path: "/admin/phutung/nhomphutung", redirect: "/admin/nhom-phu-tung" },
  { path: "/admin/phutung/phutung", redirect: "/admin/phu-tung" },
  { path: "/admin/phutung/sodophutung", redirect: "/admin/so-do-phu-tung" },
  { path: "/admin/linhkien/capnhat", redirect: "/admin/cap-nhat-linh-kien" },
  { path: "/admin/linhkien/mathaythe", redirect: "/admin/ma-thay-the" },
  { path: "/admin/nhansu/nhanvien", redirect: "/admin/nhan-vien" },
  { path: "/admin/nhansu/phanquyen", redirect: "/admin/phan-quyen" },
  { path: "/admin/noidung/chuyenmuc", redirect: "/admin/chuyen-muc" },
  { path: "/admin/noidung/tintuc", redirect: "/admin/tin-tuc" },
  { path: "/admin/noidung/faq", redirect: "/admin/faq" },
  { path: "/admin/caidat/cauhinhhethong", redirect: "/admin/cau-hinh-he-thong" },
  { path: "/admin/lichsu/hoatdong", redirect: "/admin/lich-su-hoat-dong" },
  { path: "/admin/hethong/thongbao", redirect: "/admin/thong-bao" },
  { path: "/admin/taikhoan/hoso", redirect: "/admin/ho-so" },
  { path: "/admin/taikhoan/doimatkhau", redirect: "/admin/doi-mat-khau" },

  // ================= TỰ ĐỘNG CHUYỂN HƯỚNG TỪ STAFF SANG ADMIN (UNIFIED BACKOFFICE) =================
  { path: "/staff", redirect: "/admin/dashboard" },
  { path: "/staff/dashboard", redirect: "/admin/dashboard" },
  { path: "/staff/don-hang", redirect: "/admin/don-hang" },
  { path: "/staff/khach-hang", redirect: "/admin/khach-hang" },
  { path: "/staff/phu-tung", redirect: "/admin/phu-tung" },
  { path: "/staff/so-do-phu-tung", redirect: "/admin/so-do-phu-tung" },
  { path: "/staff/dong-co-model", redirect: "/admin/dong-co-model" },
  { path: "/staff/san-pham", redirect: "/admin/san-pham" },
  { path: "/staff/ton-kho", redirect: "/admin/ton-kho" },
  { path: "/staff/the-kho", redirect: "/admin/the-kho" },
  { path: "/staff/ho-so", redirect: "/admin/ho-so" },
  { path: "/staff/doi-mat-khau", redirect: "/admin/doi-mat-khau" },
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

  // Chặn đăng nhập nếu đã đăng nhập rồi: Nhân sự vào Admin Dashboard, Khách vào Trang chủ
  if (to.path === "/dang-nhap" && authStore.isLoggedIn) {
    if (authStore.isStaffOrAdmin) return next("/admin/dashboard");
    return next("/");
  }

  // Kiểm tra Route có yêu cầu Auth không
  if (to.meta.requiresAuth) {
    if (!authStore.isLoggedIn) {
      // Chưa đăng nhập, đẩy về trang đăng nhập
      return next("/dang-nhap");
    }

    // Nếu người dùng cố truy cập khu vực Backoffice (/admin/...)
    if (to.path.startsWith('/admin')) {
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
          return next("/admin/dashboard");
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














