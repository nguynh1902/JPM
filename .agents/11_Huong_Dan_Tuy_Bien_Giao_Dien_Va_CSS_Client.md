# HƯỚNG DẪN TÙY BIẾN GIAO DIỆN & ĐẶC TẢ KỸ THUẬT TRIỂN KHAI CSS/VUE CLIENT 2026
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web Storefront  
**Tài liệu:** `.agents/11_Huong_Dan_Tuy_Bien_Giao_Dien_Va_CSS_Client.md`  
**Cấp độ:** Bắt buộc đọc và tuân thủ khi thay đổi giao diện, đổi theme, chỉnh sửa CSS hoặc triển khai component trong phân hệ Client.  
**Trạng thái phiên bản:** v2.0-2026 (Đã tích hợp đầy đủ Technical Implementation Specification của Phase 3 theo phê duyệt của Product Owner).

---

## 1. NGUYÊN TẮC BẢO TOÀN KIẾN TRÚC KỸ THUẬT (TECHNICAL INTEGRITY DIRECTIVES)

Quá trình chuyển đổi từ đặc tả thiết kế sang mã nguồn trong tương lai (Phase 4) bắt buộc phải tuân thủ nghiêm ngặt 3 nguyên tắc nền tảng:
1. **Không sửa đổi hợp đồng API Backend (Zero Backend API Drift):** Mọi endpoint Laravel hiện tại (`/api/public/*`, `/api/client/*`) được giữ nguyên 100%. Thiết kế mới được xây dựng để khớp hoàn hảo với cấu trúc dữ liệu JSON mà backend đang cung cấp.
2. **Bảo toàn cơ chế Định giá Thẩm quyền (Authoritative Pricing Preserved):** Frontend tuyệt đối không tự ý tính toán giảm giá, thuế hay tổng tiền đơn hàng. Toàn bộ tính toán tài chính và giá báo giá phải do Laravel Backend quyết định.
3. **Chuyển đổi từng bước an toàn (Additive Non-destructive Migration):** Không xóa sổ 3.569 dòng `main.css` trong một lần refactor. Thư viện Design Tokens và các component mới sẽ được đưa vào theo dạng module độc lập bổ trợ, sau đó từng màn hình sẽ được nâng cấp dần.

---

## 2. BẢN ĐỒ DÒNG CODE VÀ TÙY BIẾN TRONG `main.css` (CODE MAP)

Toàn bộ tài nguyên giao diện của Client nằm tập trung tại:
📁 `FE_JPM/src/assets/client/`
- `css/main.css`: CSS giao diện chính của TheEvent (biến màu, typography, ghi đè Bootstrap).
- `vendor/bootstrap/`: Core Bootstrap 5.3+.
- `vendor/bootstrap-icons/`: Thư viện icon chính thức (`bi bi-*`).

### Bản đồ dòng code chi tiết trong `FE_JPM/src/assets/client/css/main.css`:

| Dòng code | Hạng mục thành phần | Mục đích tùy biến theo Design System 2026 |
|---|---|---|
| **Dòng 14 – 48** | **`:root` Variables & Tokens** | Khai báo các Design Tokens JPM 2026 (`--jpm-color-*`, `--jpm-radius-*`, `--jpm-shadow-*`). |
| **Dòng 50 – 75** | **Nav Menu Colors** | Màu sắc thanh Menu Navigation, màu chữ khi hover, nền dropdown kính mờ. |
| **Dòng 86 – 128** | **Interaction Utilities** | Hiệu ứng nhấc nổi khi hover (`group-hover`, phóng to ảnh thumbnail). |
| **Dòng 2028 – 2038** | **Page Titles** | Kiểu dáng tiêu đề trang chuẩn (`.jpm-page-title`). |
| **Dòng 2040 – 2065** | **Buttons (Nút bấm)** | Ghi đè nút bấm Bootstrap: `.btn-primary` (Navy), `.btn-danger` (Marine Red), bo góc chuẩn 8px. |
| **Dòng 2075 – 2100** | **Form Controls (Cơ bản)** | Chiều cao 44px, viền 1px `--jpm-neutral-200`, bo góc chuẩn 8px của ô nhập liệu form. |
| **Dòng 2162 – 2220** | **Custom Tables** | Kiểu dáng bảng phụ tùng, bảng giỏ hàng (`.custom-table`), huy hiệu số Ref (`.ref-badge`). |
| **Dòng 2240 – 2278** | **Cards & Product Cards** | Kiểu dáng khung thẻ Card (`.card`, `.product-card`), bo góc chuẩn 12px, viền ảnh ôm khít. |
| **Dòng 2280 – 2325** | **Input Group & Search** | Kiểu dáng thanh tìm kiếm nhanh, hiệu ứng viền phát sáng khi focus (`focus-within`). |
| **Dòng 2326 – 2346** | **Badges & Tags** | Kiểu dáng nhãn dán, badge OEM, badge trạng thái tồn kho, bo góc chuẩn 6px. |

---

## 3. ĐẶC TẢ CSS VARIABLES CHUẨN HÓA (:ROOT TOKENS 2026)

Đoạn mã CSS Variables đặc tả đầy đủ hệ thống Design Tokens 2026 chuẩn bị sẵn sàng cho bước triển khai:

```css
:root {
  /* 1. MÀU THƯƠNG HIỆU (BRAND COLORS) */
  --jpm-color-navy-dark: #0A1128;
  --jpm-color-navy-subtle: #101E42;
  --jpm-color-red-marine: #F82249;
  --jpm-color-red-hover: #D9163A;
  --jpm-color-blue-electric: #0284C7;
  --jpm-color-gold-marine: #D97706;

  /* 2. THANG SẮC ĐỘ TRUNG TÍNH (NEUTRAL SLATE SCALE) */
  --jpm-neutral-900: #0F172A;
  --jpm-neutral-800: #1E293B;
  --jpm-neutral-700: #334155;
  --jpm-neutral-500: #64748B;
  --jpm-neutral-400: #94A3B8;
  --jpm-neutral-300: #CBD5E1;
  --jpm-neutral-200: #E2E8F0;
  --jpm-neutral-100: #F1F5F9;
  --jpm-neutral-50:  #F8FAFC;
  --jpm-neutral-0:   #FFFFFF;

  /* 3. HỆ THỐNG BO GÓC CHUẨN HÓA (PO DECISION 01) */
  --jpm-radius-card: 12px;
  --jpm-radius-control: 8px;
  --jpm-radius-badge: 6px;
  --jpm-radius-circle: 50%;

  /* 4. HỆ THỐNG ĐỔ BÓNG MỎNG (ELEVATIONS) */
  --jpm-shadow-sm: 0 1px 3px rgba(10, 17, 40, 0.05), 0 1px 2px rgba(10, 17, 40, 0.03);
  --jpm-shadow-md: 0 4px 12px -2px rgba(10, 17, 40, 0.08), 0 2px 6px -2px rgba(10, 17, 40, 0.04);
  --jpm-shadow-lg: 0 12px 24px -4px rgba(10, 17, 40, 0.12), 0 4px 10px -2px rgba(10, 17, 40, 0.06);
  --jpm-shadow-xl: 0 24px 48px -12px rgba(10, 17, 40, 0.20);

  /* 5. TYPOGRAPHY */
  --jpm-font-heading: 'Raleway', sans-serif;
  --jpm-font-body: 'Roboto', sans-serif;
  --jpm-font-mono: 'Roboto Mono', monospace;

  /* 6. HEADER OFFSET THEO BREAKPOINT */
  --jpm-header-height: 80px; /* Mặc định Desktop; 72px trên Tablet; 64px trên Mobile */
}
```

---

## 4. CẤU TRÚC TỔ CHỨC FILE VÀ COMPONENT ĐỀ XUẤT (FE ARCHITECTURE)

Dự kiến cấu trúc thư mục phân bổ tại `FE_JPM/src/` trong giai đoạn thực thi Phase 4:

```
FE_JPM/src/
├── assets/
│   └── client/
│       └── css/
│           ├── design-tokens.css         <-- File nạp các biến CSS Tokens 2026 độc lập
│           └── main.css                  <-- CSS chính (được tinh chỉnh an toàn)
│
├── components/
│   └── common/                           <-- Thư viện thành phần dùng chung (PO Decision 03)
│       ├── ProductCard.vue               <-- Thẻ sản phẩm chuẩn, cân đối nút báo giá
│       ├── JpmSelect.vue                 <-- Dropdown kỹ thuật lọc 4 cấp
│       └── HotspotPin.vue                <-- Ghim bản vẽ sơ đồ rã máy (PO Decision 02)
│
├── stores/
│   ├── diagramStore.ts                   <-- Pinia Store đồng bộ sơ đồ hai chiều
│   ├── cartStore.ts                      <-- Pinia Store giỏ hàng & báo giá
│   └── fitmentFilterStore.ts             <-- Pinia Store bộ lọc 4 cấp
│
└── views/client/                         <-- 20 màn hình Client (nâng cấp dần template)
```

---

## 5. ĐẶC TẢ KỸ THUẬT PINIA STORE ĐỒNG BỘ SƠ ĐỒ RÃ MÁY (USEDIAGRAMSTORE.TS)

Quản lý trạng thái tương tác hai chiều tại `SoDoPhuTung.vue`:

```typescript
import { defineStore } from 'pinia';

export interface ChiTietSoDo {
  id: number;
  so_do_id: number;
  ref_number: string;     // Số thứ tự linh kiện trên bản vẽ (vd: "1", "12A")
  qty: number;            // Số lượng lắp
  x: number;              // Tọa độ X (phần trăm 0 - 100)
  y: number;              // Tọa độ Y (phần trăm 0 - 100)
  width: number;
  height: number;
  phu_tung?: {
    id: number;
    ma_phu_tung: string;  // Mã OEM
    ten_phu_tung: string;
    gia_ban: number;
    is_gia_lien_he: boolean;
    so_luong_ton: number;
    hinh_anh?: string;
  };
}

export const useDiagramStore = defineStore('diagramStore', {
  state: () => ({
    activeModelCode: '' as string,
    activeSoDoId: null as number | null,
    chiTiets: [] as ChiTietSoDo[],
    selectedRefNumber: null as string | null,
    hoveredRefNumber: null as string | null,
    zoomLevel: 1.0,
    panPosition: { x: 0, y: 0 },
    isFullscreen: false,
    filterKeyword: ''
  }),

  getters: {
    selectedPart(state): ChiTietSoDo | undefined {
      if (!state.selectedRefNumber) return undefined;
      return state.chiTiets.find(item => item.ref_number === state.selectedRefNumber);
    },
    filteredChiTiets(state): ChiTietSoDo[] {
      if (!state.filterKeyword.trim()) return state.chiTiets;
      const kw = state.filterKeyword.toLowerCase();
      return state.chiTiets.filter(item => 
        item.ref_number.toLowerCase().includes(kw) ||
        (item.phu_tung?.ma_phu_tung && item.phu_tung.ma_phu_tung.toLowerCase().includes(kw)) ||
        (item.phu_tung?.ten_phu_tung && item.phu_tung.ten_phu_tung.toLowerCase().includes(kw))
      );
    }
  },

  actions: {
    selectRef(refNumber: string) {
      this.selectedRefNumber = refNumber;
    },
    hoverRef(refNumber: string | null) {
      this.hoveredRefNumber = refNumber;
    },
    resetView() {
      this.zoomLevel = 1.0;
      this.panPosition = { x: 0, y: 0 };
    }
  }
});
```

---

## 6. MA TRẬN DỌN DẸP & THAY THẾ BIỂU TƯỢNG BOXICONS (PO DECISION 04)

Bảng quy đổi trực tiếp toàn bộ 41 vị trí class Boxicons cũ bị lỗi trên Client sang Bootstrap Icons chính thức:

| Vị trí file trong source | Class Boxicons cũ (Lỗi) | Class Bootstrap Icons thay thế (Chuẩn) | Ý nghĩa hiển thị |
| :--- | :--- | :--- | :--- |
| `TraCuuBaoGia.vue` | `bx bx-search-alt` | `bi bi-search` | Kính lúp tra cứu mã báo giá |
| `TraCuuBaoGia.vue` | `bx bx-file` | `bi bi-file-earmark-text` | Biểu tượng tài liệu báo giá |
| `TraCuuBaoGia.vue` | `bx bx-time-five` | `bi bi-clock-history` | Đồng hồ đếm ngược 72 giờ |
| `TraCuuBaoGia.vue` | `bx bx-check-circle` | `bi bi-check-circle-fill` | Trạng thái báo giá đã duyệt |
| `TraCuuDonHang.vue` | `bx bx-package` | `bi bi-box-seam` | Kiện hàng phụ tùng |
| `TraCuuDonHang.vue` | `bx bx-car` / `bx bx-ship`| `bi bi-truck` / `bi bi-water` | Vận chuyển hàng hải & bộ |
| `TraCuuDonHang.vue` | `bx bx-map-pin` | `bi bi-geo-alt-fill` | Địa chỉ cảng đích |
| `AccountSidebar.vue` | `bx bx-user` | `bi bi-person` | Hồ sơ người dùng |
| `AccountSidebar.vue` | `bx bx-wrench` | `bi bi-wrench-adjustable` | Tủ máy Marine Garage |
| `AccountSidebar.vue` | `bx bx-history` | `bi bi-clock-history` | Lịch sử mua hàng |

---

## 7. MA TRẬN QUẢN TRỊ RỦI RO KỸ THUẬT (TECHNICAL RISK MATRIX)

| Rủi ro kỹ thuật | Mức độ | Hậu quả tiềm tàng | Giải pháp phòng ngừa & Khắc phục |
| :--- | :--- | :--- | :--- |
| **1. Xung đột CSS khi nâng cấp** | Trung bình | Biến đổi token có thể ảnh hưởng ngoài ý muốn lên các trang chưa kịp refactor | Sử dụng tiền tố rõ ràng `--jpm-*` chỉ áp dụng trong phạm vi phân hệ Client; giữ nguyên các class Bootstrap core |
| **2. Lệch tọa độ Hotspot sơ đồ** | Cao | Khi ảnh rã máy chưa tải xong kích thước tự nhiên (`naturalWidth`), việc tính tọa độ ban đầu có thể bị lệch | Lắng nghe sự kiện `@load` của thẻ ảnh trước khi render ghim Pins; khóa tỷ lệ trong Transform Container |
| **3. Spam query lọc 4 cấp** | Thấp | Thợ máy thao tác bấm đổi hãng/model liên tục có thể gửi dồn dập request API | Debounce 300ms kết hợp bộ nhớ đệm TanStack Vue Query (`staleTime: 5 * 60 * 1000`) |

---

## 8. QUY TRÌNH KIỂM TRA BIÊN DỊCH VÀ NGHIỆM THU (VERIFICATION COMMANDS)

Sau mỗi lần cập nhật CSS hoặc component tại phân hệ Client:
1. Mở terminal tại thư mục `FE_JPM`:
   ```bash
   cd c:\xampp\htdocs\JPM\FE_JPM
   npm run build
   ```
2. Nếu terminal thông báo `✓ built in ...s` thành công mà không phát sinh lỗi biên dịch CSS hay thiếu biến là mã nguồn đạt tiêu chuẩn vận hành an toàn.

---

*Tài liệu này được lưu trữ chính thức tại `.agents/11_Huong_Dan_Tuy_Bien_Giao_Dien_Va_CSS_Client.md` nhằm định hướng kỹ thuật cho toàn bộ lập trình viên và AI phát triển giao diện JPM.*
