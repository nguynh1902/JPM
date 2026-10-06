# LUỒNG NGHIỆP VỤ HỆ THỐNG (SYSTEM WORKFLOW)

> **Nguyên tắc chi phối:** Simple Workflow, Part Catalog First, Data Is The Asset.

Tài liệu này mô tả chi tiết 4 luồng nghiệp vụ xương sống của hệ thống JPM, mô tả sự tương tác giữa con người (Admin, Khách hàng) và Phần mềm.

---

## 1. LUỒNG TRA CỨU PHỤ TÙNG (3 CÁCH TÌM KIẾM CỐT LÕI)
Đây là chức năng sống còn và thường xuyên nhất của website.

### Cách 1: Tìm Trực Tiếp Bằng Mã Phụ Tùng (Direct Code Search)
*   **Đối tượng:** Thợ máy, Garage, người đã có sẵn Part Number trong tay.
*   **Luồng xử lý:** 
    1. Người dùng nhập mã phụ tùng (Ví dụ: `6E5-44352-00`) vào thanh tìm kiếm.
    2. Nhấn Enter → Hệ thống tra cứu bảng `PHU_TUNGS`.
    3. Trả về ngay **Trang Chi Tiết Phụ Tùng** (Hiển thị giá, trạng thái, mã thay thế nếu có, và danh sách máy tương thích).
    4. Thêm vào giỏ hàng.

### Cách 2: Tìm Kiếm Nhanh Bằng Bộ Lọc (Quick Filter Search)
*   **Đối tượng:** Người biết thông số động cơ nhưng không biết mã phụ tùng cụ thể.
*   **Luồng xử lý:**
    1. Tại Trang Chủ hoặc thanh công cụ, khách hàng thả dropdown chọn tuần tự: `Thương hiệu` → `Model` → `Năm sản xuất` → `Loại động cơ` → `Mã lực`.
    2. Nhấn "Tìm Kiếm".
    3. Hệ thống trả về **Danh sách phụ tùng** lắp vừa cho đúng cấu hình động cơ đó.
    4. Khách hàng lựa chọn và thêm vào giỏ hàng.

### Cách 3: Tra Cứu Catalog Sơ Đồ Lắp Ráp (Visual Diagram Navigation) - QUAN TRỌNG NHẤT
*   **Đối tượng:** Khách B2C, thợ cần nhìn bản vẽ để xác định vị trí hỏng hóc.
*   **Luồng xử lý:**
    1. Chọn Thương Hiệu ở Trang Chủ (VD: Yamaha).
    2. Chọn Loại Động Cơ (VD: Outboard).
    3. Chọn Model (VD: 150HP).
    4. Hệ thống hiển thị danh sách các Cụm Bộ Phận (Bơm nước, Chế hòa khí, Trục cam...).
    5. Chọn một Cụm → Vào **Trang Sơ Đồ Nổ (Exploded Diagram)**.
    6. Tương tác với Bản vẽ nổ (Zoom, di chuột vào các số thứ tự - Ref).
    7. Click vào số Ref trên ảnh → Highlight dòng Phụ tùng tương ứng ở bảng bên phải.
    8. Tick chọn phụ tùng và bấm [Thêm vào giỏ hàng].

---

## 2. LUỒNG BÁN HÀNG & GOM ĐƠN (ORDER TO CROSS-DOCKING)
Đây là quy trình hiện thực hóa mô hình JPM, không lưu kho lớn.

*   **Bước 1: Khách hàng lên đơn (Checkout)**
    *   Khách hàng tổng hợp phụ tùng trong Giỏ hàng.
    *   Nhập thông tin giao hàng nội địa.
    *   Xác nhận "Đặt hàng / Gửi yêu cầu". (Hệ thống tạo Record trong bảng `DON_HANGS`).

*   **Bước 2: Gom Đơn Sang Nhật (Purchase Order Consolidation)**
    *   Hàng ngày/tuần, Admin vào màn hình "Quản lý Đơn Khách".
    *   Admin chọn nhiều đơn lẻ (của nhiều khách khác nhau) → Bấm nút **"Tạo PO đi Nhật"**.
    *   Hệ thống gộp chung các mã phụ tùng giống nhau để tối ưu lượng đặt.
    *   Tạo bản ghi `PURCHASE_ORDERS` (PO).

*   **Bước 3: Xử lý Hàng Về (Cross-Docking)**
    *   Khi nhà cung cấp Nhật báo giao hàng, PO chuyển trạng thái "Đang vận chuyển Quốc Tế".
    *   Khi lô hàng về đến kho Việt Nam → Đổi trạng thái PO thành "Đã Về Kho Trung Chuyển".
    *   Staff sử dụng màn hình **"Phân loại hàng"**: Quét mã vạch lô hàng Nhật → Hệ thống tự động báo mã này thuộc Đơn hàng số mấy của Khách hàng nào.
    *   Staff nhặt bỏ vào hộp cho khách tương ứng.

*   **Bước 4: Giao Hàng Nội Địa (Last-mile)**
    *   Khi Đơn hàng của khách đã đủ hàng từ PO, chuyển trạng thái "Sẵn sàng giao".
    *   Giao cho đơn vị vận chuyển (Viettel Post, GHN...).
    *   Hệ thống tự tính phí Ship nội địa: Khách B2B (Miễn phí), Khách B2C (Cộng thêm phí vào hóa đơn cuối).

---

## 3. LUỒNG QUẢN LÝ DỮ LIỆU CATALOG (DATA ENTRY / PIM)
Dành cho Admin/Staff. Khối lượng công việc đồ sộ nhất nằm ở đây.

*   **Bước 1:** Khởi tạo `Thương Hiệu`, `Loại Phương Tiện`, `Động Cơ`, `Model Máy` (Danh mục cha/con).
*   **Bước 2:** Import danh sách `Mã Phụ Tùng (Part Number)` từ Excel (Gồm mã, Tên, Giá, Cân nặng).
*   **Bước 3:** Tạo Sơ Đồ Cụm cho Model máy → Upload ảnh Bản vẽ nổ (Microfiche).
*   **Bước 4: Map Tọa độ (Image Mapping) - Tùy chọn:** Admin dùng tool vẽ box lên bản vẽ ảnh, gán với mã phụ tùng để khi khách hover chuột có thể tương tác.
*   **Bước 5: Cập nhật Supersession:** Nếu hãng đổi mã mới, Admin vào trang Phụ tùng, khai báo "Mã cũ A bị thay thế bởi Mã mới B". Kể từ đó, khách search Mã A hệ thống tự động redirect sang Mã B.

---

## 4. LUỒNG GARAGE (XE CỦA TÔI)
Vũ khí giữ chân khách hàng (Customer Loyalty).

*   **Bước 1:** Khách (đã đăng nhập) vào mục `Garage` trong Profile.
*   **Bước 2:** Bấm "Thêm Máy" → Chọn Model máy thủy của mình (hoặc điền số Serial).
*   **Bước 3:** Hệ thống tự động ưu tiên model máy này mỗi khi khách truy cập Catalog.
*   **Bước 4:** Hệ thống tạo nhắc nhở bảo dưỡng (vd: 100 giờ chạy cần thay nhớt, lọc dầu) và tự động đề xuất list phụ tùng (Bảo dưỡng định kỳ). Khách chỉ việc bấm 1 click để đưa toàn bộ combo bảo dưỡng vào giỏ hàng.
