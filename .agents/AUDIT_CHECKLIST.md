
# 🎯 ENTERPRISE PAGE AUDIT & QA CHECKLIST
**Dự án:** JPM ERP System
**Mục tiêu:** Kiểm thử và chuẩn hóa 100% cấp độ Doanh nghiệp trước khi lên Production (UAT/Go-Live).
**Hướng dẫn sử dụng:** Áp dụng Form này cho MỌI page. Bất kỳ mục nào đánh dấu [ ] (Chưa đạt) đều phải được Fix ngay lập tức trước khi chuyển sang Page khác. TẤT CẢ các tính năng phải được KIỂM THỬ THỰC TẾ (LIVE TEST).

---

## 📄 THÔNG TIN PAGE
- **Tên Page (FE):** `[Tên Component].vue`
- **Controller (BE):** `[Tên Controller].php`
- **Bảng DB liên quan:** `[Tên Table]`
- **Người kiểm duyệt:** AI & Product Owner

---


## 🚨 BÀI HỌC TỪ SAI SÓT QUÁ KHỨ (CRITICAL PRE-FLIGHT CHECKS)
*Đây là những lỗi nghiêm trọng AI đã từng mắc phải, BẮT BUỘC phải kiểm tra chéo kỹ lưỡng trước khi báo cáo hoàn thành.*

- [ ] **Field Parity (Đồng bộ trường dữ liệu):** Đối chiếu TỪNG TRƯỜNG MỘT giữa Backend Validation (`$request->validate`) và Frontend Form UI. Tuyệt đối không để xảy ra tình trạng Backend bắt buộc (`required`) nhưng Frontend lại không có ô nhập liệu (Input field), dẫn đến lỗi `The field is required` ngớ ngẩn.
- [ ] **Payload Content-Type (Đồng bộ kiểu gửi dữ liệu):** Nếu API Backend có xử lý Upload File (`$request->hasFile`, `hinh_anh_file`), BẮT BUỘC Frontend trong hàm `saveData()` phải gom dữ liệu bằng `new FormData()` và gửi với Header `multipart/form-data`. Tuyệt đối không được dùng JSON payload (`axiosClient.post(url, this.formData)`) vì nó sẽ làm mất file đính kèm.
- [ ] **UI Component Consistency (Chuẩn hóa Component):** Nếu dữ liệu là Hình Ảnh (Logo, Bản vẽ, Avatar), KHÔNG ĐƯỢC tự ý dùng ô nhập URL dạng `type="text"`. BẮT BUỘC phải sao chép Form chuẩn (File Upload Box có ảnh Preview thu nhỏ + Nút Xóa ảnh góc trên) từ các trang đã chuẩn hóa (như `ThuongHieu.vue`, `PhuTung.vue`).
- [ ] **Testing completeness:** Khai báo "Hoàn thành 100%" chỉ được đưa ra SAU KHI đã kiểm tra cặn kẽ đường đi của data từ UI (Frontend) -> Payload (Network) -> Validation (Backend) -> Storage (Database).


## THÊM: TIÊU CHUẨN OPTIMISTIC UI 0MS (BẮT BUỘC)
- [ ] **Optimistic UI Code Pattern:** Chức năng Thêm/Sửa/Xóa TUYỆT ĐỐI không được dùng hàm gọi lại danh sách (vd: `fetchData()`, hay `queryClient.invalidateQueries` ngay lập tức). Bắt buộc phải dùng `useMutation` kết hợp với `queryClient.setQueryData` để chèn/sửa/xóa trực tiếp vào mảng danh sách trên giao diện ngay tức thì. Chỉ gọi `invalidateQueries` ở hàm `onSettled` (background) để đồng bộ ngầm. Đảm bảo trải nghiệm người dùng luôn là 0ms.

## 1️⃣ KIỂM THỬ GIAO DIỆN & TRẢI NGHIỆM (UI/UX - FRONTEND)
- [ ] **Grid & Responsive:** Layout form đã gom nhóm hợp lý chưa? Bảng (Table) có hiển thị tốt trên Mobile/Tablet không (có class `.table-responsive` bọc ngoài)?
- [ ] **Design System:** Màu sắc, Font chữ, Badge (trạng thái xanh/đỏ pastel), các cột Code/Tên đã nhấn mạnh (`fw-bold text-primary` / `text-dark`) chuẩn chưa? Nút hành động có dùng chuẩn Icon ghost chưa? Thanh công cụ Tìm kiếm/Bộ lọc có đầy đủ nút Đặt lại và Layout ngang hàng chuẩn chưa?
- [ ] **Optimistic UI:** Thêm/Sửa/Xóa có cập nhật giao diện ngay lập tức (Mutation) mà không bị chớp giật hay phải đợi load lại danh sách không?
- [ ] **State Control:** Nút Submit có trạng thái Loading (`isSubmitting`) và bị Disable khi đang gọi API để chống Spam click chưa? Danh sách có trạng thái Skeleton/Spinner (`isFetching`) chưa?
- [ ] **Debounce Search:** Ô tìm kiếm có áp dụng Debounce (ngừng gõ mới tìm) thay vì gọi API liên tục không? (Hoặc tìm kiếm dưới Local).
- [ ] **Cảnh báo (Alerts):** Thao tác nguy hiểm (Xóa, Đổi mật khẩu) đã dùng `SweetAlert2` chưa? Thành công/Thất bại đã dùng `Vue Toaster` (không hiện Toast trắng) chưa?
- [ ] **Validate Frontend:** Form có cảnh báo nếu bỏ trống các trường bắt buộc trước khi gửi request xuống Server không?

## 2️⃣ KIỂM THỬ XỬ LÝ DỮ LIỆU & LOGIC (BACKEND & DATABASE)
- [ ] **FormRequest Validation:** API có class Request riêng chưa? Đã chặn dữ liệu rác (Max/Min length, Regex chặn tiếng Việt cho Password, Check Email/Phone hợp lý) chưa?
- [ ] **Chống Trùng Lặp (Unique):** Đã kiểm tra tính duy nhất (`unique`) đối với các trường như Mã, Email, Số điện thoại (kể cả lúc Create và Update) chưa?
- [ ] **DB Transaction:** Các thao tác Create/Update/Delete (đặc biệt khi tác động nhiều bảng như Role, User, Media) đã được bọc trong `DB::beginTransaction()` và `DB::rollBack()` chưa?
- [ ] **Bảo Toàn Dữ Liệu (Integrity):** Chức năng "Xóa" là Hard Delete (xóa hẳn) hay Soft Deactivate (chuyển trạng thái = 0)? Những dữ liệu liên quan Lịch sử (Đơn hàng, Khách hàng) tuyệt đối KHÔNG được xóa cứng.
- [ ] **Identity Protection:** Các trường quan trọng (Mã số định danh, Số điện thoại, Email) có bị cấm sửa đổi tùy tiện trong hàm Update để bảo vệ hệ thống chưa?
- [ ] **Xử lý File/Hình ảnh:** Upload ảnh có validate dung lượng/chuẩn ảnh chưa? Khi update ảnh mới, hệ thống có tự động xóa (unlink) ảnh cũ trong thư mục Server để tiết kiệm dung lượng không?
- [ ] **Lazy Loading APIs (Chống Deadlock):** Các useQuery có dữ liệu phụ thuộc (Dependent Dropdowns) đã được khóa lại bằng cờ `enabled: computed(() => !!parent.value)` chưa? Đảm bảo không có hiện tượng nã 3-4 APIs cùng lúc ngay khi vừa vào trang.
- [ ] **Tối Ưu Truy Vấn (Performance):** Hàm `index()` có dùng Eager Loading (`with()`) để tránh lỗi N+1 Query làm chậm hệ thống không? Dữ liệu có giới hạn/phân trang (Pagination) hoặc Cursor thay vì `get()` toàn bộ chưa?

## 3️⃣ KIỂM THỬ BẢO MẬT & PHÂN QUYỀN (SECURITY)
- [ ] **Phân Quyền (Authorization):** Endpoint API đã được bọc Middleware kiểm tra quyền (`role|permission`) chưa?
- [ ] **Ràng buộc Quan hệ (Relationship Constraint):** Trước khi xóa 1 danh mục/nhóm, hệ thống có kiểm tra xem nó đang chứa sản phẩm hay người dùng bên trong không (không cho phép xóa nếu có dữ liệu con)?

## 4️⃣ KIỂM THỬ THỰC TẾ (LIVE FUNCTIONAL & INTEGRATION TESTING) - BẮT BUỘC
- [ ] **API Endpoint Testing:** Trực tiếp dùng `php artisan tinker` hoặc NodeJS Script gọi thử vào các endpoint với dữ liệu giả lập để XÁC MINH RẰNG API không bị Crash, không văng Exception, trả về đúng chuẩn JSON (`success`, `data`, `message`).
- [ ] **Frontend - Backend Sync:** Chắc chắn rằng tên biến Frontend gửi lên (`formData.name`) khớp 100% với tên Backend mong đợi (`$request->name`) và mapping đúng xuống DB.
- [ ] **Pagination Sync:** Xác minh lại dữ liệu mảng (`Array`) trả về từ Backend Pagination (`data.data` hoặc `data.meta`) được móc nối chính xác trên giao diện Vue (Tránh lỗi màn hình trắng do sai cấp JSON).
- [ ] **Data Mapping & Cache Invalidation (Ánh xạ Dữ liệu):** Xác minh xem dữ liệu sau khi Thêm/Sửa/Xóa ở Admin có tự động ánh xạ (phản ánh ngay lập tức) ra các trang Client (Giao diện khách hàng) hoặc các trang liên quan không? Đã xử lý dọn dẹp Cache (Browser Cache, Server Cache) triệt để chưa?

---
## 🚀 KẾT LUẬN & HÀNH ĐỘNG (ACTION PLAN)
*Liệt kê các hạng mục [ ] Chưa đạt ở trên và phương án Fix cụ thể bằng Code.*

1. Lỗi 1: ... -> Hướng fix: ...
2. Lỗi 2: ... -> Hướng fix: ...


