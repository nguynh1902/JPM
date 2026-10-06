# LỘ TRÌNH PHÁT TRIỂN & REFACTOR KIẾN TRÚC (DEVELOPMENT & MIGRATION PLAN)

Tài liệu này xác định lộ trình kép: **Tái cấu trúc hệ thống hiện tại (Refactor/Migration)** và **Phát triển Module mới**. Tuyệt đối tuân thủ nguyên tắc: Không code tính năng mới cho đến khi hoàn thành tối thiểu Phase 0 và Phase 1 của kế hoạch Migration.

---

## PHẦN 1: KẾ HOẠCH MIGRATION KIẾN TRÚC (REFACTORING PLAN)
Dựa trên kết quả Audit, code hiện tại đang đi chệch hướng DDD. Dưới đây là các bước để nắn lại cấu trúc:

### Phase 0: Audit & Preparation (Tái cấu trúc thư mục)
*   **Mục tiêu:** Không thay đổi chức năng. Thiết lập cấu trúc thư mục mới.
*   **Hành động:** 
    1. Tạo sẵn các thư mục `app/Domain/...` (Backend) và `src/modules/...` (Frontend). 
    2. Di chuyển dần dần các Model và Controller hiện tại (đang dùng Flat MVC) vào đúng Domain, cập nhật lại Namespace.

### Phase 1: Database + Index + Query Optimization
*   **Mục tiêu:** Vá ngay các lỗ hổng Performance tại tầng Database. Ngăn chặn N+1 Query.
*   **Hành động:** 
    1. Thêm Index/Composite Index cho các cột hay query (VD: `thuong_hieu_id`, `loai_may_id`).
    2. Xóa bỏ mọi lệnh `SELECT *`, chỉ lấy các cột cần thiết. Thêm `with()` (Eager Loading) để fix N+1.

### Phase 2: API & Controller Optimization
*   **Mục tiêu:** "Request nhỏ -> Response nhỏ -> Query đúng".
*   **Hành động:** Áp dụng **Cursor Pagination** cho danh sách phụ tùng. Tạo các **API Resources** chuẩn hóa. Tách Logic từ Controller ra Service.

### Phase 3: Vue Query + Frontend Re-architecture
*   **Mục tiêu:** Chuyển đổi sang Server State (Vue Query) để cải thiện UX/UI.
*   **Hành động:** Cài đặt `@tanstack/vue-query`. Thay thế các hàm `axiosClient.get` ở `onMounted` bằng `useQuery`.

### Phase 4: Tích hợp Meilisearch & Redis Caching
*   **Mục tiêu:** Tìm kiếm Full-text < 50ms (Meilisearch) và giảm Read Load (Redis).
*   **Hành động:** Cài Laravel Scout, Meilisearch Driver. Đồng bộ dữ liệu Phụ tùng lên Meilisearch. Dùng Redis Cache cho Cây danh mục (Marine Catalog).

---

## PHẦN 2: LỘ TRÌNH PHÁT TRIỂN MODULE (MODULE DEVELOPMENT PLAN)
Chỉ thực hiện sau khi Kiến trúc (Phần 1) đã ổn định. Mỗi Phase phải đạt "Definition of Done" (DoD).

### PHASE 1: IAM & NỀN TẢNG CỐT LÕI (CORE FOUNDATION)
*   **Modules:** Authentication (Sanctum), Roles & Permissions (Spatie), User Profile.
*   **DoD:** User đăng nhập thành công. Phân quyền hoạt động chính xác theo Role.

### PHASE 2: BỘ DỮ LIỆU CỐT LÕI (MARINE CATALOG & PIM)
*   **Modules:** Vehicle Catalog (Brands, Engines, Models), PIM (Parts, Categories).
*   **DoD:** Tạo được Model máy thủy và gán Phụ tùng vào Model. Các API CRUD hoạt động qua Service Layer.

### PHASE 3: TRA CỨU & TRẢI NGHIỆM KHÁCH HÀNG (SEARCH ENGINE & DIAGRAMS)
*   **Modules:** Sơ Đồ Nổ (Diagrams & Image Mapping), Garage (Xe của tôi). Tra cứu qua Meilisearch.
*   **DoD:** Giao diện Sơ đồ 3 cột hoạt động mượt mà (Click Ref trên ảnh -> Highlight bảng Phụ tùng). 

### PHASE 4: BÁN HÀNG & CHỐT ĐƠN (CART & ORDERS)
*   **Modules:** Giỏ hàng, Đơn hàng, Thanh toán.
*   **DoD:** Một đơn hàng được lưu thành công vào DB kèm địa chỉ và phí ship (Phân biệt B2B/B2C).

### PHASE 5: VẬN HÀNH SAU BÁN HÀNG (PROCUREMENT & CROSS-DOCKING)
*   **Modules:** Purchase Orders (Gom đơn PO đi Nhật), Cross-Docking Engine (Máy quét mã vạch).
*   **DoD:** Admin quét mã vạch lô hàng về, pop-up hiện yêu cầu chia hàng vào đúng đơn Khách. Trạng thái Đơn hàng chuyển "Sẵn sàng giao".

### PHASE 6: ADMIN TOOLS ĐỈNH CAO & THEO DÕI
*   **Modules:** Import Data Pipeline (Upload Excel), Audit & Activity Logging.
*   **DoD:** Upload 1 file Excel 50MB xử lý ngầm (Background Job) bằng Queue/Worker thành công không bị Timeout.
