# KIẾN TRÚC & TIÊU CHUẨN PHẦN MỀM (ARCHITECTURE & STANDARDS)

> **Nguyên tắc chi phối:** No Feature Creep, High Performance, Data Intensive.

Mọi Lập trình viên và AI tham gia vào dự án JPM bắt buộc phải tuân thủ tuyệt đối các chuẩn mực kỹ thuật và kiến trúc sau đây. Bộ tiêu chuẩn này đã được nâng cấp để giải quyết các nút thắt hiệu năng và chuẩn bị cho việc mở rộng (Scale).

---

## 1. KIẾN TRÚC TỔNG THỂ (TECH-STACK & ARCHITECTURE)
*   **Kiến trúc:** Tách bạch Frontend và Backend (SPA giao tiếp qua RESTful API). Bắt buộc áp dụng **Modular Monolith** (Domain-Driven Design) cho Backend thay vì Flat MVC truyền thống.
*   **Backend:** Laravel 11/12 (PHP 8.2+). Sử dụng mô hình `Controller -> Service -> Repository/Model` để tái sử dụng logic và dễ dàng testing.
*   **Frontend:** Vue.js 3 sử dụng **Options API** (để đồng nhất với code cũ), công cụ build Vite. Bắt buộc cấu trúc theo **Feature-Driven** (`src/modules/...`).
*   **Database:** MySQL (MariaDB). Tối ưu hóa Indexing (đặc biệt là Composite Indexes) cho các cột thường xuyên query như `SKU`, `ma_phu_tung`, `thuong_hieu_id`.

## 2. QUY CHUẨN GIAO DIỆN (UI/UX STRICT RULES)

### 2.1. Triết lý thống nhất toàn hệ thống (Universal Design System Philosophy)
*   **Ngôn ngữ Markup duy nhất:** Toàn bộ tác nhân (Admin, Staff, Client) đều thống nhất lập trình bằng **cú pháp Bootstrap 5 tối giản, sạch sẽ, chuẩn semantic HTML** (`container`, `row`, `col-*`, `card`, `table`, `form-control`, `btn`...). Tuyệt đối không nạp thêm thư viện UI khác (AntD, Vuetify, Element Plus...).
*   **Template đóng vai trò Thư viện Thiết kế Tập trung (Template as a Design System Library):**
    *   Thư mục tài nguyên trong `assets/` (Sneat cho Admin/Staff, TheEvent cho Client) được coi như một thư viện UI chuẩn mực chứa đầy đủ card, button, bảng biểu, badge, màu sắc...
    *   Giao diện các trang hiển thị đẹp đẽ bằng cách **ăn CSS từ thư viện template trong `assets/`**, tuyệt đối không viết CSS thủ công đè lên từng trang.
*   **Quy tắc Chuẩn hóa & Mở rộng bằng Mã Bootstrap 5 (BẮT BUỘC):**
    *   Khi giao diện thiếu bất kỳ kiểu dáng, màu sắc button, kiểu badge hay biến thể card nào: **Tuyệt đối KHÔNG viết CSS thuần trong file `.vue` (`<style scoped>`)**.
    *   **Tuyệt đối KHÔNG tự độ chế ra các tên class dị biệt** (CẤM: `.btn-marine-navy`, `.btn-cyan`, `.badge-marine`...).
    *   Lập trình viên / AI **BẮT BUỘC viết bằng mã/biến chuẩn Bootstrap 5 trực tiếp vào file CSS gốc của template trong thư mục `assets/`**:
        *   **Admin & Staff:** Bổ sung/ghi đè theo chuẩn Bootstrap/Sneat trong `FE_JPM/src/assets/admin/template/css/demo.css`.
        *   **Client:** Bổ sung/ghi đè theo chuẩn Bootstrap trong `FE_JPM/src/assets/client/css/main.css`.
    *   Component bên ngoài **100% chỉ gọi các class chính quy của Bootstrap 5** (`btn btn-primary`, `btn btn-danger`, `badge bg-danger`...), đảm bảo toàn bộ dự án thống nhất một cú pháp duy nhất.
*   **Quy định nghiêm ngặt về Inline Style (`style="..."`):**
    *   Inline `style=""` **DUY NHẤT** được chấp nhận cho **kích thước vật lý hoặc thông số động** (Physical / Dynamic Dimensions):
        *   Chiều rộng cột bảng cố định (VD: `style="width: 5%;"`).
        *   Chiều cao khung hình / video / sơ đồ (VD: `style="height: 450px;"`).
        *   Tọa độ hotspot động trên sơ đồ rã máy (VD: `style="top: 35%; left: 62%;"`).
    *   **TUYỆT ĐỐI CẤM** dùng inline `style=""` cho màu sắc (`color`, `background`), bo góc (`border-radius`), đổ bóng (`box-shadow`), căn lề hay font chữ... Tất cả phải dùng class Bootstrap hoặc class trong template assets.

### 2.2. Giao diện Back-office (Admin & Staff) - Sneat 1.0.0
> Xem tài liệu đặc tả chi tiết tại: [09_Admin_Staff_UI_Standard.md](09_Admin_Staff_UI_Standard.md)
*   **Tài nguyên:** Nạp độc quyền từ `FE_JPM/src/assets/admin/template/` (`core.css`, `theme-default.css`, `boxicons.css`, `demo.css`).
*   **Hệ thống Badges:** Dùng class chuẩn Sneat `.badge.rounded-pill.bg-label-*` (success, danger, warning, info, primary, secondary). Tuyệt đối cấm `.bg-pastel-*` hoặc `.badge-custom`.
*   **Hệ thống Buttons:** Dùng `.btn.btn-sm.btn-icon.btn-outline-*` và `.btn.btn-label-secondary`. Tuyệt đối cấm `.action-btn`.
*   **Ô tìm kiếm:** Dùng `.input-group.input-group-merge` kết hợp Boxicons (`bx bx-*`).
*   **Hai cấu trúc chuẩn:** Bắt buộc áp dụng **Pattern A** (Full-width Card Table + Modal) hoặc **Pattern B** (Chia đôi 4/8 Form + Table).

### 2.3. Giao diện Front-office (Client) - TheEvent 1.0.0
> Xem tài liệu đặc tả chi tiết tại: [10_Client_UI_Standard.md](10_Client_UI_Standard.md)
*   **Tài nguyên:** Nạp độc quyền từ `FE_JPM/src/assets/client/` (`bootstrap.min.css`, `bootstrap-icons.css`, `main.css`, `glightbox`, `swiper`).
*   **Phong cách & Màu sắc:** 
    *   Tông màu chủ đạo: Deep Navy Blue (`#0A1128`) cho uy tín kỹ thuật + Bright Marine Red (`#f82249`) làm điểm nhấn hành động (Mua hàng, Giỏ hàng, Giá tiền, Badge OEM).
    *   Font chữ: `Raleway` (Tiêu đề, Navigation, Buttons) & `Roboto` (Nội dung văn bản).
    *   Icons: Bootstrap Icons (`bi bi-*`).
*   **Cấu trúc Component:** Khung section chuẩn TheEvent (`section.section` -> `div.section-title`), thẻ sản phẩm Card nền trắng bo góc nhẹ (`shadow-sm`), ô tìm kiếm Catalog dùng `input-group`, bảng giỏ hàng thoáng đãng, checkbox nhỏ gọn chuẩn Bootstrap.

## 3. CÔNG NGHỆ TỐI ƯU HIỆU NĂNG (MANDATORY PERFORMANCE STACK)
Để giải quyết bài toán tra cứu dữ liệu siêu lớn và tránh N+1 Query, dự án BẮT BUỘC áp dụng:
*   **Meilisearch & Laravel Scout:** KHÔNG dùng `LIKE '%...%'` trong MySQL cho tính năng tìm kiếm Part Number hay Part Name. Phải đồng bộ data sang Meilisearch để đạt tốc độ P95 < 50ms, hỗ trợ sai chính tả và Full-text search.
*   **Redis Caching:** Bắt buộc dùng Redis để cache Cây danh mục (Marine Catalog, Brands, Models) và các dữ liệu tĩnh. Giảm thiểu Read Load cho MySQL.
*   **Stale-While-Revalidate (SWR) với Vue Query:** Ở Frontend, KHÔNG dùng `axiosClient.get` trực tiếp trong hook `mounted`. Bắt buộc dùng `@tanstack/vue-query` để lấy dữ liệu, lưu cache và đồng bộ ngầm (Server State). Dùng `useMutation` cho thao tác cập nhật để giao diện phản hồi tức thì (Optimistic UI).
*   **Queue/Worker:** Dùng cho các tác vụ nặng như Import Data (Excel) vào PIM, hoặc gửi email báo giá.

## 4. TIÊU CHUẨN BACKEND (LARAVEL STANDARDS)
*   **Chuẩn Route (`routes/api.php`):**
    *   Sử dụng Resource Route tối đa.
    *   Naming Convention: Kebab-case cho URL (`/danh-muc-phu-tungs`), camelCase cho tham số truy vấn (`?keyword=...`).
*   **Chuẩn Controller (Thin Controller):**
    *   Controller chỉ làm 4 việc: (1) Nhận Request, (2) Validate qua FormRequest, (3) Gọi Service Layer, (4) Trả về JSON thông qua API Resource.
    *   TUYỆT ĐỐI KHÔNG viết logic tính toán, gom PO hay query DB phức tạp (`where`, `join`) trực tiếp trong Controller.
*   **Chuẩn Response (API Resource):**
    *   Mọi API trả về đều phải qua Eloquent API Resource để format thành `{"data": [...], "meta": {...}}` và ẩn đi các column nhạy cảm.

## 5. BẢO MẬT & PHÂN QUYỀN
*   **Xác thực (Authentication):** Sử dụng Laravel Sanctum cấp phát Token cho Vue 3.
*   **Phân quyền (Authorization):** Sử dụng Spatie Laravel Permission. Middleware kiểm tra Role/Permission đặt ở cả tầng API Backend và `beforeEach` ở tầng Router Frontend. Phân quyền theo Hành động Nghiệp vụ (Business Action) thay vì từng nút bấm nhỏ nhặt.

## 6. QUY TẮC CHÚ THÍCH (COMMENT) BẰNG TIẾNG VIỆT TOÀN DỰ ÁN
> **Mục đích:** Đảm bảo khả năng bảo trì, bàn giao và kế thừa mã nguồn lâu dài. Bất kỳ lập trình viên hoặc AI nào tiếp quản sau này cũng có thể đọc hiểu ngay lập tức cấu trúc và nghiệp vụ của từng dòng code.

*   **Bắt buộc comment bằng tiếng Việt rõ ràng, dễ hiểu:**
    *   **Trong template Vue (HTML):** Mọi section, block, card, form, modal, table và luồng điều kiện (`v-if` / `v-else`) đều phải có thẻ comment tiếng Việt ở đầu khối (VD: `<!-- Khối 4: Tại sao chọn JPM Marine -->`, `<!-- Skeleton loading khi đang nạp dữ liệu -->`).
    *   **Trong Script Vue (JS/TS):** Mọi state, hook Vue Query, computed property, method, watcher đều phải có comment 1-2 dòng giải thích mục đích và luồng xử lý.
    *   **Trong Stylesheet (CSS/SCSS):** Các nhóm class tiện ích, biến design token, khối giao diện tùy biến phải được phân chia bằng header block comment tiếng Việt.
    *   **Trong Backend (Laravel/PHP):** Mọi Service method, Model relationship, Action, FormRequest đều phải có chú thích tiếng Việt giải thích quy tắc nghiệp vụ tàu thủy tương ứng.

