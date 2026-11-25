<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Domain\IAM\Controllers\AuthController;
use App\Domain\CMS\Controllers\DanhMucSanPhamController;
use App\Domain\CMS\Controllers\SanPhamController;
use App\Domain\MarineCatalog\Controllers\ThuongHieuController;
use App\Domain\IAM\Controllers\SystemLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'throttle:api']);

// Auth Routes (Public) - Protected by Rate Limiting
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:login');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:login');

// Public API Routes (Cacheable Catalog & Content) - Protected by public-api rate limit & Response Cache
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

    // Public Danh mục & Sản phẩm
    Route::get('/danh-muc-san-pham/grouped', [\App\Domain\CMS\Controllers\DanhMucSanPhamController::class, 'publicGrouped']);
    Route::get('/danh-muc-san-pham', [\App\Domain\CMS\Controllers\DanhMucSanPhamController::class, 'publicIndex']);
    Route::get('/san-pham', [\App\Domain\CMS\Controllers\SanPhamController::class, 'publicIndex']);
    Route::get('/san-pham/{slug}', [\App\Domain\CMS\Controllers\SanPhamController::class, 'publicShow']);
});

// Public PII / Dynamic Quote & Order Routes (NEVER cached by cache.public!)
Route::prefix('public')->middleware(['throttle:public-api'])->group(function () {
    Route::post('/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'publicStore']);
    Route::get('/yeu-cau-bao-gia/{token}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'showByToken']);
    Route::post('/yeu-cau-bao-gia/{token}/action', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'customerAction']);

    // VietQR Payment Info (Public, Miễn phí chuẩn Napas)
    Route::prefix('vietqr')->group(function () {
        Route::get('/don-hang/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'getDonHangQR']);
        Route::get('/bao-gia/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'getBaoGiaQR']);
        Route::get('/check-status/{type}/{code}', [\App\Domain\Sales\Controllers\PaymentController::class, 'checkStatus']);
    });
});

// Webhook thanh toán đối soát tự động (Ngân hàng / Casso / SePay) - Xác thực token + Rate limited
Route::post('/webhooks/payment/reconciliation', [\App\Domain\Sales\Controllers\PaymentController::class, 'webhookReconciliation'])
    ->middleware('throttle:120,1');

// Auth Routes (Protected)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
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
        Route::post('/profile/avatar', [\App\Domain\Storefront\Controllers\ProfileController::class, 'uploadAvatar']);
        Route::get('/profile/stats', [\App\Domain\Storefront\Controllers\ProfileController::class, 'getStats']);

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
    // 1. SYSTEM & IAM ROUTES (Phân quyền chặt theo chức năng)
    // =========================================================================

    // Cấu hình hệ thống & Tiện ích Cache (Chỉ admin / Super Admin / Cấu hình chung)
    Route::middleware(['role_or_permission:admin|Super Admin|Cấu hình chung,sanctum'])->group(function () {
        Route::get('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminIndex']);
        Route::post('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminUpdate']);
        Route::post('admin/cau-hinh/upload-banner', [\App\Domain\Core\Controllers\CauHinhController::class, 'uploadBanner']);

        Route::post('admin/he-thong/clear-cache', function (\Illuminate\Http\Request $request, \App\Domain\MarineCatalog\Services\CatalogCacheService $cacheService) {
            $warmup = $request->boolean('warmup', true);
            if ($warmup) {
                $result = $cacheService->warmUpCatalogCache();
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ và làm ấm lại Redis/File Cache cho Catalog',
                    'stats' => $result['stats'] ?? [],
                ]);
            }
            $cleared = $cacheService->clearAllCatalogCache();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ Cache Catalog',
                'cleared' => $cleared,
            ]);
        });
    });

    // System Logs & danh sách nhân viên read-only (Cấu hình chung cần đọc nhân viên để lọc nhật ký)
    Route::middleware(['role_or_permission:admin|Super Admin|Cấu hình chung|Quản lý Nhân sự|Quản lý Vai trò,sanctum'])->group(function () {
        Route::apiResource('admin/system-logs', SystemLogController::class)->only(['index']);
        Route::get('admin/nhan-vien', [\App\Domain\IAM\Controllers\NhanVienController::class, 'index']);
        Route::get('admin/nhan-vien/{id}', [\App\Domain\IAM\Controllers\NhanVienController::class, 'show']);
        Route::get('admin/vai-tro', [\App\Domain\IAM\Controllers\VaiTroController::class, 'index']);
        Route::get('admin/vai-tro/{id}', [\App\Domain\IAM\Controllers\VaiTroController::class, 'show']);
    });

    // Quản lý Nhân sự (Write)
    Route::middleware(['role_or_permission:admin|Super Admin|Quản lý Nhân sự,sanctum'])->group(function () {
        Route::post('admin/nhan-vien/{id}/reset-password', [\App\Domain\IAM\Controllers\NhanVienController::class, 'resetPassword']);
        Route::post('admin/nhan-vien', [\App\Domain\IAM\Controllers\NhanVienController::class, 'store']);
        Route::put('admin/nhan-vien/{id}', [\App\Domain\IAM\Controllers\NhanVienController::class, 'update']);
        Route::patch('admin/nhan-vien/{id}', [\App\Domain\IAM\Controllers\NhanVienController::class, 'update']);
        Route::delete('admin/nhan-vien/{id}', [\App\Domain\IAM\Controllers\NhanVienController::class, 'destroy']);
    });

    // Quản lý Vai trò (Write)
    Route::middleware(['role_or_permission:admin|Super Admin|Quản lý Vai trò,sanctum'])->group(function () {
        Route::post('admin/vai-tro', [\App\Domain\IAM\Controllers\VaiTroController::class, 'store']);
        Route::put('admin/vai-tro/{id}', [\App\Domain\IAM\Controllers\VaiTroController::class, 'update']);
        Route::patch('admin/vai-tro/{id}', [\App\Domain\IAM\Controllers\VaiTroController::class, 'update']);
        Route::delete('admin/vai-tro/{id}', [\App\Domain\IAM\Controllers\VaiTroController::class, 'destroy']);
    });

    // =========================================================================
    // 2. CMS (Tin tức, Chuyên mục, FAQ) — Tách bạch Xem / Soạn thảo / Xóa
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|Xem Tin tức & CMS|Soạn thảo Tin tức & CMS|Xóa Tin tức & CMS|Cấu hình chung,sanctum'])->group(function () {
        Route::get('admin/chuyen-muc', [\App\Domain\CMS\Controllers\ChuyenMucController::class, 'index']);
        Route::get('admin/chuyen-muc/{id}', [\App\Domain\CMS\Controllers\ChuyenMucController::class, 'show']);
        Route::get('admin/tin-tuc', [\App\Domain\CMS\Controllers\TinTucController::class, 'index']);
        Route::get('admin/tin-tuc/{id}', [\App\Domain\CMS\Controllers\TinTucController::class, 'show']);
        Route::get('admin/faq', [\App\Domain\CMS\Controllers\FaqController::class, 'index']);
        Route::get('admin/faq/{id}', [\App\Domain\CMS\Controllers\FaqController::class, 'show']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Soạn thảo Tin tức & CMS|Cấu hình chung,sanctum'])->group(function () {
        Route::post('admin/chuyen-muc', [\App\Domain\CMS\Controllers\ChuyenMucController::class, 'store']);
        Route::put('admin/chuyen-muc/{id}', [\App\Domain\CMS\Controllers\ChuyenMucController::class, 'update']);
        Route::post('admin/tin-tuc', [\App\Domain\CMS\Controllers\TinTucController::class, 'store']);
        Route::put('admin/tin-tuc/{id}', [\App\Domain\CMS\Controllers\TinTucController::class, 'update']);
        Route::post('admin/faq', [\App\Domain\CMS\Controllers\FaqController::class, 'store']);
        Route::put('admin/faq/{id}', [\App\Domain\CMS\Controllers\FaqController::class, 'update']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Xóa Tin tức & CMS|Cấu hình chung,sanctum'])->group(function () {
        Route::delete('admin/chuyen-muc/{id}', [\App\Domain\CMS\Controllers\ChuyenMucController::class, 'destroy']);
        Route::delete('admin/tin-tuc/{id}', [\App\Domain\CMS\Controllers\TinTucController::class, 'destroy']);
        Route::delete('admin/faq/{id}', [\App\Domain\CMS\Controllers\FaqController::class, 'destroy']);
    });

    // =========================================================================
    // 3. KHO HÀNG & TỒN KHO (Khôi phục đầy đủ GET routes + tách quyền Read/Write)
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|Xem Tồn kho|Cập nhật Tồn kho|Xem Thẻ kho|Quản lý Danh sách kho|Quản lý Đơn nhập PO,sanctum'])->group(function () {
        Route::get('admin/kho', [\App\Domain\Procurement\Controllers\KhoController::class, 'index']);
        Route::get('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'show']);
        Route::get('admin/ton-kho/hang-can-nhap', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'hangCanNhap']);
        Route::get('admin/ton-kho', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'index']);
        Route::get('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'show']);
        Route::get('admin/the-kho', [\App\Domain\Procurement\Controllers\TheKhoController::class, 'index']);
        Route::get('admin/the-kho/{id}', [\App\Domain\Procurement\Controllers\TheKhoController::class, 'show']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Quản lý Danh sách kho,sanctum'])->group(function () {
        Route::post('admin/kho', [\App\Domain\Procurement\Controllers\KhoController::class, 'store']);
        Route::put('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'update']);
        Route::delete('admin/kho/{id}', [\App\Domain\Procurement\Controllers\KhoController::class, 'destroy']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Cập nhật Tồn kho|Quản lý Danh sách kho,sanctum'])->group(function () {
        Route::post('admin/ton-kho', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'store']);
        Route::put('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'update']);
        Route::delete('admin/ton-kho/{id}', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'destroy']);
    });

    // =========================================================================
    // 4. CATALOG, PIM & SẢN PHẨM — Tách bạch Thêm / Sửa / Xóa
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|Nhân viên PIM|Thêm Động cơ & Thương hiệu|Sửa Động cơ & Thương hiệu,sanctum'])->group(function () {
        Route::post('admin/thuong-hieu', [ThuongHieuController::class, 'store']);
        Route::put('admin/thuong-hieu/{id}', [ThuongHieuController::class, 'update']);
        Route::post('admin/loai-dong-co', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'store']);
        Route::put('admin/loai-dong-co/{id}', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'update']);
        Route::post('admin/dong-co-model', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'store']);
        Route::put('admin/dong-co-model/{id}', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'update']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Xóa Động cơ & Thương hiệu,sanctum'])->group(function () {
        Route::delete('admin/thuong-hieu/{id}', [ThuongHieuController::class, 'destroy']);
        Route::delete('admin/loai-dong-co/{id}', [\App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class, 'destroy']);
        Route::delete('admin/dong-co-model/{id}', [\App\Domain\MarineCatalog\Controllers\DongCoModelController::class, 'destroy']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Nhân viên PIM|Thêm Phụ tùng|Sửa Phụ tùng,sanctum'])->group(function () {
        Route::post('admin/nhom-phu-tung', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'store']);
        Route::put('admin/nhom-phu-tung/{id}', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'update']);
        Route::post('admin/phu-tung', [\App\Domain\PIM\Controllers\PhuTungController::class, 'store']);
        Route::put('admin/phu-tung/{id}', [\App\Domain\PIM\Controllers\PhuTungController::class, 'update']);
        Route::post('admin/so-do', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'store']);
        Route::put('admin/so-do/{id}', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'update']);
        Route::post('admin/ma-thay-the', [\App\Domain\PIM\Controllers\MaThayTheController::class, 'store']);
        Route::put('admin/ma-thay-the/{id}', [\App\Domain\PIM\Controllers\MaThayTheController::class, 'update']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Xóa Phụ tùng,sanctum'])->group(function () {
        Route::delete('admin/nhom-phu-tung/{id}', [\App\Domain\PIM\Controllers\NhomPhuTungController::class, 'destroy']);
        Route::delete('admin/phu-tung/{id}', [\App\Domain\PIM\Controllers\PhuTungController::class, 'destroy']);
        Route::delete('admin/so-do/{id}', [\App\Domain\PIM\Controllers\SoDoPhuTungController::class, 'destroy']);
        Route::delete('admin/ma-thay-the/{id}', [\App\Domain\PIM\Controllers\MaThayTheController::class, 'destroy']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Nhân viên PIM|Thêm Sản phẩm|Sửa Sản phẩm,sanctum'])->group(function () {
        Route::post('admin/danh-muc-san-pham', [DanhMucSanPhamController::class, 'store']);
        Route::put('admin/danh-muc-san-pham/{id}', [DanhMucSanPhamController::class, 'update']);
        Route::post('admin/san-pham', [SanPhamController::class, 'store']);
        Route::put('admin/san-pham/{id}', [SanPhamController::class, 'update']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Xóa Sản phẩm,sanctum'])->group(function () {
        Route::delete('admin/danh-muc-san-pham/{id}', [DanhMucSanPhamController::class, 'destroy']);
        Route::delete('admin/san-pham/{id}', [SanPhamController::class, 'destroy']);
    });

    // =========================================================================
    // 5. KHÁCH HÀNG — Tách bạch Thêm/Sửa vs Khóa/Xóa
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|Thêm Khách hàng|Sửa Khách hàng,sanctum'])->group(function () {
        Route::post('admin/khach-hang', [\App\Domain\IAM\Controllers\KhachHangController::class, 'store']);
        Route::put('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'update']);
        Route::patch('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'update']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Khóa Khách hàng,sanctum'])->group(function () {
        Route::post('admin/khach-hang/{id}/reset-password', [\App\Domain\IAM\Controllers\KhachHangController::class, 'resetPassword']);
        Route::delete('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'destroy']);
    });

    // =========================================================================
    // 6. BÁN HÀNG, BÁO GIÁ, THANH TOÁN & THÔNG BÁO (Write tách khỏi Read-Only)
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|Duyệt Đơn hàng|Sửa Đơn hàng,sanctum'])->group(function () {
        Route::post('admin/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'adminStore']);
        Route::put('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'update']);
        Route::post('admin/yeu-cau-bao-gia/{id}/chuyen-don', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'convertToOrder']);
        Route::post('admin/yeu-cau-bao-gia/{id}/xac-nhan-coc', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'confirmDeposit']);
        Route::post('admin/yeu-cau-bao-gia/{id}/hoan-thanh', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'completeQuote']);

        Route::put('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'update']);
        Route::post('admin/don-hang/{id}/xac-nhan-coc', [\App\Domain\Sales\Controllers\DonHangController::class, 'confirmDeposit']);
        Route::post('admin/don-hang/{id}/cap-nhat-van-chuyen', [\App\Domain\Sales\Controllers\DonHangController::class, 'updateShipping']);
        Route::post('admin/don-hang/{id}/hoan-thanh', [\App\Domain\Sales\Controllers\DonHangController::class, 'completeOrder']);

        Route::post('admin/sales/upload-bien-lai', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'uploadReceiptImage']);
        Route::post('admin/sales/remove-bien-lai', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'removeReceiptImage']);

        Route::post('admin/thong-bao/gui-tin', [\App\Domain\Communication\Controllers\NotificationController::class, 'sendCustom']);
        Route::post('admin/thong-bao/gui-chanh-xe/{orderId}', [\App\Domain\Communication\Controllers\NotificationController::class, 'sendOrderShippingNotice']);
    });

    Route::middleware(['role_or_permission:admin|Super Admin|Xóa Đơn hàng,sanctum'])->group(function () {
        Route::delete('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'destroy']);
        Route::delete('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'destroy']);
    });

    // =========================================================================
    // 7. READ-ONLY OPERATIONS ROUTES (Dành cho nhân sự nội bộ có quyền Xem)
    // =========================================================================
    Route::middleware(['role_or_permission:admin|Super Admin|staff|Nhân viên PIM|Xem Phụ tùng|Thêm Phụ tùng|Sửa Phụ tùng|Xem Động cơ & Thương hiệu|Xem Đơn hàng|Duyệt Đơn hàng|Sửa Đơn hàng|Xem Khách hàng|Xem Tồn kho|Xem Thẻ kho|Quản lý Danh sách kho|Xem Sản phẩm|Xem Tin tức & CMS|Quản lý Nhân sự|Quản lý Vai trò|Cấu hình chung,sanctum'])->group(function () {
        // Dashboard Stats
        Route::get('admin/dashboard/stats', [\App\Domain\Analytics\Controllers\DashboardController::class, 'stats']);

        // Tra cứu Khách hàng (Read-Only)
        Route::get('admin/khach-hang', [\App\Domain\IAM\Controllers\KhachHangController::class, 'index']);
        Route::get('admin/khach-hang/{id}', [\App\Domain\IAM\Controllers\KhachHangController::class, 'show']);

        // Tra cứu Catalog, Sản phẩm, Phụ tùng, Sơ đồ, Model (Read-Only)
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

        // Xem Đơn hàng, Báo giá & Xuất PO Nhật
        Route::get('admin/yeu-cau-bao-gia', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'index']);
        Route::get('admin/yeu-cau-bao-gia/{id}', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'show']);
        Route::get('admin/don-hang/xuat-excel-po-nhat', [\App\Domain\Sales\Controllers\DonHangController::class, 'exportJapanPO']);
        Route::get('admin/don-hang', [\App\Domain\Sales\Controllers\DonHangController::class, 'index']);
        Route::get('admin/don-hang/{id}', [\App\Domain\Sales\Controllers\DonHangController::class, 'show']);

        // Xem Lịch sử Thông báo & Giao dịch thanh toán
        Route::get('admin/thong-bao/lich-su', [\App\Domain\Communication\Controllers\NotificationController::class, 'adminNotifications']);
        Route::get('admin/thanh-toan/giao-dich', [\App\Domain\Sales\Controllers\PaymentController::class, 'adminTransactions']);
    });
});
