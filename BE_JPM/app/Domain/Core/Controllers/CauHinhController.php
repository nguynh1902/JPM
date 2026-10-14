<?php

namespace App\Domain\Core\Controllers;

use App\Domain\Core\Services\CauHinhService;
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
}
