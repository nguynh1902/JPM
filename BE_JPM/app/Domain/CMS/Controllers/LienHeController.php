<?php

namespace App\Domain\CMS\Controllers;

use App\Domain\CMS\Models\LienHe;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LienHeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LienHe::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$s}%")
                    ->orWhere('noi_dung', 'like', "%{$s}%");
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->input('trang_thai'));
        }

        $stats = [
            'total' => LienHe::count(),
            'moi' => LienHe::where('trang_thai', 'Mới')->count(),
            'dang_xu_ly' => LienHe::where('trang_thai', 'Đang xử lý')->count(),
            'da_phan_hoi' => LienHe::where('trang_thai', 'Đã phản hồi')->count(),
        ];

        $hasPagination = $request->has('per_page') || $request->has('page');
        $all = $request->boolean('all', false) || !$hasPagination;
        $perPage = $all ? null : min(max((int) $request->input('per_page', 30), 1), 100);

        if ($all || !$perPage) {
            return response()->json([
                'success' => true,
                'data' => $query->limit(500)->get(),
                'stats' => $stats,
            ]);
        }

        $paginated = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'stats' => $stats,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'trang_thai' => 'required|string|in:Mới,Đang xử lý,Đã phản hồi',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái xử lý.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $item = LienHe::findOrFail($id);
        $item->update(['trang_thai' => $validated['trang_thai']]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái yêu cầu hỗ trợ!',
            'data' => $item,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $item = LienHe::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa yêu cầu hỗ trợ thành công!',
        ]);
    }
}
