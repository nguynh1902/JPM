# HƯỚNG DẪN TÙY BIẾN GIAO DIỆN & QUY TRÌNH SỬA CSS CLIENT
*(Cẩm Nang Thay Đổi Phong Cách Thiết Kế, Bảng Màu & Kiểu Dáng Component Chuẩn Bootstrap 5)*

> **Cấp độ:** Bắt buộc đọc và tuân thủ khi thay đổi giao diện, đổi theme (re-theming), hoặc tinh chỉnh phong cách thị giác của phân hệ Khách hàng (Client Storefront).  
> **Nguyên tắc nền tảng:** *"Coi bộ CSS template gốc như một thư viện Framework/Design System; viết page bằng 100% cú pháp Bootstrap 5 ngữ nghĩa để toàn bộ website tự động biến hình đồng bộ khi CSS gốc thay đổi."*

---

## 1. BẢN CHẤT KIẾN TRÚC & TRIẾT LÝ HOẠT ĐỘNG (ARCHITECTURE PHILOSOPHY)

Trong dự án JPM, mối quan hệ giữa **Tài nguyên CSS gốc** và **Mã nguồn các trang Vue** hoạt động theo mô hình **Design System 2 Tầng**:

```text
┌────────────────────────────────────────────────────────────────────────┐
│ TẦNG 1: FRAMEWORK & DESIGN SYSTEM LÕI (FE_JPM/src/assets/client/css/) │
│ - Chứa CSS Tokens, biến màu sắc (:root), typography, bóng đổ          │
│ - Chuẩn hóa và ghi đè các component Bootstrap 5 chính quy             │
│   (.btn-danger, .btn-primary, .card, .badge, .table, .form-control)   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Kế thừa tự động 100%
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│ TẦNG 2: CÁC PAGE VUE GIAO DIỆN (FE_JPM/src/views/client/...)          │
│ - Viết bằng 100% ngôn ngữ Bootstrap 5 chuẩn ngữ nghĩa                 │
│   (<div class="card">, <button class="btn btn-danger">, <div class="row">)│
│ - KHÔNG TỰ CHẾ CLASS CSS RIÊNG, KHÔNG DÙNG INLINE STYLE MÀU SẮC        │
└────────────────────────────────────────────────────────────────────────┘
```

### 👉 Ý nghĩa sống còn của mô hình này:
1. **Tính nhất quán tuyệt đối (Global UI Consistency):**
   * Coi tầng CSS gốc (`main.css`) như một **thư viện mở rộng của Bootstrap**. Mọi component, màu sắc, nút bấm, thẻ card, huy hiệu đã được định nghĩa chuẩn hóa một lần duy nhất tại đây.
   * **CÓ RỒI THÌ DÙNG LẠI:** Khi Tầng Vue gọi `class="btn btn-danger"`, thì từ **Trang chủ đến Trang Tra Cứu, Giỏ Hàng, Sơ Đồ hay Chi Tiết ĐỀU PHẢI ĂN CHUNG MỘT MÃ MÀU ĐỎ DUY NHẤT** (`--bs-danger: #f82249`), cùng một độ bo góc và cùng một hiệu ứng hover.
   * **TUYỆT ĐỐI CẤM:** Không có chuyện ở Trang chủ nút đỏ này, sang trang Giỏ hàng lại tự chế ra một màu đỏ khác hoặc dùng style riêng!
2. **Khả năng biến hình đồng loạt (Universal Re-theming):**
   * Khi bạn muốn **thay đổi toàn bộ phong cách giao diện** (ví dụ: đổi từ phong cách góc cạnh sang bo tròn mềm mại, đổi từ tông Navy/Đỏ sang tông Xanh Lá/Cam, hay đổi độ dày viền của Card):
   * Bạn **CHỈ CẦN SỬA DUY NHẤT** tại các biến và class component trong file CSS gốc (`main.css`).
   * **HÀNG CHỤC TRANG VUE CLIENT** (`TrangChu.vue`, `SoDoPhuTung.vue`, `ChiTietPhuTung.vue`, `GioHang.vue`, `ThuongHieu.vue`, `TinTuc.vue`,...) sẽ **TỰ ĐỘNG BIẾN HÌNH ĐỒNG LOẠT** theo phong cách mới ngay lập tức mà bạn không cần phải tốn công đi sửa từng file `.vue` đơn lẻ!

---

## 2. BẢN ĐỒ VỊ TRÍ CODE GỐC TRONG THƯ MỤC TÀI NGUYÊN CLIENT

Toàn bộ tài nguyên giao diện của Client nằm tập trung tại:
📁 `FE_JPM/src/assets/client/`

```text
FE_JPM/src/assets/client/
├── css/
│   └── main.css                  <-- [TRỌNG TÂM SỐ 1]: Nơi diễn ra 100% việc đổi style/theme
├── vendor/
│   ├── bootstrap/
│   │   ├── css/bootstrap.min.css <-- Core Bootstrap 5.3+ nguyên bản
│   │   └── js/bootstrap.bundle.min.js
│   ├── bootstrap-icons/          <-- Bộ icon chuẩn Bootstrap (bi bi-*)
│   ├── glightbox/                <-- Thư viện phóng to xem ảnh sơ đồ/phụ tùng
│   └── swiper/                   <-- Thư viện trượt slide sản phẩm/banner
└── js/
    └── main.js                   <-- Khởi tạo hiệu ứng animation TheEvent
```

### Cơ chế nạp & cách ly an toàn (`FE_JPM/src/layout/LayoutClient.vue`):
Toàn bộ các file CSS/JS trên được nạp động khi người dùng ở giao diện Client, và tự động dọn dẹp khi chuyển sang Admin Portal. Do đó, **mọi thay đổi trong `main.css` của Client tuyệt đối an toàn và không bao giờ làm ảnh hưởng đến Admin (Sneat)**.

---

## 3. BẢN ĐỒ DÒNG CODE CHI TIẾT TRONG `main.css` (CODE MAP)

Khi mở file `FE_JPM/src/assets/client/css/main.css`, bạn hãy tìm đến các vùng dòng sau để chỉnh sửa đúng mục tiêu:

| Dòng code | Hạng mục thành phần | Mục đích tùy biến |
|---|---|---|
| **Dòng 14 – 48** | **`:root` Variables & Tokens** | Bảng màu chủ đạo, font chữ, độ cong viền toàn cục (`--bs-border-radius`), các mức đổ bóng (`--shadow-*`). |
| **Dòng 50 – 75** | **Nav Menu Colors** | Màu sắc thanh Menu Navigation, màu chữ khi hover, nền dropdown. |
| **Dòng 86 – 128** | **Interaction Utilities** | Hiệu ứng nhấc nổi khi hover (`group-hover`, `list-item-hover`, phóng to ảnh). |
| **Dòng 2028 – 2038** | **Page Titles** | Kiểu dáng tiêu đề trang chuẩn (`.jpm-page-title`). |
| **Dòng 2040 – 2065** | **Buttons (Nút bấm)** | Ghi đè nút bấm Bootstrap: `.btn-danger`, `.btn-jpm`, `.btn-primary`. |
| **Dòng 2075 – 2100** | **Form Controls (Cơ bản)** | Chiều cao, đường viền, màu placeholder của ô nhập liệu form. |
| **Dòng 2162 – 2220** | **Custom Tables** | Kiểu dáng bảng phụ tùng, bảng giỏ hàng (`.custom-table`), huy hiệu số Ref (`.ref-badge`). |
| **Dòng 2240 – 2278** | **Cards & Product Cards** | Kiểu dáng khung thẻ Card (`.card`, `.product-card`, `.premium-product-card`), bo góc viền ảnh. |
| **Dòng 2280 – 2325** | **Input Group & Search** | Kiểu dáng thanh tìm kiếm, hiệu ứng viền phát sáng khi focus (`focus-within`). |
| **Dòng 2326 – 2346** | **Badges & Tags** | Kiểu dáng nhãn dán, badge OEM, badge danh mục (`.badge-pill-jpm`). |

---

## 4. HƯỚNG DẪN THỰC CHIẾN: CÁC BƯỚC ĐỔI GIAO DIỆN SANG PHONG CÁCH MỚI

### 4.1. Thay Đổi Bảng Màu Chủ Đạo (Re-theming Color Palette)
Để đổi toàn bộ tông màu của website Client (ví dụ: chuyển từ Navy/Đỏ sang Deep Teal/Cam hoặc tông màu theo bộ nhận diện mới), bạn chỉ cần sửa cụm `:root` ở **dòng 20 - 34** trong `main.css`:

```css
/* Vị trí: FE_JPM/src/assets/client/css/main.css (Dòng 20 - 34) */
:root { 
  --background-color: #f8f9fa; /* Màu nền tổng thể website */
  --default-color: #495057;    /* Màu chữ nội dung chính */
  --surface-color: #ffffff;    /* Màu nền các thẻ Card / Box */
  
  /* 1. MÀU TIÊU ĐỀ & MÀU CHÍNH (PRIMARY) */
  --heading-color: #0A1128;    /* SỬA TẠI ĐÂY: Màu xanh Navy hoặc màu bạn muốn */
  --bs-primary: #0A1128;
  --bs-primary-rgb: 10, 17, 40;

  /* 2. MÀU ĐIỂM NHẤN HÀNH ĐỘNG (ACCENT / DANGER) */
  --accent-color: #f82249;     /* SỬA TẠI ĐÂY: Màu đỏ Marine hoặc màu cam/vàng mới */
  --bs-danger: #f82249;
  --bs-danger-rgb: 248, 34, 73;
}
```
> **Cơ chế lan truyền:** Toàn bộ nút `.btn-danger`, nút `.btn-primary`, tiêu đề `<h1>` – `<h5>`, nhãn giá tiền, icon nổi bật, thanh tiến trình trên toàn bộ website sẽ **lập tức chuyển sang màu mới**.

---

### 4.2. Thay Đổi Kiểu Dáng Thẻ Card (`.card`, `.product-card`)
Thẻ Card là thành phần thị giác xuất hiện nhiều nhất (sản phẩm, phụ tùng, thương hiệu, tin tức). Để thay đổi phong cách thẻ Card:

#### A. Đổi độ bo cong viền (Border Radius):
* **Nếu muốn phong cách góc cạnh / cơ khí sắc nét (Luxury Minimalism nguyên bản):**
  Đặt `border-radius: 4px !important;` (hoặc `0px` nếu muốn phẳng hoàn toàn).
* **Nếu muốn phong cách hiện đại, trẻ trung, mềm mại:**
  Đặt `border-radius: 12px !important;` hoặc `16px !important;`.

#### B. Nơi sửa trong `main.css`:
Hãy tìm đến **dòng 2240 – 2270**:
```css
/* Vị trí: main.css dòng 2240 */
.card,
.index-page .card {
  background: var(--surface-color, #fff);
  
  /* 1. Sửa độ cong bo góc Card */
  border-radius: 4px !important; /* Đổi thành 12px nếu muốn bo tròn mềm */
  
  /* 2. Sửa độ bóng (Box Shadow) */
  box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important; /* Đổi bóng đậm/nhạt tùy thích */
  
  /* 3. Sửa đường viền (Border) */
  border: 1px solid #e0e6f0 !important; /* Hoặc border: none !important nếu dùng bóng đổ không viền */
  
  overflow: hidden;
  transition: all 0.3s ease;
}

/* 4. ĐỒNG BỘ BO GÓC ẢNH CON: Rất quan trọng để ảnh không bị lem góc vuông ra ngoài */
.card .card-img-wrapper,
.card .card-img-top,
.index-page .card .card-img-wrapper,
.index-page .card .card-img-top {
  border-top-left-radius: 4px !important;  /* Phải bằng đúng border-radius của card bên trên */
  border-top-right-radius: 4px !important;
}

/* 5. Sửa hiệu ứng khi Hover vào sản phẩm */
.product-card:hover,
.premium-product-card:hover {
  transform: translateY(-8px); /* Độ nhấc nổi cao hay thấp */
  box-shadow: 0 16px 40px rgba(0,0,0,0.1) !important;
}
```

---

### 4.3. Thay Đổi Kiểu Dáng Nút Bấm (Button Styling)
Toàn bộ các nút mua hàng, thêm vào giỏ, gửi báo giá được quản lý tại **dòng 2040 – 2060**:

```css
/* Vị trí: main.css dòng 2044 */
.btn-danger, .btn-jpm {
  background: var(--accent-color, #f82249);
  color: #ffffff;
  border: none;
  font-weight: 700;
  
  /* Sửa kiểu dáng nút */
  border-radius: 4px; /* Đổi thành 50px nếu muốn nút con nhộng (Pill button), hoặc 4px theo chuẩn */
  box-shadow: 0 4px 15px rgba(248, 34, 73, 0.25); /* Tắt bóng nếu muốn phong cách Flat */
  padding: 10px 24px;
  
  transition: all 0.3s ease;
}

.btn-danger:hover, .btn-jpm:hover {
  background: #e01b40;
  transform: translateY(-2px); /* Hiệu ứng nhấc nhẹ khi rê chuột */
  box-shadow: 0 8px 20px rgba(248, 34, 73, 0.35);
}
```

---

### 4.4. Thay Đổi Kiểu Dáng Ô Nhập Liệu & Thanh Tìm Kiếm (Inputs & Search)
Tìm đến **dòng 2280 – 2320**:
```css
/* Vị trí: main.css dòng 2280 */
.index-page .form-control, .index-page .form-select, .index-page .input-group-text {
  border-radius: 4px; /* Bo góc ô nhập */
  padding: 12px 14px;
  border: 1.5px solid #e0e6f0;
  background-color: #ffffff;
}

/* Hiệu ứng viền phát sáng khi người dùng click vào ô tìm kiếm */
.index-page .form-control:focus, .index-page .form-select:focus {
  border-color: var(--accent-color) !important;
  box-shadow: 0 0 0.25rem 0.12rem rgba(248, 34, 73, 0.25) !important;
}
```

---

### 4.5. Thay Đổi Font Chữ (Typography)
Để đổi sang font chữ khác (ví dụ: Inter, Montserrat, Be Vietnam Pro):
1. Nhúng font mới vào thẻ `<head>` trong `FE_JPM/index.html` hoặc `@import` đầu `main.css`.
2. Sửa định nghĩa biến tại **dòng 14 – 18** trong `main.css`:
   ```css
   :root {
     --default-font: "Be Vietnam Pro", sans-serif;
     --heading-font: "Montserrat", sans-serif;
     --nav-font: "Montserrat", sans-serif;
   }
   ```

---

## 5. BỘ QUY TẮC VÀNG BẮT BUỘC KHI SỬA CSS CLIENT (STRICT RULES)

Để giữ cho dự án luôn sạch, dễ bảo trì và không bao giờ bị lỗi hiển thị chéo giữa các màn hình, bạn **bắt buộc tuân thủ 5 quy tắc vàng** sau:

### ⚠️ QUY TẮC 1: Sửa CSS phải đi theo cú pháp và Component chuẩn của Bootstrap 5
Khi thêm hoặc sửa style trong `main.css`, bạn phải ghi đè đúng các class Bootstrap chuẩn:
* Nút bấm: `.btn-primary`, `.btn-danger`, `.btn-outline-danger`.
* Bảng: `.table`, `.table-hover`, `.table-responsive`.
* Thẻ: `.card`, `.card-body`, `.card-header`.
* Form: `.form-control`, `.form-select`, `.input-group`.
* Huy hiệu: `.badge`, `.rounded-pill`.

### ⚠️ QUY TẮC 2: Tuyệt đối CẤM đưa class cục bộ của 1 trang vào `main.css`
* **CẤM:** Không được viết các class mang tính chất của riêng 1 trang vào `main.css` (ví dụ: `.news-page-title`, `.home-banner-img`, `.contact-form-box`...).
* **CẤM:** Không được ép số dòng tóm tắt (`-webkit-line-clamp`) của 1 block trên 1 page vào `main.css`.
* **GIẢI PHÁP:** Nếu trang nào có một chi tiết hiển thị riêng biệt (ví dụ tóm tắt tin ở Trang chủ muốn 3 dòng, ở trang Tin tức muốn 2 dòng), **hãy viết vào khối `<style scoped>` ở cuối file `.vue` của trang đó**. Vue sẽ tự động gắn mã hash `data-v-xxx` để cách ly 100%, không làm hỏng trang khác.

### ⚠️ QUY TẮC 3: Tuyệt đối CẤM dùng Inline `style=""` để can thiệp màu sắc, bo góc
* Inline `style=""` **CHỈ ĐƯỢC PHÉP DÙNG** cho kích thước vật lý hoặc thông số động:
  * Chiều rộng cột bảng: `style="width: 15%;"`
  * Chiều cao cố định khung ảnh: `style="height: 400px;"`
  * Tọa độ hotspot trên sơ đồ rã máy: `style="top: 35%; left: 60%;"`
* **CẤM:** Không viết `style="background: red; border-radius: 8px; color: blue;"` trực tiếp vào thẻ HTML. Hãy dùng class Bootstrap hoặc class đã định nghĩa trong `main.css`.

### ⚠️ QUY TẮC 4: Giữ nguyên sự cách ly tuyệt đối với Admin (Sneat)
Không sửa các file vendor gốc trong `vendor/bootstrap/`. Mọi tùy biến chỉ đặt trong `main.css` của Client. Nhờ đó, giao diện Portal Admin (`LayoutAdmin.vue` dùng Sneat) hoàn toàn độc lập và không bao giờ bị ảnh hưởng khi bạn thay đổi giao diện Client.

### ⚠️ QUY TẮC 5: Kiểm tra và Build thử nghiệm sau mỗi lần đổi phong cách
Sau khi chỉnh sửa xong trong `main.css`:
1. Mở trình duyệt kiểm tra các trang trọng điểm:
   * Trang chủ (`/`)
   * Trang tra cứu sơ đồ rã máy (`/thuong-hieu/.../so-do`)
   * Trang chi tiết phụ tùng (`/chi-tiet-phu-tung/...`)
   * Trang giỏ hàng (`/gio-hang`)
2. Chạy lệnh kiểm tra biên dịch Vite trong thư mục `FE_JPM`:
   ```bash
   npm run build
   ```
   Nếu terminal báo `✓ built in ...s` thành công không có lỗi CSS Syntax là giao diện đã sẵn sàng đưa vào vận hành!

---

## 6. QUY TRÌNH NÂNG CẤP & ĐỒNG BỘ GIAO DIỆN CLIENT — LẤY TRANG CHỦ LÀM TRUNG TÂM (STANDARD OPERATING PROCEDURE)

> **Mục tiêu:** Nâng cấp toàn bộ các trang giao diện con của Client thành **MỘT THỂ THỐNG NHẤT**, đồng bộ 100% phong cách với Trang chủ đã hoàn thiện, tuyệt đối không để xảy ra tình trạng "trang này một kiểu, trang kia phong cách khác".

Mọi lập trình viên và AI khi tham gia nâng cấp giao diện Client **BẮT BUỘC** tuân thủ nghiêm ngặt 4 nguyên tắc sau:

### 🌟 NGUYÊN TẮC 1: Lấy Trang Chủ (`TrangChu.vue`) Làm Thước Đo Chuẩn Mực Duy Nhất (Design Anchor)
- `TrangChu.vue` đã được thiết kế hoàn thiện và là chuẩn mực trung tâm của dự án:
  - Cụm tiêu đề trang (`.section-title` với font `Raleway`, phụ đề `Roboto`, text uppercase màu đỏ Marine).
  - Khung thẻ Card chuẩn Luxury 4px (`.card.product-card`, viền mỏng `#e0e6f0`, bóng đổ mờ nhẹ nhàng, không lem góc).
  - Trạng thái tải giả lập (Skeleton Loading) cân đối, chuẩn tỉ lệ khung hình.
  - Bảng màu: Deep Navy Blue (`#0A1128`) cho uy tín kỹ thuật + Đỏ Marine (`#f82249`) làm điểm nhấn hành động.
- Khi làm việc trên bất kỳ trang con nào (`SanPham.vue`, `ChiTietSanPham.vue`, `KetQuaTraCuu.vue`, `SoDoPhuTung.vue`, `GioHang.vue`, `TinTuc.vue`...), **phải đối chiếu và đưa toàn bộ phong cách về cùng một thể thống nhất với Trang chủ**.

### 🔒 NGUYÊN TẮC 2: Tách Biệt Tuyệt Đối — CHỈ SỬA GIAO DIỆN, KHÔNG ĐỤNG VÀO CODE XỬ LÝ
- **Chỉ sửa:** Template HTML, cấu trúc thẻ Bootstrap 5, class màu sắc/tiện ích, bố cục chia cột responsive, khối Skeleton loading, Empty state.
- **TUYỆT ĐỐI CẤM ĐỤNG VÀO CODE XỬ LÝ:**
  - Không sửa các hàm gọi API, không đổi tên biến dữ liệu (`data`, `props`).
  - Không sửa logic tính toán trong `computed`, các hành động trong `methods` (như `addToCart`, phân trang, filter logic).
  - Không sửa cấu trúc Pinia stores (`authStore`, `cartStore`).
- **Mục đích:** Đảm bảo hệ thống chạy mượt mà, giữ nguyên vẹn 100% tính ổn định của nghiệp vụ trong khi giao diện được nâng cấp lên một tầm cao mới.

### 📋 NGUYÊN TẮC 3: Quy Trình Rà Soát & Báo Cáo Bố Cục Từng Trang Trước Khi Làm (Page Audit & Proposal)
Trước khi bắt tay vào chỉnh sửa một trang cụ thể:
1. Đọc và phân tích kỹ lưỡng file `.vue` của trang đó.
2. Đối chiếu với `TrangChu.vue` và báo cáo rõ cho chủ dự án:
   - Trang này hiện tại đang lệch phong cách ở những điểm nào?
   - Cần bổ sung thêm bố cục nào để cân xứng và chuyên nghiệp (ví dụ: Breadcrumb điều hướng, thanh Filter Bar cố định, Sticky Sidebar danh mục, Skeleton loading khi chờ dữ liệu, Empty state khi không có kết quả)?
3. Nhận được sự đồng thuận từ chủ dự án mới tiến hành thực hiện.

### ✋ NGUYÊN TẮC 4: Chỉ Làm Đúng Trang Được Chỉ Định (Targeted Execution)
- Tập trung cao độ: Chỉ chỉnh sửa đúng trang mà chủ dự án yêu cầu tại thời điểm đó.
- Không tự ý sửa lan man sang các page khác khi chưa có lệnh. Hoàn thiện xuất sắc từng page trước khi chuyển sang page tiếp theo.

---

*Tài liệu này được cập nhật và lưu trữ chính thức tại `.agents/11_Huong_Dan_Tuy_Bien_Giao_Dien_Va_CSS_Client.md` nhằm định hướng cho toàn bộ lập trình viên và AI tham gia phát triển giao diện JPM.*
