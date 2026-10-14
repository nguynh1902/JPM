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

    // Admin Routes (Được bảo vệ bởi Role Admin & Staff)
    Route::middleware(['role:admin|Nhân viên PIM,sanctum'])->group(function () {
        Route::get('admin/dashboard/stats', [\App\Domain\Analytics\Controllers\DashboardController::class, 'stats']);
        Route::get('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminIndex']);
        Route::post('admin/cau-hinh', [\App\Domain\Core\Controllers\CauHinhController::class, 'adminUpdate']);
        Route::apiResource('admin/banner', BannerController::class);
        Route::apiResource('admin/voucher', VoucherController::class);
        Route::apiResource('admin/system-logs', SystemLogController::class)->only(['index']);
        Route::apiResource('admin/thuong-hieu', ThuongHieuController::class);
        Route::apiResource('admin/loai-dong-co', \App\Domain\MarineCatalog\Controllers\LoaiDongCoController::class);
        Route::apiResource('admin/dong-co-model', \App\Domain\MarineCatalog\Controllers\DongCoModelController::class);
        Route::apiResource('admin/danh-muc-san-pham', DanhMucSanPhamController::class);
        Route::apiResource('admin/san-pham', SanPhamController::class);

        // HR & Customers
        Route::post('admin/khach-hang/{id}/reset-password', [\App\Domain\IAM\Controllers\KhachHangController::class, 'resetPassword']);
        Route::apiResource('admin/khach-hang', \App\Domain\IAM\Controllers\KhachHangController::class);
        
        Route::post('admin/nhan-vien/{id}/reset-password', [\App\Domain\IAM\Controllers\NhanVienController::class, 'resetPassword']);
        Route::apiResource('admin/nhan-vien', \App\Domain\IAM\Controllers\NhanVienController::class);
        Route::apiResource('admin/vai-tro', \App\Domain\IAM\Controllers\VaiTroController::class);
        Route::apiResource('admin/yeu-cau-bao-gia', \App\Domain\Sales\Controllers\YeuCauBaoGiaController::class);
        Route::post('admin/yeu-cau-bao-gia/{id}/chuyen-don', [\App\Domain\Sales\Controllers\YeuCauBaoGiaController::class, 'convertToOrder']);
        
        // Kho & Orders (Giai đoạn 4)
        Route::get('admin/ton-kho/hang-can-nhap', [\App\Domain\Procurement\Controllers\TonKhoController::class, 'hangCanNhap']);
        Route::apiResource('admin/kho', \App\Domain\Procurement\Controllers\KhoController::class);
        Route::apiResource('admin/ton-kho', \App\Domain\Procurement\Controllers\TonKhoController::class);
        Route::apiResource('admin/the-kho', \App\Domain\Procurement\Controllers\TheKhoController::class);
        Route::apiResource('admin/don-hang', \App\Domain\Sales\Controllers\DonHangController::class);
        Route::post('admin/don-hang/{id}/tao-po', [\App\Domain\Sales\Controllers\DonHangController::class, 'convertToPO']);
        Route::post('admin/don-nhap-hang/gom-po', [\App\Domain\Procurement\Controllers\DonNhapHangController::class, 'gomPO']);
        Route::apiResource('admin/don-nhap-hang', \App\Domain\Procurement\Controllers\DonNhapHangController::class);
        
        // Phụ tùng & Sơ đồ (Giai đoạn 4)
        Route::apiResource('admin/ma-thay-the', \App\Domain\PIM\Controllers\MaThayTheController::class);
        Route::apiResource('admin/phu-tung', \App\Domain\PIM\Controllers\PhuTungController::class);
        Route::apiResource('admin/nhom-phu-tung', \App\Domain\PIM\Controllers\NhomPhuTungController::class);
        Route::apiResource('admin/so-do', \App\Domain\PIM\Controllers\SoDoPhuTungController::class);

        // Nội dung (Giai đoạn 4)
        Route::apiResource('admin/chuyen-muc', \App\Domain\CMS\Controllers\ChuyenMucController::class);
        Route::apiResource('admin/tin-tuc', \App\Domain\CMS\Controllers\TinTucController::class);
        Route::apiResource('admin/faq', \App\Domain\CMS\Controllers\FaqController::class);
    });
});



















