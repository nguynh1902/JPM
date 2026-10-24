# QUY CHUẨN LẬP TRÌNH GIAO DIỆN CLIENT (BOOTSTRAP 5 & THEEVENT 1.0.0)

> **Cấp độ:** Bắt buộc tuân thủ 100% cho mọi trang Khách hàng (Client: Khách vãng lai, Thợ, Garage, Đối tác B2B).  
> **Nguyên tắc cốt lõi:** *"Code bằng ngôn ngữ Bootstrap 5 gọn gàng như Admin, ăn trọn bộ tài nguyên TheEvent 1.0.0, tuyệt đối không tự chế CSS riêng."*

---

## 1. NGUYÊN LÝ 2 TẦNG (ARCHITECTURE PHILOSOPHY)

Tương tự như cấu trúc chuẩn của Admin (Sneat), toàn bộ giao diện Client của JPM được xây dựng theo mô hình 2 tầng tách biệt rõ ràng:

1. **Tầng cú pháp lập trình (Markup Language): 100% Bootstrap 5**
   - Mọi cấu trúc trang, lưới phân cột, khối nội dung, bảng giỏ hàng, form tra cứu đều sử dụng class chính quy của **Bootstrap 5** (`container`, `row`, `col-12`, `col-md-6`, `col-lg-4`, `card`, `table`, `table-hover`, `form-control`, `input-group`, `d-flex`, `align-items-center`, `gap-2`, `mb-3`...).
   - HTML viết gãy gọn, sạch sẽ, chuẩn ngữ nghĩa, không lồng ghép thẻ thừa.

2. **Tầng hiển thị giao diện (Theme & UI Assets): 100% TheEvent 1.0.0**
   - Bản thân **TheEvent chính là một template cao cấp được xây dựng trên nền Bootstrap 5.3+** bởi BootstrapMade.
   - Khi áp dụng TheEvent, toàn bộ giao diện tự động mang phong cách hiện đại, thanh lịch với:
     - **Font chữ:** `Raleway` (Tiêu đề, Navigation, Nút bấm) & `Roboto` (Nội dung văn bản).
     - **Màu sắc thương hiệu JPM Marine:**
       - Deep Navy Blue (`#0A1128`): Đại diện cho biển sâu, uy tín kỹ thuật, dùng cho Header, Navbar, Tiêu đề chính.
       - Bright Marine Red (`#f82249`): Màu nhấn nổi bật cho nút Mua ngay, Giỏ hàng, Giá tiền, Badge ưu đãi.
       - Clean White (`#ffffff`) & Light Gray (`#f8f9fa`): Cho nền thẻ card và nền section tạo độ tương phản cao, dễ đọc.
     - **Hệ thống Icon:** **Bootstrap Icons (`bi bi-*`)** chính hãng.

---

## 2. NƠI CHỨA TÀI NGUYÊN VÀ CƠ CHẾ NẠP CSS CLIENT

### 2.1. Thư mục tài nguyên duy nhất của Client
Toàn bộ tài nguyên gốc của template TheEvent 1.0.0 được lưu trữ tập trung tại:
📁 `FE_JPM/src/assets/client/`
- `vendor/bootstrap/css/bootstrap.min.css`: Core Bootstrap 5.
- `vendor/bootstrap-icons/bootstrap-icons.css`: Hệ thống icon chính thức (`bi bi-*`).
- `css/main.css`: CSS giao diện chính của TheEvent (biến màu `--accent-color`, `--heading-color`, typography, animation).
- `vendor/glightbox/`: Thư viện xem ảnh phóng to (Gallery sơ đồ, ảnh phụ tùng).
- `vendor/swiper/`: Thư viện Carousel banner và slider sản phẩm.
- `vendor/bootstrap/js/bootstrap.bundle.min.js`: JS tương tác modal, dropdown của Bootstrap.

### 2.2. Cơ chế cách ly hoàn toàn qua `LayoutClient.vue`
- File [`FE_JPM/src/layout/LayoutClient.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/layout/LayoutClient.vue) chịu trách nhiệm nạp các tài nguyên này cho người dùng ngoài Client.
- Khi chuyển đổi giữa Client và Admin, hệ thống đảm bảo CSS của TheEvent và Sneat không bao giờ xung đột với nhau.

---

## 3. HỆ THỐNG COMPONENT CHUẨN CLIENT (THEEVENT DESIGN SYSTEM)

Mọi lập trình viên và AI khi code hoặc sửa các trang Client (Trang chủ, Tra cứu Catalog, Chi tiết sơ đồ rã máy, Giỏ hàng, Lịch sử đơn hàng, Tin tức...) **BẮT BUỘC** sử dụng các cấu trúc chuẩn sau:

### 3.1. Cấu trúc Khối nội dung (Section & Section Title)
Mọi section trên trang Client phải tuân thủ chuẩn của TheEvent:
```html
<section class="section py-5">
  <div class="container">
    <!-- Tiêu đề Section chuẩn TheEvent -->
    <div class="section-title text-center mb-5">
      <span class="text-uppercase text-danger fw-bold small">DANH MỤC PHỤ TÙNG</span>
      <h2 class="fw-bold text-dark mt-1">Tra Cứu Theo Thương Hiệu</h2>
      <p class="text-muted">Chọn thương hiệu động cơ thủy để bắt đầu tra cứu sơ đồ rã máy chi tiết</p>
    </div>

    <!-- Nội dung dùng Grid Bootstrap 5 -->
    <div class="row g-4">
      <!-- Cột con -->
    </div>
  </div>
</section>
```

### 3.2. Nút bấm chuẩn Client (TheEvent Buttons)
Không tự viết class nút tùy biến. Sử dụng hệ nút Bootstrap được TheEvent style hóa:
```html
<!-- Nút Mua ngay / Gửi yêu cầu báo giá (Accent Marine Red) -->
<button class="btn btn-danger px-4 py-2 fw-semibold">
  <i class="bi bi-cart-plus me-1"></i> Thêm Vào Giỏ
</button>

<!-- Nút Xem chi tiết / Tra cứu (Navy Blue) -->
<button class="btn btn-primary px-4 py-2 fw-semibold">
  <i class="bi bi-search me-1"></i> Tra Cứu Catalog
</button>

<!-- Nút Viền mỏng (Outline) -->
<button class="btn btn-outline-danger px-3 py-2">
  <i class="bi bi-file-earmark-pdf me-1"></i> Xem Bản Vẽ
</button>
```

### 3.3. Thẻ Sản Phẩm / Phụ Tùng (Product & Part Cards)
Sử dụng thẻ Card nền trắng, đổ bóng mềm của TheEvent:
```html
<div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden product-card">
  <!-- Ảnh sản phẩm / sơ đồ -->
  <div class="position-relative bg-light d-flex align-items-center justify-content-center p-3" style="height: 200px;">
    <img :src="item.image" :alt="item.name" class="img-fluid object-fit-contain h-100" />
    <span v-if="item.is_oem" class="badge bg-danger position-absolute top-0 start-0 m-2">Chính Hãng OEM</span>
  </div>
  <!-- Thông tin -->
  <div class="card-body d-flex flex-column justify-content-between p-3">
    <div>
      <small class="text-muted text-uppercase fw-semibold">{{ item.brand_name }}</small>
      <h6 class="fw-bold text-dark mb-2 text-truncate" :title="item.name">{{ item.name }}</h6>
      <div class="text-danger fw-bold fs-5 mb-2">{{ formatCurrency(item.price) }}</div>
    </div>
    <div class="d-grid mt-2">
      <button class="btn btn-outline-danger btn-sm">Xem Chi Tiết</button>
    </div>
  </div>
</div>
```

### 3.4. Bảng Giỏ Hàng & Lịch Sử Đơn Hàng (Tables)
Bảng ở Client phải gọn gàng, thoáng đãng, tuân thủ phong cách Bootstrap 5:
```html
<div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th class="text-center" style="width: 5%">
          <input type="checkbox" class="form-check-input" v-model="selectAll" />
        </th>
        <th style="width: 45%">Sản Phẩm & Phụ Tùng</th>
        <th class="text-center" style="width: 15%">Đơn Giá</th>
        <th class="text-center" style="width: 15%">Số Lượng</th>
        <th class="text-center" style="width: 15%">Thành Tiền</th>
        <th class="text-center" style="width: 5%">Xóa</th>
      </tr>
    </thead>
    <tbody>
      <!-- Hàng dữ liệu căn giữa STT, Checkbox, Đơn giá, Nút xóa -->
    </tbody>
  </table>
</div>
```

### 3.5. Ô tìm kiếm & Bộ lọc (Search Bar & Filter Inputs)
Dùng `.input-group` kết hợp icon Bootstrap Icons:
```html
<div class="input-group">
  <input type="text" class="form-control py-2 ps-3" placeholder="Nhập mã phụ tùng, số seri máy..." v-model="searchQuery" />
  <button class="btn btn-danger px-4" type="button" @click="handleSearch">
    <i class="bi bi-search me-1"></i> Tìm Kiếm
  </button>
</div>
```

---

## 4. NGUYÊN TẮC THƯ VIỆN THIẾT KẾ & "ZERO LOCAL CSS HACKS" TRÊN CLIENT

### 4.1. Coi Template TheEvent như một Thư viện Thiết kế Tập trung
- Bộ tài nguyên TheEvent tại `FE_JPM/src/assets/client/` đóng vai trò như một thư viện UI chuẩn cho toàn bộ người dùng Client. Nó đã bao hàm đầy đủ kiểu hiển thị của Section, Card sản phẩm, Typography, Buttons, Badges, Animation.
- Giống như Admin, toàn bộ giao diện Client phải đồng nhất về phong cách và màu sắc thương hiệu hải quân (Navy `#0A1128` + Marine Red `#f82249`).

### 4.2. Quy tắc Chuẩn hóa & Mở rộng bằng Mã Bootstrap 5 trong `main.css`
> **Tình huống:** Khi một trang Client (VD: Chi tiết Catalog, Báo giá B2B, Giỏ hàng) cần điều chỉnh màu sắc thương hiệu, biến thể nút, huy hiệu hoặc card mà giao diện mặc định cần đồng bộ:
- ❌ **TUYỆT ĐỐI CẤM:** 
  - Không tự viết CSS riêng trong thẻ `<style scoped>` của file Vue.
  - **Không tự độ chế ra các class có tên tùy tiện, dị biệt** (CẤM: `.btn-marine-navy`, `.badge-marine-oem`, `.my-card-custom`...).
- ✅ **BẮT BUỘC:** 
  - Mọi component bên ngoài **100% chỉ sử dụng class chính quy của Bootstrap 5**: `btn btn-primary`, `btn btn-danger`, `btn btn-outline-danger`, `badge bg-danger`, `card`, `table table-hover`...
  - Khi cần định nghĩa hoặc tinh chỉnh giao diện, **bắt buộc viết trong file CSS gốc**:
    📁 `FE_JPM/src/assets/client/css/main.css`
    **Nhưng phải viết bằng chuẩn code và biến Bootstrap 5**, ghi đè hoặc tùy biến trực tiếp trên class và CSS variables của Bootstrap:
    ```css
    /* Tinh chỉnh chuẩn Bootstrap trong main.css của Client */
    /* 1. Tùy biến nút chính quy Bootstrap theo màu thương hiệu */
    .btn-primary {
      --bs-btn-bg: #0A1128;
      --bs-btn-border-color: #0A1128;
      --bs-btn-hover-bg: #1a2750;
      --bs-btn-hover-border-color: #1a2750;
      --bs-btn-color: #ffffff;
    }
    .btn-danger {
      --bs-btn-bg: #f82249;
      --bs-btn-border-color: #f82249;
      --bs-btn-hover-bg: #e0163c;
      --bs-btn-hover-border-color: #e0163c;
      --bs-btn-color: #ffffff;
    }
    /* 2. Badge chuẩn Bootstrap */
    .badge.bg-danger {
      background-color: #f82249 !important;
    }
    ```
- **Bên ngoài component Vue:** Chỉ gọi đúng class chuẩn Bootstrap 5:
  ```html
  <button class="btn btn-primary"><i class="bi bi-box-seam me-1"></i> Tra Cứu Catalog</button>
  <button class="btn btn-danger"><i class="bi bi-cart-plus me-1"></i> Thêm Vào Giỏ</button>
  <span class="badge bg-danger">OEM GENUINE</span>
  ```
- **Lợi ích:** Không sinh ra bất kỳ class "chế" nào, giữ mã nguồn 100% Bootstrap 5 chuẩn mực, mọi trang Client khác đều dùng chung cú pháp đồng nhất.

### 4.3. Quy định nghiêm ngặt về Inline Style (`style="..."`)
- Inline `style=""` **DUY NHẤT** được chấp nhận cho **kích thước vật lý hoặc thông số động** (Physical / Dynamic Dimensions):
  - Chiều rộng cột bảng cố định (VD: `style="width: 5%;"`, `style="width: 15%;"`).
  - Chiều cao khung ảnh sản phẩm hoặc khung vẽ sơ đồ (VD: `style="height: 200px;"`, `style="height: 500px;"`).
  - Tọa độ hotspot đánh số phụ tùng trên bản vẽ sơ đồ nổ (VD: `style="top: 42%; left: 68%;"`).
- **TUYỆT ĐỐI CẤM** dùng inline `style=""` để chỉnh màu sắc, viền bo góc, đổ bóng hay khoảng cách lề. Mọi thuộc tính đó phải dùng class Bootstrap utility hoặc class định nghĩa trong `main.css`.

### 4.4. Không độ thêm CSS thủ công trong component
1. **Tuyệt đối không tự chế class CSS riêng trong `<style scoped>`:**
   - ❌ Không tự viết các class kiểu `.btn-custom-red`, `.my-card-border`, `.custom-header`...
   - ✅ Luôn tận dụng hệ class tiện ích của Bootstrap 5: `d-flex`, `align-items-center`, `justify-content-between`, `gap-*`, `mb-*`, `text-center`, `fw-bold`, `rounded-3`, `shadow-sm`...
2. **Khối `<style scoped>` trên trang Client chỉ dùng cho:**
   - Các hiệu ứng đặc thù không thể làm bằng class utility (VD: phóng to thu nhỏ bản vẽ tương tác SVG/Canvas, kéo thả zoom sơ đồ nổ).
   - Nếu là giao diện danh sách, form, bảng, thẻ thông tin: **Ưu tiên 100% Bootstrap 5 markup và xóa sạch `<style scoped>` để code luôn gọn gàng, nhẹ và dễ bảo trì**.

---

## 5. BẮT BUỘC CHÚ THÍCH (COMMENT) BẰNG TIẾNG VIỆT TOÀN DIỆN

> **Mục tiêu tối thượng:** Bất kỳ lập trình viên, kỹ sư hay AI tiếp quản dự án sau này đều có thể đọc lướt qua là hiểu ngay mục đích, cấu trúc nghiệp vụ và vị trí từng khối giao diện mà không cần phải đoán mò hay dịch ngược code.

### 5.1. Chú thích trong Template Vue (HTML Markup)
Mọi section, khối chức năng, bộ lọc, card, modal và các phân nhánh điều kiện (`v-if` / `v-else`) bắt buộc phải có thẻ chú thích tiếng Việt rõ ràng:
- **Tiêu đề khối / Section:** Đặt ở đầu mỗi thẻ `<section>` hoặc `<div>` lớn.
  ```html
  <!-- Khối 1: Tra Cứu Phụ Tùng Nhanh (Bộ Lọc Động Cơ Thông Minh) -->
  <section id="search-parts" class="...">
  ```
  ```html
  <!-- Khối 4: Tại Sao Chọn JPM Marine (Điểm Mạnh Vượt Trội) -->
  <section class="why-us-section ...">
  ```
- **Phân nhánh trạng thái dữ liệu (Loading / Empty / Real Data):**
  ```html
  <!-- Trạng thái Skeleton Loading khi đang nạp dữ liệu từ API -->
  <template v-if="isLoading">...</template>

  <!-- Trạng thái hiển thị dữ liệu thật -->
  <template v-else>...</template>
  ```
- **Các khối con quan trọng (Cột, Form, Action Buttons):**
  ```html
  <!-- Cột Trái: Tiêu đề và Mô tả giới thiệu -->
  <!-- Cột Phải: Danh sách các thẻ điểm mạnh dịch vụ -->
  <!-- Nút Thêm Vào Giỏ Hàng Nhanh -->
  ```

### 5.2. Chú thích trong Script Vue (Logic & Composables)
Mọi khai báo biến trạng thái (State), truy vấn dữ liệu (Vue Query), thuộc tính tính toán (Computed), theo dõi (Watchers) và phương thức (Methods) phải có chú thích 1-2 dòng bằng tiếng Việt:
```javascript
// Lấy danh sách thương hiệu động cơ từ API công khai (cache RAM 30 phút)
const { data: brands, isLoading: isLoadingBrands } = useQuery(...);

// Tự động đặt lại bộ lọc con khi thương hiệu cha bị thay đổi
watch(selectedBrand, () => {
  selectedEngineType.value = "";
  selectedModel.value = "";
});

// Thêm phụ tùng vào giỏ hàng và hiển thị thông báo toast
const addToCart = (product) => { ... };
```

### 5.3. Chú thích trong Stylesheet (`main.css`)
Mọi đoạn CSS mở rộng, ghi đè hoặc bổ sung phải được gom nhóm và phân cách bằng tiêu đề khối rõ ràng:
```css
/* ==============================================================
   KHỐI TIN TỨC BENTO GRID (TIN TUC & KINH NGHIEM) - 3 CỘT
   ============================================================== */
```

