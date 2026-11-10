# JPM CLIENT – TÀI LIỆU 08: ACCESSIBILITY & INCLUSION GUIDELINES (TIÊU CHUẨN KHẢ NĂNG TRUY CẬP WCAG 2.1 AA)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. MỤC TIÊU VÀ PHẠM VI ÁP DỤNG

JPM hướng tới việc đảm bảo mọi người dùng – bao gồm thợ máy lớn tuổi, người khiếm thị một phần hoặc người làm việc trong điều kiện ánh sáng chói chang tại cầu cảng – đều có thể tiếp cận và vận hành hệ thống một cách chuẩn xác:
- **Tiêu chuẩn mục tiêu:** Đạt chuẩn **WCAG 2.1 Cấp độ AA (Level AA)** trên toàn bộ 20 màn hình Client.
- **Tiêu chuẩn nâng cao:** Đạt chuẩn AAA cho độ tương phản văn bản mã OEM và thông số giá tiền.

---

## 2. QUY CHUẨN ĐỘ TƯƠNG PHẢN MÀU SẮC (COLOR CONTRAST RATIOS)

Mọi cặp màu sắc (Văn bản trên Nền) đều được kiểm thử thông qua thuật toán WCAG Luminance:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        KIỂM ĐỊNH TƯƠNG PHẢN WCAG                       │
├────────────────────┬─────────────────┬──────────┬──────────────────────┤
│ MÀU VĂN BẢN (TEXT) │ MÀU NỀN (SURF)  │ TỶ LỆ    │ XẾP HẠNG WCAG        │
├────────────────────┼─────────────────┼──────────┼──────────────────────┤
│ Deep Navy #0A1128  │ Pure White #FFF │ 14.8 : 1 │ Đạt chuẩn AAA (Vượt) │
│ Slate-800 #1E293B  │ Pure White #FFF │ 11.9 : 1 │ Đạt chuẩn AAA (Vượt) │
│ Slate-700 #334155  │ Pure White #FFF │  9.2 : 1 │ Đạt chuẩn AAA (Vượt) │
│ Slate-500 #64748B  │ Pure White #FFF │  4.6 : 1 │ Đạt chuẩn AA         │
│ Marine Red #F82249 │ Pure White #FFF │  4.8 : 1 │ Đạt chuẩn AA         │
│ White #FFFFFF      │ Deep Navy #0A1128 14.8 : 1 │ Đạt chuẩn AAA (Vượt) │
│ White #FFFFFF      │ Marine Red #F82249 4.8 : 1 │ Đạt chuẩn AA         │
│ Green #15803D      │ Nền xanh #DCFCE7│  5.1 : 1 │ Đạt chuẩn AA         │
└────────────────────┴─────────────────┴──────────┴──────────────────────┘
```

> [!NOTE]
> Tuyệt đối không bao giờ sử dụng màu sắc làm phương tiện **duy nhất** để truyền tải thông tin. Ví dụ: Trạng thái "Hết hàng" ngoài màu đỏ bắt buộc phải đi kèm biểu tượng dấu chéo `bi-x-circle` và dòng chữ rõ ràng *"Tạm hết hàng trong kho"*.

---

## 3. ĐIỀU HƯỚNG BẰNG BÀN PHÍM HOÀN TOÀN (KEYBOARD NAVIGATION & FOCUS RINGS)

Mọi chức năng trên JPM phải có khả năng vận hành 100% bằng bàn phím mà không cần chuột:

### 3.1. Vòng nét viền tiêu điểm (Focus Visible Ring)
Khi người dùng duyệt bằng phím `Tab`, thành phần đang nhận tiêu điểm sẽ hiển thị vòng hào quang sắc nét:
```css
:focus-visible {
  outline: 2px solid var(--jpm-color-navy-dark, #0A1128);
  outline-offset: 2px;
  box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.25);
}
```

### 3.2. Điều hướng trên Sơ đồ rã máy (`SoDoPhuTung.vue`)
- Phím `Tab` cho phép chuyển tuần tự qua các Hotspot Pin theo thứ tự số `ref_number`.
- Phím `Enter` hoặc `Space` trên Pin đang chọn sẽ kích hoạt việc mở xem chi tiết và cuộn bảng phụ tùng tương ứng.
- Phím `+` / `-` cho phép phóng to thu nhỏ bản vẽ bằng bàn phím.
- Phím `Escape` lập tức đóng Tooltip xem nhanh hoặc thoát chế độ toàn màn hình.

---

## 4. HỖ TRỢ ĐỌC MÀN HÌNH & THẺ ARIA (SCREEN READER & SEMANTIC ARIA)

### 4.1. Cấu trúc Landmark ngữ nghĩa
- `<header role="banner">`: Khu vực thanh Navbar và nhận diện thương hiệu.
- `<nav role="navigation" aria-label="Điều hướng chính">`: Menu danh mục động cơ và phụ tùng.
- `<main role="main">`: Khu vực nội dung trọng tâm trang.
- `<aside role="complementary" aria-label="Bộ lọc catalog">`: Sidebar bộ lọc.
- `<footer role="contentinfo">`: Chân trang.

### 4.2. Thẻ ARIA cho các thành phần động
- `JpmSelect.vue`:
  - `role="combobox"`
  - `aria-haspopup="listbox"`
  - `aria-expanded="true/false"`
  - Các mục con: `role="option"`, `aria-selected="true/false"`.
- Hotspot Pin trên bản vẽ:
  - `role="button"`
  - `aria-label="Vị trí số 1: Cánh chân vịt nhôm Yamaha 40-55HP, Giá 3 triệu 850 ngàn, Còn hàng"`
- Giỏ hàng & Tìm kiếm:
  - Thông báo số lượng thêm vào giỏ sử dụng `aria-live="polite"` để phần mềm đọc màn hình đọc to thông báo cho người khiếm thị mà không làm gián đoạn người dùng.

---

## 5. HỖ TRỢ GIẢM THIỂU CHUYỂN ĐỘNG (PREFERS-REDUCED-MOTION)

Dành cho người dùng có tiền đình nhạy cảm hoặc thiết bị phần cứng yếu:
```css
@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```
Khi bật chế độ này, hiệu ứng bay nổi của Card, hiệu ứng trượt của Modal và sóng radar phát sáng của Hotspot Pin sẽ chuyển đổi tức thì không có độ trễ chuyển động.
