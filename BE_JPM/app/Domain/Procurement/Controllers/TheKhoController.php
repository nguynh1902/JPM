<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Core\Resources\TheKhoResource;
use Illuminate\Http\Request;
use Exception;

class TheKhoController extends Controller
{
    /**
     * Danh sách lịch sử giao dịch kho (Thẻ kho)
     */
    public function show($id)
    {
        try {
            $item = TheKho::with(['kho', 'sanPham', 'phuTung', 'nguoiThucHien'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => new TheKhoResource($item)]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thẻ kho.'], 404);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = TheKho::with(['kho', 'sanPham', 'phuTung', 'nguoiThucHien'])
                ->orderBy('id', 'desc');

            if ($request->filled('warehouse_id')) {
                $query->where('id_kho', $request->warehouse_id);
            }

            if ($request->filled('loai_giao_dich')) {
                $query->where('loai_giao_dich', $request->loai_giao_dich);
            }

            if ($request->filled('tu_ngay')) {
                $query->whereDate('created_at', '>=', $request->tu_ngay);
            }

            if ($request->filled('den_ngay')) {
                $query->whereDate('created_at', '<=', $request->den_ngay);
            }

            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function ($q) use ($kw) {
                    $q->where('ma_tham_chieu', 'like', "%{$kw}%")
                      ->orWhere('ghi_chu', 'like', "%{$kw}%")
                      ->orWhereHas('phuTung', function ($sub) use ($kw) {
                          $sub->where('ma_phu_tung', 'like', "%{$kw}%")
                              ->orWhere('ten_phu_tung', 'like', "%{$kw}%");
                      })
                      ->orWhereHas('sanPham', function ($sub) use ($kw) {
                          $sub->where('ma_san_pham', 'like', "%{$kw}%")
                              ->orWhere('ten_san_pham', 'like', "%{$kw}%");
                      });
                });
            }

            $perPage = $request->get('per_page', 'all');
            if ($perPage === 'all') {
                $items = $query->get();
            } else {
                $items = $query->paginate((int) $perPage);
            }

            return response()->json([
                'success' => true,
                'data' => TheKhoResource::collection($items)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
