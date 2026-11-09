# JPM CLIENT – TÀI LIỆU 07: RESPONSIVE SYSTEM (QUY CHUẨN THÍCH ỨNG ĐA THIẾT BỊ)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. THANG ĐIỂM NGẮT CHUẨN (BREAKPOINTS SYSTEM)

Thừa hưởng cấu trúc Bootstrap 5.3.3 nhưng được chuẩn hóa chặt chẽ theo môi trường sử dụng thực tế của thợ máy tại bến tàu, kỹ sư trên tàu dịch vụ và nhân viên văn phòng:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                              THANG BREAKPOINTS JPM 2026                                │
├────────────┬──────────────┬───────────────┬────────────────────────────────────────────┤
│ BREAKPOINT │ ĐỘ RỘNG MÀN  │ CONTAINER     │ THIẾT BỊ MỤC TIÊU TIÊU BIỂU                │
├────────────┼──────────────┼───────────────┼────────────────────────────────────────────┤
│ xs         │ < 576px      │ 100% fluid    │ iPhone, Android phone dọc (thao tác 1 tay) │
│ sm         │ 576px - 767px│ 540px         │ Điện thoại xoay ngang, máy tính bảng mini  │
│ md         │ 768px - 991px│ 720px         │ iPad 9.7/10.2", Tablet công trường chống sốc│
│ lg         │ 992px - 1199p│ 960px         │ Laptop nhỏ 13-inch, iPad Pro dọc           │
│ xl         │ 1200px - 1399│ 1140px        │ Màn hình máy tính để bàn chuẩn Full HD     │
│ xxl        │ >= 1400px    │ 1320px        │ Màn hình đồ họa rộng 2K/4K, Văn phòng điều │
│            │              │               │ hành cảng hàng hải                         │
└────────────┴──────────────┴───────────────┴────────────────────────────────────────────┘
```

---

## 2. MA TRẬN BIẾN ĐỔI THÀNH PHẦN THEO BREAKPOINT (COMPONENT ADAPTATION MATRIX)

| Thành phần giao diện | Desktop Lớn (`xl`, `xxl`) | Tablet (`md`, `lg`) | Mobile (`xs`, `sm`) |
| :--- | :--- | :--- | :--- |
| **Navbar & Header** | Chiều cao 80px, hiển thị đầy đủ menu, ô tìm kiếm 340px, Garage pill, giỏ hàng | Chiều cao 72px, ô tìm kiếm thu gọn dạng icon, menu chính thu vào Drawer | Chiều cao 64px, logo rút gọn, nút tìm kiếm mở Search Sheet, menu hamburger |
| **Layout Offset** | `padding-top: 80px;` | `padding-top: 72px;` | `padding-top: 64px;` |
| **Lưới ProductCard** | 4 Cột (`col-xl-3`) | 3 Cột (`col-lg-4`) hoặc 2 Cột (`col-md-6`) | 2 Cột trên `sm`, 1-2 Cột trên `xs` kèm card padding 12px |
| **Sidebar Bộ lọc** | Cột cố định 280px bên trái, `position: sticky; top: 100px;` | Nút bấm *"Bộ lọc ⚙️"* mở Drawer trượt từ cạnh trái | Nút nổi dưới chân trang *"Bộ lọc (3)"*, mở Bottom Sheet toàn màn hình |
| **Sơ đồ rã máy (`SoDoPhuTung.vue`)** | Split View 2 cột ngang (55% Sơ đồ / 45% Bảng) có thanh kéo Resizer | Split View tỷ lệ 50/50, bảng phụ tùng thu gọn cột số lượng | Chuyển sang Tab View (Tab 1: Sơ đồ Pinch Zoom / Tab 2: Bảng tra cứu) |
| **Bảng dữ liệu kỹ thuật** | Bảng đầy đủ 6-8 cột (Số, Mã, Tên, Đơn vị, Tồn kho, Giá, Nút mua) | Bảng thu gọn 4-5 cột chính, ẩn cột Đơn vị và Thông số chi tiết | Chuyển đổi từ `table` sang danh sách Thẻ cuộn dọc (Card-based list view) |
| **Modal & Popup** | Căn giữa màn hình, độ rộng 600px - 900px, bo góc 12px | Căn giữa, độ rộng 90% màn hình, bo góc 12px | Trở thành Bottom Sheet trượt từ đáy màn hình, bo góc trên 16px |
| **Chân trang (Footer)** | 4 Cột dàn ngang đầy đủ thông tin, logo, chứng chỉ | 2 Cột x 2 Hàng cân đối | 1 Cột xếp chồng, các nhóm liên kết gập mở dạng Accordion |

---

## 3. THIẾT KẾ CẢM ỨNG VÀ THAO TÁC NGOÀI BẾN CẢNG (RUGGED FIELD MOBILE UX)

Trong thực tế, thợ máy thường phải vừa cầm linh kiện dính dầu mỡ vừa tra cứu bằng điện thoại dưới ánh nắng gay gắt:
1. **Độ tương phản cao dưới trời nắng (High Sunlight Contrast):**
   - Không sử dụng màu xám quá nhạt cho chữ. Mọi chữ thông số kỹ thuật và mã OEM trên thiết bị di động đều đạt độ tương phản tối thiểu 7:1 với nền.
2. **Khu vực bấm ngón tay cái (Thumb-Friendly Bottom Bar):**
   - Trên màn hình di động, thanh hành động mua hàng hoặc yêu cầu báo giá được ghim cố định ở đáy màn hình (Sticky Bottom Action Bar):
     `position: fixed; bottom: 0; left: 0; right: 0; height: 60px; z-index: 1020;`
   - Giúp thợ máy có thể bấm *"Thêm vào giỏ"* hoặc *"Gọi kỹ sư"* bằng đúng một ngón tay cái mà không cần với tay lên đầu màn hình.
3. **Cơ chế chống rung giật bố cục (CLS Prevention):**
   - Mọi khung ảnh phụ tùng và khung sơ đồ đều được gán tỷ lệ khung hình cố định (`aspect-ratio: 1/1` hoặc `aspect-ratio: 16/9`) kèm hình nền placeholder mờ để ngăn hiện tượng nhảy trang khi ảnh tải chậm qua mạng 3G/4G ngoài biển.
