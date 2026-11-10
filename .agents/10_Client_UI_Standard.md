# QUY CHUẨN LẬP TRÌNH GIAO DIỆN CLIENT & UNIFIED DESIGN SYSTEM 2026
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web Storefront  
**Tài liệu:** `.agents/10_Client_UI_Standard.md`  
**Cấp độ:** Bắt buộc tuân thủ 100% cho mọi lập trình viên và AI Agent khi phân tích, thiết kế hoặc xây dựng giao diện Client.  
**Trạng thái phiên bản:** v2.0-2026 (Đã tích hợp toàn diện 11 tài liệu đặc tả của Phase 3 Unified Design System theo phê duyệt của Product Owner).

---

## 0. CHỈ THỊ ĐIỀU HÀNH DÀNH RIÊNG CHO AI AGENT (AGENT GOVERNANCE DIRECTIVES)

Mọi AI Agent (Antigravity, Claude, Copilot, Cursor...) khi làm việc với phân hệ Client của JPM **BẮT BUỘC** ghi nhớ và tuân thủ các quy tắc sau:

1. **JPM Client đã có Design System 2026 chính thức:** Không suy diễn từ các giao diện Bootstrap thông thường. Mọi thiết kế phải theo định vị **"MODERN LUXURY MARINE TECHNOLOGY E-COMMERCE"** ("Tối giản đường nét – Tối đa khoảng thở – Chính xác kỹ thuật – Đậm chất biển sâu").
2. **DEC-01 (Border Radius chuẩn hóa 4 cấp):**
   - Card sản phẩm, Khung Panel, Modal: **`12px`** (`--jpm-radius-card`).
   - Button, Input, Select, Dropdown menu: **`8px`** (`--jpm-radius-control`).
   - Tag, Badge trạng thái, Chip lọc: **`6px`** (`--jpm-radius-badge`).
   - Avatar người dùng, Nút icon tròn: **`50%`** (`--jpm-radius-circle`).
   *(Triệt tiêu hoàn toàn quy chuẩn cũ 4px và các giá trị tự phát khác).*
3. **NGUYÊN TẮC KẾ THỪA TỰ ĐỘNG TỪ TEMPLATE (AUTOMATIC TEMPLATE INHERITANCE - ZERO REDUNDANT SCOPED CSS):**
   - Toàn bộ quy chuẩn về Bo góc (`Card 12px`, `Control 8px`, `Badge 6px`), Bảng màu (`Deep Navy`, `Marine Red`), Bóng đổ và Typography đã được đóng gói tập trung tại `FE_JPM/src/assets/client/css/main.css`.
   - Các Page chỉ cần sử dụng đúng class ngữ nghĩa chuẩn Bootstrap 5 (`.card`, `.btn`, `.form-control`, `.form-select`, `.badge`) là **TỰ ĐỘNG KẾ THỪA 100%**, KHÔNG CẦN và TUYỆT ĐỐI KHÔNG đi sửa từng dòng từng phần trong `<style scoped>` của từng Page code.
   - Tránh tuyệt đối việc tự đặt các class con rồi gõ cứng `border-radius: 12px !important` hay `border-radius: 8px !important` trong từng file `.vue`.
4. **DEC-02 (Interactive Hotspots trên Exploded Diagram):**
   - Hỗ trợ tương tác hai chiều (Two-way reactive bridge) giữa Ghim Hotspot trên bản vẽ rã máy và Dòng phụ tùng trên bảng danh mục.
   - Sử dụng tọa độ phần trăm bất biến (`x`, `y`, `width`, `height`) từ cơ sở dữ liệu `chi_tiet_so_dos`.
5. **DEC-03 (Reusable Shared Components):**
   - `ProductCard.vue` và `JpmSelect.vue` là 2 component dùng chung cốt lõi được định hướng xây dựng.
   - Khắc phục triệt để lỗi khuyết nút giỏ hàng khi sản phẩm cần báo giá: Chân thẻ luôn cân đối 100% với nhãn giá và nút hành động tương ứng (`Thêm giỏ` hoặc `Yêu cầu báo giá`).
6. **DEC-04 (Hệ thống Biểu tượng 100% Bootstrap Icons):**
   - Phân hệ Client chỉ sử dụng duy nhất Bootstrap Icons (`bi bi-*`). Tuyệt đối không dùng Boxicons (`bx bx-*`) trên Client.
7. **HỢP NHẤT TOÀN DIỆN THIẾT KẾ PHASE 3 VÀO TÀI LIỆU .AGENTS (UNIFIED SOURCE OF TRUTH):**
   - Toàn bộ 11 tài liệu đặc tả của Phase 3 Unified Design System (Bao gồm Design Foundation, Layout, Component Library, Interaction, Hotspots, Page Blueprint, Responsive, Accessibility, Technical Implementation, Decision Log) đã được **XÁC NHẬP VÀ ĐÓNG GÓI TRỌN VẸN 100%** vào thư mục `.agents/` (tập trung tại `10_Client_UI_Standard.md` và `11_Huong_Dan_Tuy_Bien_Giao_Dien_Va_CSS_Client.md`).
   - Thư mục riêng lẻ `Phase_3_Unified_Design_System` được dọn dẹp và xóa bỏ hoàn toàn nhằm tinh gọn dự án, triệt tiêu phân mảnh tài liệu. Mọi Agent và Kỹ sư chỉ tra cứu duy nhất tại `.agents/`.

8. **CHỈ THỊ THỰC CHIẾN TINH GỌN TỪ PRODUCT OWNER (PO LEAN UX DIRECTIVE 2026):**
   - **Tên màn hình & Tiêu đề thẻ dứt khoát, tự nhiên:**
     * `Garage của tôi` (thay cho mọi cách gọi rườm rà như `Hạm đội Garage`).
     * `Thông tin cá nhân` (thay cho `Thông Tin Cá Nhân & Doanh Nghiệp`).
     * `Địa chỉ giao hàng` (thay cho `Sổ Địa Chỉ Giao Hàng`).
     * `Vật tư thường mua` (thay cho `Vật Tư Thường Mua & Định Kỳ`).
     * `Danh sách động cơ` (thay cho `Danh Sách Động Cơ Trong Garage`).
   - **Triệt tiêu toàn bộ dòng phụ đề đếm số lượng thừa (No Redundant Counters):**
     * Tuyệt đối không chèn các dòng text nhỏ đếm số lượng dưới tiêu đề card (như: "0 động cơ đang quản lý trong garage", "Quản lý và đặt nhanh các vật tư tiêu hao (0 mục)"). Giao diện phải sạch sẽ, tinh giản.
   - **Hiển thị vai trò tài khoản dạng văn bản thuần (Plain Text Role):**
     * Dưới avatar người dùng, chỉ hiển thị chữ trơn (plain text) đại diện vai trò: `Admin`, `Nhân viên`, hoặc `Khách hàng`.
     * Font chữ: 13.5px, màu slate trung tính `#64748b`, font-weight 500.
     * TUYỆT ĐỐI KHÔNG đóng khung vai trò vào card/badge nền đỏ, nền hồng, không có viền bao và không gắn icon khiên/khóa bảo vệ làm rối mắt.
   - **Phân định màu sắc & kích thước nút bấm hành động (Action Button Hierarchy & Standardization):**
     * **Nút Thêm mới đối tượng (Thêm địa chỉ mới, Thêm sản phẩm mới):** Sử dụng màu **Xanh Primary chuẩn Bootstrap 5 (`#0d6efd`)** thông qua class `.btn-blue-primary` (bo góc 8px). Đã đưa vào CSS template dùng chung (`main.css`), tuyệt đối không khai báo cục bộ trong `<style scoped>`.
     * **Nút Hành động Chính / Chỉnh sửa / Mua hàng / Lưu:** Sử dụng màu **Đỏ Marine (`#F82249` / `.btn-danger`)**.
     * **Quy chuẩn kích thước nút bấm trên Card Header (`.btn-action-header`):**
       - Khóa cứng: `height: 38px !important; min-height: 38px !important; max-height: 38px !important; line-height: 36px !important;`
       - Padding: `0 16px !important;`
       - Font size: `13.5px !important; font-weight: 600;`
       - Border-radius: `var(--jpm-radius-control, 8px) !important;`
       - Display: `inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important;`
       - **Đảm bảo:** Cả thẻ `<button>` và thẻ `<router-link>` (thẻ `<a>`) trên mọi trang đều có kích thước điểm ảnh chính xác 100% như nhau, triệt tiêu hiện tượng lệch 38px vs 33px.
     * **Bộ ba lớp Component Card chuẩn toàn Client (`main.css`):**
       - `.jpm-card`: Bo góc 12px, viền `#e2e8f0`, đổ bóng mềm `0 4px 16px rgba(10, 17, 40, 0.04)`.
       - `.jpm-card-header`: Padding `16px 24px`, chiều cao khóa `min-height: 70px`, viền đáy `1px solid #e2e8f0`.
       - `.jpm-card-title`: Font Raleway 700, 18px (`1.125rem`), màu Deep Navy `#0A1128`.
   - **Xóa bỏ lỗi chồng khoảng đệm (Section Padding Stacking Elimination):**
     * Trong các khung trang tài khoản (LayoutTaiKhoan.vue), thẻ `<section>` bắt buộc mang `class="py-0" style="background: transparent;"` để triệt tiêu padding 60px 0 và nền trắng mặc định của template TheEvent, loại bỏ khoảng cách trống 170px và dải trắng vô duyên. Khoảng đệm header chuẩn hóa là `padding-top: 92px` trên thẻ `<main>`.

---

## 1. TRIẾT LÝ KIẾN TRÚC GIAO DIỆN CLIENT (ARCHITECTURE PHILOSOPHY)

Toàn bộ giao diện Client được xây dựng theo mô hình **3 Tầng Thống Nhất**:

```
┌────────────────────────────────────────────────────────────────────────┐
│ TẦNG 1: THIẾT KẾ ĐỊNH DANH (DESIGN SYSTEM & TOKENS 2026)               │
│ - Tông màu biển sâu Deep Navy (#0A1128), Đỏ Marine (#F82249), Slate.   │
│ - Typography: Raleway (Heading/Brand) + Roboto (Body, Tabular Numbers).│
│ - Hệ bo góc chuẩn hóa 4 cấp (12px, 8px, 6px, 50%).                     │
├────────────────────────────────────────────────────────────────────────┤
│ TẦNG 2: CÚ PHÁP LẬP TRÌNH (100% BOOTSTRAP 5 CHUẨN NGỮ NGHĨA)           │
│ - Mọi cấu trúc trang, lưới phân cột dùng class chính quy Bootstrap 5:  │
│   (container, row, col-*, card, table, form-control, d-flex...).       │
│ - HTML sạch sẽ, gãy gọn, chuẩn accessibility WCAG 2.1 AA.              │
├────────────────────────────────────────────────────────────────────────┤
│ TẦNG 3: CÁCH LY PHÂN HỆ QUA LAYOUTCLIENT.VUE                           │
│ - Phân định rõ ràng: Client dùng TheEvent assets, Admin dùng Sneat.    │
│ - Quản lý khoảng đệm Header Offset tập trung qua --jpm-header-height.   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. HỆ THỐNG MÀU SẮC & DESIGN TOKENS CHUẨN HÓA

### 2.1. Bảng màu thương hiệu (Brand Identity)
- **Primary Brand Color (`--jpm-color-navy-dark`):** `#0A1128` – Đại diện cho biển sâu Nhật Bản, uy tín kỹ thuật, dùng cho Header, Navbar, Footer, Section Dark, Heading H1/H2. Độ tương phản: 14.8:1 (WCAG AAA).
- **Secondary Brand Color (`--jpm-color-navy-subtle`):** `#101E42` – Tầng biển nổi, dùng cho Dropdown nền tối, Card tối, Active menu.
- **Accent / Primary CTA (`--jpm-color-red-marine`):** `#F82249` – Màu đỏ cứu sinh hàng hải & nhiệt huyết công nghệ. Dùng cho nút Thêm vào giỏ, nút Tìm kiếm chính, Hotspot Active pin, Badge Sale.
- **Accent Hover (`--jpm-color-red-hover`):** `#D9163A` – Đỏ trầm khi hover nút chính.
- **Technical Action / Info (`--jpm-color-blue-electric`):** `#0284C7` – Xanh công nghệ hàng hải, dùng cho link mã OEM, nút xem sơ đồ rã máy, Breadcrumb active.
- **Luxury Marine Metallic (`--jpm-color-gold-marine`):** `#D97706` – Ánh đồng chân vịt hàng hải, dùng cho huy hiệu đại lý chính thức và hạng Garage VIP.

### 2.2. Thang sắc độ trung tính (Neutral Slate Scale)
- `--jpm-neutral-900`: `#0F172A` (Deep Charcoal - Tiêu đề, Tên phụ tùng, Giá in đậm).
- `--jpm-neutral-800`: `#1E293B` (Slate Bold - Nội dung quan trọng, Nhãn form).
- `--jpm-neutral-700`: `#334155` (Slate Standard - Thân bài viết, Mô tả kỹ thuật).
- `--jpm-neutral-500`: `#64748B` (Slate Muted - Metadata, Ngày đăng, Mã phụ tùng phụ).
- `--jpm-neutral-400`: `#94A3B8` (Slate Light - Placeholder ô nhập liệu).
- `--jpm-neutral-300`: `#CBD5E1` (Border Focus/Active, Divider bảng phụ tùng).
- `--jpm-neutral-200`: `#E2E8F0` (Border Subtle - Viền Card chuẩn, đường chia bảng).
- `--jpm-neutral-100`: `#F1F5F9` (Neutral Surface - Nền ô tìm kiếm, nền bảng zebra).
- `--jpm-neutral-50`:  `#F8FAFC` (Page Background - Nền toàn trang Client 2026).
- `--jpm-neutral-0`:   `#FFFFFF` (Card Surface - Mặt nổi của Card sản phẩm, Modal).

### 2.3. Hệ màu trạng thái nghiệp vụ phụ tùng
- **Còn hàng (In Stock):** Nền `#DCFCE7`, Chữ `#15803D` (Huy hiệu Sẵn sàng giao ngay).
- **Chờ báo giá (Quote Required):** Nền `#DBEAFE`, Chữ `#1D4ED8` (Giá biến động theo tỷ giá JPY/USD).
- **Hàng đặt trước (Pre-order):** Nền `#FEF3C7`, Chữ `#B45309` (Đặt từ xưởng Nhật Bản).
- **Mã thay thế (Superseded):** Nền `#EDE9FE`, Chữ `#6D28D9` (Cảnh báo nâng cấp mã OEM).
- **Tạm hết hàng (Out of Stock):** Nền `#FEE2E2`, Chữ `#B91C1C`.

---

## 3. HỆ THỐNG TYPOGRAPHY (ĐỒ HỌA CHỮ CHUẨN HÓA)

- **Font Tiêu đề & Thương hiệu:** `Raleway` (Google Font - Geometric Sans).
- **Font Thân trang, Biểu mẫu & Bảng:** `Roboto` (Google Font - Legibility tối ưu).
- **Font Mã OEM & Số liệu kỹ thuật:** `Roboto Mono` kết hợp `font-variant-numeric: tabular-nums;` để triệt tiêu hiện tượng xê dịch pixel chiều ngang khi số nhảy.

### Thang phân cấp chữ (Type Scale):
- **Display 1:** 44px (Desktop) / 32px (Mobile) - Weight 800 - Line height 1.15.
- **H1:** 32px (Desktop) / 24px (Mobile) - Weight 700 - Line height 1.25.
- **H2:** 24px (Desktop) / 20px (Mobile) - Weight 700 - Line height 1.30.
- **H3:** 20px (Desktop) / 18px (Mobile) - Weight 600 - Line height 1.35.
- **H4:** 16px (Desktop) / 15px (Mobile) - Weight 600 - Line height 1.40.
- **Body Standard:** 14px - Weight 400 - Line height 1.50.
- **Body Small:** 13px (Desktop) / 12px (Mobile) - Weight 400 - Line height 1.45.
- **Technical OEM:** 13px (Roboto Mono) - Weight 600 - Letter spacing +0.05em.
- **Price Hero:** 22px - Weight 700. Price Card: 16px - Weight 700.

---

## 4. HỆ THỐNG BO GÓC, ĐỔ BÓNG & KHOẢNG CÁCH (PO DECISION 01)

### 4.1. Bảng quy chuẩn Bo góc (Border Radius):
```css
--jpm-radius-card: 12px;     /* Card sản phẩm, Khung Panel, Modal, Khung sơ đồ */
--jpm-radius-control: 8px;   /* Button, Input, Select (JpmSelect), Dropdown, Tooltip */
--jpm-radius-badge: 6px;     /* Tag lọc, Badge trạng thái, Chip, Mã OEM Pill, Hotspot pin */
--jpm-radius-circle: 50%;    /* Avatar, Nút icon tròn, Đèn báo trạng thái */
```

### 4.2. Hệ thống Đổ bóng xanh mỏng (Elevations):
- `--jpm-shadow-sm`: `0 1px 3px rgba(10, 17, 40, 0.05), 0 1px 2px rgba(10, 17, 40, 0.03)` (Card tĩnh mặc định).
- `--jpm-shadow-md`: `0 4px 12px -2px rgba(10, 17, 40, 0.08), 0 2px 6px -2px rgba(10, 17, 40, 0.04)` (Card hover bay nổi 4px).
- `--jpm-shadow-lg`: `0 12px 24px -4px rgba(10, 17, 40, 0.12), 0 4px 10px -2px rgba(10, 17, 40, 0.06)` (Dropdown mở, Tooltip).
- `--jpm-shadow-xl`: `0 24px 48px -12px rgba(10, 17, 40, 0.20)` (Modal popup, Drawer).

---

## 5. BỐ CỤC TOÀN HỆ THỐNG & GIẢI QUYẾT LỖI LAYOUT OFFSET

### 5.1. Cơ chế bù trừ Header Offset tập trung (Centralized Layout Offset):
- **Bãi bỏ hoàn toàn anti-pattern:** Tuyệt đối không viết `style="padding-top: 110px;"` trong các file Vue con.
- **Quản trị duy nhất tại `LayoutClient.vue`:**
  - Desktop ( $\ge$ 1200px ): `--jpm-header-height: 80px;`
  - Tablet ( 768px - 1199px ): `--jpm-header-height: 72px;`
  - Mobile ( < 768px ): `--jpm-header-height: 64px;`
  - Toàn bộ nội dung `<router-view />` được bọc trong container có `padding-top: var(--jpm-header-height);`.

### 5.2. Khung chứa (Container System):
- Độ rộng tối ưu hóa 2026: **`max-width: 1320px`** (`container-xxl`).
- Lưới sản phẩm chuẩn Catalog: 4 Cột trên Desktop lớn, 3 Cột trên Laptop, 2 Cột trên Tablet/Mobile landscape, 1-2 Cột trên Mobile dọc.

---

## 6. ĐẶC TẢ THÀNH PHẦN DÙNG CHUNG CỐT LÕI (PO DECISION 03)

### 6.1. Component `ProductCard.vue`
- **Mục tiêu:** Thống nhất hiển thị phụ tùng trên toàn bộ 20 màn hình, xóa bỏ việc viết lặp HTML thẻ sản phẩm.
- **Giải quyết lỗi bất đối xứng chân thẻ khi báo giá:**
  - Hàng có giá: Giá niêm yết bên trái + Nút `Thêm giỏ` (`bi-cart-plus`) bên phải.
  - Hàng cần báo giá: Nhãn `Giá: Liên hệ` bên trái + Nút `Yêu cầu báo giá` (`bi-file-earmark-text`) có cùng kích thước bên phải.
  - Chân thẻ luôn luôn đối xứng và thẳng hàng 100% trên toàn bộ lưới Grid.
- **API Props đề xuất:** `id`, `tenPhuTung`, `maOem`, `thuongHieu`, `hinhAnhUrl`, `giaBan`, `isGiaLienHe`, `tonKho`, `trangThaiHang`, `isSuperseded`, `maMoiNhat`.
- **Emits đề xuất:** `@add-to-cart`, `@request-quote`, `@quick-view`.

### 6.2. Component `JpmSelect.vue`
- **Mục tiêu:** Dropdown kỹ thuật chuyên dụng thay thế `<select>` mặc định của trình duyệt, hỗ trợ chuỗi lọc động 4 cấp:
  $$\text{Brand} \longrightarrow \text{Engine Type} \longrightarrow \text{Horsepower/Model} \longrightarrow \text{Engine Model}$$
- **Tính năng bắt buộc:** Tìm kiếm trong menu (`searchable`), Xóa chọn (`clearable`), Tải động (`loading`), Trạng thái khóa (`disabled` khi cấp cha chưa chọn), Điều hướng bàn phím đầy đủ (`Tab`, `ArrowDown`, `ArrowUp`, `Enter`, `Escape`).

---

## 7. ĐẶC TẢ BẢN VẼ RÃ MÁY & INTERACTIVE HOTSPOTS (PO DECISION 02)

Tại màn hình `SoDoPhuTung.vue`:
1. **Ánh xạ tọa độ phần trăm:** Dữ liệu tọa độ từ bảng `chi_tiet_so_dos` (`x`, `y`, `width`, `height`) được đặt trực tiếp dạng CSS:
   `left: x%; top: y%; transform: translate(-50%, -50%);`
   Cả ảnh bản vẽ và các Hotspot Pin cùng nằm chung trong Transform Stage Container để khi Pan và Zoom không bao giờ bị lệch vị trí.
2. **Kênh đồng bộ hai chiều (Two-way Reactive Bridge):**
   - Click Hotspot trên bản vẽ $\to$ Kích hoạt trạng thái Selected (đỏ Marine, sóng radar) $\to$ Tự động cuộn mượt dòng tương ứng trên bảng danh mục (`scrollIntoView({ behavior: 'smooth', block: 'nearest' })`).
   - Click dòng trên bảng $\to$ Kích hoạt Hotspot tương ứng trên bản vẽ $\to$ Phóng to Pin và di chuyển bản vẽ đưa Pin vào trung tâm.
3. **Thích ứng trên Mobile:** Chuyển sang mô hình Tabbed Workspace (Tab Bản vẽ Pinch-Zoom + Tab Danh mục phụ tùng) kết hợp Bottom Sheet nổi tóm tắt linh kiện khi chạm ngón tay.

---

## 8. QUY CHUẨN BIỂU TƯỢNG (PO DECISION 04)

- Phân hệ Client sử dụng **100% Bootstrap Icons (`bi bi-*`)**.
- Tuyệt đối cấm sử dụng Boxicons (`bx bx-*`) trên Client.
- Quy chuẩn kích thước icon: 14px (Badge/Tag), 16px (Nút nhỏ, Bảng), 18px (Nút chuẩn, Menu), 24px (Khối tiêu đề).
- Khoảng cách icon và văn bản: Luôn kèm class `me-1` hoặc `me-2` (4px - 8px), không để icon dính sát chữ.

---

## 9. QUY CHUẨN PHÂN ĐỊNH CSS TOÀN CỤC VS CỤC BỘ

- **`main.css` (CSS dùng chung toàn Client):**
  - Chỉ chứa: Design tokens `:root`, tùy biến các class chính quy Bootstrap 5 (`.btn-primary`, `.btn-danger`, `.badge`, `.card`), utility dùng chung (`.text-truncate-2`), class component nền tảng (`.product-card`, `.btn-cart-action`).
  - CẤM: Đưa class đặc thù của 1 trang, số dòng line-clamp cục bộ, hay margin/padding riêng của 1 section vào `main.css`.
- **`<style scoped>` trong Component Vue:**
  - Dành riêng cho tinh chỉnh cục bộ đặc thù: Số dòng tóm tắt `-webkit-line-clamp`, tỷ lệ khung ảnh riêng của trang, khoảng cách lề cục bộ.
  - **CẤM:** Không viết lặp lại các quy chuẩn toàn cục (`border-radius: 12px`, `border-radius: 8px`, màu nền, font chữ) trong `<style scoped>` vì các component đã tự động kế thừa từ `main.css`. Chỉ can thiệp khi trang đó tồn tại class tự chế cũ bị lệch chuẩn.
- **Inline Style (`style="..."`):**
  - DUY NHẤT được chấp nhận cho thông số vật lý động: Chiều rộng cột bảng (`style="width: 15%;"`), Tọa độ Hotspot động trên sơ đồ (`style="top: 42%; left: 68%;"`).
  - TUYỆT ĐỐI CẤM dùng inline style cho màu sắc, bo góc, đổ bóng hay khoảng cách lề.

---

## 10. DANH MỤC 20 MÀN HÌNH CLIENT & ĐỊNH HƯỚNG BỐ CỤC

1. `TrangChu.vue`: Đại sảnh công nghệ, Hero banner tích hợp bộ lọc 4 cấp `JpmSelect.vue`, Lưới phụ tùng `ProductCard.vue`.
2. `KetQuaTraCuu.vue`: Trung tâm tìm kiếm mã OEM và danh mục, Sidebar bộ lọc dải giá/kho hàng, Lưới sản phẩm `ProductCard.vue`.
3. `ThuongHieu.vue`: Trang các hãng mẹ (Yanmar, Yamaha, Suzuki, Honda), Series Selector, Catalog ủy quyền.
4. `SoDoPhuTung.vue`: Không gian bản vẽ rã máy tương tác hai chiều, Split View Resizer, Hotspots pins tỏa sáng, Toolbar Zoom/Pan.
5. `ChiTietPhuTung.vue`: Hồ sơ kỹ thuật phụ tùng theo mã OEM, Bảng tra cứu model tương thích (Fitment Guide), Link mở sơ đồ rã máy.
6. `SanPham.vue`: Danh mục toàn bộ phụ tùng, Sticky filter sidebar, Lưới sản phẩm chuẩn 4 cột.
7. `ChiTietSanPham.vue`: Chi tiết sản phẩm bán lẻ, Tab thông số kỹ thuật, Băng tải sản phẩm gợi ý bảo dưỡng.
8. `GioHang.vue`: Quản lý giỏ hàng hỗn hợp (Nhóm hàng có sẵn tính tổng cọc + Nhóm hàng báo giá kỹ thuật).
9. `TraCuuBaoGia.vue`: Tra cứu mã báo giá, Đồng hồ đếm ngược 72 giờ, Thay thế toàn bộ icon Boxicons lỗi sang Bootstrap Icons.
10. `TraCuuDonHang.vue`: Theo dõi 5 chặng vận chuyển từ Nhật Bản về cảng Việt Nam, Thay thế toàn bộ Boxicons sang Bootstrap Icons.
11. `TinTuc.vue`: Tạp chí hàng hải, Bento grid tin tức kỹ thuật, Thẻ bài viết bo góc 12px.
12. `ChiTietTinTuc.vue`: Bài viết chuyên sâu, Widget phụ tùng liên quan trực tiếp đến bài viết (`ProductCard.vue`).
13. `HoTro.vue`: Trung tâm trợ giúp, Hotline kỹ sư 24/7, Accordion FAQ câu hỏi thường gặp.
14. `LayoutTaiKhoan.vue`: Khung bố cục cá nhân, `AccountSidebar.vue` đồng bộ 100% Bootstrap Icons và bo góc 12px.
15. `TaiKhoan.vue`: Hồ sơ chủ tàu/thợ máy, Thông tin xuất hóa đơn GTGT công ty hàng hải, Địa chỉ nhận hàng tại bến cảng.
16. `Garage.vue`: Tủ máy Marine Garage cá nhân, Danh sách động cơ sở hữu, Modal thêm máy dùng `JpmSelect.vue`.
17. `GarageChiTietMay.vue`: Chi tiết từng động cơ đã lưu, Sổ bảo dưỡng định kỳ, Sơ đồ rã máy tương thích riêng cho máy.
18. `VatTuThuongMua.vue`: Danh mục vật tư tiêu hao định kỳ (lọc dầu, bugi, anode...), Nút mua lại nguyên gói theo chu kỳ.
19. `LichSuDonHang.vue`: Quản lý toàn bộ đơn hàng đã giao dịch, Tải hóa đơn VAT và phiếu bảo hành chính hãng.
20. `XacThuc.vue`: Cổng đăng nhập/đăng ký thợ máy và chủ tàu, Split screen công nghệ biển sâu sang trọng.

---

## 11. BẢNG TỔNG HỢP TRẠNG THÁI QUYẾT ĐỊNH & ĐIỂM CHỜ PHÊ DUYỆT

| Mã quyết định | Nội dung quyết định | Trạng thái |
| :--- | :--- | :--- |
| **DEC-01** | Border Radius: Card/Modal 12px, Controls 8px, Badges 6px, Circle 50% | **ĐÃ PHÊ DUYỆT (Official)** |
| **DEC-02** | Exploded Diagram Hotspots tương tác 2 chiều (Ghim <--> Bảng) | **ĐÃ PHÊ DUYỆT (Official)** |
| **DEC-03** | Reusable Shared Components: `ProductCard.vue` & `JpmSelect.vue` | **ĐÃ PHÊ DUYỆT (Official)** |
| **DEC-04** | Hệ thống Icon: 100% Bootstrap Icons (`bi bi-*`), loại bỏ Boxicons | **ĐÃ PHÊ DUYỆT (Official)** |
| **DEC-05** | Phân cấp nút Thêm mới (Xanh Bootstrap `#0d6efd`) vs Nút chính (Đỏ Marine `#F82249`), Plain text Role | **ĐÃ PHÊ DUYỆT (Official)** |
| **DEC-PENDING-01** | Cho phép thanh toán cọc hàng có sẵn và gửi báo giá hàng hiếm cùng lúc | *Chờ PO phê duyệt* |
| **DEC-PENDING-02** | Mô hình Tabbed Workspace + Bottom Sheet cho sơ đồ trên Mobile < 400px | *Chờ PO phê duyệt* |
| **DEC-PENDING-03** | Mở rộng phạm vi tìm kiếm nhanh gồm Mã OEM, Tên phụ tùng và Model máy | *Chờ PO phê duyệt* |
| **DEC-PENDING-04** | Hoàn thiện chuẩn Light/Slate ở Phase 4, chuyển Dark Mode sang Phase 5 | *Chờ PO phê duyệt* |

---

*Tài liệu này là SOURCE OF TRUTH tối cao cho toàn bộ hoạt động thiết kế và lập trình giao diện JPM Client.*
