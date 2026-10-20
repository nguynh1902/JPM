# QUY CHUẨN LẬP TRÌNH GIAO DIỆN ADMIN & STAFF (BOOTSTRAP 5 & SNEAT 1.0.0)

> **Cấp độ:** Bắt buộc tuân thủ 100% cho mọi trang Quản trị (Admin) và Nhân viên (Staff).  
> **Nguyên tắc cốt lõi:** *"Code bằng ngôn ngữ Bootstrap 5, ăn trọn bộ tài nguyên Sneat 1.0.0, tuyệt đối không tự chế CSS riêng."*

---

## 1. NGUYÊN LÝ 2 TẦNG (ARCHITECTURE PHILOSOPHY)

Giao diện Admin & Staff của JPM được xây dựng theo mô hình 2 tầng tách biệt rõ ràng:

1. **Tầng cú pháp lập trình (Markup Language): 100% Bootstrap 5**
   - Mọi cấu trúc layout, phân cột responsive, bảng biểu, form nhập, modal đều sử dụng class chính quy của **Bootstrap 5** (`row`, `col-12`, `col-md-4`, `card`, `table`, `table-hover`, `form-control`, `modal`, `d-flex`, `gap-2`, `mb-3`...).
   - Tuyệt đối không dùng các framework UI khác (không Tailwind, Vuetify, Ant Design, Element Plus...).

2. **Tầng hiển thị giao diện (Theme & UI Assets): 100% Sneat 1.0.0**
   - Bản thân **Sneat chính là một bộ Design System được biên dịch trên nền SCSS của Bootstrap 5**.
   - Khi áp dụng Sneat, các thẻ mặc định của Bootstrap tự động thừa hưởng font chữ (`Public Sans`), màu sắc thương hiệu (Primary tím `#696cff`), bo góc (`border-radius: 0.375rem`), viền và đổ bóng mềm (`box-shadow`).
   - Sneat mở rộng thêm các tiện ích UI cao cấp độc quyền: `bg-label-*`, `btn-label-*`, `btn-icon`, `input-group-merge`, `avatar-initial`...

---

## 2. NƠI CHỨA TÀI NGUYÊN VÀ CƠ CHẾ NẠP CSS

### 2.1. Thư mục tài nguyên duy nhất
Toàn bộ tài nguyên của Sneat được lưu trữ tập trung tại:
📁 `FE_JPM/src/assets/admin/template/`
- `vendor/fonts/boxicons.css`: Bộ icon Boxicons chính thức.
- `vendor/css/core.css`: CSS cốt lõi của Sneat (Bootstrap 5 compiled).
- `vendor/css/theme-default.css`: Màu sắc, typography, component style của Sneat.
- `css/demo.css`: Tinh chỉnh phụ của template.
- `vendor/libs/perfect-scrollbar/`: Thư viện cuộn mượt cho Sidebar.
- `vendor/libs/apex-charts/`: Thư viện biểu đồ Dashboard.

### 2.2. Cơ chế nạp & cách ly an toàn (`LayoutAdmin.vue`)
- **Nạp động khi vào Admin/Staff:** File `FE_JPM/src/layout/LayoutAdmin.vue` tự động gắn các file CSS trên vào `<head>` kèm thuộc tính `data-layout="admin"`.
- **Dọn dẹp sạch khi rời Admin/Staff:** Trong hook `unmounted()`, layout tự động tháo gỡ toàn bộ các thẻ `<link data-layout="admin">` và `<script data-layout="admin">`.
- **Cam kết:** Giao diện Client (TheEvent) và Admin (Sneat) cách ly 100%, không bao giờ bị đè style lẫn nhau.

---

## 3. HỆ THỐNG COMPONENT CHUẨN SNEAT (UI DESIGN SYSTEM)

Mọi lập trình viên và AI khi code trang Admin/Staff **BẮT BUỘC** sử dụng các component chuẩn sau đây, **NGHIÊM CẤM** tự viết CSS trong `<style scoped>`:

### 3.1. Nhãn trạng thái (Badges)
Sneat sử dụng hệ màu nền pastel chữ đậm (`bg-label-*`). Không tự viết `.badge-custom` hay `.bg-pastel-*`.
```html
<!-- Hoạt động / Thành công -->
<span class="badge rounded-pill bg-label-success">HOẠT ĐỘNG</span>

<!-- Tạm khóa / Ngừng / Thất bại -->
<span class="badge rounded-pill bg-label-danger">TẠM KHÓA</span>

<!-- Chờ xử lý / Cảnh báo -->
<span class="badge rounded-pill bg-label-warning">CHỜ XỬ LÝ</span>

<!-- Thông tin / Tin tức / B2B -->
<span class="badge rounded-pill bg-label-info">TIN TỨC</span>

<!-- Mặc định / Quản trị -->
<span class="badge rounded-pill bg-label-primary">QUẢN TRỊ VIÊN</span>

<!-- Trung tính / Khác -->
<span class="badge rounded-pill bg-label-secondary">MẶC ĐỊNH</span>
```

### 3.2. Nút bấm & Nút thao tác (Buttons & Actions)
Tuyệt đối không tự viết `.action-btn` trong `<style scoped>`.
```html
<!-- Nút chính thêm mới / lưu -->
<button class="btn btn-primary"><i class="bx bx-plus me-1"></i> Thêm Mới</button>

<!-- Nút phụ / Hủy / Đặt lại (Label Button) -->
<button class="btn btn-label-secondary"><i class="bx bx-refresh me-1"></i> Đặt Lại</button>

<!-- Cột Hành Động trong bảng (Icon Buttons chuẩn Sneat) -->
<div class="d-flex align-items-center justify-content-center gap-1">
  <button type="button" class="btn btn-sm btn-icon btn-outline-primary" title="Chỉnh sửa">
    <i class="bx bx-edit-alt"></i>
  </button>
  <button type="button" class="btn btn-sm btn-icon btn-outline-danger" title="Xóa">
    <i class="bx bx-trash"></i>
  </button>
</div>
```

### 3.3. Ô tìm kiếm & Nhập liệu (Input Group Merge)
Sử dụng `.input-group-merge` của Sneat để icon nằm liền mạch bên trong ô nhập:
```html
<div class="input-group input-group-merge">
  <span class="input-group-text"><i class="bx bx-search"></i></span>
  <input type="text" class="form-control" placeholder="Tìm kiếm tên, mã sản phẩm..." v-model="searchQuery" />
</div>
```

### 3.4. Ảnh đại diện & Thumbnail (Avatars)
Sneat cung cấp sẵn hệ thống avatar tròn / vuông có fallback chữ cái:
```html
<!-- Thumbnail sản phẩm / sơ đồ / phụ tùng (Bo góc nhẹ) -->
<div class="avatar avatar-md d-inline-block">
  <img v-if="item.image" :src="getImageUrl(item.image)" class="rounded object-fit-cover w-100 h-100" />
  <span v-else class="avatar-initial rounded bg-label-secondary"><i class="bx bx-image fs-4"></i></span>
</div>

<!-- Avatar người dùng / nhân viên / khách hàng (Tròn xoe) -->
<div class="avatar avatar-sm d-inline-block">
  <img v-if="user.avatar" :src="getImageUrl(user.avatar)" class="rounded-circle object-fit-cover w-100 h-100" />
  <span v-else class="avatar-initial rounded-circle bg-label-primary">{{ user.name.charAt(0) }}</span>
</div>
```

---

## 4. HAI CẤU TRÚC TRANG KINH ĐIỂN (STANDARD PAGE PATTERNS)

Mọi trang danh mục quản trị trong hệ thống Admin/Staff phải thuộc 1 trong 2 Pattern sau để đảm bảo tính đồng bộ 100%:

### PATTERN A: Trang Bảng Danh Sách Toàn Khung (Full-width Card Table + Modal Popup)
> **Áp dụng cho:** Đơn hàng, Sản phẩm, Phụ tùng, Khách hàng, Nhân viên, Sơ đồ, Tin tức, Banner, Voucher...

- **Card Header:** Flex-wrap, bên trái là Tiêu đề (`<h5 class="mb-0 fw-bold text-dark">`), bên phải là Nút Hành Động chính (`<button class="btn btn-primary" data-bs-toggle="modal">`).
- **Card Body Bộ Lọc (Filters):** Nằm gọn trong `py-3 border-bottom`, phân cột bằng `row g-2 align-items-center`. Có ô tìm kiếm `input-group-merge`, các ô `v-select` và nút `btn btn-label-secondary` Đặt lại.
- **Bảng dữ liệu (Table):**
  - Bọc bởi `<div class="table-responsive text-nowrap">`.
  - Thẻ `table`: `<table class="table table-hover align-middle mb-0">`.
  - Header: `<thead class="table-light">` với các cột STT, Cột Hành Động căn giữa (`text-center fw-bold`).
  - Thân bảng: `<tbody class="table-border-bottom-0">`.
- **Card Footer:** `<div class="card-footer py-3 border-top"><span class="text-muted fw-medium">Tổng cộng: X mục</span></div>`.
- **Modal Popup:** Chuẩn Bootstrap 5 / Sneat với `<div class="modal-dialog modal-xl modal-dialog-scrollable">`, chân trang có nút `btn btn-label-secondary` (Hủy) và `btn btn-primary` (Lưu).

---

### PATTERN B: Trang Chia Đôi (Split 4/8: Form Thêm Mới Bên Trái + Bảng Dữ Liệu Bên Phải)
> **Áp dụng cho:** Danh mục sản phẩm, Nhóm phụ tùng, Kho, Thương hiệu, Loại động cơ, Linh kiện, Chuyên mục...

- **Bố cục chính:** `<div class="row">`
- **Cột trái (Col-12 col-md-4 mb-4): Form nhập liệu trực tiếp**
  - `<div class="card">`
  - Header: `<div class="card-header border-bottom"><h5 class="mb-0 fw-bold text-dark">{{ isEditMode ? 'Cập Nhật...' : 'Thêm Mới...' }}</h5></div>`
  - Body: Form các trường nhập liệu (`label.form-label.text-dark.fw-semibold`), chân form có cặp nút Hủy (`btn btn-label-secondary`) và Lưu (`btn btn-primary`).
- **Cột phải (Col-12 col-md-8 mb-4): Bảng danh sách**
  - `<div class="card">`
  - Header: Tiêu đề danh sách kèm ô tìm kiếm nhanh `input-group-merge` (hoặc bộ lọc loại).
  - Table: `table table-hover align-middle mb-0`, `thead.table-light`.
  - Dòng đang sửa được đánh dấu bằng class `.table-active`.
  - Footer: Tổng cộng số lượng mục.

---

## 5. NGUYÊN TẮC THƯ VIỆN THIẾT KẾ & "ZERO LOCAL CSS HACKS"

### 5.1. Coi Template Sneat như một Thư viện Thiết kế Tập trung (Design System Library)
- Bộ tài nguyên Sneat tại `FE_JPM/src/assets/admin/template/` được đối xử như một thư viện UI lõi chứa đầy đủ các thành phần: thẻ card, bảng biểu, hệ màu chữ/nền, các kiểu nút bấm (`btn-*`, `btn-label-*`), các kiểu huy hiệu (`badge`, `bg-label-*`).
- Toàn bộ giao diện Admin & Staff phải hiển thị đồng nhất nhờ kế thừa các class tiện ích có sẵn trong thư viện này.

### 5.2. Quy tắc Mở rộng Thư viện (Extending Sneat via `demo.css`)
> **Tình huống:** Khi một trang nghiệp vụ cần một kiểu nút màu mới, một biến thể badge đặc thù, hoặc kiểu hiển thị mới mà Sneat mặc định chưa có:
- ❌ **TUYỆT ĐỐI CẤM:** Không tự viết class CSS riêng trong thẻ `<style scoped>` của file Vue đó (vì sẽ làm phân mảnh code, trang khác không dùng lại được, gây rác mã nguồn).
- ✅ **BẮT BUỘC:** Vào trực tiếp file CSS của template Admin:
  📁 `FE_JPM/src/assets/admin/template/css/demo.css`
  Viết thêm định nghĩa class chuẩn tại đây theo phong cách Bootstrap/Sneat. Ví dụ:
  ```css
  /* Bổ sung trong demo.css */
  .btn-teal {
    color: #fff;
    background-color: #20c997;
    border-color: #20c997;
  }
  .btn-teal:hover {
    background-color: #1aa179;
    border-color: #1aa179;
  }
  .bg-label-teal {
    background-color: #e8fadf !important;
    color: #20c997 !important;
  }
  ```
- **Bên ngoài component Vue:** Chỉ việc gọi class ra dùng như class Bootstrap thông thường:
  ```html
  <button class="btn btn-teal"><i class="bx bx-check me-1"></i> Xác Nhận</button>
  <span class="badge rounded-pill bg-label-teal">HOÀN TẤT</span>
  ```
- **Lợi ích:** Mọi trang Admin và Staff khác khi cần đều có thể gọi ngay class này, đảm bảo tính tái sử dụng và đồng bộ 100% trên toàn dự án.

### 5.3. Quy định nghiêm ngặt về Inline Style (`style="..."`)
- Inline `style=""` **DUY NHẤT** được chấp nhận cho **kích thước vật lý hoặc thông số động** (Physical / Dynamic Dimensions):
  - Chiều rộng cột bảng mang tính cố định (VD: `style="width: 5%;"`, `style="width: 120px;"`).
  - Chiều cao khung hiển thị ảnh/video hoặc khung vẽ sơ đồ (VD: `style="height: 450px;"`).
  - Tọa độ hotspot động trên sơ đồ rã máy (VD: `style="top: 35%; left: 62%;"`).
- **TUYỆT ĐỐI CẤM** dùng inline `style=""` để chỉnh màu chữ, màu nền, viền bo góc, đổ bóng hay căn lề tùy tiện. Mọi thuộc tính đó phải dùng class của Bootstrap hoặc Sneat.

### 5.4. Không độ thêm CSS thủ công trong component
1. **Tuyệt đối không viết CSS thủ công để định nghĩa lại những thứ Sneat đã có:**
   - ❌ CẤM tự chế: `.action-btn { background: none; border: none; ... }` $\rightarrow$ ✅ DÙNG: `.btn.btn-sm.btn-icon.btn-outline-primary`
   - ❌ CẤM tự chế: `.badge-custom { padding: ... }`, `.bg-pastel-success` $\rightarrow$ ✅ DÙNG: `.badge.rounded-pill.bg-label-success`
   - ❌ CẤM tự chế: `.custom-scroll { height: calc(...) }` $\rightarrow$ ✅ DÙNG: Cấu trúc cuộn chuẩn bảng hoặc để bảng hiển thị tự nhiên với phân trang / scrollbar Bootstrap.
2. **Khối `<style scoped>` trong các file Vue Admin:**
   - Chỉ được phép chứa các căn chỉnh logic tương tác đặc thù (VD: SVG Canvas, hotspot sơ đồ nổ).
   - Nếu trang chỉ có bảng, form, modal, bộ lọc: **Xóa bỏ hoàn toàn khối `<style scoped>`** để code sạch, nhẹ và tối ưu nhất.
