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

### 3.3. Thẻ Sản Phẩm / Phụ Tùng / Card Chung (Product & Card Standard)
- **Quy chuẩn độ cong Card thống nhất toàn bộ Client:** Toàn bộ thẻ `.card`, `.product-card`, `.premium-product-card` trên giao diện Client được quy chuẩn bo góc sắc nét, thanh lịch chuẩn **`border-radius: 4px;`** (Luxury Minimalism 4px nguyên bản của hệ thống) tự động ăn theo template `main.css`.
- **Cơ chế tự động hóa qua CSS Template:** Khi lập trình viên viết code chỉ cần khai báo class `card` (hoặc `product-card`), thẻ sẽ **tự động thừa hưởng độ cong 4px**, viền mỏng `#e0e6f0`, đổ bóng mờ thanh lịch và hiệu ứng overflow clipping. **Tuyệt đối không cần thêm các class bo góc rời rạc** (`rounded-3`, `rounded-2`) hay dùng inline `style="border-radius: ..."` gây bất nhất giao diện giữa các trang.
- **Ảnh thẻ con (`.card-img-wrapper`, `.card-img-top`):** Tự động bo góc trên `4px` (`border-top-left-radius: 4px; border-top-right-radius: 4px;`) để ảnh ôm khít tuyệt đối góc cong của Card, không bị lem góc vuông thừa ra ngoài.

Cấu trúc mẫu chuẩn cho Card:
```html
<div class="card h-100 product-card position-relative">
  <!-- Ảnh sản phẩm / sơ đồ -->
  <div class="card-img-wrapper position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center">
    <img :src="item.image" :alt="item.name" class="card-img-top w-100 transition-transform" />
    <span v-if="item.is_oem" class="badge bg-danger position-absolute top-0 start-0 m-2">Chính Hãng OEM</span>
  </div>
  <!-- Thông tin -->
  <div class="card-body d-flex flex-column justify-content-between p-3 bg-white">
    <div>
      <div class="product-category mb-1 text-truncate">{{ item.brand_name }}</div>
      <h5 class="card-title mb-1">
        <router-link :to="`/chi-tiet-san-pham/${item.slug}`" class="stretched-link">{{ item.name }}</router-link>
      </h5>
      <div class="product-sku mb-3">
        <span class="sku-label">Mã:</span> <span class="sku-code">{{ item.sku }}</span>
      </div>
      <div class="text-danger fw-bold price-value">{{ formatCurrency(item.price) }}</div>
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

## 4. NGUYÊN TẮC PHÂN ĐỊNH KIẾN TRÚC CSS: `main.css` (DÙNG CHUNG TOÀN CỤC) VS `<style scoped>` (ĐẶC THÙ TỪNG TRANG)

### 4.1. Vai trò của `main.css` (Tương tự Bootstrap Framework Core)
- File `FE_JPM/src/assets/client/css/main.css` đóng vai trò như **Framework CSS / Design System dùng chung toàn website Client**, tương tự như vai trò của Bootstrap core.
- **CHỈ CHỨA NHỮNG GÌ DÙNG CHUNG VÀ TÁI SỬ DỤNG TRÊN TOÀN BỘ CÁC TRANG CLIENT:**
  1. **Design Tokens & Biến CSS toàn cục:** `--accent-color`, `--heading-color`, `--heading-font`, `--default-font`...
  2. **Chuẩn hóa & Ghi đè Component Bootstrap 5 chính quy:** Tùy biến `.btn-primary`, `.btn-danger`, `.badge.bg-danger`, `.card`, `.dropdown` theo màu sắc và tỷ lệ thương hiệu JPM Marine:
     ```css
     /* Tinh chỉnh chuẩn Bootstrap trong main.css của Client */
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
     .badge.bg-danger {
       background-color: #f82249 !important;
     }
     .card {
       border-radius: 4px !important;
       box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important;
       border: 1px solid #e0e6f0 !important;
       overflow: hidden;
     }
     ```
  3. **Tiện ích dùng chung toàn cục (Global Shared Utilities):** Các class tiện ích tái sử dụng ở mọi trang như `.text-truncate-2`, `.text-truncate-3`, `.skeleton-box`, `.skeleton-line`...
  4. **Component nghiệp vụ dùng chung (Global Core Components):** Thẻ sản phẩm dùng chung `.product-card`, `.premium-product-card`, nút giỏ hàng `.btn-cart-action`, tiêu đề khối `.section-title`.
- ❌ **TUYỆT ĐỐI CẤM ĐƯA VÀO `main.css`:**
  - **CẤM** viết các class mang tính đặc thù cục bộ của một trang hay một section riêng biệt (ví dụ: `.news-section .card-title`, `.news-img-thumb`, `.cat-bar`, `.zone`, `.lat-title`...).
  - **CẤM** cấu hình số dòng hiển thị nội dung (`-webkit-line-clamp`) cục bộ cho một trang vào `main.css`. Vì mỗi trang có yêu cầu hiển thị khác nhau (trang này tóm tắt 3 dòng, trang khác tóm tắt 2 dòng hoặc 1 dòng hoặc hiển thị đầy đủ). Đưa vào `main.css` sẽ gây ô nhiễm stylesheet toàn cục, ghi đè không kiểm soát và làm vỡ layout các trang khác!

### 4.2. Vai trò và Quy định của `<style scoped>` trong Component Vue (Cục bộ từng trang)
- Mỗi trang hoặc component Vue (`.vue`) khi có các tinh chỉnh hiển thị **riêng biệt của chính nó** thì **BẮT BUỘC** viết trong khối `<style scoped>` ở cuối file `.vue` tương ứng.
- **Thuộc tính `scoped`** của Vue sẽ tự động gắn mã hash nhận diện (ví dụ `[data-v-xxxxxx]`), đảm bảo CSS được **cách ly tuyệt đối 100%**, không bao giờ bị rò rỉ hay gây xung đột với các trang khác trong hệ thống.
- **Những trường hợp BẮT BUỘC viết trong `<style scoped>` của trang:**
  1. **Số dòng hiển thị nội dung đặc thù của block trong trang:** Ví dụ đoạn tóm tắt bài viết ở Trang chủ cần hiển thị tối đa 3 dòng kèm dấu `...` (`-webkit-line-clamp: 3;`), trong khi ở trang Tin tức cần hiển thị 2 dòng hoặc hiển thị đầy đủ.
  2. **Kích thước ảnh, thumbnail hoặc tỷ lệ khung hình (aspect-ratio) đặc thù:** Ví dụ thumbnail tin tức Trang chủ là 170px (tỷ lệ 4/3), banner riêng của từng trang...
  3. **Khoảng cách lề (`margin`/`padding`) và layout tinh chỉnh cục bộ:** Ví dụ khoảng cách sát giữa danh mục, tiêu đề và tóm tắt của block tin tức Trang chủ, ngày tháng cố định đáy (`mt-auto`).
  4. **Màu sắc danh mục hoặc trạng thái hover riêng biệt của component:** Ví dụ `.news-category` chữ đỏ thương hiệu, hover tiêu đề giữ nguyên màu Navy `#0A1128` (không bị chuyển đỏ).
  5. **Hiệu ứng đặc thù không thể làm bằng class utility:** Zoom sơ đồ nổ, phóng to thu nhỏ bản vẽ tương tác SVG/Canvas...
- ❌ **LƯU Ý TRÁNH LẠM DỤNG TRONG `<style scoped>`:**
  - Không tự chế class cho những thứ Bootstrap 5 đã hỗ trợ sẵn: Luôn ưu tiên dùng class utility của Bootstrap (`d-flex`, `align-items-center`, `justify-content-between`, `gap-*`, `mb-*`, `text-center`, `fw-bold`, `rounded-3`, `shadow-sm`).
  - Không viết lại các tiện ích đã có sẵn trong `main.css` (như `.text-truncate-2`, `.text-truncate-3`).

### 4.3. Quy định nghiêm ngặt về Inline Style (`style="..."`)
- Inline `style=""` **DUY NHẤT** được chấp nhận cho **kích thước vật lý hoặc thông số động** (Physical / Dynamic Dimensions):
  - Chiều rộng cột bảng cố định (VD: `style="width: 5%;"`, `style="width: 15%;"`).
  - Chiều cao khung ảnh sản phẩm hoặc khung vẽ sơ đồ (VD: `style="height: 200px;"`, `style="height: 500px;"`).
  - Tọa độ hotspot đánh số phụ tùng trên bản vẽ sơ đồ nổ (VD: `style="top: 42%; left: 68%;"`).
- **TUYỆT ĐỐI CẤM** dùng inline `style=""` để chỉnh màu sắc, viền bo góc, đổ bóng hay khoảng cách lề. Mọi thuộc tính đó phải dùng class Bootstrap utility, class dùng chung trong `main.css`, hoặc `<style scoped>` nếu là tinh chỉnh riêng của trang.

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

