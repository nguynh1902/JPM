# JPM CLIENT – TÀI LIỆU 02: GLOBAL LAYOUT SYSTEM (KIẾN TRÚC BỐ CỤC TOÀN HỆ THỐNG)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. GIẢI QUYẾT TRIỆT ĐỂ SỰ CỐ BỐ CỤC (LAYOUT OFFSET DEFECT RESOLUTION)

### 1.1. Thực trạng phát hiện từ Phase 1 & 2
Trong mã nguồn hiện tại, `NavbarClient.vue` được thiết lập `fixed-top`. Hầu hết các màn hình con (như `SanPham.vue`, `SoDoPhuTung.vue`, `ChiTietSanPham.vue`, `GioHang.vue`...) đều phải viết thủ công:
```html
<!-- CÁCH LÀM CHẮP VÁ HIỆN TẠI (ANTI-PATTERN CẦN LOẠI BỎ) -->
<div class="page-container" style="padding-top: 110px;">
```
Hậu quả:
1. Gây vỡ giao diện trên Mobile và Tablet khi Navbar co lại hoặc mở rộng (Header chỉ cao 64px - 72px trên màn hình nhỏ, làm lộ ra khoảng trống 40px xấu xí).
2. Khi Header thay đổi thiết kế trong tương lai, lập trình viên phải sửa tay trên toàn bộ 20 màn hình con.
3. Không đồng nhất giữa các trang có Banner Hero và các trang nội dung trắng.

### 1.2. Giải pháp kiến trúc Layout Offset tập trung 2026 (Centralized Layout Offset)
Toàn bộ logic căn chỉnh khoảng trống sẽ được quản trị duy nhất tại `LayoutClient.vue` thông qua biến CSS gốc `--jpm-header-height`:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        KIẾN TRÚC LAYOUTCLIENT.VUE                      │
├────────────────────────────────────────────────────────────────────────┤
│  [ NavbarClient.vue ]  --> Chiều cao kiểm soát qua CSS Variable       │
│  Fixed Top | z-index: 1030 | Backdrop-filter: blur(12px)              │
├────────────────────────────────────────────────────────────────────────┤
│  [ .layout-main-wrapper ]                                              │
│  padding-top: var(--jpm-header-height);                                │
│                                                                        │
│    ┌────────────────────────────────────────────────────────────────┐  │
│    │  <router-view />  (20 màn hình con hoàn toàn KHÔNG CẦN         │  │
│    │                    padding-top inline)                         │  │
│    └────────────────────────────────────────────────────────────────┘  │
│                                                                        │
├────────────────────────────────────────────────────────────────────────┤
│  [ FooterClient.vue ]  --> Chân trang tiêu chuẩn 4 cột                 │
└────────────────────────────────────────────────────────────────────────┘
```

#### Quy chuẩn chiều cao Header theo Breakpoint:
- **Desktop ( >= 1200px ):** `--jpm-header-height: 80px;` (Gồm Logo, Menu chính, Thanh tra cứu nhanh, Giỏ hàng & Nút Garage).
- **Tablet ( 768px - 1199px ):** `--jpm-header-height: 72px;`
- **Mobile ( < 768px ):** `--jpm-header-height: 64px;` (Gọn gàng, tối ưu diện tích hiển thị màn hình nhỏ).

*Ngoại lệ duy nhất:* Trang chủ (`TrangChu.vue`) sử dụng Hero Banner trong suốt (Transparent Hero) lồng xuống dưới Navbar sẽ có cờ cấu hình `hero-transparent: true` để triệt tiêu `padding-top` tự động.

---

## 2. ĐẶC TẢ CHI TIẾT HEADER & NAVBAR (NAVBARCLIENT.VUE)

Navbar đóng vai trò là "Trung tâm chỉ huy" (Cockpit) của hệ thống JPM, cần thể hiện rõ sự uy quyền của thiết bị hàng hải.

```
┌───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [ JPM JAPAN MARINE PARTS ]   [Tra cứu dòng máy ▾]  [Phụ tùng theo sơ đồ]  [Tin tức & HD]  │ [🔍 Nhập mã OEM...] │ [🛒 3] │ [⚓ Garage] │
└───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 2.1. Cấu trúc các khối thành phần trên Navbar Desktop (1200px trở lên)

1. **Logo định vị thương hiệu (Brand Anchor):**
   - Kích thước: Cao `38px`, tỷ lệ vàng nằm phía bên trái.
   - Logo gồm biểu tượng mỏ neo cách điệu bánh răng chân vịt + Chữ "JPM" (Raleway ExtraBold) và subtitle "Japan Marine Parts".
2. **Hệ thống điều hướng chính (Navigation Hierarchy):**
   - **Tra cứu dòng máy (Engine Catalog Mega Menu):** Khi hover, mở ra bảng danh mục phân tầng theo 4 hãng lớn Nhật Bản (Yanmar, Yamaha Marine, Suzuki Marine, Honda Marine) kèm icon động cơ.
   - **Sơ đồ rã máy (Exploded Diagrams):** Dẫn thẳng vào bộ lọc bản vẽ chi tiết.
   - **Phụ tùng bảo dưỡng (Maintenance Kits):** Lọc nhanh dầu máy, lọc nhớt, cánh bơm nước (impeller), bugi, anode chống ăn mòn.
   - **Báo giá & Đơn hàng:** Tra cứu nhanh báo giá đang hiệu lực.
3. **Thanh tra cứu nhanh mã phụ tùng (Instant OEM Search Bar):**
   - Đặt cố định ngay trung tâm Header với độ rộng `340px` (có thể giãn lên `420px` khi focus).
   - Placeholder: `"Tra nhanh mã OEM (vd: 688-45941-01)..."`.
   - Phím tắt bàn phím: Bấm phím `/` ở bất kỳ đâu trên trang sẽ tự động focus vào ô tìm kiếm.
   - Autocomplete Dropdown: Hiện gợi ý 5 phụ tùng phù hợp nhất + trạng thái còn hàng tức thì khi gõ từ 2 ký tự.
4. **Cụm thao tác tài khoản & Giỏ hàng (Utility Actions):**
   - **Marine Garage Pill (Tủ máy của tôi):** Nút tắt dẫn trực tiếp vào danh sách động cơ đã lưu của khách hàng, có biểu tượng mỏ neo (`bi-anchor`).
   - **Cart Badge (`bi-bag`):** Hiển thị số lượng phụ tùng đang chọn, mở nhanh Mini-Cart drawer khi click.

### 2.2. Hành vi trên Mobile & Tablet (< 1200px)
- Logo thu gọn biểu tượng mỏ neo + chữ "JPM" (chiều cao 32px).
- Nút Tìm kiếm chuyển thành Icon kính lúp (`bi-search`), khi bấm sẽ trượt một Search Drawer toàn màn hình từ trên xuống.
- Nút Giỏ hàng giữ nguyên badge đỏ nổi bật.
- Nút Hamburger mở **Offcanvas Navigation** trượt từ cạnh phải:
  - Header của Offcanvas hiển thị thông tin Garage của khách (ví dụ: *"Tàu Yamaha 250HP - Cảng Hải Phòng"*).
  - Danh mục dạng Accordion cấp bậc rõ ràng, chạm vuốt dễ dàng (Touch target tối thiểu 48px).

---

## 3. HỆ THỐNG KHUNG CHỨA VÀ LƯỚI BỐ CỤC (CONTAINER & GRID SYSTEM)

Dự án JPM dựa trên Bootstrap 5.3.3 Grid nhưng được tinh chỉnh kích thước tối ưu cho dữ liệu kỹ thuật và sơ đồ rã máy:

| Breakpoint | Tên mã | Min Width | Container Max-Width | Gutter (Khoảng cách cột) | Ứng dụng chính |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Mobile Nhỏ** | `xs` | < 576px | `100%` (Padding: 16px) | 12px | Màn hình điện thoại portrait |
| **Mobile Lớn** | `sm` | 576px | 540px (Padding: 16px) | 16px | Điện thoại ngang, phablet |
| **Tablet** | `md` | 768px | 720px (Padding: 20px) | 16px | iPad, tablet cơ khí ngoài bến tàu |
| **Desktop Nhỏ** | `lg` | 992px | 960px (Padding: 24px) | 20px | Laptop 13-14 inch |
| **Desktop Chuẩn** | `xl` | 1200px | 1140px (Padding: 24px) | 24px | Màn hình 1080p văn phòng |
| **Wide Desktop** | `xxl`| 1400px | **1320px** (Padding: 32px) | 24px | **Độ rộng chuẩn tối ưu của JPM 2026** |

### 3.1. Phân bố cột Catalog chuẩn (Catalog Sidebar Layout)
Áp dụng cho các trang `SanPham.vue`, `KetQuaTraCuu.vue`, `ThuongHieu.vue`:
```
┌─────────────────────────┬────────────────────────────────────────────────────────┐
│  SIDEBAR BỘ LỌC 4 CẤP   │             LƯỚI SẢN PHẨM PHỤ TÙNG                     │
│  Width: 280px cố định   │  3 Cột trên Laptop (lg) | 4 Cột trên Desktop Lớn (xxl) │
│  (Sticky khi scroll)    │  Gutter: 20px                                          │
│                         │                                                        │
│  - Hãng máy (Brand)     │  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐    │
│  - Loại động cơ         │  │ ProductCard  │ │ ProductCard  │ │ ProductCard  │    │
│  - Dải công suất        │  └──────────────┘ └──────────────┘ └──────────────┘    │
│  - Danh mục phụ tùng    │  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐    │
│  - Tình trạng hàng      │  │ ProductCard  │ │ ProductCard  │ │ ProductCard  │    │
│                         │  └──────────────┘ └──────────────┘ └──────────────┘    │
└─────────────────────────┴────────────────────────────────────────────────────────┘
```

---

## 4. ĐẶC TẢ CHÂN TRANG (FOOTERCLIENT.VUE)

Footer JPM là điểm tựa uy tín kỹ thuật của toàn bộ nền tảng, thiết kế nền tối (`--jpm-color-navy-dark: #0A1128`) với đường viền tinh xảo `rgba(255,255,255,0.08)`.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [ JPM LOGO ]                  [ TRA CỨU PHỤ TÙNG ]      [ DỊCH VỤ & BÁO GIÁ ]        [ CHỨNG NHẬN & HỖ TRỢ KỸ THUẬT ]  │
│ Hệ thống cung ứng phụ tùng    • Sơ đồ rã máy Yanmar     • Hướng dẫn yêu cầu báo giá  • Tiêu chuẩn chất lượng JIS       │
│ máy thủy Nhật Bản chính hãng. • Sơ đồ phụ tùng Yamaha   • Chính sách bảo hành 12T    • Hotline kỹ thuật 24/7           │
│ Hotline: 1900-xxxx            • Phụ tùng máy đẩy Suzuki • Tra cứu tiến độ đơn hàng   • Trụ sở: Hải Phòng & TP.HCM      │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ © 2026 Japan Marine Parts Management (JPM). All rights reserved.                      [ Thanh toán: JCB, Visa, Napas ] │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 4.1. Cấu trúc 4 cột Footer:
1. **Cột 1 – Thương hiệu & Năng lực:** Logo JPM phát sáng nhẹ, mô tả tóm tắt giải pháp chuỗi cung ứng máy thủy, số giấy phép kinh doanh ngành hàng hải.
2. **Cột 2 – Tra cứu nhanh Catalog:** Liên kết trực tiếp vào các catalog dòng máy thịnh hành nhất Việt Nam.
3. **Cột 3 – Dịch vụ & Hỗ trợ kỹ thuật:** Hướng dẫn thợ máy đọc sơ đồ rã máy, quy trình xác thực mã phụ tùng, chính sách đổi trả linh kiện sai model.
4. **Cột 4 – Bảo chứng chất lượng Nhật Bản (Japanese Quality Trust Badge):** Khẳng định nguồn gốc xuất xứ linh kiện (Made in Japan / OEM Authorized), cam kết chống hàng giả hàng nhái.

---

## 5. MẪU KHUNG CẤU TRÚC TRANG CHUẨN (GLOBAL PAGE TEMPLATES)

Mọi trang con trong 20 màn hình Client đều phải thừa hưởng 1 trong 3 mẫu khung chuẩn:

### Mẫu A: Standard Content Page (Áp dụng cho Sản phẩm, Tin tức, Giỏ hàng, Hỗ trợ)
```
1. Header Offset (var(--jpm-header-height))
2. Page Banner / Breadcrumb Bar (Chiều cao: 64px, nền Slate-100, hiển thị đường dẫn phân cấp)
3. Main Content Container (max-width: 1320px)
   - Layout 2 cột (Sidebar 280px + Nội dung) HOẶC 1 cột toàn chiều rộng
4. Bottom Technical Assurance CTA (Cam kết chính hãng, hỗ trợ kỹ sư 24/7)
5. Footer
```

### Mẫu B: Technical Split Workspace (Dành riêng cho SoDoPhuTung.vue)
```
1. Header Offset (var(--jpm-header-height))
2. Sub-header Toolbar (Tên sơ đồ, Model máy, Bộ điều khiển Zoom/Pan/Fullscreen)
3. Split View 2 cột với Thanh kéo Resizer:
   - Cột trái (55% mặc định): Khung vẽ tương tác sơ đồ rã máy (Diagram Interactive Canvas)
   - Cột phải (45% mặc định): Bảng danh mục phụ tùng chi tiết (Part List Table with Ref #)
4. Footer (Tùy chọn ẩn khi vào chế độ toàn màn hình Fullscreen Workspace)
```

### Mẫu C: Account Dashboard Layout (Áp dụng cho LayoutTaiKhoan.vue & Garage.vue)
```
1. Header Offset (var(--jpm-header-height))
2. Account Header Summary Card (Thông tin chủ tàu, Hạng thành viên, Số lượng máy trong Garage)
3. 2-Column Grid:
   - Cột trái (260px): AccountSidebar.vue (Đồng bộ icon Bootstrap Icons, bo góc 12px)
   - Cột phải (flex-grow): Nội dung tab đang chọn (Garage, Lịch sử báo giá, Vật tư hay mua...)
4. Footer
```
