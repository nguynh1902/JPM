# JPM CLIENT – TÀI LIỆU 03: REUSABLE COMPONENT LIBRARY (THƯ VIỆN THÀNH PHẦN DÙNG CHUNG)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. THÀNH PHẦN DÙNG CHUNG TRỌNG TÂM 1: `PRODUCTCARD.VUE`
### Thực thi nghiêm ngặt: PO DECISION 01 (RADIUS 12px) & DECISION 03 (SHARED COMPONENT)

Trong mã nguồn cũ, logic hiển thị sản phẩm bị sao chép rải rác trên ít nhất 4 màn hình (`TrangChu.vue`, `SanPham.vue`, `ThuongHieu.vue`, `KetQuaTraCuu.vue`). `ProductCard.vue` mới thống nhất 100% trải nghiệm người dùng, chuẩn hóa phân cấp thị giác và giải quyết triệt để lỗi bất đối xứng chân thẻ.

```
┌────────────────────────────────────────────────────────┐
│ [YAMAHA]                     [ Còn hàng: Sẵn sàng 🟢 ] │
│ ┌────────────────────────────────────────────────────┐ │
│ │                                                    │ │
│ │             HÌNH ẢNH PHỤ TÙNG (1:1)                │ │
│ │              object-fit: contain                   │ │
│ │                                                    │ │
│ └────────────────────────────────────────────────────┘ │
│ Mã OEM: 688-45941-01-EL (Chính hãng)                   │
│ Cánh chân vịt nhôm máy đẩy 3 cánh 11-1/2 x 13          │
│ Dòng máy: 40HP - 55HP (2 thì & 4 thì)                  │
├────────────────────────────────────────────────────────┤
│ GIÁ BÁN NIÊM YẾT                  HÀNH ĐỘNG THƯƠNG MẠI │
│ 3.850.000 đ                       ┌──────────────────┐ │
│ (Chưa VAT)                        │ [🛒 Thêm giỏ]    │ │
│                                   └──────────────────┘ │
└────────────────────────────────────────────────────────┘
```

### 1.1. Giải pháp khắc phục triệt để lỗi bất đối xứng chân thẻ (Asymmetric Bottom Bug Fix)
- **Lỗi hiện tại:** Khi sản phẩm có `is_gia_lien_he: true` hoặc `gia_ban = 0`, thẻ sản phẩm cũ ẩn hoàn toàn nút `.btn-cart-action`. Điều này tạo ra một khoảng trống đục lỗ khó coi ở góc dưới bên phải, làm lệch hàng cột khi hiển thị dạng lưới (Grid).
- **Thiết kế chuẩn hóa 2026:**
  - Nếu sản phẩm **Có giá cụ thể:** Hiển thị khối giá rõ ràng + Nút đỏ `Thêm vào giỏ` (`bi-cart-plus`).
  - Nếu sản phẩm **Báo giá (Contact for Quote):** Hiển thị nhãn `"Giá: Liên hệ"` bằng chữ kỹ thuật nổi bật + Nút xanh công nghệ `Yêu cầu báo giá` (`bi-file-earmark-text`) có kích thước và chiều cao cân bằng hoàn hảo (38px x 100% cân đối). Chân thẻ luôn luôn có đầy đủ 2 bên: Thông tin giá bên trái và Nút hành động tương ứng bên phải.

```
┌────────────────────────────────────────────────────────┐
│ TRƯỜNG HỢP: SẢN PHẨM CẦN BÁO GIÁ                       │
├────────────────────────────────────────────────────────┤
│ GIÁ PHỤ TÙNG                      HÀNH ĐỘNG            │
│ Giá: Liên hệ kỹ sư                ┌──────────────────┐ │
│ (Theo tỷ giá Yên/USD)             │ [📄 Báo giá]     │ │
│                                   └──────────────────┘ │
└────────────────────────────────────────────────────────┘
```

### 1.2. Đặc tả API của Component `ProductCard.vue`

```typescript
// Giao ước dữ liệu đề xuất cho ProductCard (TypeScript Interface Spec)
interface ProductCardProps {
  id: number | string;
  tenPhuTung: string;
  maOem: string;
  thuongHieu?: string;             // Ví dụ: 'Yanmar', 'Yamaha', 'Suzuki'
  hinhAnhUrl?: string;             // Ảnh vuông 1:1, mặc định có fallback ảnh máy thủy
  giaBan?: number;                 // Số tiền (VNĐ)
  isGiaLienHe?: boolean;           // Cờ báo giá
  tonKho?: number;                 // Số lượng thực tế
  trangThaiHang?: 'in_stock' | 'pre_order' | 'quote_required' | 'out_of_stock';
  isSuperseded?: boolean;          // Cảnh báo mã thay thế
  maMoiNhat?: string;              // Mã thay thế nếu có
  loaiDongCoPhuHop?: string;       // Ví dụ: 'Yanmar 4JH4-TE, 4JH5E'
}

interface ProductCardEmits {
  (e: 'add-to-cart', product: ProductCardProps): void;
  (e: 'request-quote', product: ProductCardProps): void;
  (e: 'quick-view', productId: number | string): void;
}
```

### 1.3. Quy chuẩn kích thước và Trạng thái tương tác (States)
- **Bo góc tổng thể:** Bắt buộc `12px` (`--jpm-radius-card`).
- **Bo góc nút hành động:** Bắt buộc `8px` (`--jpm-radius-control`).
- **Bo góc nhãn thương hiệu & Badge:** Bắt buộc `6px` (`--jpm-radius-badge`).
- **Hiệu ứng Hover:**
  - Nâng nhẹ bề mặt `translateY(-4px)`.
  - Đổ bóng chuyển từ `--jpm-shadow-sm` sang `--jpm-shadow-md`.
  - Viền chuyển màu sang `--jpm-color-navy-dark` hoặc `--jpm-color-red-marine` (1px mảnh).
  - Ảnh phóng to tinh tế `transform: scale(1.04); transition: transform 0.25s ease;`.
- **Trạng thái Hết hàng (Out of Stock):**
  - Giảm độ mờ cả thẻ xuống `opacity: 0.7`.
  - Phủ badge đỏ xám: `"Tạm hết hàng"`.
  - Nút chuyển thành `Tìm mã thay thế` (`bi-search`).

---

## 2. THÀNH PHẦN DÙNG CHUNG TRỌNG TÂM 2: `JPMSELECT.VUE`
### Thực thi nghiêm ngặt: BỘ LỌC KỸ THUẬT 4 CẤP (CASCADING FILTER DROPDOWN)

Dropdown mặc định của trình duyệt (`<select>`) không đáp ứng được thẩm mỹ Luxury Marine và gây lỗi hiển thị danh sách dài hàng trăm model động cơ. `JpmSelect.vue` là component Dropdown kỹ thuật chuyên dụng thay thế toàn bộ logic sao chép trong `TrangChu.vue`, `SanPham.vue`, `SoDoPhuTung.vue`.

```
┌────────────────────────────────────────────────────────┐
│ THƯƠNG HIỆU ĐỘNG CƠ *                                  │
│ ┌────────────────────────────────────────────────────┐ │
│ │ ⚓ Yanmar Marine                       [✕] [▾]      │ │
│ └────────────────────────────────────────────────────┘ │
│   ┌──────────────────────────────────────────────────┐ │
│   │ [🔍 Tìm kiếm hãng máy thủy...]                   │ │
│   ├──────────────────────────────────────────────────┤ │
│   │ ⚓ Yanmar Marine (Động cơ diesel tàu cá & du th…)│ │
│   │ ⚙️ Yamaha Outboards (Máy đẩy gắn ngoài cao tốc)  │ │
│   │ 🚢 Suzuki Marine (Động cơ 4 thì thế hệ mới)      │ │
│   │ ⚡ Honda Marine (Động cơ VTEC công nghệ cao)     │ │
│   └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
```

### 2.1. Hỗ trợ đầy đủ chuỗi lọc phụ thuộc 4 cấp (Cascading Dependency)
```
[CẤP 1: HÃNG ĐỘNG CƠ] ──> [CẤP 2: LOẠI ĐỘNG CƠ] ──> [CẤP 3: CÔNG SUẤT / DÒNG] ──> [CẤP 4: MODEL CHÍNH XÁC]
     (Brand)               (Inboard / Outboard)         (HP / Series)                  (Engine Model)
Ví dụ: Yamaha Marine   ──> Gắn ngoài (Outboard) ──> Dải 150HP - 250HP      ──> F200FETX (2022-2025)
```
- Khi cấp trên thay đổi $\to$ cấp dưới lập tức hiển thị trạng thái Loading skeleton, reset giá trị con, và kích hoạt `emit('change')`.
- Khi cấp trên chưa chọn $\to$ cấp dưới ở trạng thái `disabled: true` với nhãn chú thích rõ: *"Vui lòng chọn Hãng máy trước"*.

### 2.2. Đặc tả API của Component `JpmSelect.vue`

```typescript
interface JpmSelectOption {
  value: string | number;
  label: string;
  subtitle?: string;              // Thông số bổ sung (vd: "4 thì, 2.8L, 4 xi-lanh")
  badge?: string;                 // Nhãn phụ (vd: "Phổ biến", "Mới")
  disabled?: boolean;
}

interface JpmSelectProps {
  modelValue: string | number | null;
  options: JpmSelectOption[];
  placeholder?: string;
  label?: string;                 // Nhãn hiển thị phía trên
  required?: boolean;
  searchable?: boolean;           // Cho phép gõ tìm kiếm bên trong dropdown
  clearable?: boolean;            // Cho phép nút bấm [X] để xóa chọn
  loading?: boolean;              // Trạng thái đang tải dữ liệu từ API
  disabled?: boolean;             // Trạng thái vô hiệu hóa
  errorMessage?: string;          // Lỗi xác thực
  emptyText?: string;             // Thông báo khi không tìm thấy kết quả
}

interface JpmSelectEmits {
  (e: 'update:modelValue', value: string | number | null): void;
  (e: 'change', option: JpmSelectOption | null): void;
  (e: 'clear'): void;
}
```

### 2.3. Khả năng tương tác phím & Khả năng truy cập (A11y)
- Nhấn phím `Tab` để focus vào select.
- Nhấn `Enter` hoặc `Space` để mở danh sách lựa chọn.
- Nhấn phím `ArrowDown` / `ArrowUp` để di chuyển vệt chọn trong menu.
- Nhấn phím `Escape` để lập tức đóng menu mà không lưu thay đổi.
- Bo góc menu: `8px` (`--jpm-radius-control`), đổ bóng `--jpm-shadow-lg`.

---

## 3. ĐẶC TẢ CHI TIẾT 22 THÀNH PHẦN GIAO DIỆN NỀN TẢNG (UI BUILDING BLOCKS)

### 3.1. Nhóm Nút bấm & Thao tác (Buttons)
1. **Button Primary (`.btn-jpm-primary`):** Nền đỏ `--jpm-color-red-marine` (`#F82249`), chữ trắng, font-weight 600, bo góc 8px. Ứng dụng: Thêm vào giỏ hàng, Xác nhận thanh toán.
2. **Button Navy Tech (`.btn-jpm-navy`):** Nền biển sâu `--jpm-color-navy-dark` (`#0A1128`), viền mảnh `1px solid rgba(255,255,255,0.1)`, chữ trắng. Ứng dụng: Xem sơ đồ rã máy, Tra cứu catalog.
3. **Button Outline (`.btn-jpm-outline`):** Nền trong suốt, viền `--jpm-neutral-300`, chữ `--jpm-neutral-800`. Hover chuyển sang nền xám nhạt `--jpm-neutral-100`. Ứng dụng: Xem chi tiết, Lọc thêm.
4. **Button Quote (`.btn-jpm-quote`):** Nền xanh dương đậm nhạt `--jpm-status-quote-bg` (`#DBEAFE`), viền và chữ xanh dương `#1D4ED8`. Ứng dụng: Yêu cầu báo giá kỹ thuật.
5. **Icon Button Circle (`.btn-icon-circle`):** Bo góc `50%`, kích thước 40px x 40px, căn giữa icon. Ứng dụng: Nút Zoom/Pan sơ đồ, Nút đóng modal.

### 3.2. Nhóm Biểu mẫu (Form Controls)
6. **Input Text / Search (`.jpm-input`):** Chiều cao 44px, viền 1px `--jpm-neutral-200`, bo góc 8px. Khi focus: viền chuyển màu navy `#0A1128` kèm vòng sáng mỏng `box-shadow: 0 0 0 3px rgba(10, 17, 40, 0.1)`.
7. **Textarea (`.jpm-textarea`):** Bo góc 8px, padding 12px, tối thiểu 3 dòng, phục vụ điền ghi chú yêu cầu báo giá hoặc số khung động cơ.
8. **Checkbox Kỹ thuật (`.jpm-checkbox`):** Ô vuông kích thước 18px, bo góc 4px. Khi tick có dấu kiểm trắng trên nền xanh navy hoặc đỏ.
9. **Radio Filter (`.jpm-radio`):** Hình tròn 18px, bo tròn 50%, chấm trung tâm 8px khi active.

### 3.3. Nhóm Nhãn trạng thái & Thẻ (Badges & Chips)
10. **Badge Trạng thái tồn kho (`.badge-stock`):** Bo góc 6px, padding 4px 10px, chữ in đậm 11px. Xanh lá: Sẵn hàng; Vàng: Đặt trước; Xanh dương: Báo giá.
11. **Badge OEM Code (`.badge-oem`):** Font Roboto Mono, chữ đậm, nền `--jpm-neutral-100`, viền mảnh `--jpm-neutral-300`, bo góc 6px. Cho phép click 1 chạm để sao chép mã (Copy OEM to clipboard).
12. **Chip / Filter Pill (`.jpm-chip`):** Dùng để hiển thị các bộ lọc đang chọn (vd: `Hãng: Yanmar [✕]`). Bo góc 6px, nền nhạt, có nút xóa nhanh.

### 3.4. Nhóm Khung chứa & Hiển thị dữ liệu (Surfaces & Data)
13. **Card Container (`.jpm-card`):** Nền trắng `#FFFFFF`, viền 1px `--jpm-neutral-200`, bo góc chuẩn 12px, đổ bóng `--jpm-shadow-sm`.
14. **Data Table (`.jpm-table`):** Dùng cho Bảng phụ tùng sơ đồ rã máy (`SoDoPhuTung.vue`) và Lịch sử đơn hàng. Header nền Slate-100 cố định (Sticky thead), sọc dòng nhẹ xen kẽ, hover đổi màu Slate-50, chữ số căn phải bằng `font-variant-numeric: tabular-nums`.
15. **Modal Container (`.jpm-modal`):** Bo góc 12px, nền trắng cao cấp, Header phân cách đường viền mảnh 1px, hiệu ứng mở mượt mà scale từ 0.95 lên 1.0.
16. **Tooltip Kỹ thuật (`.jpm-tooltip`):** Nền đen biển sâu `#0A1128`, chữ trắng 12px, bo góc 8px, có mũi tên định vị chuẩn xác Hotspot.

### 3.5. Nhóm Điều hướng phụ & Tín hiệu (Navigation & Feedback)
17. **Tabs Kỹ thuật (`.jpm-tabs`):** Gạch chân chuyển động (Sliding underline), font Raleway 600, không sử dụng tab viền bo lỗi thời kiểu Bootstrap cũ.
18. **Accordion (`.jpm-accordion`):** Dành cho trang Câu hỏi thường gặp (`HoTro.vue`) và Mô tả chi tiết kỹ thuật. Mở rộng trơn tru bằng CSS Grid (`grid-template-rows: 0fr -> 1fr`).
19. **Breadcrumb Bar (`.jpm-breadcrumb`):** Ngăn cách bằng biểu tượng chevron (`bi-chevron-right`), tự động thu gọn dấu `...` trên thiết bị di động.
20. **Pagination (`.jpm-pagination`):** Các ô số 36px x 36px, bo góc 8px, số active nền navy chữ trắng.
21. **Empty State (`.jpm-empty-state`):** Minh họa động cơ hoặc mỏ neo dạng Line-Art, tiêu đề rõ ràng kèm nút hành động định hướng (vd: *"Không tìm thấy phụ tùng theo bộ lọc – Xóa tất cả bộ lọc"*).
22. **Toast / Notification (`.jpm-toast`):** Nổi ở góc trên bên phải, bo góc 12px, đổ bóng `--jpm-shadow-lg`, hiển thị thông báo thêm vào giỏ thành công hoặc sao chép mã OEM.

---

## 4. MA TRẬN ÁNH XẠ THÀNH PHẦN DÙNG CHUNG VÀO 20 MÀN HÌNH CLIENT

| Màn hình Client | ProductCard.vue | JpmSelect.vue | Table Kỹ thuật | Modal Báo giá | Hotspot Pin | Badge OEM |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| 1. `TrangChu.vue` | ● | ● | ○ | ○ | ○ | ● |
| 2. `KetQuaTraCuu.vue` | ● | ● | ○ | ● | ○ | ● |
| 3. `ThuongHieu.vue` | ● | ● | ○ | ○ | ○ | ● |
| 4. `SoDoPhuTung.vue` | ○ | ● | ● | ● | ● | ● |
| 5. `ChiTietPhuTung.vue` | ● (liên quan) | ○ | ● (thông số) | ● | ○ | ● |
| 6. `SanPham.vue` | ● | ● | ○ | ○ | ○ | ● |
| 7. `ChiTietSanPham.vue` | ● (gợi ý) | ○ | ● | ● | ○ | ● |
| 8. `GioHang.vue` | ○ | ○ | ● (danh sách) | ● | ○ | ● |
| 9. `TraCuuBaoGia.vue` | ○ | ○ | ● | ● (chi tiết) | ○ | ● |
| 10. `TraCuuDonHang.vue` | ○ | ○ | ● | ○ | ○ | ● |
| 11. `TinTuc.vue` | ○ | ○ | ○ | ○ | ○ | ○ |
| 12. `ChiTietTinTuc.vue` | ● (sản phẩm bài) | ○ | ○ | ○ | ○ | ○ |
| 13. `HoTro.vue` | ○ | ○ | ○ | ○ | ○ | ○ |
| 14. `LayoutTaiKhoan.vue` | ○ | ○ | ○ | ○ | ○ | ○ |
| 15. `TaiKhoan.vue` | ○ | ○ | ○ | ○ | ○ | ○ |
| 16. `Garage.vue` | ○ | ● (thêm máy) | ● | ○ | ○ | ○ |
| 17. `GarageChiTietMay.vue` | ● (khuyên dùng) | ○ | ● (sơ đồ rã) | ○ | ○ | ● |
| 18. `VatTuThuongMua.vue` | ● | ○ | ● | ● | ○ | ● |
| 19. `LichSuDonHang.vue` | ○ | ○ | ● | ○ | ○ | ● |
| 20. `XacThuc.vue` | ○ | ○ | ○ | ○ | ○ | ○ |

*(Ghi chú: ● = Thành phần chính bắt buộc; ○ = Không áp dụng)*
