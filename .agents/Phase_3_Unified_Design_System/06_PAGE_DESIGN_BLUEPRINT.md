# JPM CLIENT – TÀI LIỆU 06: MASTER PAGE DESIGN BLUEPRINT (BẢN THIẾT KẾ ĐỊNH HƯỚNG 20 MÀN HÌNH)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. TỔNG QUAN PHÂN HỆ 20 MÀN HÌNH CLIENT

Hệ thống 20 màn hình Client của JPM được cấu trúc thành 6 nhóm hành trình trải nghiệm người dùng:
1. **Khám phá & Tìm kiếm:** `TrangChu.vue`, `KetQuaTraCuu.vue`, `ThuongHieu.vue`.
2. **Kỹ thuật & Sơ đồ rã máy:** `SoDoPhuTung.vue`, `ChiTietPhuTung.vue`.
3. **Thương mại & Bán hàng:** `SanPham.vue`, `ChiTietSanPham.vue`, `GioHang.vue`.
4. **Theo dõi đơn hàng & Báo giá:** `TraCuuBaoGia.vue`, `TraCuuDonHang.vue`.
5. **Nội dung & Hỗ trợ kỹ thuật:** `TinTuc.vue`, `ChiTietTinTuc.vue`, `HoTro.vue`.
6. **Không gian cá nhân & Tủ máy Marine Garage:** `LayoutTaiKhoan.vue`, `TaiKhoan.vue`, `Garage.vue`, `GarageChiTietMay.vue`, `VatTuThuongMua.vue`, `LichSuDonHang.vue`, `XacThuc.vue`.

---

## 2. BẢN ĐẶC TẢ CHI TIẾT TỪNG MÀN HÌNH

### MÀN HÌNH 01: `TrangChu.vue` (Đại sảnh Nền tảng Công nghệ Hàng hải)
- **Mục tiêu:** Định vị vị thế nền tảng phụ tùng máy thủy số 1 Nhật Bản, cho phép thợ máy và chủ tàu bắt đầu tra cứu động cơ hoặc mã OEM ngay trong 3 giây đầu tiên.
- **Bố cục chính:**
  1. *Hero Section (Công nghệ Deep Marine):* Nền xanh biển sâu `#0A1128`, slogan uy tín, tích hợp **Thanh tra cứu động cơ 4 cấp nổi bật** sử dụng `JpmSelect.vue`.
  2. *Quick OEM Search Bar:* Thanh nhập mã phụ tùng trực tiếp cho thợ máy chuyên nghiệp.
  3. *Brand Showcase Carousel:* 4 trụ cột động cơ Nhật (Yanmar, Yamaha, Suzuki, Honda) với logo nổi và đường dẫn thẳng vào catalog.
  4. *Exploded Diagram Highlights:* Trưng bày 6 cụm sơ đồ máy thủy thịnh hành (Đầu bò, Hộp số, Cánh quạt...).
  5. *Featured Parts Grid:* Danh sách linh kiện bảo dưỡng hay thay thế dùng `ProductCard.vue` (Lưới 4 cột).
  6. *Japanese Quality Assurance Banner:* Cam kết linh kiện chính ngạch, xuất xứ CO/CQ.
- **Nâng cấp 2026:** Loại bỏ slider chữ chạy rườm rà cũ; đưa bộ lọc 4 cấp lên vị trí tiêu điểm trên Hero Banner; thống nhất thẻ phụ tùng về `ProductCard.vue` (bo góc 12px).

---

### MÀN HÌNH 02: `KetQuaTraCuu.vue` (Trung tâm Kết quả Tra cứu Hàng hải)
- **Mục tiêu:** Hiển thị kết quả tìm kiếm đa năng theo từ khóa, mã OEM, hoặc cấu hình động cơ đã chọn.
- **Bố cục chính:**
  1. *Search Summary Bar:* Hiển thị từ khóa đang tìm, số lượng kết quả khớp (Ví dụ: *"18 phụ tùng tương thích cho Yamaha Outboard 200HP"*), các chip bộ lọc có thể gỡ bỏ (`.jpm-chip`).
  2. *2-Column Layout (Sidebar 280px + Grid 3 cột):*
     - Cột trái: Bộ lọc danh mục con, dải giá, tình trạng kho hàng (`In Stock`, `Pre-order`).
     - Cột phải: Lưới sản phẩm dùng `ProductCard.vue` hoặc Tab chuyển đổi sang kết quả Sơ đồ rã máy liên quan.
- **Xử lý trạng thái rỗng (Empty State):** Khi không tìm thấy phụ tùng, hiển thị gợi ý thông minh: *"Kiểm tra lại định dạng mã OEM"* hoặc *"Gửi ảnh chụp số máy cho kỹ sư JPM để tra cứu hộ"*.

---

### MÀN HÌNH 03: `ThuongHieu.vue` (Trang Thương hiệu & Hệ sinh thái Hãng)
- **Mục tiêu:** Giới thiệu từng hãng máy thủy (Yanmar, Yamaha, Suzuki...), dải sản phẩm động cơ và danh mục phụ tùng ủy quyền.
- **Bố cục chính:**
  1. *Brand Hero Cover:* Ảnh động cơ công nghiệp độ nét cao, giới thiệu xuất xứ và thế mạnh hãng.
  2. *Engine Series Selector:* Bộ lọc dòng động cơ gắn ngoài (Outboard) / máy thủy đặt trong (Inboard) sử dụng `JpmSelect.vue`.
  3. *Brand Catalog Grid:* Các nhóm linh kiện cốt lõi của hãng hiển thị bằng `ProductCard.vue`.
- **Nâng cấp 2026:** Bãi bỏ các khối chữ dày đặc cũ; bổ sung huy hiệu đại lý chính thức và cam kết thời gian nhập phụ tùng gốc từ xưởng Nhật.

---

### MÀN HÌNH 04: `SoDoPhuTung.vue` (Không gian Bản vẽ Rã máy Tương tác)
- **Mục tiêu:** Màn hình kỹ thuật quan trọng nhất của JPM – Nơi thợ máy xem bản vẽ cơ khí 2D, chọn ghim Hotspot và đặt mua linh kiện chính xác từng con ốc vít.
- **Bố cục chính:**
  1. *Sub-header Navigation:* Hiển thị Model máy (`Yanmar 4JH4-TE`), Tên cụm rã máy (`Cooling Fresh Water System`), Breadcrumb liên kết.
  2. *Technical Split Workspace (55% / 45%):*
     - Bên trái: Diagram Canvas với Pan/Zoom, thanh công cụ Floating, và các Hotspot Pin tỏa sáng theo tọa độ `chi_tiet_so_dos`.
     - Bên phải: Bảng `jpm-table` danh mục linh kiện, hỗ trợ tìm kiếm nhanh theo Ref # hoặc tên phụ tùng.
- **Đặc tả tương tác:** Đồng bộ hai chiều hoàn hảo (PO Decision 02) như đã mô tả tại Tài liệu 05.
- **Nâng cấp 2026:** Loại bỏ thanh trượt cuộn đôi khó chịu; gỡ bỏ khoảng đệm inline `padding-top: 110px`; hỗ trợ chế độ xem Fullscreen toàn màn hình cho thợ máy.

---

### MÀN HÌNH 05: `ChiTietPhuTung.vue` (Hồ sơ Kỹ thuật Phụ tùng)
- **Mục tiêu:** Chi tiết chuyên sâu của một linh kiện phụ tùng cụ thể theo mã OEM.
- **Bố cục chính:**
  1. *Product Media (50%):* Thư viện ảnh chụp thực tế linh kiện, zoom chi tiết ren ốc, bề mặt gia công.
  2. *Specification & Purchase Panel (50%):* Tên phụ tùng, Mã OEM chuẩn, Mã thay thế Supersession (nếu có), Tình trạng tồn kho, Giá bán niêm yết hoặc Nút yêu cầu báo giá.
  3. *Compatibility Engine Matrix:* Bảng tra cứu danh sách toàn bộ các model động cơ tương thích với linh kiện này (Fitment Guide).
  4. *Diagram Reference Link:* Nút bấm mở ngược lại sơ đồ rã máy nơi linh kiện này được lắp đặt.
- **Nâng cấp 2026:** Đặt nút sao chép mã OEM 1 chạm; cảnh báo rõ ràng mã thay thế kỹ thuật.

---

### MÀN HÌNH 06: `SanPham.vue` (Thư viện Catalog Phụ tùng Toàn diện)
- **Mục tiêu:** Danh mục toàn bộ sản phẩm phụ tùng thương mại, lọc theo chủng loại vật tư.
- **Bố cục chính:**
  1. *Breadcrumb & Category Header:* Tên danh mục lớn (vd: Hệ thống làm mát, Cụm nhiên liệu, Chân vịt...).
  2. *Sticky Filter Sidebar:* Bộ lọc hãng máy, mức công suất, tình trạng kho và tầm giá.
  3. *Product Grid (3 cột Desktop / 2 cột Mobile):* Dùng thống nhất `ProductCard.vue`.
  4. *Pagination Bar:* Điều hướng trang gọn gàng.
- **Nâng cấp 2026:** Thay thế toàn bộ mã HTML thẻ sản phẩm cũ bị trùng lặp bằng component `ProductCard.vue`.

---

### MÀN HÌNH 07: `ChiTietSanPham.vue` (Chi tiết Sản phẩm Thương mại)
- **Mục tiêu:** Màn hình mua sắm phụ tùng bán lẻ cho khách hàng phổ thông.
- **Bố cục chính:**
  1. *Product Overview:* Ảnh sản phẩm, Hãng sản xuất, Mã SKU/OEM, Đánh giá, Giá bán, Nút chọn số lượng và Nút Thêm vào giỏ (`.btn-jpm-primary`).
  2. *Technical Specs Tab:* Kích thước vật lý, trọng lượng, chất liệu chế tạo, tiêu chuẩn chống ăn mòn.
  3. *Related Maintenance Products:* Băng tải sản phẩm gợi ý kèm (như gioăng phớt, bu lông siết tương ứng) sử dụng `ProductCard.vue`.

---

### MÀN HÌNH 08: `GioHang.vue` (Trung tâm Quản trị Giỏ hàng & Báo giá Hỗn hợp)
- **Mục tiêu:** Quản lý danh sách phụ tùng đã chọn trước khi tiến hành đặt hàng hoặc gửi yêu cầu báo giá.
- **Bố cục chính:**
  1. *Cart Item Table (70%):* Phân tách 2 nhóm hàng rõ ràng:
     - Nhóm A (Sản phẩm có giá): Tăng giảm số lượng, tính tổng tạm tính.
     - Nhóm B (Sản phẩm báo giá): Hiển thị mã OEM, số lượng cần báo giá, ô ghi chú kỹ thuật.
  2. *Order Summary Card (30% Sticky):* Tổng tiền tạm tính cho hàng có sẵn, cước vận chuyển ước tính, nút tiến hành thanh toán hoặc gửi báo giá.
- **Nâng cấp 2026:** Bo góc 12px cho bảng tóm tắt, khắc phục tình trạng bảng tràn ngang trên điện thoại bằng dạng Card view responsive.

---

### MÀN HÌNH 09: `TraCuuBaoGia.vue` (Cổng Tra cứu & Xác nhận Báo giá Kỹ thuật)
- **Mục tiêu:** Cho phép khách hàng nhập mã báo giá (`Mã BG`) và số điện thoại để xem bảng chiết tính giá phụ tùng nhập khẩu từ Nhật.
- **Bố cục chính:**
  1. *Lookup Form:* Ô nhập mã báo giá to rõ ràng, bo góc 8px.
  2. *Quotation Status Card:*
     - Trạng thái: Đang thẩm định / Đã có báo giá / Đã chốt / Hết hạn.
     - Đồng hồ đếm ngược hiệu lực 72 giờ.
     - Bảng chi tiết đơn giá từng mã phụ tùng kèm thời gian giao hàng dự kiến.
  3. *Action Buttons:* Nút `[Chấp thuận báo giá & Đặt cọc]` (Đỏ Marine) hoặc `[Yêu cầu thương thảo lại]`.
- **Nâng cấp 2026:** Sửa chữa toàn bộ 41 lỗi icon Boxicons hỏng thành Bootstrap Icons (`bi-file-earmark-check`, `bi-clock-history`).

---

### MÀN HÌNH 10: `TraCuuDonHang.vue` (Cổng Theo dõi Vận chuyển & Giao hàng Hàng hải)
- **Mục tiêu:** Theo dõi lộ trình linh kiện chuyển từ kho Nhật Bản về cảng Việt Nam và chuyển phát đến garage.
- **Bố cục chính:**
  1. *Order Search Box:* Tra cứu nhanh bằng mã đơn hoặc số điện thoại.
  2. *Visual Order Timeline (Trục lộ trình 5 chặng):*
     - Chặng 1: Tiếp nhận đơn $\to$ Chặng 2: Xuất kho Nhật $\to$ Chặng 3: Thông quan cảng $\to$ Chặng 4: Đang giao nội địa $\to$ Chặng 5: Đã bàn giao nghiệm thu.
  3. *Order Items Summary:* Danh sách phụ tùng trong kiện hàng.
- **Nâng cấp 2026:** Loại bỏ icon Boxicons hỏng; chuẩn hóa timeline theo chuẩn Bootstrap Icons và sắc màu trạng thái chuẩn.

---

### MÀN HÌNH 11: `TinTuc.vue` (Tạp chí Kỹ thuật & Bản tin Hàng hải)
- **Mục tiêu:** Chia sẻ kiến thức bảo trì động cơ tàu thủy, cẩm nang đọc sơ đồ rã máy, lịch triển lãm thiết bị hàng hải.
- **Bố cục chính:**
  1. *Featured Editorial Card:* Bài viết tiêu điểm khổ lớn với ảnh nền du thuyền sang trọng.
  2. *Article Grid (3 cột):* Thẻ tin tức bo góc 12px, ngày đăng, chuyên mục kỹ thuật.
  3. *Newsletter Subscription Box:* Nhận tài liệu kỹ thuật máy thủy định kỳ.

---

### MÀN HÌNH 12: `ChiTietTinTuc.vue` (Bài viết Chuyên sâu & Hướng dẫn Kỹ thuật)
- **Mục tiêu:** Đọc nội dung hướng dẫn chi tiết, có tích hợp liên kết trực tiếp đến các phụ tùng được đề cập trong bài.
- **Bố cục chính:**
  1. *Article Header:* Tiêu đề (Raleway H1), tác giả kỹ sư trưởng, ngày đăng.
  2. *Article Rich Content:* Nội dung văn bản với font Roboto 15px, khoảng giãn dòng 1.6 dễ đọc, hình ảnh cơ khí minh họa có chú thích rõ ràng.
  3. *Mentioned Parts Widget:* Khối phụ tùng liên quan trực tiếp đến bài viết sử dụng `ProductCard.vue` ở cuối trang.

---

### MÀN HÌNH 13: `HoTro.vue` (Trung tâm Trợ giúp & Dịch vụ Khách hàng)
- **Mục tiêu:** Giải đáp thắc mắc về phương thức giao nhận linh kiện cồng kềnh (trục chân vịt, lốc máy), chính sách bảo hành.
- **Bố cục chính:**
  1. *Quick Contact Cards (3 hộp):* Hotline kỹ thuật 24/7, Kênh Zalo Kỹ sư hỗ trợ đọc sơ đồ, Địa chỉ kho cảng.
  2. *Accordion FAQ:* Các câu hỏi thường gặp nhóm theo: Tra cứu mã OEM, Đặt hàng phụ tùng hiếm, Chính sách đổi trả.
  3. *Direct Feedback Form:* Biểu mẫu gửi câu hỏi kỹ thuật trực tuyến.

---

### MÀN HÌNH 14: `LayoutTaiKhoan.vue` (Khung Bố cục Quản trị Khách hàng)
- **Mục tiêu:** Cung cấp thanh điều hướng thống nhất (Account Sidebar) cho toàn bộ phân hệ khách hàng đăng nhập.
- **Bố cục chính:**
  - Header tóm tắt tài khoản (Tên khách/garage, cấp độ thành viên).
  - Cột trái: `AccountSidebar.vue` (Hồ sơ, Garage của tôi, Vật tư hay mua, Báo giá của tôi, Lịch sử đơn hàng, Đăng xuất). Toàn bộ icon chuyển sang Bootstrap Icons.
  - Cột phải: Khung hiển thị nội dung động `<router-view />`.

---

### MÀN HÌNH 15: `TaiKhoan.vue` (Hồ sơ Chủ tàu & Thông tin Doanh nghiệp)
- **Mục tiêu:** Quản lý thông tin liên hệ, mã số thuế xuất hóa đơn GTGT công ty hàng hải, địa chỉ nhận phụ tùng tại các cảng/bến tàu.
- **Bố cục chính:** Form thông tin cá nhân/doanh nghiệp, trường chọn tỉnh thành/bến cảng giao hàng mặc định, đổi mật khẩu bảo mật.

---

### MÀN HÌNH 16: `Garage.vue` (Tủ máy Hàng hải của tôi - My Marine Garage)
- **Mục tiêu:** Tính năng độc bản của JPM – Nơi chủ tàu lưu lại danh sách các động cơ đang sở hữu (vd: Ca nô Yamarin động cơ Yamaha F200, Tàu đánh cá máy Yanmar 6CX-GTY).
- **Bố cục chính:**
  1. *Garage Statistics Bar:* Tổng số động cơ, số máy đến hạn bảo dưỡng định kỳ.
  2. *Engine Fleet Grid (2-3 cột):* Thẻ từng cỗ máy (Ảnh máy, Model, Năm sản xuất, Số giờ vận hành).
  3. *Add Engine Modal:* Form thêm máy mới sử dụng bộ lọc phụ thuộc 4 cấp `JpmSelect.vue`.
  4. *Quick Access Actions:* Nút `[Xem sơ đồ rã máy dòng này]` hoặc `[Mua gói bảo dưỡng chuẩn]`.

---

### MÀN HÌNH 17: `GarageChiTietMay.vue` (Hồ sơ Chi tiết Động cơ Đã lưu)
- **Mục tiêu:** Đi sâu vào từng cỗ máy cụ thể trong tủ máy cá nhân.
- **Bố cục chính:**
  - Thông số kỹ thuật chi tiết của máy.
  - Danh sách tất cả các sơ đồ rã máy của riêng model này.
  - Sổ theo dõi lịch sử bảo trì, thay nhớt hộp số, thay cánh bơm nước.
  - Danh mục vật tư tương thích 100% được gợi ý riêng cho cỗ máy.

---

### MÀN HÌNH 18: `VatTuThuongMua.vue` (Danh mục Vật tư Tiêu hao Định kỳ)
- **Mục tiêu:** Giúp thợ máy và chủ tàu mua lại cực nhanh các phụ tùng bảo dưỡng thường xuyên (Lọc dầu, bugi, anode, nhớt máy, ron nắp quy lát) chỉ bằng 1 cú click.
- **Bố cục chính:**
  - Danh sách phụ tùng hay mua hiển thị dưới dạng bảng kỹ thuật kết hợp `ProductCard.vue`.
  - Nút `[Mua lại toàn bộ theo chu kỳ]` giúp đẩy nhanh toàn bộ gói bảo dưỡng vào giỏ hàng.

---

### MÀN HÌNH 19: `LichSuDonHang.vue` (Lịch sử Đơn hàng & Hóa đơn Chứng từ)
- **Mục tiêu:** Quản lý toàn bộ các đơn hàng đã mua, tải về file PDF hóa đơn VAT và phiếu bảo hành chính hãng.
- **Bố cục chính:**
  - Bộ lọc theo thời gian, trạng thái đơn (Đã giao, Đang vận chuyển).
  - Bảng danh sách đơn hàng có mã đơn, ngày mua, tổng giá trị, nút xem chi tiết đơn.

---

### MÀN HÌNH 20: `XacThuc.vue` (Cổng Đăng nhập & Xác thực Thợ máy/Chủ tàu)
- **Mục tiêu:** Màn hình đăng nhập, đăng ký tài khoản đại lý hoặc khách hàng cá nhân.
- **Bố cục chính:**
  - Thiết kế 2 nửa màn hình (Split screen):
    - Nửa trái: Hình ảnh du thuyền công nghệ biển sâu kèm slogan chất lượng Nhật Bản.
    - Nửa phải: Biểu mẫu đăng nhập sạch sẽ, nhập số điện thoại hoặc email, đăng nhập nhanh qua Google hoặc tài khoản thợ máy.
  - Bo góc form: `12px`, input chuẩn `8px`.
