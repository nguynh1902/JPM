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
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search']);
            $hasPagination = $request->has('per_page') || $request->has('page');
            $all = $request->boolean('all', false) || !$hasPagination;
            $perPage = $all ? null : min(max((int) $request->input('per_page', 50), 1), 100);

            $khachHangs = $this->khachHangService->getAll($filters, $perPage);

            if ($all || !$perPage) {
                return response()->json([
                    'success' => true,
                    'data' => KhachHangResource::collection($khachHangs)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => KhachHangResource::collection($khachHangs->items()),
                'meta' => [
                    'current_page' => $khachHangs->currentPage(),
                    'last_page'    => $khachHangs->lastPage(),
                    'per_page'     => $khachHangs->perPage(),
                    'total'        => $khachHangs->total(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(KhachHangRequest $request): JsonResponse
    {
        try {
            $kh = $this->khachHangService->create($request->validated());
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
            $data = $request->validated();
            if ($request->boolean('remove_avatar')) {
                $data['remove_avatar'] = true;
            }
            $kh = $this->khachHangService->update($id, $data);
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
            $tempPassword = $this->khachHangService->resetPassword($id);
            return response()->json([
                'success' => true,
                'message' => "Đã đặt lại mật khẩu thành công! Mật khẩu tạm thời: {$tempPassword}",
                'temporary_password' => $tempPassword,
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

    public function addAddress(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:100',
            'so_dien_thoai'  => 'required|string|max:20',
            'dia_chi_cu_the' => 'required|string|max:255',
            'loai_dia_chi'   => 'nullable|string|max:20',
            'la_mac_dinh'    => 'nullable|boolean',
        ], [
            'ten_nguoi_nhan.required' => 'Vui lòng nhập tên người nhận.',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại nhận hàng.',
            'dia_chi_cu_the.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        ]);

        try {
            $kh = $this->khachHangService->addAddress((int) $id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm địa chỉ giao hàng',
                'data' => new KhachHangResource($kh),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateAddress(Request $request, $id, $addressId): JsonResponse
    {
        $validated = $request->validate([
            'ten_nguoi_nhan' => 'required|string|max:100',
            'so_dien_thoai'  => 'required|string|max:20',
            'dia_chi_cu_the' => 'required|string|max:255',
            'loai_dia_chi'   => 'nullable|string|max:20',
            'la_mac_dinh'    => 'nullable|boolean',
        ], [
            'ten_nguoi_nhan.required' => 'Vui lòng nhập tên người nhận.',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại nhận hàng.',
            'dia_chi_cu_the.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        ]);

        try {
            $kh = $this->khachHangService->updateAddress((int) $id, (int) $addressId, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật địa chỉ giao hàng',
                'data' => new KhachHangResource($kh),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function setDefaultAddress($id, $addressId): JsonResponse
    {
        try {
            $kh = $this->khachHangService->setDefaultAddress((int) $id, (int) $addressId);
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt làm địa chỉ mặc định',
                'data' => new KhachHangResource($kh),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteAddress($id, $addressId): JsonResponse
    {
        try {
            $kh = $this->khachHangService->deleteAddress((int) $id, (int) $addressId);
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa địa chỉ giao hàng',
                'data' => new KhachHangResource($kh),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

