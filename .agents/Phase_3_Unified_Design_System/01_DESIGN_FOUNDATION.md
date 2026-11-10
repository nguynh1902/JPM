# JPM CLIENT – TÀI LIỆU 01: DESIGN FOUNDATION (NỀN TẢNG THIẾT KẾ 2026)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Tác giả:** Đội ngũ Kiến trúc sư Phần mềm & Giám đốc Thiết kế Sản phẩm JPM  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. TRIẾT LÝ THIẾT KẾ VÀ ĐỊNH VỊ THƯƠNG HIỆU

### 1.1. Định vị thương hiệu
> **"MODERN LUXURY MARINE TECHNOLOGY E-COMMERCE"**  
> *(Nền tảng thương mại điện tử công nghệ máy thủy hiện đại, tinh hoa và chuẩn xác kỹ thuật hàng hải Nhật Bản)*

JPM không phải là một website bán phụ tùng thông thường mang giao diện Bootstrap đơn điệu, cũng không phải là một giao diện nhiều chi tiết rối mắt. JPM 2026 hướng tới sự kết hợp độc bản giữa:
- **Chuẩn xác kỹ thuật cơ khí hàng hải:** Thể hiện rõ nét đặc thù máy thủy công nghiệp (Yanmar, Yamaha, Suzuki, Volvo Penta, Mercury, Honda Marine...).
- **Đẳng cấp trải nghiệm du thuyền & tàu dịch vụ (Marine Luxury):** Giao diện có chiều sâu, tĩnh tại, màu sắc đại dương sang trọng, tương tác dứt khoát và mượt mà.
- **Tiêu chuẩn công nghệ số 2026:** Tối ưu hóa hiệu năng, khoảng thở rộng rãi, phân cấp thông tin khoa học (Visual Hierarchy), hỗ trợ thao tác nhanh cho thợ máy trên công trường lẫn chủ tàu trên thiết bị bảng/di động.

### 1.2. Tuyên ngôn nguyên tắc (Design Manifesto)
> **"Tối giản đường nét – Tối đa khoảng thở – Chính xác kỹ thuật – Đậm chất biển sâu."**

```
┌────────────────────────────────────────────────────────────────────────┐
│                        TỨ TRỤ THIẾT KẾ JPM 2026                         │
├───────────────────┬────────────────────┬──────────────────┬────────────┤
│ 1. BIỂN SÂU       │ 2. CHUẨN XÁC       │ 3. TINH GỌN      │ 4. TỐC ĐỘ  │
│ (Deep Marine DNA) │ (Technical Rigor)  │ (Modern Luxury)  │ (Swift UX) │
│ Sắc xanh thẳm     │ Mã OEM, thông số   │ Viền mảnh 1px,   │ Lọc 4 cấp  │
│ #0A1128 kết hợp đỏ│ chân vịt, sơ đồ    │ bo góc chuẩn hóa,│ tức thì, tra│
│ cờ hàng hải       │ rã máy hiển thị    │ triệt tiêu bóng  │ cứu sơ đồ  │
│ #F82249 rực lửa.  │ đơn sắc, rõ ràng.  │ đổ thô ráp cũ kỹ.│ siêu mượt. │
└───────────────────┴────────────────────┴──────────────────┴────────────┘
```

---

## 2. HỆ THỐNG MÀU SẮC (COLOR SYSTEM & DESIGN TOKENS)

Hệ màu JPM 2026 được chuẩn hóa thành 5 nhóm: **Brand Identity**, **Neutral Slate Scale**, **Surface & Background**, **Semantic Status**, và **Interactive States**.

### 2.1. Bảng màu định danh thương hiệu (Brand Palette)

| Tên Token CSS | Mã HEX | Vai trò nghiệp vụ | Ví dụ thành phần sử dụng | Độ tương phản (WCAG AA/AAA) |
| :--- | :--- | :--- | :--- | :--- |
| `--jpm-color-navy-dark` | `#0A1128` | **Primary Brand Color** – Màu biển sâu chủ đạo, uy quyền và vững chắc | Top Header, Footer, Hero Background, Section Dark, Heading cấp 1 | 14.8:1 trên nền trắng (AAA) |
| `--jpm-color-navy-subtle` | `#101E42` | **Secondary Brand Color** – Tầng biển nổi | Dropdown menu nền tối, Card tối, Navbar sticky, Active sidebar item | 12.2:1 trên nền trắng (AAA) |
| `--jpm-color-red-marine` | `#F82249` | **Accent / Brand Primary CTA** – Màu đỏ cứu sinh hàng hải & nhiệt huyết kỹ thuật | Nút Thêm vào giỏ, Nút Tìm kiếm chính, Hotspot Active pin, Badge HOT/Sale | 4.8:1 trên nền trắng (AA Large/Text bold) |
| `--jpm-color-red-hover` | `#D9163A` | **Accent Hover** – Đỏ trầm khi hover/active | Trạng thái hover của nút đỏ, active filter tag | 5.8:1 trên nền trắng (AA) |
| `--jpm-color-blue-electric` | `#0284C7` | **Technical Action / Info** – Xanh hàng hải công nghệ | Link mã OEM, nút Xem sơ đồ rã máy, Breadcrumb active, Hotspot default | 4.6:1 trên nền trắng (AA) |
| `--jpm-color-gold-marine` | `#D97706` | **Luxury Marine Metallic** – Ánh đồng chân vịt | Huy hiệu đại lý ủy quyền, Thẻ bảo hành chính hãng, Khách hàng VIP Garage | 4.5:1 trên nền tối `#0A1128` |

### 2.2. Thang sắc độ trung tính (Neutral Slate Scale - Nền công nghiệp hiện đại)

Thay thế hoàn toàn bảng xám xỉn màu của Bootstrap cổ điển bằng dải Slate cao cấp:

```
[#0F172A] ── [#1E293B] ── [#334155] ── [#64748B] ── [#94A3B8] ── [#CBD5E1] ── [#E2E8F0] ── [#F1F5F9] ── [#F8FAFC] ── [#FFFFFF]
 Slate-900    Slate-800    Slate-700    Slate-500    Slate-400    Slate-300    Slate-200    Slate-100    Slate-50     Pure White
 (Heading)    (Subhead)    (Body text)  (Muted/Icon) (Placeholder)(Border str) (Border sub) (Surface sub)(Surface bg) (Card bg)
```

| Token CSS | Mã HEX | Tên gọi chuẩn | Ứng dụng cụ thể |
| :--- | :--- | :--- | :--- |
| `--jpm-neutral-900` | `#0F172A` | Deep Charcoal | Tiêu đề bài viết, Tên phụ tùng, Giá tiền in đậm |
| `--jpm-neutral-800` | `#1E293B` | Slate Bold | Văn bản nội dung quan trọng, Nhãn Input form |
| `--jpm-neutral-700` | `#334155` | Slate Standard | Nội dung thân trang (Body text), Mô tả kỹ thuật |
| `--jpm-neutral-500` | `#64748B` | Slate Muted | Số mã phụ tùng phụ, Metadata, Ngày đăng, Biểu tượng nhạt |
| `--jpm-neutral-400` | `#94A3B8` | Slate Light | Placeholder trường nhập liệu, Icon hỗ trợ |
| `--jpm-neutral-300` | `#CBD5E1` | Border Focus/Active | Đường viền khi focus nhẹ, divider bảng phụ tùng |
| `--jpm-neutral-200` | `#E2E8F0` | Border Subtle | Đường viền Card, đường chia dòng bảng rã máy mặc định |
| `--jpm-neutral-100` | `#F1F5F9` | Neutral Surface | Nền ô tìm kiếm, nền bảng sọc (zebra stripe), nền ô thông số |
| `--jpm-neutral-50` | `#F8FAFC` | Page Background | Nền toàn trang Client (thay thế nền xám đục cũ `#f7f7f7`) |
| `--jpm-neutral-0` | `#FFFFFF` | Card Surface | Mặt nổi của Card sản phẩm, Modal, Dropdown Panel |

### 2.3. Hệ màu trạng thái nghiệp vụ (Semantic Status Palette)

Các trạng thái đặc thù của ngành phụ tùng (Hàng có sẵn, Đặt trước, Tạm hết, Báo giá, Ngừng sản xuất):

| Trạng thái nghiệp vụ | Background Token | Text / Border Token | Mã HEX Text | Mã HEX Nền | Ứng dụng |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Còn hàng (In Stock)** | `--jpm-status-stock-bg` | `--jpm-status-stock-text` | `#15803D` | `#DCFCE7` | Huy hiệu "Còn hàng trong kho", Sẵn sàng giao ngay |
| **Chờ báo giá (Quote Required)**| `--jpm-status-quote-bg` | `--jpm-status-quote-text` | `#1D4ED8` | `#DBEAFE` | Phụ tùng giá biến động theo tỷ giá Yên/USD |
| **Hàng đặt trước (Pre-order)** | `--jpm-status-order-bg` | `--jpm-status-order-text` | `#B45309` | `#FEF3C7` | Phụ tùng chính hãng đặt từ xưởng Nhật Bản |
| **Mã thay thế (Superseded)** | `--jpm-status-super-bg` | `--jpm-status-super-text` | `#6D28D9` | `#EDE9FE` | Cảnh báo mã OEM cũ đã được nâng cấp mã mới |
| **Hết hàng (Out of Stock)** | `--jpm-status-out-bg` | `--jpm-status-out-text` | `#B91C1C` | `#FEE2E2` | Tạm ngừng cung ứng, liên hệ kỹ thuật tìm mã thế |

---

## 3. HỆ THỐNG ĐỒ HỌA CHỮ (TYPOGRAPHY SYSTEM)

Dự án JPM thống nhất khai thác cặp font chuẩn quốc tế:
1. **Raleway (Google Font):** Đại diện cho phong cách thiết kế Marine sang trọng, hình học tinh tế (Geometric Sans), ứng dụng cho các Tiêu đề lớn (Display, H1, H2, H3) và Tên thương hiệu.
2. **Roboto & Roboto Mono (Google Font):** Tối ưu hóa tính dễ đọc (Legibility), ứng dụng cho Toàn bộ Body, Input Form, Nhãn, Bảng thông số kỹ thuật, Bảng rã máy, Mã OEM và Giá tiền (Tabular Numbers).

### 3.1. Thang phân cấp chữ (Type Scale Hierarchy)

| Cấp bậc phân loại | Font Family | Size (Desktop) | Size (Mobile) | Weight | Line Height | Letter Spacing | Mục đích & Ví dụ thực tế |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Display 1** | `Raleway` | 44px (2.75rem) | 32px (2.0rem) | 800 Bold | 1.15 | -0.02em | Hero Trang chủ, Slogan công nghệ hàng hải |
| **Heading 1 (H1)** | `Raleway` | 32px (2.00rem) | 24px (1.5rem) | 700 Bold | 1.25 | -0.01em | Tiêu đề trang (Sơ đồ phụ tùng Yanmar 4JH4...) |
| **Heading 2 (H2)** | `Raleway` | 24px (1.50rem) | 20px (1.25rem)| 700 Bold | 1.30 | 0.00em | Tiêu đề Section (Cụm rã máy, Sản phẩm liên quan) |
| **Heading 3 (H3)** | `Raleway` | 20px (1.25rem) | 18px (1.125rem)| 600 SemiBold| 1.35 | 0.00em | Tiêu đề Card lớn, Tên nhóm phụ tùng, Tên dòng máy |
| **Heading 4 (H4)** | `Roboto` | 16px (1.00rem) | 15px (0.938rem)| 600 SemiBold| 1.40 | 0.00em | Tên phụ tùng trong ProductCard, Tiêu đề Widget |
| **Body Large** | `Roboto` | 16px (1.00rem) | 15px (0.938rem)| 400 Regular | 1.50 | 0.00em | Lời mở đầu bài viết tin tức, Giới thiệu catalog |
| **Body Standard** | `Roboto` | 14px (0.875rem)| 14px (0.875rem)| 400 Regular | 1.50 | 0.00em | Nội dung bài viết, Form label, Mô tả sản phẩm |
| **Body Small** | `Roboto` | 13px (0.813rem)| 12px (0.75rem) | 400 Regular | 1.45 | 0.00em | Chú thích phụ, Địa chỉ chi nhánh, Metadata |
| **Caption / Tiny** | `Roboto` | 11px (0.688rem)| 11px (0.688rem)| 500 Medium | 1.30 | +0.02em | Nhãn trạng thái nhỏ, Copyright footer |
| **Technical OEM** | `Roboto Mono` | 13px (0.813rem)| 12px (0.75rem) | 600 SemiBold| 1.20 | +0.05em | **Mã phụ tùng (Ví dụ: `688-45941-01-EL`), Serial** |
| **Price Hero** | `Roboto` | 22px (1.375rem)| 18px (1.125rem)| 700 Bold | 1.20 | -0.01em | Giá chi tiết sản phẩm (`12.850.000 đ`) |
| **Price Card** | `Roboto` | 16px (1.00rem) | 15px (0.938rem)| 700 Bold | 1.20 | 0.00em | Giá hiển thị trên Product Card danh sách |

> [!IMPORTANT]
> **Quy tắc Font Tabular:** Mọi thành phần hiển thị giá tiền, số lượng tồn kho và tọa độ số đồ rã máy (`ref_number`, `qty`, `price`) bắt buộc sử dụng CSS thuộc tính:
> `font-variant-numeric: tabular-nums;` để ngăn chặn hiện tượng xê dịch pixel chiều ngang khi số nhảy.

---

## 4. HỆ THỐNG BO GÓC CHUẨN HÓA (BORDER RADIUS SYSTEM)
### Thực thi nghiêm ngặt: PO DECISION 01 – OPTION A ĐÃ PHÊ DUYỆT

Tuyệt đối bãi bỏ 15 giá trị bo góc lộn xộn cũ (`2px`, `3px`, `4px`, `5px`, `10px`, `15px`, `20px`, `25px`, `30px`, `50px`). Từ Phase 3, toàn bộ giao diện Client JPM chỉ tuân thủ đúng 4 cấp độ:

```
┌────────────────────────────────────────────────────────────────────────┐
│                      HỆ THỐNG BORDER RADIUS CHUẨN HÓA                  │
├─────────────────────┬──────────────┬───────────────────────────────────┤
│ TOKEN CSS           │ GIÁ TRỊ      │ PHẠM VI ÁP DỤNG DUY NHẤT          │
├─────────────────────┼──────────────┼───────────────────────────────────┤
│ --jpm-radius-card   │ 12px         │ Card sản phẩm, Khung Panel, Modal,│
│                     │              │ Alert container, Khung sơ đồ rã   │
├─────────────────────┼──────────────┼───────────────────────────────────┤
│ --jpm-radius-control│ 8px          │ Button, Input, Select (JpmSelect),│
│                     │              │ Dropdown Menu, Textarea, Tooltip  │
├─────────────────────┼──────────────┼───────────────────────────────────┤
│ --jpm-radius-badge  │ 6px          │ Tag lọc, Badge trạng thái, Chip,  │
│                     │              │ Mã OEM Pill, Hotspot pin số       │
├─────────────────────┼──────────────┼───────────────────────────────────┤
│ --jpm-radius-circle │ 50% (Hoàn hảo│ Avatar người dùng, Nút icon tròn, │
│                     │ tròn tuyệt đối) Đèn báo Online/Offline, Zoom btn  │
└─────────────────────┴──────────────┴───────────────────────────────────┘
```

---

## 5. HỆ THỐNG KHOẢNG CÁCH (SPACING SYSTEM - 8-POINT GRID)

Sử dụng hệ số tỷ lệ chuẩn 4px/8px để tạo nhịp điệu thị giác (Visual Cadence) liền mạch giữa máy tính để bàn và màn hình cảm ứng:

| Token Spacing | Giá trị Pixel | Ứng dụng nội bộ component | Ứng dụng bố cục cấu trúc Layout |
| :--- | :--- | :--- | :--- |
| `--jpm-space-1` | 4px | Khoảng cách giữa Icon và Text trong badge nhỏ | Viền micro |
| `--jpm-space-2` | 8px | Padding nút nhỏ, khoảng cách nhãn và input | Margin giữa các nút trong button group |
| `--jpm-space-3` | 12px | Padding của Input form chuẩn, Padding Badge | Gap của filter pill group |
| `--jpm-space-4` | 16px | Padding nội bộ Card phụ tùng, Padding Table cell| Grid gap trên Mobile |
| `--jpm-space-5` | 20px | Padding Card chi tiết, Header Modal | Grid gap trên Tablet |
| `--jpm-space-6` | 24px | Padding Modal body, Thẻ thông số kỹ thuật | Grid gap trên Desktop (`row-gap`, `col-gap`)|
| `--jpm-space-8` | 32px | Khoảng cách giữa các khối chức năng trong trang | Khoảng cách Sub-section |
| `--jpm-space-12` | 48px | Khoảng cách chân trang, Header offset mobile | Section padding trên Tablet |
| `--jpm-space-16` | 64px | Padding trên/dưới của Section tiêu chuẩn | Section padding trên Desktop |
| `--jpm-space-20` | 80px | Padding lớn của Hero banner công nghệ | Đại sảnh trang chủ |

---

## 6. ĐỔ BÓNG & PHÂN TẦNG THỊ GIÁC (SHADOW & ELEVATION SYSTEM)

Triệt tiêu hoàn toàn các bóng đổ đen kịt cổ điển dạng `0 0 30px rgba(0,0,0,0.3)`. Hệ thống bóng đổ 2026 sử dụng tông màu xanh biển sâu siêu mỏng kết hợp khuếch tán rộng để tạo cảm giác linh kiện bay bổng trên mặt kính hàng hải:

| Tầng hiển thị (Elevation) | Token CSS | Giá trị Đổ bóng | Mục đích sử dụng |
| :--- | :--- | :--- | :--- |
| **Layer 0 (Flat)** | `--jpm-shadow-none` | `none` | Bảng dữ liệu, Input nền xám nhạt |
| **Layer 1 (Subtle Card)** | `--jpm-shadow-sm` | `0 1px 3px rgba(10, 17, 40, 0.05), 0 1px 2px rgba(10, 17, 40, 0.03)` | Card sản phẩm tĩnh mặc định, Khung thông tin kỹ thuật |
| **Layer 2 (Elevated Hover)**| `--jpm-shadow-md` | `0 4px 12px -2px rgba(10, 17, 40, 0.08), 0 2px 6px -2px rgba(10, 17, 40, 0.04)` | **Card hover state (bay nổi nhẹ 3px)**, Thanh công cụ sơ đồ rã máy |
| **Layer 3 (Floating Popover)**| `--jpm-shadow-lg` | `0 12px 24px -4px rgba(10, 17, 40, 0.12), 0 4px 10px -2px rgba(10, 17, 40, 0.06)`| JpmSelect Dropdown mở ra, Hotspot Tooltip preview |
| **Layer 4 (Modal Overlay)** | `--jpm-shadow-xl` | `0 24px 48px -12px rgba(10, 17, 40, 0.20)` | Cửa sổ Modal Quick-View, Modal Báo giá, Drawer giỏ hàng |

---

## 7. MẶT PHẲNG & ĐƯỜNG VIỀN KỸ THUẬT (SURFACE & BORDER)

```
┌────────────────────────────────────────────────────────┐
│ NỀN TRANG TỔNG THỂ: --jpm-neutral-50 (#F8FAFC)        │
│  ┌──────────────────────────────────────────────────┐  │
│  │ MẶT CARD NỔI: --jpm-neutral-0 (#FFFFFF)         │  │
│  │ VIỀN CHÍNH XÁC: 1px solid --jpm-neutral-200      │  │
│  │ RADIUS: 12px                                     │  │
│  │                                                  │  │
│  │  ┌────────────────────────────────────────────┐  │  │
│  │  │ KHỐI THÔNG SỐ: --jpm-neutral-100 (#F1F5F9)│  │  │
│  │  │ VIỀN: 1px solid --jpm-neutral-200          │  │  │
│  │  │ RADIUS: 8px                                │  │  │
│  │  └────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
```
- **Độ dày đường viền (Border Width):** Duy nhất `1px` đồng nhất toàn hệ thống. Không sử dụng viền dày 2px/3px gây thô ráp.
- **Màu viền tiêu chuẩn:** `--jpm-border-subtle: #E2E8F0` cho bề mặt sáng; `--jpm-border-dark: rgba(255,255,255,0.12)` cho khối nền tối.
- **Hiệu ứng kính mờ (Backdrop Blur):** Header Sticky và Thanh công cụ sơ đồ Zoom/Pan sử dụng:
  `background: rgba(10, 17, 40, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);`

---

## 8. HỆ THỐNG BIỂU TƯỢNG (ICON SYSTEM - BOOTSTRAP ICONS DUY NHẤT)
### Thực thi nghiêm ngặt: PO DECISION 04 – OPTION A ĐÃ PHÊ DUYỆT

Toàn bộ biểu tượng Boxicons (`bx bx-*`) được loại bỏ 100% khỏi phân hệ Client. Danh mục ánh xạ icon chuẩn Bootstrap Icons (`bi bi-*`):

| Nhóm chức năng | Tên Icon Bootstrap (`bi bi-*`) | Kích thước chuẩn | Ứng dụng thực tế |
| :--- | :--- | :--- | :--- |
| **Điều hướng & Danh mục** | `bi-grid-fill`, `bi-compass`, `bi-list` | 18px / 20px | Menu bar, Mega menu danh mục máy thủy |
| **Tra cứu sơ đồ rã máy** | `bi-diagram-3`, `bi-search`, `bi-zoom-in` | 16px / 18px | Công cụ bản vẽ Exploded Diagram, Pan/Zoom |
| **Thương mại & Giỏ hàng** | `bi-bag`, `bi-bag-check-fill`, `bi-cart3` | 18px / 20px | Giỏ hàng, Thêm phụ tùng vào giỏ, Checkout |
| **Báo giá kỹ thuật** | `bi-file-earmark-text`, `bi-send` | 16px / 18px | Yêu cầu báo giá phụ tùng đặc biệt |
| **Trạng thái & Cảnh báo** | `bi-check-circle-fill`, `bi-exclamation-triangle-fill` | 14px / 16px | Huy hiệu tồn kho, Cảnh báo mã thay thế Supersession |
| **Thao tác nhanh** | `bi-arrow-right`, `bi-x-lg`, `bi-chevron-down` | 14px / 16px | Mũi tên CTA, Nút đóng modal, Nút sổ dropdown |
| **Tài khoản & Garage** | `bi-person-circle`, `bi-wrench-adjustable` | 18px / 20px | Hồ sơ khách hàng, Quản lý tủ máy cá nhân |

---

## 9. ĐỀ XUẤT ĐẶC TẢ CSS VARIABLES (CHO BƯỚC TRIỂN KHAI TƯƠNG LAI)

*(Lưu ý: Đoạn mã CSS này là tài liệu đặc tả thiết kế, KHÔNG ĐƯỢC CHÈN vào source code trong Phase 3)*

```css
:root {
  /* 1. BRAND COLORS */
  --jpm-color-navy-dark: #0A1128;
  --jpm-color-navy-subtle: #101E42;
  --jpm-color-red-marine: #F82249;
  --jpm-color-red-hover: #D9163A;
  --jpm-color-blue-electric: #0284C7;
  --jpm-color-gold-marine: #D97706;

  /* 2. NEUTRAL SLATE SCALE */
  --jpm-neutral-900: #0F172A;
  --jpm-neutral-800: #1E293B;
  --jpm-neutral-700: #334155;
  --jpm-neutral-500: #64748B;
  --jpm-neutral-400: #94A3B8;
  --jpm-neutral-300: #CBD5E1;
  --jpm-neutral-200: #E2E8F0;
  --jpm-neutral-100: #F1F5F9;
  --jpm-neutral-50:  #F8FAFC;
  --jpm-neutral-0:   #FFFFFF;

  /* 3. BORDER RADIUS (PO DECISION 01) */
  --jpm-radius-card: 12px;
  --jpm-radius-control: 8px;
  --jpm-radius-badge: 6px;
  --jpm-radius-circle: 50%;

  /* 4. SPACING SCALE (8-POINT) */
  --jpm-space-1: 4px;
  --jpm-space-2: 8px;
  --jpm-space-3: 12px;
  --jpm-space-4: 16px;
  --jpm-space-5: 20px;
  --jpm-space-6: 24px;
  --jpm-space-8: 32px;
  --jpm-space-12: 48px;
  --jpm-space-16: 64px;
  --jpm-space-20: 80px;

  /* 5. ELEVATIONS & SHADOWS */
  --jpm-shadow-sm: 0 1px 3px rgba(10, 17, 40, 0.05), 0 1px 2px rgba(10, 17, 40, 0.03);
  --jpm-shadow-md: 0 4px 12px -2px rgba(10, 17, 40, 0.08), 0 2px 6px -2px rgba(10, 17, 40, 0.04);
  --jpm-shadow-lg: 0 12px 24px -4px rgba(10, 17, 40, 0.12), 0 4px 10px -2px rgba(10, 17, 40, 0.06);
  --jpm-shadow-xl: 0 24px 48px -12px rgba(10, 17, 40, 0.20);

  /* 6. TYPOGRAPHY */
  --jpm-font-heading: 'Raleway', sans-serif;
  --jpm-font-body: 'Roboto', sans-serif;
  --jpm-font-mono: 'Roboto Mono', monospace;
}
```
