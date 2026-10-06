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
*   **Tái sử dụng Template:** Không thiết kế UI mới.
    *   Client (Khách hàng): Sử dụng **TheEvent**.
    *   Back-office (Admin/Staff): Sử dụng **Sneat**.
*   **Grid System & Bootstrap 5:** Bắt buộc dùng hệ thống lưới của Bootstrap (`col-md-6`, `col-12`) cho mọi layout. Đảm bảo Responsive 100%. Không tự viết CSS tùy chỉnh (`<style scoped>`) cho Layout trừ khi bất khả kháng.
*   **Form & Table Layout (Đặc biệt cho Sneat Admin):**
    *   Form phải gộp các input liên quan trên cùng 1 hàng. Trường Trạng Thái dùng `col-md-5`.
    *   Bảng (Table) phải được bọc bởi `<div class="table-responsive">`.
    *   Thẻ trạng thái (Badges) phải dùng màu pastel (`.bg-pastel-success`, `.bg-pastel-danger`), chữ in hoa (Uppercase).
    *   Cột hành động (Action) và STT (ID) bắt buộc căn giữa (`text-center`).

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
