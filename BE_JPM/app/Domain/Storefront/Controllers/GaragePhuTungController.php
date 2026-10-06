<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Storefront\Models\GaragePhuTung;
use App\Domain\IAM\Models\GarageMayThuy;
use Illuminate\Support\Facades\DB;

class GaragePhuTungController extends Controller
{
    // Lấy danh sách phụ tùng của 1 máy
    public function index($garage_id, Request $request)
    {
        try {
            $user = $request->user();
            
            // Kiểm tra máy này có thuộc về user không
            $garage = GarageMayThuy::where('id', $garage_id)->where('id_tai_khoan', $user->id)->first();
            if (!$garage) {
                return response()->json(['message' => 'Không tìm thấy máy trong Garage của bạn'], 404);
            }

            $items = GaragePhuTung::where('garage_id', $garage_id)
                ->with(['phuTung.thuongHieu'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
        }
    }

    // Thêm phụ tùng vào máy
    public function store(Request $request)
    {
        $request->validate([
            'garage_id' => 'required|exists:garage_may_thuy,id',
            'phu_tung_id' => 'required|exists:phu_tungs,id',
            'so_luong' => 'nullable|integer|min:1',
            'ghi_chu' => 'nullable|string'
        ]);

        try {
            $user = $request->user();
            
            // Kiểm tra quyền sở hữu máy
            $garage = GarageMayThuy::where('id', $request->garage_id)->where('id_tai_khoan', $user->id)->first();
            if (!$garage) {
                return response()->json(['message' => 'Không có quyền truy cập máy này'], 403);
            }

            DB::beginTransaction();
            
            $item = GaragePhuTung::updateOrCreate(
                [
                    'garage_id' => $request->garage_id,
                    'phu_tung_id' => $request->phu_tung_id,
                ],
                [
                    'so_luong' => DB::raw('so_luong + ' . ($request->so_luong ?? 1)),
                    'ghi_chu' => $request->ghi_chu
                ]
            );

            DB::commit();

            $item->load(['phuTung.thuongHieu']);

            return response()->json(['message' => 'Đã thêm phụ tùng vào máy!', 'item' => $item], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
        }
    }

    // Xóa phụ tùng khỏi máy
    public function destroy($id, Request $request)
    {
        try {
            $user = $request->user();
            
            $item = GaragePhuTung::find($id);
            if (!$item) {
                return response()->json(['message' => 'Không tìm thấy dữ liệu'], 404);
            }

            $garage = GarageMayThuy::where('id', $item->garage_id)->where('id_tai_khoan', $user->id)->first();
            if (!$garage) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $item->delete();
            return response()->json(['message' => 'Đã gỡ phụ tùng khỏi máy'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
        }
    }

    // Lấy chi tiết phụ tùng của 1 máy, gom nhóm theo nhóm phụ tùng
    public function chiTiet($garage_id, Request $request)
    {
        try {
            $user = $request->user();

            // Kiểm tra máy này có thuộc về user không
            $garage = GarageMayThuy::where('id', $garage_id)
                ->where('id_tai_khoan', $user->id)
                ->with(['dongCoModel.thuongHieu', 'dongCoModel.loaiDongCo'])
                ->first();

            if (!$garage) {
                return response()->json(['message' => 'Không tìm thấy máy trong Garage của bạn'], 404);
            }

            // Lấy tất cả phụ tùng của máy kèm eager loading
            $items = GaragePhuTung::where('garage_id', $garage_id)
                ->with(['phuTung.nhomPhuTung', 'phuTung.thuongHieu'])
                ->get();

            // Gom nhóm theo tên nhóm phụ tùng
            $grouped = $items->groupBy(function ($item) {
                return $item->phuTung?->nhomPhuTung?->ten_nhom ?? 'Chưa phân nhóm';
            })->map(function ($groupItems, $groupName) {
                $firstItem = $groupItems->first();
                return [
                    'nhom_ten' => $groupName,
                    'nhom_id'  => $firstItem->phuTung?->nhomPhuTung?->id,
                    'items'    => $groupItems->values(),
                ];
            })->values();

            return response()->json([
                'garage' => $garage,
                'groups' => $grouped,
                'total'  => $items->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật số lượng phụ tùng
    public function update($id, Request $request)
    {
        try {
            $user = $request->user();

            $item = GaragePhuTung::find($id);
            if (!$item) {
                return response()->json(['message' => 'Không tìm thấy dữ liệu'], 404);
            }

            $garage = GarageMayThuy::where('id', $item->garage_id)->where('id_tai_khoan', $user->id)->first();
            if (!$garage) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $request->validate(['so_luong' => 'required|integer|min:1']);

            $item->update(['so_luong' => $request->so_luong]);

            return response()->json(['message' => 'Đã cập nhật số lượng!', 'item' => $item]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
        }
    }
}
