# KIẾN TRÚC MÃ NGUỒN JPM (CODE ARCHITECTURE)

> **Nguyên tắc chi phối:** Domain-Driven Design (DDD), Business Flow Organization, Separation of Concerns.
> **CẢNH BÁO:** Dự án **tuyệt đối không** sử dụng kiến trúc Flat MVC (gom toàn bộ Controller/Model vào chung một thư mục) như những ứng dụng nhỏ lẻ. Bắt buộc phải áp dụng **Modular Monolith** theo các khối nghiệp vụ (Domain).

Tài liệu này là thiết kế Kiến trúc Mã nguồn (Code Architecture) chuẩn mực. Mục tiêu là code có thể mở rộng lên hàng triệu dòng trong 10 năm mà không bị rác (Spaghetti Code).

---

## 1. CẤU TRÚC THƯ MỤC BACKEND (LARAVEL - DDD APPROACH)
Thay vì nhét mọi thứ vào `app/Models` hay `app/Http/Controllers`, JPM sử dụng kiến trúc **Domain-Driven Directory Structure**. Mọi thứ được nhóm theo khối nghiệp vụ đã chốt ở tài liệu Database.

```text
app/
├── Domain/                   # Tầng Nghiệp Vụ Cốt Lõi (Độc lập với HTTP)
│   ├── IAM/                  # (Identity & CRM) Users, Addresses, Garages
│   ├── MarineCatalog/        # (Danh mục Máy thủy) Brands, Engines, Models
│   ├── PIM/                  # (Phụ tùng & Sơ đồ) Parts, Diagrams, Hotspots
│   ├── Sales/                # (Bán hàng) Carts, Orders, Payments
│   └── Procurement/          # (Cross-docking) POs, Receipts, Allocations
│       ├── Models/           # Models thuộc riêng về Domain này
│       ├── Services/         # Service Layer (Logic nghiệp vụ phức tạp nằm đây)
│       ├── Repositories/     # Data Access Layer (Chỉ chứa query DB phức tạp)
│       └── DTOs/             # Data Transfer Objects (Định dạng dữ liệu luân chuyển)
│
├── Http/                     # Tầng Giao Tiếp (HTTP / API)
│   ├── Controllers/
│   │   └── Api/V1/
│   │       ├── MarineCatalog/    # Chứa các Controller cho Catalog
│   │       ├── PIM/
│   │       ├── Sales/
│   │       └── Procurement/      # Chứa PoController, ReceiptController...
│   ├── Requests/                 # FormRequest Validation (Tổ chức theo Domain)
│   ├── Resources/                # API Resources để format JSON trả về (Tổ chức theo Domain)
│   └── Middleware/               # Middleware xác thực, check quyền
│
└── Infrastructure/           # Tầng Giao Tiếp Bên Ngoài (Zalo API, Payment Gateway)
```

## 2. CẤU TRÚC THƯ MỤC FRONTEND (VUE 3 - FEATURE-DRIVEN)
Frontend sẽ KHÔNG sử dụng cấu trúc gom chung `src/views` và `src/components` thành bãi rác. Chúng ta sử dụng cấu trúc **Feature-Driven** (Gói gọn mọi file liên quan đến 1 nghiệp vụ vào chung 1 thư mục).

```text
src/
├── modules/                  # Chứa các tính năng theo luồng nghiệp vụ
│   ├── CatalogSearch/        # (Luồng 3: Tra cứu Sơ đồ)
│   │   ├── components/       # DiagramViewer.vue, RefTable.vue
│   │   ├── views/            # CatalogHome.vue, DiagramDetail.vue
│   │   ├── store/            # Pinia / Vue Query cấu hình cho module
│   │   └── composables/      # Logic reuse (VD: useDiagramZoom.js)
│   ├── Checkout/             # (Luồng Bán Hàng)
│   ├── AdminPIM/             # (Tool Nhập liệu Data)
│   │   ├── components/       # ImageMapper.vue, ExcelUploader.vue
│   │   └── views/            # DiagramEditor.vue
│   └── AdminCrossDocking/    # (Tool Nhận Hàng)
│       └── components/       # BarcodeScanner.vue
│
├── shared/                   # Dùng chung cho toàn bộ dự án
│   ├── components/           # Button, Modal, DataTable (Các UI Element nguyên thủy)
│   ├── layouts/              # AdminLayout.vue (Sneat), ClientLayout.vue (TheEvent)
│   ├── assets/               # CSS, SCSS, Images chung
│   └── utils/                # Helpers, API Axios instances
│
└── router/                   # Cấu hình Route
    ├── admin.js              # Load các route Admin
    ├── client.js             # Load các route Client
    └── index.js              # Gộp route
```

## 3. THIẾT KẾ CÁC TẦNG KIẾN TRÚC KỸ THUẬT

### 3.1. API & Request Layer
*   **Routing:** Mọi Route phải được phân nhóm theo Domain trong `routes/api.php` bằng `Route::group`.
*   **Request Layer:** Controller tuyệt đối không tự validate dữ liệu. Mọi Input phải đi qua `app/Http/Requests`.

### 3.2. Controller Layer
*   **Nguyên tắc "Mỏng" (Thin Controller):** Controller CHỈ ĐƯỢC làm 3 việc: Nhận Request đã validate -> Gọi Service Layer tương ứng -> Trả về API Resource. Controller không chứa lệnh `if/else` nghiệp vụ dài dòng hay câu truy vấn `where` của Eloquent.

### 3.3. Service Layer Architecture
Đây là trái tim của hệ thống.
*   **Nguyên tắc "Dày" (Thick Service):** Mọi logic tính toán (VD: Logic Gom Đơn PO, Tính phí Ship, Match hàng Cross-docking) phải nằm trong thư mục `Domain/.../Services/`.
*   Mỗi Service chỉ làm một nhiệm vụ (Single Responsibility). Ví dụ: `CrossDockingAllocationService` chỉ làm nhiệm vụ tính toán xem lô hàng Nhật vừa về sẽ được chia cho những đơn nào.

### 3.4. Resource Layer
*   Frontend không bao giờ được nhìn thấy tên cột Database thật. Eloquent Resource làm nhiệm vụ ẩn giấu ID thật, đổi tên biến chuẩn `camelCase` cho VueJS tiêu thụ, và bọc metadata phân trang thống nhất.

## 4. QUY TẮC ĐẶT TÊN VÀ ĐỊNH TUYẾN (CONVENTIONS)

### 4.1. File & Namespace Convention
*   **Backend:** Theo chuẩn PSR-4 của Laravel. Class name là `StudlyCaps`, Method name là `camelCase`. Namespaces phải phản ánh đúng thư mục (VD: `App\Domain\Procurement\Services`).
*   **Frontend:** 
    *   Tên Component: `PascalCase` (VD: `DiagramViewer.vue`). Không dùng tên chung chung như `View.vue` hay `Index.vue`.
    *   Tên views: Hậu tố báo hiệu đây là một trang (VD: `DiagramDetailPage.vue`).

### 4.2. Dependency Rule (Quy tắc Gọi chéo)
Để tránh hệ thống dính liền vào nhau (Tight Coupling), quy định chiều gọi như sau:
*   Controller $\rightarrow$ Gọi Service.
*   Service $\rightarrow$ Gọi Repository/Model.
*   **Cross-Domain:** Nếu Service ở `Sales` cần lấy thông tin Phụ tùng từ `PIM`, nó phải gọi `PartService` của `PIM`, **Tuyệt đối không được** tự ý query trực tiếp vào `PartModel` của Domain khác.

---

## 5. BẢN ĐỒ PHỤ THUỘC MODULE (MODULE DEPENDENCY DIAGRAM)

```mermaid
graph TD
    %% Tầng Dưới Cùng (Core)
    IAM[IAM Domain - Auth/Users]
    Catalog[Marine Catalog - Brands/Models]

    %% Tầng Giữa (Dữ liệu nền tảng)
    PIM[PIM & Parts Domain]
    PIM -->|Requires Fitment info| Catalog
    
    %% Tầng Trực Tiếp Sinh Lãi
    Sales[Sales & Orders]
    Sales -->|Needs Customer info| IAM
    Sales -->|Needs Part Info| PIM

    %% Tầng Vận Hành Đỉnh Cấp (Cross-Docking)
    Procurement[Procurement & Cross-Docking]
    Procurement -->|Consolidates Orders| Sales
    Procurement -->|Needs Supplier/Part Info| PIM

    classDef core fill:#e1f5fe,stroke:#01579b,stroke-width:2px;
    classDef operational fill:#f3e5f5,stroke:#4a148c,stroke-width:2px;
    
    class IAM,Catalog core;
    class PIM,Sales,Procurement operational;
```

> **Kết luận:** Với cấu trúc này, khi Team phát triển cần sửa lỗi về tính năng Quét mã vạch Cross-Docking, họ chỉ cần mở đúng Domain `Procurement` ở Backend và module `AdminCrossDocking` ở Frontend. Không ai giẫm chân lên code của ai. Mọi thứ được cô lập và an toàn tuyệt đối.
