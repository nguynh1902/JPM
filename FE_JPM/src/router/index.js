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
    component: () => import("../views/client/guest/DanhSachPhuTung.vue"),
    meta: { layout: "client" },
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
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  // BÁN HÀNG
  {
    path: "/admin/banhang/donhang",
    component: () => import("../views/admin/BanHang/DonHang.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/banhang/thanhtoan",
    // component: () => import("../views/admin/BanHang/ThanhToan.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // VẬN HÀNH (CHUỖI CUNG ỨNG)
  {
    path: "/admin/vanhanh/po",
    component: () => import("../views/admin/VanHanh/PO.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // QUẢN LÝ KHO
  {
    path: "/admin/kho/kho",
    component: () => import("../views/admin/Kho/Kho.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/kho/tonkho",
    component: () => import("../views/admin/Kho/TonKho.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/kho/thekho",
    component: () => import("../views/admin/Kho/TheKho.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/kho/cross-docking",
    component: () => import("../views/admin/Kho/CrossDocking.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // MÁY THỦY
  {
    path: "/admin/maythuy/loaidongco",
    component: () => import("../views/admin/MayThuy/LoaiDongCo.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/maythuy/thuonghieu",
    component: () => import("../views/admin/MayThuy/ThuongHieu.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/maythuy/model",
    component: () => import("../views/admin/MayThuy/DongCoModel.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin"] },
  },
  // SẢN PHẨM PHỔ THÔNG
  {
    path: "/admin/sanpham/danhmucsanpham",
    component: () => import("../views/admin/SanPham/DanhMucSanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/sanpham/sanpham",
    component: () => import("../views/admin/SanPham/SanPham.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // LINH KIỆN (Thay thế cho Phụ tùng)
  {
    path: "/admin/phutung/nhomphutung",
    component: () => import("../views/admin/PhuTung/NhomPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/phutung/phutung",
    component: () => import("../views/admin/PhuTung/PhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin"] },
  },
  {
    path: "/admin/linhkien/capnhat",
    component: () => import("../views/admin/LinhKien/CapNhatLinhKien.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/phutung/sodophutung",
    component: () => import("../views/admin/PhuTung/SoDoPhuTung.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/linhkien/mathaythe",
    component: () => import("../views/admin/LinhKien/MaThayThe.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  

  // KHÁCH HÀNG & CRM
  {
    path: "/admin/khachhang/khachhang",
    component: () => import("../views/admin/KhachHang/KhachHang.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/khachhang/lichsumua",
    // component: () => import("../views/admin/KhachHang/LichSuMua.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // MARKETING (Đã ẩn theo yêu cầu)

  // NHÂN SỰ
  {
    path: "/admin/nhansu/nhanvien",
    component: () => import("../views/admin/NhanSu/NhanVien.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/nhansu/phanquyen",
    component: () => import("../views/admin/NhanSu/PhanQuyen.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // NỘI DUNG
  {
    path: "/admin/noidung/chuyenmuc",
    component: () => import("../views/admin/NoiDung/ChuyenMuc.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/noidung/tintuc",
    component: () => import("../views/admin/NoiDung/TinTuc.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/noidung/faq",
    component: () => import("../views/admin/NoiDung/Faq.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // HỆ THỐNG
  {
    path: "/admin/caidat/cauhinhhethong",
    component: () => import("../views/admin/HeThong/CauHinhHeThong.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/lichsu/hoatdong",
    component: () => import("../views/admin/HeThong/LichSuHoatDong.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/hethong/thongbao",
    component: () => import("../views/admin/HeThong/DanhSachThongBao.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // TÀI KHOẢN
  {
    path: "/admin/taikhoan/hoso",
    component: () => import("../views/admin/TaiKhoan/HoSo.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },
  {
    path: "/admin/taikhoan/doimatkhau",
    component: () => import("../views/admin/TaiKhoan/DoiMatKhau.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["admin", "staff"] },
  },

  // ================= STAFF ROUTES =================
  
  ];

import { useAuthStore } from "../stores/authStore";

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0, behavior: "smooth" };
    }
  },
});

const PROTOTYPE_MODE = false; // Set false to enable Auth Guard

router.beforeEach((to, from, next) => {
  // Cuộn lên đầu trang mỗi khi chuyển route
  window.scrollTo(0, 0);

  // Lấy store auth
  const authStore = useAuthStore();

  // Giai đoạn UI Prototype: Bỏ qua Auth Guard để Review UI
  if (PROTOTYPE_MODE) {
    return next();
  }

  // Chặn đăng nhập nếu đã đăng nhập rồi
  if (to.path === "/dang-nhap" && authStore.isLoggedIn) {
    return next("/");
  }

  // Kiểm tra Route có yêu cầu Auth không
  if (to.meta.requiresAuth) {
    if (!authStore.isLoggedIn) {
      // Chưa đăng nhập, đẩy về trang đăng nhập
      return next("/dang-nhap");
    }

    // Kiểm tra phân quyền (Role-based)
    if (to.meta.roles && to.meta.roles.length > 0) {
      const hasRole = to.meta.roles.some((role) => authStore.hasRole(role));
      if (!hasRole) {
        // Không có quyền, đẩy về trang chủ báo lỗi
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














