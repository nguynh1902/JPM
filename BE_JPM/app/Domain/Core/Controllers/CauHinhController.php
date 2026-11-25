<?php

namespace App\Domain\Core\Controllers;

use App\Domain\Core\Services\CauHinhService;
use App\Domain\Core\Services\ImageUploadService;
use App\Domain\MarineCatalog\Services\CatalogCacheService;
use App\Domain\Sales\Services\VietQRService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CauHinhController extends Controller
{
    protected CauHinhService $cauHinhService;

    public function __construct(CauHinhService $cauHinhService)
    {
        $this->cauHinhService = $cauHinhService;
    }

    /**
     * API công khai cho Client lấy toàn bộ cấu hình website (được cache).
     *
     * @return JsonResponse
     */
    public function publicIndex(): JsonResponse
    {
        try {
            $settings = $this->cauHinhService->getPublicSettings();

            return response()->json([
                'success' => true,
                'data'    => $settings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải cấu hình hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API dành cho Quản trị viên lấy danh sách chi tiết cấu hình.
     *
     * @return JsonResponse
     */
    public function adminIndex(): JsonResponse
    {
        try {
            $settings = $this->cauHinhService->getAllForAdmin();

            return response()->json([
                'success' => true,
                'data'    => $settings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải cấu hình quản trị: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API dành cho Quản trị viên cập nhật hàng loạt cấu hình.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function adminUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
        ], [
            'settings.required' => 'Dữ liệu cấu hình không được để trống.',
            'settings.array'    => 'Dữ liệu cấu hình phải ở định dạng mảng.',
        ]);

        try {
            $updatedSettings = $this->cauHinhService->updateBatch($request->input('settings'));
            VietQRService::clearBankConfigCache();
            CatalogCacheService::bumpVersion();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cấu hình website thành công!',
                'data'    => $updatedSettings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API upload ảnh Banner cho Cấu hình hệ thống (Banner cửa hàng, sự kiện...)
     */
    public function uploadBanner(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file ảnh banner.',
            'file.image'    => 'File tải lên phải là hình ảnh hợp lệ.',
            'file.mimes'    => 'Chỉ chấp nhận định dạng JPG, PNG, WebP.',
            'file.max'      => 'Kích thước file ảnh không được vượt quá 5MB.',
        ]);

        try {
            $url = ImageUploadService::storeUploadedFile(
                $request->file('file'),
                'uploads/banners',
                'banner',
                1920
            );

            return response()->json([
                'success' => true,
                'message' => 'Tải ảnh banner lên thành công!',
                'url'     => $url,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu ảnh banner.',
            ], 500);
        }
    }
}
