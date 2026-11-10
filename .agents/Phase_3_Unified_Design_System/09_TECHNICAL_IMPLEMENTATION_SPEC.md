# JPM CLIENT – TÀI LIỆU 09: TECHNICAL IMPLEMENTATION SPEC (ĐẶC TẢ KỸ THUẬT TRIỂN KHAI VUE 3)
**Dự án:** Japan Marine Parts Management (JPM)  
**Phân hệ:** Frontend Client Web  
**Giai đoạn:** Phase 3 – Unified Design System & Client Design Blueprint  
**Phiên bản tài liệu:** 1.0.0-2026  
**Trạng thái:** CHÍNH THỨC TRÌNH DUYỆT (Zero Source Code Modified)

---

## 1. NGUYÊN TẮC BẢO TOÀN KIẾN TRÚC HIỆN TẠI

Quá trình chuyển đổi từ thiết kế sang mã nguồn trong tương lai (Phase 4) phải tuân thủ nghiêm ngặt 3 nguyên tắc bất khả xâm phạm:
1. **Không sửa đổi hợp đồng API Backend (Zero Backend API Drift):** Mọi endpoint Laravel hiện tại (`/api/public/*`, `/api/client/*`) được giữ nguyên 100%. Thiết kế mới được xây dựng để khớp hoàn hảo với cấu trúc dữ liệu JSON mà backend đang cung cấp.
2. **Bảo toàn cơ chế Định giá Thẩm quyền (Authoritative Pricing Preserved):** Frontend tuyệt đối không tự tính toán giảm giá, thuế hay tổng tiền đơn hàng. Toàn bộ tính toán tài chính và giá báo giá phải do Laravel Backend quyết định.
3. **Chuyển đổi từng bước an toàn (Additive Non-destructive Migration):** Không xóa sổ 3.569 dòng `main.css` trong một lần refactor. Thư viện Design Tokens và các component mới sẽ được đưa vào theo dạng module độc lập bổ trợ, sau đó từng màn hình sẽ được nâng cấp dần.

---

## 2. CẤU TRÚC TỔ CHỨC FILE VÀ THÀNH PHẦN (PROPOSED FE ARCHITECTURE)

Dự kiến cấu trúc thư mục phân bổ tại `FE_JPM/src/` trong giai đoạn thực thi:

```
FE_JPM/src/
├── assets/
│   └── css/
│       ├── design-tokens.css         <-- Nơi tập hợp toàn bộ CSS Variables JPM 2026
│       ├── typography.css            <-- Font Raleway & Roboto rules
│       ├── layout-system.css         <-- Header height & layout offset rules
│       └── main.css                  <-- Style hiện tại (được chuẩn hóa dần)
│
├── components/
│   └── common/                       <-- Thư viện thành phần dùng chung
│       ├── ProductCard.vue           <-- Thẻ sản phẩm chuẩn (PO Decision 03)
│       ├── JpmSelect.vue             <-- Dropdown lọc 4 cấp (PO Decision 03)
│       ├── JpmButton.vue             <-- Nút chuẩn hóa bo góc 8px
│       ├── JpmBadge.vue              <-- Badge OEM và tồn kho bo góc 6px
│       ├── JpmModal.vue              <-- Hộp thoại chuẩn bo góc 12px
│       └── HotspotPin.vue            <-- Ghim bản vẽ sơ đồ (PO Decision 02)
│
├── stores/
│   ├── diagramStore.ts               <-- Pinia Store đồng bộ sơ đồ hai chiều
│   ├── cartStore.ts                  <-- Pinia Store giỏ hàng & báo giá
│   └── fitmentFilterStore.ts         <-- Pinia Store lưu chuỗi lọc 4 cấp
│
└── views/client/                     <-- 20 màn hình Client (nâng cấp dần template)
```

---

## 3. KIẾN TRÚC ĐỒNG BỘ HAI CHIỀU SƠ ĐỒ RÃ MÁY (PINIA REACTIVE STORE SPEC)

Để phục vụ tính năng Hotspot tương tác hai chiều tại `SoDoPhuTung.vue` mà không làm phân mảnh logic, một Pinia Store chuyên biệt sẽ quản trị trạng thái dùng chung:

```typescript
// Đặc tả TypeScript đề xuất cho Pinia Store quản lý sơ đồ rã máy
import { defineStore } from 'pinia';

export interface ChiTietSoDo {
  id: number;
  so_do_id: number;
  ref_number: string;     // Số trên bản vẽ (vd: "1", "12A")
  qty: number;            // Số lượng
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
    soDoList: [] as any[],
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

## 4. CHIẾN LƯỢC DỌN DẸP TOÀN BỘ BOXICONS (PO DECISION 04 CLEANUP MATRIX)

Tại Phase 2 đã ghi nhận 41 điểm lỗi giao diện do sử dụng Boxicons (`bx bx-*`) trong khi gói font này không được nhúng đầy đủ, dẫn đến các ô vuông lỗi. Bảng quy đổi trực tiếp sang Bootstrap Icons:

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

## 5. PHÂN TÍCH RỦI RO KỸ THUẬT VÀ GIẢI PHÁP PHÒNG NGỪA (RISK & MITIGATION)

```
┌────────────────────────────────────────────────────────────────────────┐
│                      MA TRẬN QUẢN TRỊ RỦI RO KỸ THUẬT                  │
├─────────────────┬──────────┬───────────────────────┬───────────────────┤
│ RỦI RO KỸ THUẬT │ MỨC ĐỘ   │ HẬU QUẢ TIỀM TẨNG     │ GIẢI PHÁP PHÒNG   │
├─────────────────┼──────────┼───────────────────────┼───────────────────┤
│ 1. Xung đột CSS │ Trung    │ Sửa đổi token có thể  │ Sử dụng tiền tố   │
│ biến thể cũ     │ bình     │ ảnh hưởng ngoài ý muốn│ rõ ràng (--jpm-*) │
│                 │          │ lên trang Admin       │ chỉ phạm vi Client│
├─────────────────┼──────────┼───────────────────────┼───────────────────┤
│ 2. Lệch tọa độ  │ Cao      │ Khi ảnh rã máy chưa   │ Lắng nghe sự kiện │
│ Hotspot sơ đồ   │          │ tải xong kích thước tự│ @load của thẻ ảnh │
│                 │          │ nhiên sẽ định vị sai  │ trước khi vẽ pins │
├─────────────────┼──────────┼───────────────────────┼───────────────────┤
│ 3. Hiệu năng lọc│ Thấp -   │ Thợ máy chọn nhanh làm│ Debounce 300ms và │
│ 4 cấp liên tục  │ Trung    │ spam API query liên   │ Cache TanStack    │
│                 │ bình     │ tục về server         │ Vue Query (5 phút)│
└─────────────────┴──────────┴───────────────────────┴───────────────────┘
```
