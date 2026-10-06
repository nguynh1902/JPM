<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Storefront\Models\VatTuLuuTru;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VatTuLuuTruController extends Controller
{
    /**
     * Lay danh sach vat tu luu tru cua user, kem thong tin phu tung
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $danhSach = VatTuLuuTru::where('user_id', $user->id)
            ->with(['sanPham.danhMuc'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($danhSach);
    }

    /**
     * Them vao danh sach
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'san_pham_id' => 'required|exists:san_phams,id',
            'ghi_chu'     => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Su dung updateOrCreate de tranh duplicate error do unique constraint
            $item = VatTuLuuTru::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'id_san_pham' => $request->san_pham_id,
                ],
                [
                    'ghi_chu' => $request->ghi_chu,
                ]
            );

            // Load relation de front-end co data hien thi luon
            $item->load(['sanPham.danhMuc']);

            return response()->json(['message' => 'Đã lưu vật tư thành công!', 'item' => $item], 201);
        } catch (\Exception $e) {
            Log::error('VatTuLuuTru Store Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lưu vật tư.'], 500);
        }
    }

    /**
     * Xoa khoi danh sach
     */
    public function destroy(Request $request, $san_pham_id)
    {
        $user = $request->user();
        
        try {
            VatTuLuuTru::where('user_id', $user->id)
                ->where('id_san_pham', $san_pham_id)
                ->delete();

            return response()->json(['message' => 'Đã xóa vật tư khỏi danh sách lưu trữ!']);
        } catch (\Exception $e) {
            Log::error('VatTuLuuTru Delete Error: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa.'], 500);
        }
    }
}



