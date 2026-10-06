# NGUYÊN TẮC CỐT LÕI DỰ ÁN JPM (PROJECT PRINCIPLES)

Tài liệu này là **kim chỉ nam tối cao** cho toàn bộ quá trình phân tích, thiết kế, phát triển và vận hành hệ thống JPM. Mọi tài liệu, tính năng, dòng code và quyết định kiến trúc được đưa ra sau này **TUYỆT ĐỐI KHÔNG ĐƯỢC** đi ngược lại 5 nguyên tắc dưới đây.

---

## 1. BUSINESS FIRST (Nghiệp Vụ Là Số 1)
Hệ thống phần mềm sinh ra để phục vụ chính xác mô hình kinh doanh ngoài đời thực, chứ không phải gò ép doanh nghiệp theo khuôn mẫu phần mềm.
*   **Không áp đặt mô hình TMĐT đại trà:** JPM không phải Shopee, Lazada hay Amazon. Đây là hệ thống chuyên biệt cho mảng phụ tùng kỹ thuật tàu thuyền.
*   **Đúng bản chất giao dịch:** Khách hàng (Thợ, Garage, Doanh nghiệp) mua phụ tùng để sửa chữa, không mua vì cảm xúc hay săn sale. Do đó, hệ thống không cần Voucher, Flash Sale, Tích điểm, Livestream hay Gamification.

## 2. PART CATALOG FIRST (Lấy Tra Cứu Làm Trung Tâm)
Giá trị lớn nhất và cốt lõi nhất của hệ thống **KHÔNG phải là chức năng bán hàng**, mà là giúp khách hàng **TÌM ĐÚNG PHỤ TÙNG**.
*   **Trải nghiệm tìm kiếm là linh hồn:** Hệ thống phải xoay quanh 3 cách tìm kiếm: (1) Mã phụ tùng trực tiếp, (2) Bộ lọc thông số nhanh, (3) Tra cứu Catalog qua Sơ đồ nổ (Exploded Diagram).
*   **Tính chính xác kỹ thuật:** Việc liên kết đúng Thương hiệu → Model → Sơ đồ cụm → Bản vẽ nổ → Reference Number → Mã phụ tùng là sống còn. Không được phép có sai sót trong luồng này.
*   **Bán hàng là hệ quả:** Khi khách hàng tìm được chính xác thứ họ cần thông qua Catalog, việc đặt hàng chỉ là bước xác nhận thủ tục cuối cùng.

## 3. DATA IS THE ASSET (Dữ Liệu Là Tài Sản Cốt Lõi)
Tài sản lớn nhất của dự án không nằm ở giao diện hay code, mà nằm ở khối lượng dữ liệu khổng lồ do chủ doanh nghiệp thu thập (Model máy, Mã phụ tùng, Sơ đồ, Mã thay thế/Supersession).
*   **Tối ưu công cụ nhập liệu (PIM):** Hệ thống Admin phải ưu tiên số 1 cho các công cụ (Tools) giúp chủ doanh nghiệp nhập liệu, map sơ đồ, import dữ liệu từ Excel một cách cực kỳ nhanh chóng và chính xác.
*   **Khả năng mở rộng dữ liệu vô hạn:** Website sẽ lớn lên từng ngày nhờ dữ liệu này. Cấu trúc DB phải đáp ứng việc phình to dữ liệu Catalog mà không làm giảm tốc độ truy vấn.

## 4. SIMPLE WORKFLOW (Quy Trình Vận Hành Tối Giản)
Hệ thống vận hành theo mô hình trung chuyển (Cross-docking), không duy trì tồn kho lớn.
*   **Mua hàng đơn giản:** Gom chung luồng Giỏ hàng thành một Request duy nhất để Admin báo giá / gom đơn (PO) đặt sang Nhật. Không làm giỏ hàng kép (Dual-path) rườm rà.
*   **Vận hành tinh gọn:** Loại bỏ hoàn toàn quy trình Nhập kho - Xuất kho - Kiểm kê phức tạp của các hệ thống ERP thông thường.
*   **Tối ưu Cross-docking:** Hàng từ Nhật về đến VN chỉ được phân loại và đóng gói giao ngay cho khách (B2B miễn phí, B2C thu phí). Không lưu kho.

## 5. NO FEATURE CREEP (Nói "KHÔNG" Với Các Tính Năng Thừa)
Chỉ giữ lại những gì thực sự tạo ra giá trị cho mô hình kinh doanh JPM.
*   **Kiến trúc sạch:** Tuân thủ triệt để Laravel, Vue 3 (Options API), Bootstrap 5. Sử dụng 100% Template Sneat và TheEvent. Tuyệt đối không tự sáng chế UI hay dùng thư viện thừa.
*   **Cắt bỏ tính năng rác:** Không làm module Review/Rating sản phẩm (thợ mua phụ tùng kỹ thuật không cần review), không Affiliate, không Marketplace đa nhà bán. Tập trung toàn lực xử lý mượt mà luồng Catalog và Gom đơn.
*   **Yêu cầu đối với Developer/AI:** Nếu được yêu cầu làm một tính năng vi phạm nguyên tắc "No Feature Creep", Developer/AI có nghĩa vụ phải cảnh báo và từ chối thực hiện trừ khi có quyết định bắt buộc từ cấp cao nhất.

---
*Tài liệu này là nền tảng để xây dựng bộ tài liệu V2 của dự án JPM.*
