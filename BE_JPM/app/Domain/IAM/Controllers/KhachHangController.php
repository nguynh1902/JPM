<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\IAM\Services\KhachHangService;
use App\Domain\Core\Resources\KhachHangResource;
use App\Domain\IAM\Requests\KhachHangRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KhachHangController extends Controller
{
    protected $khachHangService;

    public function __construct(KhachHangService $khachHangService)
    {
        $this->khachHangService = $khachHangService;
    }

    public function show($id): JsonResponse
    {
        try {
            $kh = $this->khachHangService->getById($id);
            return response()->json([
                'success' => true,
                'data' => new KhachHangResource($kh)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $khachHangs = $this->khachHangService->getAll();
            return response()->json([
                'success' => true,
                'data' => KhachHangResource::collection($khachHangs)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(KhachHangRequest $request): JsonResponse
    {
        try {
            $kh = $this->khachHangService->create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Thêm khách hàng thành công',
                'data' => new KhachHangResource($kh)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(KhachHangRequest $request, $id): JsonResponse
    {
        try {
            $kh = $this->khachHangService->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khách hàng thành công',
                'data' => new KhachHangResource($kh)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resetPassword($id): JsonResponse
    {
        try {
            $this->khachHangService->resetPassword($id);
            return response()->json([
                'success' => true,
                'message' => 'Đã reset mật khẩu về mặc định (123456789012)'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->khachHangService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa tài khoản khách hàng thành công'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json(['success' => false, 'message' => 'Không thể xóa khách hàng này vì đã phát sinh dữ liệu (đơn hàng, báo giá...). Vui lòng Khóa tài khoản thay vì xóa.'], 409);
            }
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            $code = str_contains($e->getMessage(), 'Không thể xóa') ? 409 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }
}

