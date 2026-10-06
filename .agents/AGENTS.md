# Project Rules: JPM ERP System

## 1. UI/UX Standard (10/10 Premium Quality)
- **Aesthetic**: All user interfaces must be 10/10, modern, clean, and timeless. Avoid "childish" or overly colorful designs. Use the standard established in `HangMay.vue` and `LoaiMay.vue`.
- **Typography & Hierarchy**: Emphasize important data (e.g., Names) with `fw-bold text-dark`. De-emphasize secondary data (e.g., Codes) with `text-muted` or `text-secondary`.
- **Table Alignment**: 
  - Center align (`text-center`) STT, Status badges, and Action buttons. 
  - Provide explicit column widths (e.g., `style="width: 5%"`).
- **Badges & Colors**: Use custom pastel badges (`.bg-pastel-success`, `.bg-pastel-danger`) with uppercase text for statuses instead of default harsh background colors. (Green = Active, Red = Inactive/Locked).
- **Action Buttons**: Use ghost/icon buttons (`.action-btn`) without borders, centered horizontally, with a hover effect. Avoid dropdowns for primary actions (Edit, Delete) unless there are more than 3 actions.
- **Table Headers**: Make headers subtle (`text-transform: uppercase; font-size: 0.75rem; color: #a1acb8;`).
- **Image/Avatar/Logo**: Tuyệt đối không tự chế style tròn/vuông. Luôn sử dụng đúng cấu trúc khối ảnh chuẩn trong bảng của `ThuongHieu.vue`: Thẻ `div` bọc ngoài (`d-inline-flex justify-content-center align-items-center border rounded`, kích thước `60x60px`, nền `#fff`), chứa thẻ `img` (`max-height: 40px; max-width: 50px; object-fit: contain;`) và có icon placeholder (`bx bx-image`) nếu rỗng.
- **Form Layout**: Luôn gom nhóm các input liên quan vào chung 1 hàng (row) để tối ưu Grid. Đặc biệt, **Trường Trạng Thái (Status) ưu tiên thiết lập kích thước `col-md-5`** (hoặc tỷ lệ phù hợp) để đảm bảo độ dài Menu Trạng Thái không bị cắt chữ (như lỗi hiển thị "Ho..."). Không sử dụng class `.push-down` để Menu thả xuống (overlay) hoạt động theo mặc định.
- **Modal Size**: Kích thước chiều rộng của Modal phải đồng nhất trên toàn hệ thống (mặc định dùng `.modal-dialog` chuẩn 500px). Modal chỉ được phép thay đổi chiều cao (dài/ngắn) tùy theo số lượng input. Tuyệt đối KHÔNG dùng `.modal-lg` hay `.modal-xl` trừ khi form có quá nhiều trường dữ liệu phức tạp cần không gian rộng.
- **Global Consistency (Ngoại trừ Dashboard)**: Mọi trang thuộc khu vực Admin (Quản lý Đơn hàng, v.v...) BẮT BUỘC phải tuân thủ tuyệt đối phong cách thiết kế, tỷ lệ kích thước, nút bấm và màu sắc đã được thiết lập chuẩn ở các trang `Model.vue`, `LoaiMay.vue`, `ThuongHieu.vue`. Tuyệt đối không tự ý sáng tạo layout mới nếu không có yêu cầu.
- **Data & Architecture Integrity**: Code phải tuân thủ tuyệt đối cấu trúc Database hiện tại, logic mối quan hệ (relationships) giữa các entity/page, và bám sát tài liệu đặc tả (documentation) của dự án. Không tự ý thay đổi luồng dữ liệu cốt lõi.

# Frontend Development Rules for JPM Project

## 1. Responsive First (Bắt buộc)
Tất cả các giao diện Frontend (UI/UX) khi được tạo ra hoặc chỉnh sửa đều PHẢI đảm bảo tính Responsive 100%. 
- Sử dụng triệt để hệ thống Grid của Bootstrap (ví dụ: `col-12`, `col-md-6`, `col-lg-3`, `col-xl-9`).
- Đảm bảo giao diện hiển thị hoàn hảo và không bị tràn khung trên các thiết bị Di động (Mobile), Máy tính bảng (Tablet) và Màn hình lớn (Desktop).
- Các bảng dữ liệu lớn (Table) bắt buộc phải bọc trong thẻ `div` với class `.table-responsive` để có thể cuộn ngang trên điện thoại.
- Các thanh điều hướng (Menu, Tabs) cần có phương án thu gọn (Collapse/Dropdown) hoặc cuộn ngang khi xem ở màn hình nhỏ.
- Luôn kiểm tra giao diện ở nhiều kích thước (breakpoints: xs, sm, md, lg, xl, xxl) trước khi hoàn thiện.

## 2. UI Prototype Standard (Quy tắc đồng bộ giao diện Admin)
*   **Reference Pages (Chuẩn UI gốc):** Mọi trang Admin mới BẮT BUỘC phải kế thừa 1 trong 2 Layout chuẩn sau đây:
    - **Layout Loại 1 (Split Layout 4-8):** Dành cho các trang có Form ngắn gọn, ít trường nhập liệu (VD: Tên, Mã, Trạng thái). Không cần Bộ lọc phức tạp.
      - *Cấu trúc:* Khung màn hình chia làm 2 cột: Cột trái (`col-md-4`) chứa Form Thêm/Sửa nằm cố định. Cột phải (`col-md-8`) chứa Bảng Danh sách.
      - *Trang tham chiếu gốc:* `ThuongHieu.vue`, `LoaiMay.vue`.
    - **Layout Loại 2 (Full-width Layout & Modal):** Dành cho các trang có Form dài, nhiều dữ liệu, cần không gian hiển thị rộng và có Bộ Lọc (Filter) nhiều tiêu chí.
      - *Cấu trúc:* Bảng Danh sách nằm trong Card chiếm toàn bộ chiều rộng (`100%`). Thanh Bộ lọc (Filter Bar) tích hợp bên trong phần đầu của Card Bảng. Form nhập liệu được đặt ẩn trong một Popup Modal (kích thước chuẩn 500px).
      - *Trang tham chiếu gốc:* `Model.vue`, `DanhSachNV.vue`.
*   **Chiến Lược Code Nhanh (Copy & Paste):** TUYỆT ĐỐI KHÔNG VIẾT CODE LẠI TỪ ĐẦU cho các trang Admin.
    1. Xác định mức độ phức tạp của trang mới (Cần Layout Loại 1 hay Loại 2).
    2. Nếu là Layout 1: Mở `ThuongHieu.vue` hoặc `LoaiMay.vue`, **Copy 100% toàn bộ source code**, dán sang file mới, sau đó chỉ việc sửa lại biến data, nội dung Text, v-model và API.
    3. Nếu là Layout 2: Mở `Model.vue` hoặc `DanhSachNV.vue`, **Copy 100% toàn bộ source code**, dán sang file mới, sau đó sửa lại thông tin, field tìm kiếm và các cột table tương ứng.
*   **Thứ tự ưu tiên thiết kế UI mới:** 
    1.  Tuân thủ tuyệt đối chiến lược Copy & Paste ở trên để giữ nguyên bộ khung (Table, Form, Modal, Badge, Button, Filter).
    2.  Nếu UI phức tạp đến mức cả 2 Layout trên không thể đáp ứng, mới tìm trong **Template Sneat**.
    3.  Chỉ khi cả 2 nguồn trên không có mới được phép **Thiết kế Component mới**.
*   **Tính Đồng Bộ:** Không tự ý sáng tạo giao diện hay trộn lẫn 2 Layout. Mọi trang mới tạo ra bắt buộc phải mang lại cảm giác "cùng một mẹ sinh ra" với Reference Pages.

## 3. Mandatory UI Audit Workflow (Quy trình Bắt buộc)
Mọi Page trước khi được code mới hoặc chỉnh sửa đều phải trải qua 5 bước "UI Audit" và chờ Product Owner phê duyệt báo cáo.
*   **Bước 1. Đối chiếu Business:** Chức năng là gì? Ai dùng? Mục đích nghiệp vụ?
*   **Bước 2. Đối chiếu Workflow:** Nằm ở bước nào? Input/Output?
*   **Bước 3. Đối chiếu Database:** Sai/Thiếu/Dư trường dữ liệu? Sai quan hệ? Sai kiểu dữ liệu?
*   **Bước 4. Đối chiếu Pages & Features:** Thiếu Form/Table/Button/Action?
*   **Bước 5. Đối chiếu UI Reference:** Không tự ý sáng tạo. Tuân thủ Reference Pages -> Sneat Template -> Code mới.
*   **Output bắt buộc:** Phải xuất báo cáo theo form chuẩn (Đánh giá ✅ Đúng / ⚠ Thiếu / ❌ Sai, và Danh sách thay đổi Level 1/2/3). Chỉ tiến hành code sau khi được phê duyệt.

## 4. Client UI Design System (Global CSS over Bootstrap)
Đối với các giao diện hướng Khách hàng (Client/Guest pages):
- **Phong cách Hiện Đại, Gọn Gàng (Modern Minimalism):** Mọi thiết kế Client phải hướng tới sự "thanh thoát, mảnh mai, thoáng đãng". Sử dụng font chữ Geometric Sans-serif (Montserrat) làm chủ đạo. Chữ trên Menu, Heading lớn cần ép nét mỏng (Thin/Light 300-400), viết hoa (Uppercase) và kéo giãn khoảng cách chữ (letter-spacing rộng). Tuyệt đối tránh các chi tiết rườm rà, nét quá đặc hay icon thô kệch.
- **Không viết CSS cục bộ (`<style scoped>`)** cho các component có tính tái sử dụng cao (Cards, Buttons, Inputs, Badges...).
- **Tận dụng tối đa Bootstrap Classes**: Mọi thành phần UI cần ưu tiên sử dụng các class tiêu chuẩn của Bootstrap (ví dụ: `.card`, `.form-control`, `.form-select`, `.badge`, `.btn`).
- **Ghi đè (Override) tại `main.css`**: Các định dạng đặc thù của JPM Theme (như bo góc 16px cho card, màu hover đỏ, custom select, tag phổ biến) đã được tích hợp và ghi đè trực tiếp lên Bootstrap tại file `src/assets/client/css/main.css`.
- **Tuyệt đối không tự ý dùng native `<select>`**: Giao diện Client yêu cầu thẩm mỹ Premium (10/10), không sử dụng thẻ `<select>` mặc định của HTML. Bắt buộc dùng Custom Dropdown component hoặc cấu trúc HTML/CSS `<ul><li>` đã được định nghĩa chuẩn trong `main.css` để tránh lỗi hiển thị màu mặc định (xanh dương) của hệ điều hành.

## 6. Enterprise UX & Performance Gold Standards (Cốt lõi Trải nghiệm)
Toàn bộ dự án phải tuân thủ nghiêm ngặt các tiêu chuẩn UX/UI cấp độ Enterprise để đảm bảo tốc độ và trải nghiệm mượt mà nhất:
*   **Optimistic UI (Cập nhật tức thời 0ms):** Khi Thêm/Sửa/Xóa dữ liệu, KHÔNG gọi lại API danh sách (fetchData) ngay lập tức. Bắt buộc phải cập nhật/thêm phần tử trực tiếp vào mảng dữ liệu (Local State Mutation) để giao diện hiển thị ngay lập tức (instant feedback).
*   **Skeleton Loading & State Separation:** Luôn tách biệt biến trạng thái Loading. Tuyệt đối không dùng chung 1 biến "isLoading" cho cả bảng danh sách và nút Submit. Dùng "isFetching" cho bảng (hiển thị vòng xoay/skeleton) và "isSubmitting" cho nút bấm. Không để bảng dữ liệu bị chớp giật khi đang thao tác.
*   **Ngăn chặn Deadlock API (Concurrency Control):** Chú ý không gọi đồng thời quá nhiều API nặng lúc khởi tạo trang (Mounted/Created) để tránh làm treo Server cục bộ (php artisan serve). Dùng cờ kiểm tra (VD: "if(!authStore.user)") để giảm tải.
*   **Micro-interactions & Modern Alerts:** Tuyệt đối KHÔNG dùng các hộp thoại mặc định của trình duyệt ("window.alert", "window.confirm"). Bắt buộc tích hợp "SweetAlert2" cho các xác nhận quan trọng (Xóa, Cảnh báo) và "Vue Toaster" cho các thông báo nhanh (Thành công/Thất bại).
*   **Debounce & Validation:** Các ô tìm kiếm phải có Debounce. Form phải chặn lỗi từ Frontend trước khi Submit để tối ưu đường truyền.

## 7. Advanced Performance & Reliability (Enterprise-Grade)
Dự án được triển khai cho các khách hàng doanh nghiệp lớn với dữ liệu khổng lồ, do đó bắt buộc phải áp dụng các chiến lược tối ưu hóa và chống chịu rủi ro (Resilience) ở mức cao nhất:
*   **Stale-While-Revalidate (SWR) & Caching với Vue Query:** 
    - Để tối ưu UX, hệ thống áp dụng kiến trúc **Stale-While-Revalidate** bằng thư viện `@tanstack/vue-query` thay thế cho việc pre-fetch toàn cục (Promise.all khổng lồ) ở Layout.
    - **BẮT BUỘC:** Tại tất cả các trang View (như `ThuongHieu.vue`, `DanhSach.vue`), KHÔNG dùng API call trực tiếp (`axiosClient.get`) trong hook `mounted`. Bắt buộc phải sử dụng `useQuery` để tự động lấy dữ liệu từ Cache (nếu có) và đồng thời fetch ngầm dưới nền.
    - **Optimistic UI:** Mọi thao tác Thêm/Sửa/Xóa phải sử dụng `useMutation` kết hợp với `queryClient.setQueryData` hoặc `queryClient.invalidateQueries` để cập nhật giao diện tức thời mà không cần chờ Server phản hồi. Không dùng Pinia Cache (`erpCacheStore`) để chứa dữ liệu bảng nữa.
*   **Lazy Loading & API Throttling (Chống Deadlock Server):**
    - TUYỆT ĐỐI KHÔNG fetch toàn bộ dữ liệu lọc (Filters như Model, Loại Động Cơ, Năm) ngay khi vừa load trang nếu chưa xác định được Parent (Thương Hiệu).
    - **BẮT BUỘC** áp dụng cờ `enabled` trong `useQuery` để khóa các API phụ thuộc. Ví dụ: `enabled: computed(() => !!selectedBrand.value)`.
    - Điều này giúp trì hoãn (Lazy load) việc gọi API cho đến khi User thực sự tương tác, giải phóng băng thông lúc khởi tạo trang và tránh hiện tượng thắt cổ chai (Deadlock) ở Backend.
*   **Offline Support & Transaction Resilience (Chống gián đoạn kết nối):** 
    - Xử lý triệt để rủi ro mất mạng (Wifi/4G) hoặc cúp điện đột ngột khi đang thực hiện giao dịch (nhập kho, bán hàng, thanh toán...). 
    - **Ở Frontend:** Các tác vụ tạo/sửa quan trọng phải được lưu tạm (Draft/Queue) ở IndexedDB hoặc LocalStorage. Nếu mất mạng, hệ thống phải báo trạng thái "Offline Mode" và tự động gửi lại (Sync) khi có mạng trở lại.
    - **Ở Backend:** Mọi luồng giao dịch liên kết chặt chẽ (như trừ kho, cộng tiền, lưu lịch sử) BẮT BUỘC phải đặt trong `DB::beginTransaction()` và `DB::rollBack()` để đảm bảo tính toàn vẹn dữ liệu (Data Integrity).
*   **Mandatory Implementation Plan (Kế hoạch trước khi Code):** 
    - **TRƯỚC KHI BẮT ĐẦU CODE** bất kỳ chức năng hoặc module mới nào, AI (Lập trình viên) BẮT BUỘC phải tạo ra một bản thiết kế hoặc tài liệu `implementation_plan.md` trình bày rõ: Logic nghiệp vụ, luồng dữ liệu (Data flow), đề xuất bảng DB mới (nếu có), và các công nghệ áp dụng.
    - **Duyệt trước khi làm:** Chỉ được phép tiến hành code thực tế sau khi Product Owner (User) đã đọc, góp ý và duyệt bản thiết kế này. Luôn chủ động tư vấn công nghệ xịn (Redis, WebSockets, RabbitMQ...) nếu thấy hệ thống có khả năng phình to.

## 8. Database Search & Scaling (Giai đoạn mở rộng)
Để đảm bảo tốc độ tra cứu phụ tùng và dữ liệu siêu nhanh (<50ms) cho hệ thống ERP lớn:
*   **Search Engine (Meilisearch):** Bắt buộc áp dụng Laravel Scout kết hợp Meilisearch cho các tính năng tìm kiếm cốt lõi (như Tra cứu phụ tùng) thay cho `LIKE / FULLTEXT` của MySQL để hỗ trợ sai chính tả, tìm kiếm tiếng Việt và tốc độ tức thì.
*   **Caching Strategy (Redis):** Áp dụng Redis Cache cho các dữ liệu tĩnh hoặc ít thay đổi nhưng bị gọi nhiều (Ví dụ: danh sách Thương Hiệu, Nhóm Phụ Tùng, Models). Tuân thủ TTL (Time-To-Live) phù hợp cho từng loại data.
*   **Database Indexing:** Mọi bảng lớn phải được đánh Index chiến lược ngay từ đầu (`thuong_hieu_id`, `nhom_phu_tung_id`, v.v...). Các quan hệ như "Phụ tùng thuộc Model nào" bắt buộc phải dùng bảng Pivot (vd: `phu_tung_models`) để truy xuất linh hoạt, không gộp data lỏng lẻo.
*   **Media Storage (Cloudinary):** Định hướng tách hình ảnh phụ tùng ra CDN ngoài (Cloudinary) thay vì lưu trữ ở server local để tối ưu tốc độ tải trang trên toàn cầu.

# 9. JPM — MASTER ARCHITECTURE & PERFORMANCE REFACTORING
* **Vai trò:** Senior Software Architect + Backend/Frontend Engineer + Database/Performance Architect.
* **Mục tiêu:** Xây dựng hệ thống có kiến trúc tốt, tốc độ cao, khả năng mở rộng mạnh (High Performance + Data Intensive + Scalable + Maintainable). Không chỉ viết code cho chạy.
* **Phân tích chủ động:** Phải chủ động phân tích và đề xuất giải pháp công nghệ phù hợp (Redis, Meilisearch, CDN, Queue, Replica, v.v.), không yêu cầu User tự quyết định.
* **Phân chia trách nhiệm:** 
  - MySQL: SOURCE OF TRUTH (Nghiệp vụ chính).
  - Meilisearch: CHỈ DÙNG CHO SEARCH (Part Number, Brand, Model...).
  - Redis: CHỈ DÙNG CHO CACHE (Navigation, Popular data).
  - Vue Query: SERVER STATE.
  - Pinia: CLIENT STATE.
  - CDN/Object Storage: Lưu trữ Media.
* **Quy trình BẮT BUỘC trước khi Refactor/Code:**
  1. Audit source code thực tế toàn bộ hệ thống (Database, Laravel, Vue, Search, Media, Performance).
  2. Báo cáo trực tiếp cho User trong khung chat: Tóm tắt Audit, Kiến trúc Mục tiêu và Kế hoạch Migration (KHÔNG cần tạo file tài liệu).
  3. Trình bày cho User và chờ phê duyệt.
* **Quy tắc khi code:** 
  - Khởi điểm: Modular Monolith + Clean Architecture + MySQL. Scale khi có nhu cầu thực tế.
  - Tối ưu Query: Tránh N+1, không SELECT *, luôn dùng Index, Pagination/Cursor.
  - Tối ưu API: Request nhỏ -> Response nhỏ -> Query đúng -> Cache đúng.
  - Tối ưu Search: P95 < 50ms, đồng bộ Index từ MySQL.
  - **Dọn dẹp rác (Clean Workspace):** TUYỆT ĐỐI không để lại các file nháp, script test (như test_*.php, *.js) ở thư mục gốc sau khi chạy xong. Sinh ra để test xong thì phải xóa đi ngay lập tức để không làm rác dự án.
  - KHÔNG được âm thầm sửa toàn bộ kiến trúc nếu thấy lỗi. Phải báo cáo: CURRENT -> PROBLEM -> PROPOSED -> WHY -> IMPACT -> MIGRATION PLAN.

# 10. JPM — NAMING CONVENTION (BẮT BUỘC TUYỆT ĐỐI)
Toàn bộ dự án phải tuân thủ quy tắc đặt tên **NHẤT QUÁN XUYÊN SUỐT** từ FE đến BE, từ tên file đến tên biến trong component.

## Quy tắc đặt tên File & Component
- **Tên file Vue (FE)** phải trùng khớp hoàn toàn với **Tên Model (BE)**, **Tên Controller (BE)**, và **Tên Route (API)**.
- **Bảng đối chiếu chuẩn:**

| FE Vue File | Vue Component `name:` | BE Controller | BE Model | API Route |
|---|---|---|---|---|
| `ThuongHieu.vue` | `ThuongHieu` | `ThuongHieuController.php` | `ThuongHieu.php` | `admin/thuong-hieu` |
| `DongCoModel.vue` | `DongCoModel` | `DongCoModelController.php` | `DongCoModel.php` | `admin/dong-co-model` |
| `PhuTung.vue` | `PhuTung` | `PhuTungController.php` | `PhuTung.php` | `admin/phu-tung` |
| `SoDoPhuTung.vue` | `SoDoPhuTung` | `SoDoPhuTungController.php` | `SoDoPhuTung.php` | `admin/so-do` |
| `SanPham.vue` | `SanPham` | `SanPhamController.php` | `SanPham.php` | `admin/san-pham` |
| `KhachHang.vue` | `KhachHang` | `KhachHangController.php` | `KhachHang.php` | `admin/khach-hang` |
| `NhanVien.vue` | `NhanVien` | `NhanVienController.php` | `NhanVien.php` | `admin/nhan-vien` |
| `Faq.vue` | `Faq` | `FaqController.php` | `Faq.php` | `admin/faq` |

## Quy tắc bắt buộc
1. **Vue `name:` PHẢI bằng tên file Vue** (VD: file `KhachHang.vue` → `name: "KhachHang"`). Tuyệt đối KHÔNG đặt tên khác (VD: `DanhSachKH`, `KhachHangAdmin`, `DanhSachPhuTung`...).
2. **KHÔNG được dùng hậu tố Admin/Admin** trong component name (VD: ~~`TinTucAdmin`~~, ~~`VoucherAdmin`~~ → dùng `TinTuc`, `Voucher`).
3. **Khi tạo Entity mới:** Tên Entity phải nhất quán từ DB migration → Model → Controller → Resource → FE Vue file → Vue component name → API Route.
4. **Tên DB cột (column)** là kỹ thuật nội bộ, KHÔNG bắt buộc phải đổi tên cột khi đổi tên hiển thị UI (Label). Việc đổi tên cột DB gây nguy hiểm dây chuyền toàn hệ thống.
5. **Route API** dùng kebab-case (VD: `admin/dong-co-model`), BE/FE file dùng PascalCase (VD: `DongCoModel`).

## Quy tắc đặt tên Hàm (Method Naming)
- **BE Laravel RESTful (apiResource):** BẮT BUỘC giữ nguyên 5 tên chuẩn của framework: `index()`, `store()`, `show()`, `update()`, `destroy()`. KHÔNG được đổi tên vì Laravel Router tự động map HTTP method vào đúng các hàm này — đổi tên là 404 ngay.
- **BE Custom Methods (ngoài RESTful):** Tên hàm PHẢI khớp với tên endpoint API mà FE đang gọi. Ví dụ: FE gọi `/public/dong-co-model` thì BE phải có hàm `dongCoModel()`. Dùng camelCase.
- **FE Vue Methods:** Giữ nguyên camelCase tiếng Anh cho các hàm lifecycle và utility phổ biến (`resetForm`, `openAddModal`, `confirmDelete`, `saveData`...). Không bắt buộc đổi sang tiếng Việt. Quan trọng là nhất quán trong cùng một component.
- **Quy tắc vàng:** Tên hàm FE gọi API (`axiosClient.get/post/put/delete`) và tên hàm BE xử lý request đó phải phản ánh cùng một hành động nghiệp vụ — dễ đọc, dễ trace khi debug.

## 11. Chuẩn Mẫu Code (Template) Bắt Buộc - Optimistic UI (0ms Updates)
Khi code chức năng Thêm/Sửa/Xóa ở Frontend (Vue.js), TUYỆT ĐỐI KHÔNG dùng axios trực tiếp và load lại trang. BẮT BUỘC sử dụng Pattern `useMutation` của `@tanstack/vue-query` để chèn/xóa phần tử trực tiếp vào mảng dữ liệu trên máy khách (Optimistic Update) nhằm mang lại trải nghiệm độ trễ bằng 0 (0ms) cho người dùng.

**Mẫu Code Chuẩn (Template Bắt Buộc):**
```javascript
// 1. DELETE — Optimistic UI
const deleteMutation = useMutation({
  mutationFn: async (id) => axiosClient.delete(`/admin/endpoint/${id}`),
  onMutate: async (id) => {
    await queryClient.cancelQueries({ queryKey: ['data-key'] });
    const previousData = queryClient.getQueryData(['data-key']);
    // Xóa lập tức trên giao diện
    queryClient.setQueryData(['data-key'], (old) => {
      const list = Array.isArray(old) ? old : (old?.data || []);
      return list.filter(item => item.id !== id);
    });
    return { previousData };
  },
  onError: (err, id, context) => {
    queryClient.setQueryData(['data-key'], context.previousData); // Phục hồi nếu lỗi
    toaster.error("Lỗi khi xóa!");
  }
});

// 2. SAVE (Thêm/Sửa) — Optimistic UI
const saveMutation = useMutation({
  mutationFn: async (payload) => {
    return payload.id 
      ? axiosClient.put(`/admin/endpoint/${payload.id}`, payload)
      : axiosClient.post('/admin/endpoint', payload);
  },
  onMutate: async (newRecord) => {
    await queryClient.cancelQueries({ queryKey: ['data-key'] });
    const previousData = queryClient.getQueryData(['data-key']);
    // Chèn lập tức trên giao diện (Lưu ý: Thêm mới thì chèn vào CUỐI (Append) để khớp vị trí Server)
    queryClient.setQueryData(['data-key'], (old) => {
      const list = Array.isArray(old) ? old : (old?.data || []);
      if (newRecord.id) {
        return list.map(item => item.id === newRecord.id ? { ...item, ...newRecord } : item);
      }
      return [...list, { ...newRecord, id: Date.now() }];
    });
    return { previousData };
  },
  onError: (err, vars, context) => {
    queryClient.setQueryData(['data-key'], context.previousData);
  }
});
```
Mọi file Vue mới tạo ra hoặc sửa lại đều phải tuân thủ đúng khối mã chuẩn này!
