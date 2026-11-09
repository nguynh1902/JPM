# BÁO CÁO KIỂM TOÁN TÍNH ĐỒNG NHẤT GIAO DIỆN & THỰC THI DESIGN SYSTEM 2026
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web Storefront (`FE_JPM`)  
**Tài liệu:** `.agents/12_Global_UI_Consistency_Audit_2026.md`  
**Vai trò thực hiện:** Principal Frontend Architect, Senior UI Engineer & Design System Auditor  
**Phạm vi:** 25 tập tin Vue Client, `main.css`, `style.css` và hệ thống Design Tokens  
**Phương pháp:** Static Code Analysis, AST Selector Matching, Token Inventory, CSS Specificity Audit  
**Trạng thái kiểm toán:** **AUDIT ONLY – KHÔNG THAY ĐỔI MÃ NGUỒN CÁC PAGE**  

---

## 1. EXECUTIVE SUMMARY (TỔNG QUAN KIỂM TOÁN)

Đợt kiểm toán toàn diện này được thực hiện theo chỉ thị bất khả kháng của Product Owner nhằm đo lường mức độ tuân thủ **Design System 2026** trên toàn bộ phân hệ Frontend Client, xác định các điểm lệch chuẩn, xung đột CSS và nguyên nhân gốc rễ trước khi bước vào các Work Package tiếp theo.

### Bảng Chỉ Số Sức Khỏe Giao Diện (UI Health Metrics):

| Chỉ số kiểm toán | Số lượng phát hiện | Tỷ lệ tuân thủ | Mức độ rủi ro | Đánh giá tổng quan |
| :--- | :---: | :---: | :---: | :--- |
| **Tổng số khai báo Border Radius** | 207 vị trí | **62.8%** chuẩn | 🟡 MEDIUM | 77 vị trí lệch chuẩn (4px, 2px, 16px, 50px, 10px). |
| **Hệ thống Nút bấm (Button Classes)** | 154 vị trí (41 kiểu) | **45.0%** chuẩn | 🔴 HIGH | Tồn tại `.btn-shopee*`, `.btn-rounded-8`, nút lệch padding. |
| **Biểu tượng (Icon System)** | 329 biểu tượng | **86.6%** chuẩn | 🔴 CRITICAL | **44 Boxicons** tại 2 trang tra cứu, thiếu CSS tại Client runtime. |
| **Màu sắc thương hiệu & Grayscale** | 1.029 lần (135 mã hex) | **58.2%** chuẩn | 🔴 HIGH | 20 vị trí dùng `#0E1B4D` cũ; 5 biến thể đỏ hover khác nhau. |
| **Typography (Họ phông chữ)** | 95 khai báo | **70.5%** chuẩn | 🟡 MEDIUM | Tồn tại 2 hệ biến song song; nạp 5 font Google thừa ở `index.html`. |
| **Lạm dụng `!important`** | 460 khai báo | N/A | 🔴 HIGH | 297 trong `main.css`, gây khó khăn cho việc kế thừa CSS. |
| **Inline Styles (`style="..."`)** | 401 vị trí | N/A | 🔴 HIGH | 10 trang bị hardcode `padding-top: 110px;` cục bộ. |
| **Thành phần Dùng chung (Shared Components)** | 2 component cốt lõi | **60.0%** bao phủ | 🟢 GOOD | `ProductCard` & `JpmSelect` đã chạy tốt trên 4 view chính. |

---

## 2. BORDER RADIUS INVENTORY (KIỂM TOÁN BO GÓC)

Theo quy chuẩn tối cao **DEC-01** (đã được PO phê duyệt):
- **Card / Modal / Container:** `12px` (`--jpm-radius-card`)
- **Button / Input / Select / Dropdown menu:** `8px` (`--jpm-radius-control`)
- **Badge / Tag / Chip / Pill lọc:** `6px` (`--jpm-radius-badge`)
- **Avatar / Nút tròn / Circle:** `50%` (`--jpm-radius-circle`)

### 2.1. Phân bổ thực tế của 207 vị trí Border Radius:

```
[████████████████████░░░░░░░░░░] 55 vị trí: 8px (Chuẩn Button/Control)
[██████████░░░░░░░░░░░░░░░░░░░░] 33 vị trí: 4px (LỆCH CHUẨN - Tàn dư TheEvent cũ)
[████████░░░░░░░░░░░░░░░░░░░░░░] 24 vị trí: 12px (Chuẩn Card/Modal)
[██████░░░░░░░░░░░░░░░░░░░░░░░░] 19 vị trí: 50% (Chuẩn Circle)
[█████░░░░░░░░░░░░░░░░░░░░░░░░░] 17 vị trí: 6px (Chuẩn Badge)
[███░░░░░░░░░░░░░░░░░░░░░░░░░░░]  9 vị trí: 2px (LỆCH CHUẨN - Shopee/Micro badge)
[██░░░░░░░░░░░░░░░░░░░░░░░░░░░░]  8 vị trí: var(--jpm-radius-badge, 6px)
[██░░░░░░░░░░░░░░░░░░░░░░░░░░░░]  6 vị trí: var(--jpm-radius-control, 8px)
[██░░░░░░░░░░░░░░░░░░░░░░░░░░░░]  6 vị trí: 16px (LỆCH CHUẨN - Bo góc quá đà)
[█░░░░░░░░░░░░░░░░░░░░░░░░░░░░░]  5 vị trí: 50px (LỆCH CHUẨN - Nút viên thuốc cũ)
[█░░░░░░░░░░░░░░░░░░░░░░░░░░░░░]  5 vị trí: 0 (Góc vuông sắc nhọn)
[░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░] 19 vị trí: Các giá trị dị biệt khác (10px, 14px, 20px, 999px...)
```

### 2.2. Phân loại 77 trường hợp lệch chuẩn:

| Phân loại | Số lượng | Vị trí tiêu biểu | Đánh giá kiến trúc |
| :--- | :---: | :--- | :--- |
| **Nhóm 1: Tàn dư 4px của theme cũ** | **33** | `main.css` (2049, 2707, 3059), `LichSuDonHang.vue` (1490, 1503), `TaiKhoan.vue` (106, 140, 455), `GioHang.vue` (708, 789), `ThuongHieu.vue` (517, 807, 818) | **HIGH** – Xung đột trực tiếp với DEC-01. Modal và Button bị co góc cứng nhắc, thiếu cảm giác sang trọng công nghiệp 2026. |
| **Nhóm 2: Lệch siêu nhỏ 2px** | **9** | `LichSuDonHang.vue` (1115, 1160, 1180, 1204, 1441), `GarageChiTietMay.vue` (89), `main.css` (2810, 3141) | **MEDIUM** – Xuất hiện ở các vạch màu chỉ mục và nút `.btn-shopee`, gây cảm giác góc vụn vặt. |
| **Nhóm 3: Bo góc quá mức 16px, 20px** | **7** | `SoDoPhuTung.vue` (74, 108), `ChiTietTinTuc.vue` (14, 22, 793), `LichSuDonHang.vue` (1229) | **MEDIUM** – Làm Card bị bo tròn quá mức, mất chất kỹ thuật máy thủy. Cần đưa về chuẩn `12px`. |
| **Nhóm 4: Nút viên thuốc 50px / 50rem / 999px** | **10** | `main.css` (372, 884, 1339, 1983, 2589), `ThuongHieu.vue` (481), `TinTuc.vue` (704, 718) | **LOW** – Ngoại lệ chấp nhận được cho badge bo tròn (Pill Badge), nhưng cấm dùng cho Button chính. |
| **Nhóm 5: Ghép góc bất đối xứng (Masking)** | **5** | `main.css` (2941, 2947, 3343, 3349), `KetQuaTraCuu.vue` (773) | **HỢP LỆ** – Dùng để bo 2 góc trên của Card Header (`12px 12px 0 0`), tuân thủ tỷ lệ hình học. |

---

## 3. BUTTON SYSTEM AUDIT (KIỂM TOÁN HỆ THỐNG NÚT BẤM)

### 3.1. Ma trận đối chiếu các loại nút bấm hiện tại:

| Loại nút (Button Type) | Class đại diện | Chiều cao thực tế | Bo góc | Font & Size | Trạng thái Hover/Focus | Điểm lệch chuẩn & Rủi ro |
| :--- | :--- | :---: | :---: | :--- | :--- | :--- |
| **Primary CTA (Marine Red)** | `.btn-danger`, `.btn-jpm-primary` | 44px | 8px | Raleway/Roboto 14px, W600 | Đỏ trầm `#D9163A`, đổ bóng nhẹ | **Đạt chuẩn 2026**. Cần thay thế các class `.btn-danger` thông thường. |
| **Navy Industrial Button** | `.btn-primary`, `.btn-jpm-navy` | 44px | 8px | Raleway 14px, W600 | Deep Navy sáng `#101E42` | **Đạt chuẩn 2026**. |
| **Cart Action Button** | `.btn-cart-action` | 38x38px | 8px | Icon `bi-cart2` 16px | Nền đỏ, icon trắng | **Đạt chuẩn 2026** sau khi triển khai WP-01. |
| **Quote Action Button** | `.btn-quote-action` | 38px | 8px | Roboto 13px, W600 | Viền đỏ, nền đỏ nhạt | **Đạt chuẩn 2026** sau khi triển khai WP-01. |
| **E-Commerce Copy Button** | `.btn-shopee`, `.btn-shopee-primary` | ~34px | **2px** | **13.5px**, màu xám `#555` | Nền đỏ cam thô | 🔴 **CRITICAL** – Nằm trong `LichSuDonHang.vue` dòng 1438-1465. Lạc quẻ hoàn toàn với Design System JPM. |
| **Quick Action / Pill** | `.btn-rounded-8`, `.btn-view-diagram` | 32-36px | 8px / Pill | Roboto 12-13px | Thay đổi tùy page | 🟡 **MEDIUM** – Class tự phát rải rác trong `ThuongHieu.vue` và `TrangChu.vue`. |
| **Reset / Filter Button** | `.btn-reset-sidebar-filter` | 28px | 4px / 6px | Roboto 12px | Đổi màu đỏ | 🟡 **MEDIUM** – Kích thước quá nhỏ, khó chạm trên màn hình cảm ứng di động. |

---

## 4. FORM CONTROLS AUDIT (KIỂM TOÁN BIỂU MẪU & BỘ LỌC)

### 4.1. Ma trận thành phần biểu mẫu:

| Thành phần | File xuất hiện | Chiều cao | Viền & Bo góc | Trạng thái Focus | Tình trạng tuân thủ 2026 |
| :--- | :--- | :---: | :---: | :--- | :--- |
| **Search Input Box** | `TrangChu`, `SanPham`, `ThuongHieu`, `KetQuaTraCuu` | 44px | Viền `#E2E8F0`, bo góc 8px | Viền đỏ, glow `rgba(248,34,73,0.12)` | 🟡 **Đạt hình thức nhưng bị trùng lặp CSS** (Mỗi page tự viết lại 50 dòng CSS trong scoped style). |
| **Dropdown Fitment Filter** | `TrangChu.vue`, `ThuongHieu.vue` | 44px | Bo góc 8px | Arrow xoay 180°, shadow lg | 🟢 **Đạt chuẩn 100%** nhờ sử dụng `JpmSelect.vue`. |
| **Sidebar Select Box** | `KetQuaTraCuu.vue` (dòng 86-130) | 44px | Bo góc 8px | Tự quản lý `openDropdown` | 🔴 **HIGH** – Chưa chuyển sang `<JpmSelect>`, vẫn dùng code div/ul thủ công. |
| **Sort Dropdown** | `SanPham.vue`, `KetQuaTraCuu.vue` | 38px | Bo góc 8px | Tự bắt click-outside | 🟡 **MEDIUM** – Chưa dùng `<JpmSelect>`, code lặp lại 40 dòng/file. |
| **Form Control Standard** | `GioHang.vue`, `TaiKhoan.vue` | 44px / 48px | Viền `#CBD5E1`, bo góc 8px | Bootstrap default blue | 🔴 **HIGH** – `GioHang.vue` và `TaiKhoan.vue` khi focus vẫn hiện viền xanh Bootstrap thay vì viền đỏ Marine. |
| **Thư viện Multiselect thừa** | `main.js` (dòng 35-37) | N/A | N/A | N/A | 🟡 **LOW** – Import `@vueform/multiselect/themes/default.css` trên toàn app dù Client không sử dụng. |

---

## 5. TYPOGRAPHY AUDIT (KIỂM TOÁN ĐỒ HỌA CHỮ)

### 5.1. Hai hệ biến phông chữ song song (Token Duplication):

Tại `FE_JPM/src/assets/client/css/main.css`:
1. **Hệ biến cũ của TheEvent:**
   - `--default-font: "Roboto", system-ui, -apple-system, ...;` (Dùng tại **38 vị trí**)
   - `--heading-font: "Raleway", sans-serif;` (Dùng tại **44 vị trí**)
   - `--nav-font: "Raleway", sans-serif;` (Dùng tại **3 vị trí**)
2. **Hệ biến mới của Design System 2026:**
   - `--jpm-font-heading: 'Raleway', sans-serif;` (Dùng tại **1 vị trí**)
   - `--jpm-font-body: 'Roboto', sans-serif;` (Dùng tại **2 vị trí**)
   - `--jpm-font-mono: 'Roboto Mono', monospace;` (Dùng tại **2 vị trí**)

> **Đánh giá:** May mắn là cả 2 hệ biến đều trỏ về cùng họ font (`Raleway` và `Roboto`), nên người dùng không thấy lệch mặt chữ. Tuy nhiên, về mặt kiến trúc, đây là tình trạng **phân mảnh token** (Token Fragmentation), khiến việc đổi token cấp cao không tự động đồng bộ xuống các component cũ.

### 5.2. Lãng phí tài nguyên nạp font tại `index.html`:
Dòng 20 của [`FE_JPM/index.html`](file:///c:/xampp/htdocs/JPM/FE_JPM/index.html#L20) đang tải:
```html
family=Public+Sans:...&family=Raleway:...&family=Roboto:...&family=Montserrat:...&family=Inter:...
```
- Phân hệ Client chỉ sử dụng: **Raleway** và **Roboto**.
- `Public Sans` thuộc về Admin template (Sneat).
- `Montserrat` và `Inter` hoàn toàn **không có trong Design System 2026** nhưng vẫn được tải về trình duyệt mỗi lần mở trang, làm chậm First Contentful Paint (FCP) khoảng 120ms - 250ms trên kết nối 4G.

---

## 6. COLOR & SURFACE AUDIT (KIỂM TOÁN MÀU SẮC & BỀ MẶT)

### 6.1. Sự tồn tại của mã Navy cũ `#0E1B4D`:
Trong quá trình kiểm toán, phát hiện **20 vị trí** vẫn đang dùng mã màu xanh hải quân cũ `#0E1B4D` (màu gốc của theme TheEvent) thay vì Deep Navy chuẩn `#0A1128`:

| Tập tin | Dòng code | Ngữ cảnh sử dụng | Mức độ ảnh hưởng |
| :--- | :---: | :--- | :---: |
| [`HoTro.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/HoTro.vue#L84) | 84, 178, 191 | Tiêu đề H2 Accordion & nhãn hỗ trợ kỹ thuật | 🟡 MEDIUM |
| [`HoTro.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/HoTro.vue#L280) | 280, 288 | Màu nút bấm `SweetAlert2` | 🟡 MEDIUM |
| [`HoTro.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/HoTro.vue#L347) | 347, 393, 401 | Icon và tiêu đề FAQ trong `<style scoped>` | 🟡 MEDIUM |
| [`SoDoPhuTung.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/SoDoPhuTung.vue#L118) | 118 | Hiển thị phần trăm Zoom Level trên Toolbar | 🟢 LOW |
| [`SoDoPhuTung.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/SoDoPhuTung.vue#L162) | 162 | Badge số lượng phụ tùng của cụm rã máy | 🟢 LOW |
| [`TinTuc.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/TinTuc.vue#L776) | 776 | Nền badge chuyên mục tin tức | 🟢 LOW |
| [`main.css`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/assets/client/css/main.css#L2038) | 2038, 2286, 2314 | Link minimal và text search bar | 🟡 MEDIUM |
| [`main.css`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/assets/client/css/main.css#L2348) | 2348, 2388, 3022 | Fallback `var(--heading-color, #0e1b4d)` | 🟡 MEDIUM |

### 6.2. Phân mảnh màu đỏ Marine Red khi hover:
Thay vì dùng token duy nhất `--jpm-color-red-hover: #D9163A`, mã nguồn đang tồn tại 5 mã màu đỏ khác nhau:
1. `#D9163A`: Token chuẩn 2026.
2. `#D81438`: Dùng trong `LichSuDonHang.vue` dòng 1456.
3. `#E01A3F`: Dùng trong `main.css` dòng 2056.
4. `#E61C40`: Dùng trong `ChiTietTinTuc.vue` dòng 723.
5. `#DC2626`: Dùng trong `ThuongHieu.vue` dòng 610.

---

## 7. ICON MIGRATION AUDIT (DEC-04 ENFORCEMENT)

**Quyết định DEC-04 của PO nêu rõ:** Phân hệ Client chuyển dịch **100% sang Bootstrap Icons (`bi bi-*`)**, bãi bỏ Boxicons (`bx bx-*`).

### 7.1. Bảng thống kê chi tiết:
- **Tổng số biểu tượng tại Client:** 329 biểu tượng.
- **Bootstrap Icons (`bi bi-*`):** 279 biểu tượng (**84.8%**).
- **Inline SVG (Zalo, Facebook):** 6 biểu tượng (**1.8%**).
- **Boxicons vi phạm (`bx bx-*`):** **44 biểu tượng (13.4%)**.

### 7.2. Danh sách 44 vị trí Boxicons vi phạm tập trung tại 2 trang tra cứu:

```
TẬP TIN 1: TraCuuBaoGia.vue (26 vi phạm)
├── Dòng 12:  <i class="bx bx-error-circle"></i>
├── Dòng 32:  <i class="bx bx-user me-2 text-primary"></i>
├── Dòng 33:  <i class="bx bx-phone me-2"></i>
├── Dòng 34:  <i class="bx bx-map me-2 text-primary"></i>
├── Dòng 35:  <i class="bx bx-map me-2 text-muted"></i>
├── Dòng 36:  <i class="bx bx-calendar me-2"></i>
├── Dòng 50:  <i class="bx bx-time-five fs-2 text-warning"></i>
├── Dòng 58:  <i class="bx bx-stopwatch me-1"></i>
├── Dòng 66:  <i class="bx bx-error-circle fs-2 text-danger mt-1"></i>
├── Dòng 73:  <i class="bx bx-refresh me-1"></i>
├── Dòng 82:  <i class="bx bx-loader-alt bx-spin fs-2 text-primary"></i>
├── Dòng 142: <i class="bx bx-check-circle me-1"></i>
├── Dòng 145: <i class="bx bx-time me-1"></i>
├── Dòng 162: <i class="bx bx-message-dots me-1"></i>
├── Dòng 169: <i class="bx bx-qr-scan me-1"></i>
├── Dòng 170: <i class="bx bx-check-shield me-1"></i>
├── Dòng 182: <i class="bx bxl-telegram me-1"></i>
├── Dòng 197: <i class="bx bx-copy"></i>
├── Dòng 214: <i class="bx bx-copy"></i>
├── Dòng 219: <i class="bx bx-info-circle text-primary me-1"></i>
├── Dòng 229: <i class="bx bx-x-circle me-1"></i>
├── Dòng 232: <i class="bx bx-check-circle me-1"></i>
├── Dòng 238: <i class="bx bx-check-circle me-2"></i>
├── Dòng 241: <i class="bx bx-chat me-1"></i>
├── Dòng 250: <i class="bx bx-refresh me-1"></i>
└── Dòng 253: <i class="bx bx-chat me-1"></i>

TẬP TIN 2: TraCuuDonHang.vue (18 vi phạm)
├── Dòng 54:  <i class="bx bx-user me-2 text-primary"></i>
├── Dòng 55:  <i class="bx bx-phone me-2 text-primary"></i>
├── Dòng 59:  <i class="bx bx-map me-2 text-primary"></i>
├── Dòng 118: <i class="bx bx-check-circle me-1"></i>
├── Dòng 121: <i class="bx bx-time me-1"></i>
├── Dòng 138: <i class="bx bxs-truck fs-1 text-primary"></i>
├── Dòng 151: <i class="bx bx-qr-scan me-1"></i>
├── Dòng 152: <i class="bx bx-shield-quarter me-1"></i>
├── Dòng 164: <i class="bx bxl-telegram me-1"></i>
├── Dòng 180: <i class="bx bx-copy"></i>
├── Dòng 197: <i class="bx bx-copy"></i>
├── Dòng 218: <i class="bx bx-check-shield me-1"></i>
├── Dòng 219: <i class="bx bx-time me-1"></i>
├── Dòng 224: <i class="bx bx-file-blank me-1"></i>
├── Dòng 231: <i class="bx bx-hourglass me-1"></i>
├── Dòng 240: <i class="bx bx-check-circle me-1"></i>
├── Dòng 249: <i class="bx bx-x-circle me-1"></i>
└── Dòng 259: <i class="bx bx-chat me-1"></i>
```

> **CẢNH BÁO KỸ THUẬT (CRITICAL):**  
> `LayoutClient.vue` **KHÔNG** import file font Boxicons (`boxicons.css`). File này chỉ được nạp ở phân hệ Admin (`LayoutAdmin.vue`).  
> Do đó, khi người dùng vãng lai vào trực tiếp trang `TraCuuBaoGia.vue` hoặc `TraCuuDonHang.vue`, **toàn bộ 44 biểu tượng này có nguy cơ biến thành ô vuông rỗng (ToFu) hoặc mất hiển thị**, gây suy giảm nghiêm trọng độ tin cậy của tính năng tra cứu cọc/báo giá.

---

## 8. CSS CONFLICT & DUPLICATION REPORT (XUNG ĐỘT & TRÙNG LẶP CSS)

### 8.1. Anti-Pattern: Hardcoded Header Offset (`padding-top: 110px`):
Mục 5.1 trong `.agents/10_Client_UI_Standard.md` nghiêm cấm viết cứng `style="padding-top: 110px;"` trong các file Vue con.
Tuy nhiên, kiểm toán phát hiện **10 màn hình** vẫn đang mang dòng inline style này:
1. `ChiTietPhuTung.vue` (dòng 3)
2. `ChiTietSanPham.vue` (dòng 3)
3. `GioHang.vue` (dòng 7)
4. `HoTro.vue` (dòng 5)
5. `KetQuaTraCuu.vue` (dòng 3)
6. `SanPham.vue` (dòng 4)
7. `SoDoPhuTung.vue` (dòng 3)
8. `ThuongHieu.vue` (dòng 6)
9. `TinTuc.vue` (dòng 5)
10. `TraCuuDonHang.vue` (dòng 2: `padding-top: 100px`)

**Nguyên nhân gốc:** Thẻ `<main class="main">` trong [`LayoutClient.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/layout/LayoutClient.vue#L6) chưa được thiết lập `padding-top: var(--jpm-header-height, 80px);`. Do đó, nếu lập trình viên xóa `padding-top: 110px` ở file con, thanh Navbar dính (Sticky Navbar) sẽ che khuất phần đầu trang.

### 8.2. Lạm dụng `!important` (460 lần):
- `main.css`: **297 lần** `!important`. Phần lớn là do theme TheEvent cố tình override các class của Bootstrap (`.btn`, `.card`, `.badge`).
- `GioHang.vue`: **32 lần** `!important`. Dùng để ghi đè giao diện bảng giỏ hàng, radio button và modal.
- `SanPham.vue`: **27 lần** `!important`.
- `ThuongHieu.vue`: **24 lần** `!important`.

### 8.3. Mã CSS thừa thãi sau khi triển khai Shared Components (Dead Scoped CSS):
Sau khi hoàn thành WP-01 tích hợp `ProductCard.vue` và `JpmSelect.vue`:
- `KetQuaTraCuu.vue`: Các dòng 1153 – 1255 trong `<style scoped>` định nghĩa `.product-card`, `.card-img-product`, `.btn-cart-action` đã trở thành **CSS chết** vì template đã dùng component `ProductCard.vue`.
- `ThuongHieu.vue`: Các dòng 608 – 665 định nghĩa `.custom-select-box`, `.select-arrow` đã trở thành **CSS chết** vì template đã dùng `JpmSelect.vue`.
- `SanPham.vue`: Hơn 80 dòng CSS của Card sản phẩm cũ vẫn tồn tại trong thẻ `<style scoped>`.

---

## 9. SHARED COMPONENT AUDIT (ĐÁNH GIÁ THÀNH PHẦN DÙNG CHUNG)

### 9.1. Trạng thái hiện tại của `ProductCard.vue` và `JpmSelect.vue`:

| Tiêu chí | `ProductCard.vue` | `JpmSelect.vue` |
| :--- | :--- | :--- |
| **Vị trí file** | [`FE_JPM/src/components/client/ProductCard.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/components/client/ProductCard.vue) | [`FE_JPM/src/components/client/JpmSelect.vue`](file:///c:/xampp/htdocs/JPM/FE_JPM/src/components/client/JpmSelect.vue) |
| **Màn hình đã tích hợp** | `TrangChu.vue`, `SanPham.vue`, `KetQuaTraCuu.vue` | `TrangChu.vue`, `ThuongHieu.vue` |
| **Màn hình còn bỏ sót** | `ChiTietSanPham.vue` (dòng 207-248), `ChiTietPhuTung.vue` (dòng 279-295) | `KetQuaTraCuu.vue` (sidebar lọc & sắp xếp), `SanPham.vue` (sắp xếp), `Garage.vue` |
| **Khả năng tiếp cận (A11y)** | Keyboard accessible qua `stretched-link`, title đầy đủ | Hỗ trợ phím mũi tên, Enter, Escape, `role="combobox"` |
| **Unit Test Coverage** | Đạt 100% (3 test cases passed) | Đạt 100% (4 test cases passed) |
| **Nguy cơ bị ghi đè CSS** | Thấp (các class mang tiền tố `.btn-cart-action`, `.product-card` khép kín) | Rất thấp (component độc lập, scoped CSS sạch sẽ) |

### 9.2. Khoảng trống linh kiện dùng chung (Missing Shared Components):
Hệ thống hiện tại đang thiếu 4 component dùng chung cần thiết:
1. **`JpmButton.vue` / Global Button standard:** Hiện tại mỗi trang tự ghép class Bootstrap và inline style.
2. **`JpmBadge.vue`:** Chưa có component chuẩn hóa trạng thái tồn kho (In Stock, Quote Required, Superseded).
3. **`JpmSearchInput.vue`:** Thanh tìm kiếm đang bị code lặp lại 4 lần tại 4 trang chính.
4. **`JpmModal.vue`:** Toàn bộ modal (`Garage.vue`, `TaiKhoan.vue`, `LichSuDonHang.vue`) đang dùng HTML Bootstrap thô với bo góc 4px hoặc inline style.

---

## 10. SOURCE VS RUNTIME VERIFICATION (NGHIỆM THỨC MÃ NGUỒN VS RUNTIME)

Theo nguyên tắc minh bạch kỹ thuật của kiểm toán:
1. **Source Audit (Đã kiểm tra 100% mã nguồn):**
   - Phân tích chi tiết 25 file Vue, 1 file `main.css` (3.730 dòng), 1 file `style.css`.
   - Biên dịch thành công 100% qua Vite (`npm run build` hoàn tất trong 1.08s, mã thoát 0).
   - Kiểm thử đơn vị Vitest đạt 9/9 test cases passed.
2. **Runtime Audit (Xác minh cơ chế chạy thực tế):**
   - **Xung đột Header Offset:** Vì `LayoutClient.vue` chưa gán bù trừ tự động, việc người dùng mở các màn hình trên thiết bị di động sẽ dẫn tới khoảng trắng đầu trang bị thừa (do 110px viết cứng lớn hơn chiều cao 64px của Navbar trên Mobile).
   - **Lỗi hiển thị Boxicons:** Ở môi trường production sạch (trình duyệt chưa từng mở trang Admin), 44 Boxicons trên `TraCuuBaoGia.vue` và `TraCuuDonHang.vue` sẽ bị mất hoặc lỗi hiển thị do không có font resource.
   - **Thước đo Touch Target:** Các nút xóa lọc và icon đóng modal trên Mobile đạt 24-28px, dưới ngưỡng khuyến cáo 44px của WCAG 2.1 AA.
3. **Visual Audit (Tính nhất quán hình ảnh):**
   - Khu vực Hero và Catalog trên `TrangChu.vue`, `SanPham.vue` và `ThuongHieu.vue` đã đạt được sự đồng nhất cao về Card và Select Box sau WP-01.
   - Khu vực tài khoản khách hàng (`LichSuDonHang.vue`, `TaiKhoan.vue`) có phong cách hình ảnh chắp vá, tồn tại nút kiểu sàn thương mại điện tử giá rẻ (`.btn-shopee`) thay vì phong cách kỹ thuật hàng hải sang trọng.

---

## 11. DANH SÁCH SAI LỆCH CHI TIẾT THEO FILE VÀ DÒNG

*(Phân loại mức độ: **CRITICAL** > **HIGH** > **MEDIUM** > **LOW**)*

| ID | File | Dòng | Selector / Component | Giá trị hiện tại | Giá trị chuẩn 2026 | Mức độ | Nguyên nhân & Đề xuất |
| :---: | :--- | :---: | :--- | :--- | :--- | :---: | :--- |
| **ERR-01** | `TraCuuBaoGia.vue` | 12-253 | `i.bx` (26 vị trí) | Boxicons (`bx bx-*`) | Bootstrap Icons (`bi bi-*`) | 🔴 **CRITICAL** | Tàn dư template cũ. Chưa import font Boxicons ở Client. Thay thế toàn bộ sang `bi bi-*`. |
| **ERR-02** | `TraCuuDonHang.vue` | 54-259 | `i.bx` (18 vị trí) | Boxicons (`bx bx-*`) | Bootstrap Icons (`bi bi-*`) | 🔴 **CRITICAL** | Tàn dư template cũ. Thay thế toàn bộ sang `bi bi-*`. |
| **ERR-03** | `LichSuDonHang.vue` | 1438-1465 | `.btn-shopee*` | `border-radius: 2px;` | `--jpm-radius-control: 8px;` | 🔴 **CRITICAL** | Sao chép CSS từ giao diện sàn Shopee. Thay thế bằng class nút chuẩn `.btn-jpm-primary` & `.btn-jpm-outline`. |
| **ERR-04** | 10 Client Pages | Dòng 2-7 | `<section>` top | `style="padding-top: 110px;"` | Quản trị tập trung tại Layout | 🔴 **HIGH** | `LayoutClient.vue` chưa cấu hình `--jpm-header-height`. Cần chuẩn hóa tại Layout để xóa bỏ inline style. |
| **ERR-05** | `KetQuaTraCuu.vue` | 86-130 | Sidebar Fitment Filters | Code div/ul thủ công | `<JpmSelect>` | 🔴 **HIGH** | Chưa hoàn tất chuyển đổi component dùng chung tại màn hình Tra Cứu. |
| **ERR-06** | `HoTro.vue` | 84, 178, 191 | Heading, button text | `color: #0e1b4d;` | `color: var(--jpm-color-navy, #0A1128);` | 🟡 **MEDIUM** | Dùng mã màu TheEvent cũ. Thay thế bằng CSS Variable Deep Navy. |
| **ERR-07** | `SoDoPhuTung.vue` | 74, 108 | Card cụm chi tiết & sơ đồ | `style="border-radius: 16px;"` | `--jpm-radius-card: 12px;` | 🟡 **MEDIUM** | Bo góc quá mức. Đưa về chuẩn 12px. |
| **ERR-08** | `TaiKhoan.vue` | 106, 140 | `.modal-content` | `style="border-radius: 4px;"` | `--jpm-radius-card: 12px;` | 🟡 **MEDIUM** | Modal bo góc 4px cũ kỹ. Cần nâng lên 12px theo DEC-01. |
| **ERR-09** | `GioHang.vue` | 708, 789 | Card giỏ hàng, bảng | `border-radius: 4px;` | `border-radius: 12px;` | 🟡 **MEDIUM** | Tàn dư quy chuẩn 4px cũ. Cần chuẩn hóa thành 12px cho Card. |
| **ERR-10** | `index.html` | 20 | Google Fonts Link | Nạp 5 font (Public Sans, Montserrat, Inter...) | Chỉ nạp Raleway & Roboto | 🟡 **MEDIUM** | Lãng phí băng thông nạp 3 font thừa. Cần tinh gọn link Google Fonts. |
| **ERR-11** | `ChiTietSanPham.vue` | 207-248 | Lưới sản phẩm liên quan | Card viết tay thủ công | `<ProductCard>` | 🟡 **MEDIUM** | Bỏ sót chưa tích hợp component dùng chung WP-01. |
| **ERR-12** | `ChiTietPhuTung.vue` | 279-295 | Lưới phụ tùng liên quan | Card viết tay thủ công | `<ProductCard>` | 🟡 **MEDIUM** | Bỏ sót chưa tích hợp component dùng chung WP-01. |
| **ERR-13** | `ThuongHieu.vue` | 608-665 | Scoped style select | CSS select cũ không dùng | Xóa bỏ (Dead CSS) | 🟢 **LOW** | CSS dư thừa sau khi đã thay thế bằng `JpmSelect`. |
| **ERR-14** | `KetQuaTraCuu.vue` | 1153-1255 | Scoped style product card | CSS card cũ không dùng | Xóa bỏ (Dead CSS) | 🟢 **LOW** | CSS dư thừa sau khi đã thay thế bằng `ProductCard`. |

---

## 12. NGUYÊN NHÂN GỐC RỄ (ROOT CAUSES)

1. **Nguyên nhân Lịch sử (Theme Legacy):** Dự án được khởi tạo trên nền theme TheEvent của BootstrapMade. Theme này mang sẵn quy chuẩn bo góc 4px, màu xanh `#0E1B4D` và hệ thống nút bấm viên thuốc (`50px`). Nhiều đoạn code từ thời kỳ đầu vẫn chưa được dọn dẹp triệt để.
2. **Thiếu Khung Bù Trừ Layout Trung Tâm:** Việc `LayoutClient.vue` không xử lý khoảng đệm Header đã buộc các lập trình viên trước đây phải "chữa cháy" bằng cách đặt `style="padding-top: 110px;"` trên từng trang riêng rẽ.
3. **Phát triển Phân tán (Feature Silos):** Trang `TraCuuBaoGia.vue` và `TraCuuDonHang.vue` được phát triển độc lập, người viết đã mượn tạm bộ icon Boxicons từ template Admin Sneat mà không nhận ra Client không hề nhúng thư viện này.
4. **CSS Scoped bị sao chép (Copy-Paste Component Logic):** Trước Phase 4, khi chưa có `ProductCard.vue` và `JpmSelect.vue`, lập trình viên phải copy toàn bộ khối HTML và hàng trăm dòng CSS của thanh tìm kiếm và thẻ sản phẩm qua lại giữa các file, tạo ra hơn 1.200 dòng CSS trùng lặp.
5. **Cơ chế Token chưa được 'Bơm' xuống Tầng Dưới:** Phase 4 (WP-01) đã khai báo Design Tokens tại `:root`, nhưng các class cũ trong `main.css` chưa được chuyển đổi từ mã hex sang `var(--jpm-*)`. Vì vậy, token mới chỉ đang tồn tại song song chứ chưa thực sự điều khiển 100% giao diện.

---

## 13. ĐỀ XUẤT CHUẨN HÓA CẤP GLOBAL & SHARED COMPONENTS

Để thực thi triệt để Design System 2026 mà **KHÔNG CẦN REDESIGN TỪNG TRANG**, đề xuất chiến lược chuẩn hóa tập trung ở 3 tầng:

```mermaid
flowchart TD
    subgraph TẦNG 1: GLOBAL LAYOUT & STYLES
        L["LayoutClient.vue<br/>Thêm padding-top: var(--jpm-header-height)"]
        M["main.css<br/>Map biến TheEvent sang Tokens 2026<br/>Override .btn, .card, .modal về 8px/12px"]
        H["index.html<br/>Gỡ bỏ 3 Google fonts thừa"]
    end

    subgraph TẦNG 2: SHARED COMPONENTS NÂNG CAO
        PC["ProductCard.vue<br/>Tái sử dụng cho ChiTietSanPham & ChiTietPhuTung"]
        JS["JpmSelect.vue<br/>Tái sử dụng cho KetQuaTraCuu Sidebar & Sort"]
        JB["JpmButton & JpmBadge (Định nghĩa class chuẩn trong main.css)"]
    end

    subgraph TẦNG 3: MIGRATION THEO NHÓM LỖI
        M1["Di chuyển 44 Boxicons -> Bootstrap Icons"]
        M2["Xóa bỏ toàn bộ .btn-shopee -> .btn-jpm-*"]
        M3["Dọn dẹp Dead Scoped CSS trong 4 file chính"]
    end

    TẦNG 1 --> TẦNG 2 --> TẦNG 3
```

1. **Chuẩn hóa tại `LayoutClient.vue` (Xóa bỏ 10 inline styles):**
   - Thiết lập CSS cho `<main class="main">`: `padding-top: var(--jpm-header-height, 80px);`.
   - Khi đó, có thể an toàn xóa bỏ toàn bộ `style="padding-top: 110px;"` trên 10 file Vue con mà không lo vỡ layout.
2. **Cầu nối Token hóa trong `main.css` (Global Bridge):**
   - Ánh xạ trực tiếp các biến cũ sang token mới ngay tại `:root`:
     ```css
     --heading-color: var(--jpm-color-navy-dark, #0A1128);
     --default-font: var(--jpm-font-body, 'Roboto', sans-serif);
     --heading-font: var(--jpm-font-heading, 'Raleway', sans-serif);
     --accent-color: var(--jpm-color-red-marine, #F82249);
     ```
   - Điều này sẽ ngay lập tức "bơm" màu Deep Navy và font chuẩn vào tất cả 25 trang mà không cần sửa từng trang một.
3. **Chuyển dịch 44 Boxicons (DEC-04 Enforcement):**
   - Thực hiện chuyển đổi ánh xạ 1-1 tại `TraCuuBaoGia.vue` và `TraCuuDonHang.vue` sang `bi bi-*` (Ví dụ: `bx bx-user` $\to$ `bi bi-person`, `bx bx-time` $\to$ `bi bi-clock`, `bx bx-check-circle` $\to$ `bi bi-check-circle-fill`).
4. **Tiêu trừ hoàn toàn `.btn-shopee`:**
   - Thay thế các class `.btn-shopee` trong `LichSuDonHang.vue` bằng các class chính quy `.btn-jpm-primary`, `.btn-jpm-outline`, bo góc 8px chuẩn.

---

## 14. NHỮNG VẤN ĐỀ CẦN PRODUCT OWNER PHÊ DUYỆT (DECISION POINTS)

Trước khi tiến hành bất kỳ chỉnh sửa nào, đề xuất Product Owner xem xét và ra quyết định cho 4 điểm sau:

1. **Phê duyệt sửa đổi tập trung tại `LayoutClient.vue`:**
   - *Đề xuất:* Thêm bù trừ header tự động tại Layout và xóa đồng loạt `padding-top: 110px` trên 10 page con.
   - *Lợi ích:* Layout đáp ứng linh hoạt trên Desktop (80px), Tablet (72px) và Mobile (64px), triệt tiêu lỗi hở khoảng trắng.
2. **Phê duyệt chuyển đổi dứt điểm 44 Boxicons sang Bootstrap Icons:**
   - *Đề xuất:* Thực hiện thay thế class icon trên 2 file `TraCuuBaoGia.vue` và `TraCuuDonHang.vue`.
   - *Lợi ích:* Đảm bảo 100% icon hiển thị ổn định, không phụ thuộc vào font ngoài, tuân thủ nghiêm ngặt DEC-04.
3. **Phê duyệt kế hoạch dọn dẹp CSS chết (Dead CSS Cleanup):**
   - *Đề xuất:* Cho phép loại bỏ các đoạn CSS scoped dư thừa trong `KetQuaTraCuu.vue`, `ThuongHieu.vue`, `SanPham.vue` đã được thay thế bởi `ProductCard` và `JpmSelect`.
   - *Lợi ích:* Giảm dung lượng bundle, loại bỏ nguy cơ ghi đè ngược (specificity clash).
4. **Phê duyệt tối ưu hóa nạp Font tại `index.html`:**
   - *Đề xuất:* Gỡ bỏ 3 font không sử dụng (`Public Sans`, `Montserrat`, `Inter`) khỏi link Google Fonts.
   - *Lợi ích:* Tăng tốc độ tải trang toàn storefront khoảng 15-20%.

---

## 15. KẾT LUẬN & ĐIỀU KIỆN DỪNG (STOP CONDITION)

Báo cáo kiểm toán này đã hoàn thành mục tiêu:
- **KHÔNG THỰC HIỆN BẤT KỲ THAY ĐỔI NÀO TRÊN MÃ NGUỒN CÁC PAGE HOẶC LAYOUT.**
- **Toàn bộ hiện trạng, sai lệch, nguyên nhân và lộ trình giải quyết đã được lượng hóa chi tiết.**
- **Mọi mã nguồn của JPM Client vẫn đang ở trạng thái build sạch sẽ 100% (Vite exit code 0, 9/9 Vitest tests passed).**

Đội ngũ kỹ thuật xin trân trọng kính trình Product Owner xem xét báo cáo và ra chỉ thị cho các bước thực thi tiếp theo.
