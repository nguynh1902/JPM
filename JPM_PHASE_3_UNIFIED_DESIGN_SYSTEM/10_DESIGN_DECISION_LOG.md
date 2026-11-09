# JPM CLIENT – TÀI LIỆU 10: DESIGN DECISION LOG (NHẬT KÝ QUYẾT ĐỊNH THIẾT KẾ)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. MỤC ĐÍCH TÀI LIỆU

Nhật ký quyết định thiết kế (Design Decision Log) đóng vai trò là "Bản hiến pháp kỹ thuật" ghi nhận toàn bộ các quyết sách đã được Product Owner (PO) phê chuẩn chính thức, đồng thời tổng hợp các câu hỏi kiến trúc đang chờ PO cho ý kiến định hướng trước khi bước vào giai đoạn thực thi mã nguồn.

---

## 2. DANH MỤC CÁC QUYẾT ĐỊNH ĐÃ ĐƯỢC PRODUCT OWNER PHÊ DUYỆT (OFFICIALLY APPROVED)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                      BẢNG TỔNG HỢP CÁC QUYẾT ĐỊNH ĐÃ PHÊ DUYỆT                         │
├─────────┬───────────────────────────┬──────────────┬───────────────────┬───────────────┤
│ MÃ ĐỊNH │ VẤN ĐỀ THIẾT KẾ           │ PHƯƠNG ÁN    │ NỘI DUNG CHỐT     │ TRẠNG THÁI    │
├─────────┼───────────────────────────┼──────────────┼───────────────────┼───────────────┤
│ DEC-01  │ Hệ thống Border Radius    │ Phương án A  │ Card/Modal: 12px; │ ĐÃ PHÊ DUYỆT  │
│         │ toàn bộ phân hệ Client    │              │ Button/Input: 8px;│ (Approved)    │
│         │                           │              │ Badge/Tag: 6px;   │               │
│         │                           │              │ Avatar/Circle: 50%│               │
├─────────┼───────────────────────────┼──────────────┼───────────────────┼───────────────┤
│ DEC-02  │ Trải nghiệm bản vẽ sơ đồ  │ Phương án A  │ Đồng bộ 2 chiều:  │ ĐÃ PHÊ DUYỆT  │
│         │ Exploded Diagram Hotspots │              │ Ghim <--> Bảng;   │ (Approved)    │
│         │                           │              │ Hover xem nhanh,  │               │
│         │                           │              │ Click cuộn dòng   │               │
├─────────┼───────────────────────────┼──────────────┼───────────────────┼───────────────┤
│ DEC-03  │ Chuẩn hóa thành phần dùng │ Phương án A  │ Xây dựng 2 core:  │ ĐÃ PHÊ DUYỆT  │
│         │ chung (Shared Components) │              │ - ProductCard.vue │ (Approved)    │
│         │                           │              │ - JpmSelect.vue   │               │
├─────────┼───────────────────────────┼──────────────┼───────────────────┼───────────────┤
│ DEC-04  │ Thống nhất thư viện biểu  │ Phương án A  │ 100% Bootstrap    │ ĐÃ PHÊ DUYỆT  │
│         │ tượng toàn Client         │              │ Icons (bi bi-*);  │ (Approved)    │
│         │                           │              │ Xóa sạch Boxicons │               │
└─────────┴───────────────────────────┴──────────────┴───────────────────┴───────────────┘
```

### Chi tiết thực thi các quyết định đã duyệt:
1. **DEC-01 (Border Radius):** Tuyệt đối loại bỏ toàn bộ các giá trị tự phát như `4px`, `5px`, `15px`, `25px`, `30px`, `50px` trong CSS cũ. Mọi component mới phải dùng biến `--jpm-radius-*`.
2. **DEC-02 (Interactive Hotspots):** Khai thác trực tiếp tọa độ phần trăm từ bảng `chi_tiet_so_dos` (`x`, `y`, `width`, `height`), kết nối qua `useDiagramStore` của Pinia.
3. **DEC-03 (Shared Components):**
   - `ProductCard.vue`: Khắc phục lỗi khuyết nút giỏ hàng khi sản phẩm cần báo giá; luôn đảm bảo chân thẻ đối xứng 100%.
   - `JpmSelect.vue`: Hỗ trợ tìm kiếm, xóa chọn, tải dữ liệu động và phụ thuộc chuỗi 4 cấp (Hãng $\to$ Loại máy $\to$ Công suất $\to$ Model).
4. **DEC-04 (Icon System):** Thay thế toàn bộ 41 vị trí lỗi icon Boxicons tại `TraCuuBaoGia.vue`, `TraCuuDonHang.vue` và `AccountSidebar.vue` bằng Bootstrap Icons.

---

## 3. CÁC QUYẾT ĐỊNH CÒN CHỜ Ý KIẾN PRODUCT OWNER (PENDING VALIDATION)

Đội ngũ kiến trúc sư trình Product Owner xem xét và phê duyệt 4 điểm định hướng nghiệp vụ sau trước khi bước vào Phase 4:

### ĐIỂM CHỜ DUYỆT 01 (DEC-PENDING-01): Quy trình Đơn hàng Hỗn hợp (Mixed Order Flow)
- **Vấn đề:** Khi giỏ hàng có cả sản phẩm đã có giá (ví dụ: Cánh chân vịt 3.850.000 đ) và sản phẩm chưa có giá / chờ báo giá (ví dụ: Trục khuỷu Yanmar hiếm).
- **Đề xuất của Đội ngũ Thiết kế (Khuyến nghị):** Cho phép khách hàng tách đơn hoặc tạo **"Đơn hàng hỗn hợp"** – Khách có thể thanh toán cọc trước cho phần hàng có sẵn để xưởng giữ hàng, đồng thời hệ thống tự động sinh phiếu yêu cầu báo giá cho phần linh kiện còn lại.
- **Phương án dự phòng:** Bắt buộc khách hàng phải đợi Kỹ sư phản hồi toàn bộ báo giá rồi mới thanh toán gộp một lần.

---

### ĐIỂM CHỜ DUYỆT 02 (DEC-PENDING-02): Hành vi Sơ đồ rã máy trên Điện thoại siêu nhỏ (< 400px)
- **Vấn đề:** Trên màn hình điện thoại rất nhỏ (như iPhone SE 375px), bản vẽ kỹ thuật chi tiết nhiều ốc vít có thể khó nhìn nếu hiển thị chung với bảng.
- **Đề xuất của Đội ngũ Thiết kế (Khuyến nghị):** Áp dụng mô hình **Tabbed Workspace** (Tab 1 xem Bản vẽ toàn màn hình có ghim chạm; Tab 2 xem Bảng danh sách) kèm thanh Bottom Sheet nổi tóm tắt linh kiện khi bấm vào ghim.
- **Phương án dự phòng:** Khóa chế độ xem sơ đồ ở chiều ngang (Landscape mode) hoặc khuyến cáo mở trên Tablet/Desktop.

---

### ĐIỂM CHỜ DUYỆT 03 (DEC-PENDING-03): Phạm vi của Thanh tìm kiếm nhanh OEM Search
- **Vấn đề:** Thanh tìm kiếm nhanh trên Header chỉ nên tìm theo Mã OEM và Tên phụ tùng, hay nên tìm kiếm cả Số khung động cơ (Serial Number / Engine Model)?
- **Đề xuất của Đội ngũ Thiết kế (Khuyến nghị):** Hỗ trợ tìm đa năng cả Mã OEM, Tên phụ tùng và Model động cơ, có tab phân loại rõ ràng trong hộp kết quả gợi ý xổ xuống (Autocomplete Dropdown).

---

### ĐIỂM CHỜ DUYỆT 04 (DEC-PENDING-04): Chế độ Giao diện Ban đêm (Marine Dark Mode)
- **Vấn đề:** Các kỹ sư hàng hải làm việc ca đêm trên tàu thuyền thường có nhu cầu giao diện tối để không bị lóa mắt.
- **Đề xuất của Đội ngũ Thiết kế:** Phase 4 tập trung hoàn thiện giao diện chuẩn **Modern Luxury Marine (Deep Navy + Crisp Slate Surface)** đạt độ tương phản chuẩn mực. Tính năng chuyển đổi Dark/Light mode toàn diện sẽ được xếp vào lộ trình Phase 5 sau khi hoàn tất nghiệm thu các chức năng cốt lõi.
