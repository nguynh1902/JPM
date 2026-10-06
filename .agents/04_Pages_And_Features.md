# ĐẶC TẢ GIAO DIỆN & TÍNH NĂNG (PAGES AND FEATURES)

> **Nguyên tắc chi phối:** Part Catalog First, No Feature Creep.

Hệ thống giao diện được phân tách rõ rệt: **Front-office (Client)** tập trung hoàn toàn vào UX/UI Tra cứu Catalog; **Back-office (Admin/Staff)** tập trung hoàn toàn vào UX/UI Nhập liệu Data (PIM) và Xử lý Đơn (Cross-docking).

---

## 1. PHÂN HỆ KHÁCH HÀNG (CLIENT)
*(Sử dụng Template TheEvent, tối giản, chuyên nghiệp, phản hồi cực nhanh)*

### 1.1. Trang Catalog & Sơ Đồ Lắp Ráp (`SoDoPhuTung.vue`) - [CORE]
Đây là trang đắt giá nhất của hệ thống, thay thế hoàn toàn cấu trúc trang Chi tiết Sản phẩm của TMĐT thông thường.
*   **Bố cục (Layout):** Chia làm 3 khu vực chức năng (Grid System).
    *   **Khu vực 1 (Trái - 25%):** Menu Dọc chứa danh sách các Cụm Bộ Phận (Assemblies) của máy (VD: Cylinder Head, Crankshaft). Cho phép search nhanh tên cụm.
    *   **Khu vực 2 (Giữa - 50%):** Viewport hiển thị Bản Vẽ Kỹ Thuật (Exploded Diagram). Bắt buộc có công cụ Zoom (Phóng to/Thu nhỏ), Pan (Kéo thả). Khi rê chuột (hover) vào một Số Reference (Tọa độ X,Y), hiển thị Tooltip tên phụ tùng.
    *   **Khu vực 3 (Phải - 25%):** Bảng danh sách Phụ tùng thuộc cụm đang chọn. Các cột: Ref#, Mã phụ tùng (SKU), Tên, Số lượng cần trên 1 cụm, Giá, Checkbox.
*   **Tính năng tương tác:** Click vào Ref trên hình → Row tương ứng ở Bảng Phải sẽ nhấp nháy/highlight. Ngược lại, click vào Row → Vị trí Ref trên bản vẽ sẽ đổi màu. Bấm [Thêm vào giỏ] các item đã tick.

### 1.2. Trang Tìm Kiếm & Lọc Nhanh (`DanhSachPhuTung.vue`)
*   **Bố cục:** Bảng lưới (Data Table) đơn giản, giống giao diện B2B. Không cần ảnh to rực rỡ, ưu tiên mã và thông số.
*   **Tính năng:** Hiển thị rõ cột **Mã Thay Thế (Supersession)**. Nếu khách tìm mã cũ, hệ thống tự hiển thị mã mới và có Badge cảnh báo đỏ "Superseded by...".
*   **Bộ lọc Dropdown:** `Năm` → `Loại Động Cơ` → `Mã Lực (HP)` (Dữ liệu select theo dạng Cascading/Liên đới).

### 1.3. Trang Quản Lý Garage (`Garage.vue`)
*   **Tính năng:** Giao diện dạng Card các dòng máy khách đang sở hữu. Nút [Tra cứu phụ tùng cho máy này] sẽ dẫn thẳng vào Trang Catalog, bỏ qua các bước chọn rườm rà. Nút [Mua combo bảo dưỡng] sẽ thả toàn bộ nhớt, lọc gió, bugi vào giỏ.

### 1.4. Giỏ Hàng Tối Giản (`GioHang.vue`)
*   **Bố cục:** Một bảng duy nhất, gom nhóm theo Thương hiệu hoặc Nhóm Máy.
*   **Tính năng:** Loại bỏ luồng "Mua ngay" vs "Yêu cầu báo giá". Mọi thao tác submit giỏ hàng đều đi về một form duy nhất: [Gửi Đơn Yêu Cầu]. Website không bắt buộc thanh toán online ngay lập tức nếu giá trị quá lớn, tạo không gian thương lượng B2B.

---

## 2. PHÂN HỆ QUẢN TRỊ (ADMIN & STAFF)
*(Sử dụng Template Sneat, UI dạng Dashboard, Form nhập liệu nhiều tính năng)*

### 2.1. Tool Nhập Liệu Data (PIM Workspace) - [CORE]
Giao diện giúp Admin tạo ra "Tài sản Dữ liệu".
*   **Tính năng Import Excel:** Xử lý file hàng trăm ngàn dòng (Mã phụ tùng, Tên, Cân nặng). Yêu cầu có thanh tiến trình (Progress Bar), báo cáo dòng lỗi (Invalid rows) chứ không chết ngang (Crash).
*   **Màn Hình Map Sơ Đồ (Image Mapping Editor):**
    *   Màn hình cho phép Upload ảnh Sơ đồ lên.
    *   Admin dùng chuột Kéo thả tạo các Box tọa độ (Hotspots) trên bức ảnh.
    *   Gán ID_Phu_Tung và Số Ref vào từng Box đó để Frontend lấy dữ liệu render.
*   **Quản Lý Supersession:** Form nối "Mã cũ" vào "Mã mới".

### 2.2. Màn Hình Gom Đơn Sang Nhật (PO Consolidation)
*   **Bố cục:** Bảng Master-Detail.
*   **Tính năng:** 
    *   Bên trái là danh sách các mã phụ tùng mà khách hàng đang chờ (Cộng gộp tổng số lượng cần mua của tất cả các đơn).
    *   Admin duyệt và bấm "Tạo PO sang Hãng". Hệ thống xuất ra file Excel đúng chuẩn của nhà cung cấp Nhật.

### 2.3. Màn Hình Xử Lý Hàng Về (Cross-Docking Terminal)
Thay thế cho hệ thống Quản lý Kho cũ kỹ.
*   **Tính năng:** Giao diện tối ưu cho máy quét mã vạch (Barcode Scanner).
*   **Luồng:** Staff cầm kiện hàng Nhật về → Cầm súng quét mã vạch trên túi nilon phụ tùng → Màn hình kêu "Bíp" và hiện to dòng chữ: **"BỎ VÀO RỔ SỐ #15 (CỦA KHÁCH NGUYỄN VĂN A)"**. 
*   Đồng thời trừ số lượng trên PO và cập nhật % hoàn thành của Đơn hàng. Nếu Đơn hàng đủ 100%, tự động chuyển sang luồng Gọi Ship Nội địa.
