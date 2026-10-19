<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Domain\IAM\Controllers\AuthController;
use App\Domain\CMS\Controllers\DanhMucSanPhamController;
use App\Domain\CMS\Controllers\SanPhamController;
use App\Domain\MarineCatalog\Controllers\ThuongHieuController;
use App\Domain\CMS\Controllers\BannerController;
use App\Domain\CMS\Controllers\VoucherController;
use App\Domain\IAM\Controllers\SystemLogController;

// Auth Routes (Public) - Protected by Rate Limiting
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');

// Public API Routes cho khách hàng - Protected by public-api rate limit & Response Cache
Route::prefix('public')->middleware(['throttle:public-api', 'cache.public'])->group(function () {
    Route::get('/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'publicIndex']);
    Route::get('/thuong-hieu', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'thuongHieu']);
    Route::get('/thuong-hieu/{code}', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'thuongHieuChiTiet']);
    Route::get('/loai-dong-co', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'loaiDongCo']);
    Route::get('/nhom-phu-tung', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'nhomPhuTung']);
    Route::get('/dong-co-model', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'dongCoModel']);
    Route::get('/so-do-model/{modelCode}', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'soDoModel']);
        Route::get('/so-do-by-id/{modelId}', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'soDoByModelId']);
    Route::get('/nam-san-xuat', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'namSanXuat']);
    Route::get('/tra-cuu', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'traCuu']);
    Route::get('/tra-cuu-so-do', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'traCuuSoDo']);
    Route::post('/tra-cuu-nhanh', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'traCuuNhanh']);
    Route::get('/quick-search', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'quickSearch']);
    Route::get('/phu-tung/{code}', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'chiTietPhuTung']);
    Route::get('/phu-tung-noi-bat', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'phuTungNoiBat']);
      Route::get('/san-pham-ban-chay', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'sanPhamBanChay']);
      Route::get('/faqs', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'faqs']);
    Route::post('/lien-he', [\App\Domain\Storefront\Controllers\TraCuuController::class, 'submitLienHe']);
    Route::get('/tin-tuc-moi-nhat', [\App\Domain\Storefront\Controllers\TinTucController::class, 'moiNhat']);
    Route::get('/tin-tuc', [\App\Domain\Storefront\Controllers\TinTucController::class, 'danhSach']);
    Route::get('/tin-tuc-categories', [\App\Domain\Storefront\Controllers\TinTucController::class, 'categories']);
    Route::get('/tin-tuc/{id}', [\App\Domain\Storefront\Controllers\TinTucController::class, 'chiTiet']);
    
    // Public: Garage Dropdowns
    Route::get('/garage/thuong-hieu', [\App\Domain\Storefront\Controllers\GarageController::class, 'getThuongHieu']);
    Route::get('/garage/models-by-brand', [\App\Domain\Storefront\Controllers\GarageController::class, 'getModelsByBrand']);

    // Public: Yêu Cầu Báo Giá từ Guest (Không cần đăng nhập)
    Route::post('/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'publicStore']);
    Route::get('/yeu-cau-bao-gia/{token}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'showByToken']);
    Route::post('/yeu-cau-bao-gia/{token}/action', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'customerAction']);

    // Public Danh mục & Sản phẩm
    Route::get('/danh-muc-san-pham/grouped', [\App\Domain\CMS\Controllers\DanhMucSanPhamController::class, 'publicGrouped']);
    Route::get('/danh-muc-san-pham', [\App\Domain\CMS\Controllers\DanhMucSanPhamController::class, 'publicIndex']);
    Route::get('/san-pham', [\App\Domain\CMS\Controllers\SanPhamController::class, 'publicIndex']);
    Route::get('/san-pham/{slug}', [\App\Domain\CMS\Controllers\SanPhamController::class, 'publicShow']);
});

// VietQR Payment & Polling (Public, No Response Cache)
Route::prefix('public/vietqr')->group(function () {
    Route::get('/don-hang/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'getDonHangQR']);
    Route::get('/bao-gia/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'getBaoGiaQR']);
    Route::get('/check-status/{type}/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'checkStatus']);
});

// Payment Gateway Webhooks (Banking / SePay / Casso IPN)
Route::prefix('webhooks')->group(function () {
    Route::post('/payment/reconciliation', [\App\Domain\Sales\Controllers\PaymentController::class, 'webhookReconciliation']);
    Route::post('/sepay', [\App\Domain\Sales\Controllers\PaymentController::class, 'webhookReconciliation']);
    Route::post('/casso', [\App\Domain\Sales\Controllers\PaymentController::class, 'webhookReconciliation']);
});

// Auth Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/me', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/me/deactivate', [AuthController::class, 'deactivateAccount']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Client Routes
    Route::prefix('client')->group(function () {
        Route::get('/profile', [\App\Domain\Storefront\Controllers\ProfileController::class, 'getProfile']);
        Route::put('/profile', [\App\Domain\Storefront\Controllers\ProfileController::class, 'updateProfile']);
        Route::put('/profile/password', [\App\Domain\Storefront\Controllers\ProfileController::class, 'changePassword']);
        Route::put('/profile/email', [\App\Domain\Storefront\Controllers\ProfileController::class, 'changeEmail']);
        Route::get('/vat-tu-luu-tru', [\App\Domain\Storefront\Controllers\VatTuLuuTruController::class, 'index']);
        Route::post('/vat-tu-luu-tru', [\App\Domain\Storefront\Controllers\VatTuLuuTruController::class, 'store']);
        Route::delete('/vat-tu-luu-tru/{san_pham_id}', [\App\Domain\Storefront\Controllers\VatTuLuuTruController::class, 'destroy']);

        Route::get('/profile/addresses', [\App\Domain\Storefront\Controllers\ProfileController::class, 'getAddresses']);
        Route::post('/profile/addresses', [\App\Domain\Storefront\Controllers\ProfileController::class, 'addAddress']);
        Route::put('/profile/addresses/{id}', [\App\Domain\Storefront\Controllers\ProfileController::class, 'updateAddress']);
        Route::delete('/profile/addresses/{id}', [\App\Domain\Storefront\Controllers\ProfileController::class, 'deleteAddress']);
        Route::put('/profile/addresses/{id}/set-default', [\App\Domain\Storefront\Controllers\ProfileController::class, 'setDefaultAddress']);
        
        // Cart Data
        Route::get('/profile/cart', [\App\Domain\Storefront\Controllers\ProfileController::class, 'getCart']);
        Route::post('/profile/cart', [\App\Domain\Storefront\Controllers\ProfileController::class, 'updateCart']);
        
        // Notifications
        Route::get('/notifications', [\App\Domain\Storefront\Controllers\ProfileController::class, 'getNotifications']);
        Route::post('/notifications/read', [\App\Domain\Storefront\Controllers\ProfileController::class, 'markNotificationsAsRead']);

        // History Routes
        Route::get('/history/quotes', [\App\Domain\Storefront\Controllers\HistoryController::class, 'getQuotes']);
        Route::get('/history/orders', [\App\Domain\Storefront\Controllers\HistoryController::class, 'getOrders']);
        
        // Garage Routes
        Route::get('/garage', [\App\Domain\Storefront\Controllers\GarageController::class, 'index']);
        Route::post('/garage', [\App\Domain\Storefront\Controllers\GarageController::class, 'store']);
        Route::put('/garage/{id}', [\App\Domain\Storefront\Controllers\GarageController::class, 'update']);
        Route::delete('/garage/{id}', [\App\Domain\Storefront\Controllers\GarageController::class, 'destroy']);

        // Garage Phụ Tùng Routes
        Route::get('/garage/{garage_id}/phu-tung', [\App\Domain\Storefront\Controllers\GaragePhuTungController::class, 'index']);
        Route::get('/garage/{garage_id}/chi-tiet', [\App\Domain\Storefront\Controllers\GaragePhuTungController::class, 'chiTiet']);
        Route::post('/garage-phu-tung', [\App\Domain\Storefront\Controllers\GaragePhuTungController::class, 'store']);
        Route::patch('/garage-phu-tung/{id}', [\App\Domain\Storefront\Controllers\GaragePhuTungController::class, 'update']);
        Route::delete('/garage-phu-tung/{id}', [\App\Domain\Storefront\Controllers\GaragePhuTungController::class, 'destroy']);
    });

    // =========================================================================
    // 1. STRICTLY ADMIN-ONLY ROUTES (Chỉ Admin mới có quyền truy cập)
    // =========================================================================
    Route::middleware(['role:admin,sanctum'])->group(function () {
        // Cấu hình hệ thống (API keys, cổng thanh toán, email...)
        Route::get('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminIndex']);
        Route::post('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminUpdate']);

        // Quản lý Nhân sự & Phân quyền
        Route::post('admin/nhan-vien/{id}/reset-password', [\App\Domain\IAM\Controllers\NhanVienController::class, 'resetPassword']);
        Route::apiResource('admin/nhan-vien', \App\Domain\IAM\Controllers\NhanVienController::class);
        Route::apiResource('admin/vai-tro', \App\Domain\IAM\Controllers\VaiTroController::class);

        // Nhật ký hệ thống / Audit Logs
        Route::apiResource('admin/system-logs', SystemLogController::class)->only(['index']);

        // Marketing, Banner, Voucher
        Route::apiResource('admin/banner', BannerController::class);
        Route::apiResource('admin/voucher', VoucherController::class);



        // Quản trị Danh mục Kho (Thêm/Sửa/Xóa Kho chứa)
        Route::post('admin/kho', [\App\Domain\Procurement\Controllers\KhoController::class, 'store']);
        Route::put('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'update']);
        Route::delete('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'destroy']);

        // Nội dung & Tin tức & FAQ
        Route::apiResource('admin/chuyen-muc', \App\Domain\CMS\Controllers\ChuyenMucController::class);
        Route::apiResource('admin/tin-tuc', \App\Domain\CMS\Controllers\TinTucController::class);
        Route::apiResource('admin/faq', \App\Domain\CMS\Controllers\FaqController::class);

        // Master Catalog: Thêm/Sửa/Xóa Thương hiệu, Loại động cơ, Nhóm phụ tùng, Danh mục SP
        Route::post('admin/thuong-hieu', [ThuongHieuController::class, 'store']);
        Route::put('admin/thuong-hieu/{id}', [ThuongHieuController::class, 'update']);
        Route::delete('admin/thuong-hieu/{id}', [ThuongHieuController::class, 'destroy']);

        Route::post('admin/loai-dong-co', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'store']);
        Route::put('admin/loai-dong-co/{id}', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'update']);
        Route::delete('admin/loai-dong-co/{id}', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'destroy']);

        Route::post('admin/nhom-phu-tung', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'store']);
        Route::put('admin/nhom-phu-tung/{id}', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'update']);
        Route::delete('admin/nhom-phu-tung/{id}', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'destroy']);

        Route::post('admin/danh-muc-san-pham', [DanhMucSanPhamController::class, 'store']);
        Route::put('admin/danh-muc-san-pham/{id}', [DanhMucSanPhamController::class, 'update']);
        Route::delete('admin/danh-muc-san-pham/{id}', [DanhMucSanPhamController::class, 'destroy']);

        // Hành động Quản trị nhạy cảm: Reset mật khẩu KH, Thêm/Sửa/Xóa KH
        Route::post('admin/khach-hang/{id}/reset-password', [\App\Domain\IAM\Controllers\KhachHangController::class, 'resetPassword']);
        Route::post('admin/khach-hang', [\App\Domain\IAM\Controllers\KhachHangController::class, 'store']);
        Route::put('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'update']);
        Route::patch('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'update']);
        Route::delete('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'destroy']);

        // Thêm/Sửa/Xóa Sản phẩm & Phụ tùng & Model & Sơ đồ & Tồn kho & Xóa đơn
        Route::post('admin/san-pham', [SanPhamController::class, 'store']);
        Route::put('admin/san-pham/{id}', [SanPhamController::class, 'update']);
        Route::delete('admin/san-pham/{id}', [SanPhamController::class, 'destroy']);

        Route::post('admin/phu-tung', [\App\Domain\PIM\Controllers\PhuTungController::class, 'store']);
        Route::put('admin/phu-tung/{id}', [\App\Domain\PIM\Controllers\PhuTungController::class, 'update']);
        Route::delete('admin/phu-tung/{id}', [\App\Domain\PIM\Controllers\PhuTungController::class, 'destroy']);

        Route::post('admin/dong-co-model', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'store']);
        Route::put('admin/dong-co-model/{id}', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'update']);
        Route::delete('admin/dong-co-model/{id}', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'destroy']);

        Route::post('admin/so-do', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'store']);
        Route::put('admin/so-do/{id}', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'update']);
        Route::delete('admin/so-do/{id}', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'destroy']);

        Route::post('admin/ton-kho', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'store']);
        Route::put('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'update']);
        Route::delete('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'destroy']);

        Route::delete('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'destroy']);
        Route::delete('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'destroy']);

        // Tiện ích Cache & Tối ưu hóa Catalog
        Route::post('admin/he-thong/clear-cache', function (\Illuminate\Http\Request $request, \App\Domain\MarineCatalog\Services\CatalogCacheService $cacheService) {
            $warmup = $request->boolean('warmup', true);
            if ($warmup) {
                $result = $cacheService->warmUpCatalogCache();
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ và làm ấm lại Redis/File Cache cho Catalog',
                    'stats' => $result['stats'] ?? [],
                ]);
            } else {
                $cleared = $cacheService->clearAllCatalogCache();
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ Cache Catalog',
                    'cleared' => $cleared,
                ]);
            }
        });
    });

    // =========================================================================
    // 2. OPERATIONS ROUTES (Dành cho cả ADMIN và STAFF / Nhân viên vận hành)
    // =========================================================================
    Route::middleware(['role:admin|staff|Nhân viên PIM,sanctum'])->group(function () {
        // Dashboard Stats
        Route::get('admin/dashboard/stats', [\App\Domain\Analytics\Controllers\DashboardController::class, 'stats']);

        // Tra cứu Khách hàng (Xem danh sách & Chi tiết để giao hàng/tư vấn)
        Route::get('admin/khach-hang', [\App\Domain\IAM\Controllers\KhachHangController::class, 'index']);
        Route::get('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'show']);

        // Tra cứu Catalog, Sản phẩm, Phụ tùng, Sơ đồ, Model (Read-Only đối với Staff)
        Route::get('admin/thuong-hieu', [ThuongHieuController::class, 'index']);
        Route::get('admin/thuong-hieu/{id}', [ThuongHieuController::class, 'show']);
        Route::get('admin/loai-dong-co', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'index']);
        Route::get('admin/loai-dong-co/{id}', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'show']);
        Route::get('admin/nhom-phu-tung', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'index']);
        Route::get('admin/nhom-phu-tung/{id}', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'show']);
        Route::get('admin/danh-muc-san-pham', [DanhMucSanPhamController::class, 'index']);
        Route::get('admin/danh-muc-san-pham/{id}', [DanhMucSanPhamController::class, 'show']);
        Route::get('admin/san-pham', [SanPhamController::class, 'index']);
        Route::get('admin/san-pham/{id}', [SanPhamController::class, 'show']);
        Route::get('admin/phu-tung', [\App\Domain\PIM\Controllers\PhuTungController::class, 'index']);
        Route::get('admin/phu-tung/{id}', [\App\Domain\PIM\Controllers\PhuTungController::class, 'show']);
        Route::get('admin/dong-co-model', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'index']);
        Route::get('admin/dong-co-model/{id}', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'show']);
        Route::get('admin/so-do', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'index']);
        Route::get('admin/so-do/{id}', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'show']);
        Route::get('admin/ma-thay-the', [\App\Domain\PIM\Controllers\MaThayTheController::class, 'index']);
        Route::get('admin/ma-thay-the/{id}', [\App\Domain\PIM\Controllers\MaThayTheController::class, 'show']);

        // Nghiệp vụ Bán hàng & Báo giá (Staff thao tác trực tiếp)
        Route::get('admin/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'index']);
        Route::get('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'show']);
        Route::post('admin/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'store']);
        Route::put('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'update']);
        Route::delete('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'destroy']);
        Route::post('admin/yeu-cau-bao-gia/{id}/chuyen-don', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'convertToOrder']);

        // Nghiệp vụ Đơn hàng
        Route::get('admin/don-hang/xuat-excel-po-nhat', [\App\Domain\Sales\Controllers\DonHangController::class, 'exportJapanPO']);
        Route::get('admin/don-hang', [\App\Domain\Sales\Controllers\DonHangController::class, 'index']);
        Route::get('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'show']);
        Route::put('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'update']);
        Route::delete('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'destroy']);

        // Nghiệp vụ Kho (Xem tồn kho, cảnh báo cạn, xem thẻ kho & xuất file)
        Route::get('admin/kho', [\App\Domain\Procurement\Controllers\KhoController::class, 'index']);
        Route::get('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'show']);
        Route::get('admin/ton-kho', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'index']);
        Route::get('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'show']);
        Route::get('admin/ton-kho/hang-can-nhap', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'hangCanNhap']);
        Route::get('admin/the-kho', [\App\Domain\Procurement\Controllers\TheKhoController::class, 'index']);
        Route::get('admin/the-kho/{id}', [\App\Domain\Procurement\Controllers\TheKhoController::class, 'show']);

        // Nghiệp vụ VietQR & Đối soát thanh toán
        Route::get('admin/thanh-toan/giao-dich', [\App\Domain\Sales\Controllers\PaymentController::class, 'adminTransactions']);
        Route::post('admin/thanh-toan/mo-phong', [\App\Domain\Sales\Controllers\PaymentController::class, 'adminSimulatePayment']);

        // Nghiệp vụ Thông báo Zalo ZNS / SMS Chành xe & Khách hàng
        Route::get('admin/thong-bao/lich-su', [\App\Domain\Communication\Controllers\NotificationController::class, 'adminNotifications']);
        Route::post('admin/thong-bao/gui-tin', [\App\Domain\Communication\Controllers\NotificationController::class, 'sendCustom']);
        Route::post('admin/thong-bao/gui-chanh-xe/{orderId}', [\App\Domain\Communication\Controllers\NotificationController::class, 'sendOrderShippingNotice']);
    });
});



















