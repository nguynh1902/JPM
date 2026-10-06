# ĐẶC TẢ NGHIỆP VỤ JPM (BUSINESS SPECIFICATION)

> **Nguyên tắc chi phối:** Business First, Part Catalog First.

## 1. TỔNG QUAN MÔ HÌNH KINH DOANH
JPM không phải là một website thương mại điện tử bán lẻ thông thường (như Shopee, Lazada hay Amazon). 
Bản chất của JPM là một hệ thống **Part Catalog (Danh mục phụ tùng)** kết hợp **Order Management (Quản lý đơn hàng/gom đơn)**.

*   **Sản phẩm cốt lõi:** Phụ tùng máy thủy, động cơ tàu thuyền (Yamaha, Honda, Mercury...). Các sản phẩm này mang tính kỹ thuật cao, không thể "mua đại" mà phải lắp ráp chính xác đến từng con ốc (Reference Number) trên bản vẽ sơ đồ nổ (Exploded Diagram).
*   **Giá trị mang lại lớn nhất:** Cung cấp công cụ tra cứu (Microfiche) khổng lồ, chính xác và nhanh chóng nhất để khách hàng tự tìm ra đúng mã phụ tùng họ cần, tránh sai sót khi mua hàng.
*   **Hành vi mua hàng:** Dựa trên nhu cầu sửa chữa kỹ thuật, không dựa trên cảm xúc hay các chương trình khuyến mãi (No Flash Sale, No Gamification).

## 2. CHÂN DUNG KHÁCH HÀNG & TÁC NHÂN (ACTORS)

### 2.1. Khách Hàng (Customer)
Được chia làm 2 nhóm chính:
1.  **B2B (Thợ máy, Garage, Xưởng đóng tàu, Cửa hàng bán lẻ):**
    *   Hành vi: Mua số lượng nhiều, tần suất cao, biết rõ mã phụ tùng hoặc cực kỳ rành về bản vẽ kỹ thuật.
    *   Chính sách: Công ty chịu phí ship nội địa. Có yêu cầu khắt khe về thời gian giao hàng và độ chính xác của phụ tùng (mã thay thế/supersession).
2.  **B2C (Chủ tàu, Khách lẻ):**
    *   Hành vi: Mua lẻ tẻ để tự sửa chữa/bảo dưỡng cơ bản. Phụ thuộc rất nhiều vào công cụ tra cứu theo Năm/Mã lực để tìm đúng xe của mình.
    *   Chính sách: Khách tự trả phí ship nội địa.

### 2.2. Nhân Viên (Staff)
*   Nhiệm vụ: Cập nhật thông tin phụ tùng, xử lý đơn hàng, theo dõi các yêu cầu báo giá. Đóng gói và phân loại hàng (Cross-docking) khi hàng từ Nhật về Việt Nam.

### 2.3. Quản Trị Viên (Admin / Chủ Doanh Nghiệp)
*   Nhiệm vụ: Duyệt các Purchase Order (PO) lớn đẩy sang nhà cung cấp Nhật. Quyết định giá bán. Sử dụng công cụ (PIM) để nạp khối lượng dữ liệu khổng lồ (bản vẽ, ref, mã phụ tùng) vào hệ thống.

## 3. MÔ HÌNH BÁN HÀNG & CHUỖI CUNG ỨNG (SUPPLY CHAIN)

Hệ thống JPM vận hành theo mô hình tinh gọn, tránh đọng vốn và tồn kho lớn (Low Inventory Model).

*   **Bước 1 - Ghi nhận Yêu cầu (Request/Order):** Khách hàng tìm được phụ tùng trên Catalog, đưa vào giỏ hàng và tạo Đơn đặt hàng (hoặc Yêu cầu báo giá nếu là hàng đặc biệt).
*   **Bước 2 - Gom Đơn (Consolidation):** Website đóng vai trò thu thập. Công ty sẽ gom nhiều đơn lẻ của nhiều khách hàng thành một hoặc nhiều Purchase Order (PO) lớn.
*   **Bước 3 - Đặt hàng Quốc tế:** Công ty gửi PO sang Nhật Bản (hoặc hãng sản xuất).
*   **Bước 4 - Vận chuyển Quốc tế:** Hàng từ Nhật Bản về Việt Nam. Chi phí ship quốc tế do Công ty (JPM) hoàn toàn chịu trách nhiệm. Khách hàng không bị tính phí này.
*   **Bước 5 - Kho Trung Chuyển (Cross-docking):** Khi lô hàng lớn về đến Việt Nam, Kho hàng của JPM không làm nhiệm vụ lưu trữ lâu dài. Kho chỉ là nơi để bóc dỡ, phân loại nhanh (Sort), đóng gói lại theo từng đơn hàng của khách. 
*   **Bước 6 - Giao Hàng Nội Địa (Last-mile Delivery):** Hàng lập tức được giao cho khách. (B2B: JPM chịu phí, B2C: Khách chịu phí).

**Hệ quả đối với Phần mềm:**
*   Hệ thống không cần module Quản lý kho (WMS) phức tạp, không cần thẻ kho, kiểm kê hàng ngày.
*   Chỉ cần theo dõi luồng hàng (Order Tracking) và Trạng thái PO từ Nhật về VN.

## 4. TÀI SẢN DỮ LIỆU (DATA AS THE ASSET)

Hệ thống sẽ "sống" hay "chết" phụ thuộc vào Data. Data của JPM không phải là hình ảnh đẹp của một chiếc áo, mà là mạng lưới liên kết thông tin:
`Thương hiệu` → `Động cơ` → `Model` → `Sơ đồ lắp ráp (Bản vẽ nổ)` → `Tọa độ vị trí (Ref)` → `Mã Phụ tùng OEM`.

Bên cạnh đó, dữ liệu cực kỳ quan trọng khác là **Supersession (Mã thay thế)**: Khi hãng mẹ ở Nhật ngừng sản xuất mã `A` và thay bằng mã `B`, hệ thống JPM bắt buộc phải cập nhật và cảnh báo chuẩn xác cho khách hàng để tránh mua nhầm hàng hoặc báo hết hàng ảo.
