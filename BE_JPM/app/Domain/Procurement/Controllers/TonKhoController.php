<?php namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller; use App\Domain\Procurement\Models\TonKho; use App\Domain\Core\Resources\TonKhoResource; use Exception; class TonKhoController extends Controller { public function index() { try { return response()->json(["success" => true, "data" => TonKhoResource::collection(TonKho::with(["kho", "sanPham", "phuTung"])->orderBy('id', 'desc')->cursorPaginate(50))]); } catch (Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); } }
            public function store(\Illuminate\Http\Request $request) {
        try {
            // Validate
            if (!$request->id_kho || !$request->id_san_pham || !$request->loai_san_pham) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin bắt buộc'], 400);
            }
            
            // Check if exists
            $exists = TonKho::where('id_kho', $request->id_kho)
                            ->where('id_san_pham', $request->id_san_pham)
                            ->where('loai_san_pham', $request->loai_san_pham)
                            ->first();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này đã có trong kho, vui lòng dùng chức năng Cập nhật'], 400);
            }

            $tonKho = new TonKho();
            $tonKho->id_kho = $request->id_kho;
            $tonKho->id_san_pham = $request->id_san_pham;
            $tonKho->loai_san_pham = $request->loai_san_pham;
            $tonKho->so_luong_ton = $request->so_luong_ton ?? 0;
            $tonKho->so_luong_dang_giu = 0;
            $tonKho->save();
            
            return response()->json(['success' => true, 'message' => 'Thêm tồn kho thành công', 'data' => $tonKho]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(\Illuminate\Http\Request $request, $id) {
        try {
            $tonKho = TonKho::find($id);
            if (!$tonKho) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi tồn kho'], 404);
            }
            
            // Only update what we can safely update
            $tonKho->id_kho = $request->id_kho ?? $tonKho->id_kho;
            $tonKho->so_luong_ton = $request->so_luong_ton ?? $tonKho->so_luong_ton;
            $tonKho->save();
            
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id) {
        try {
            $tonKho = TonKho::find($id);
            if (!$tonKho) {
                return response()->json(["success" => false, "message" => "Không tìm thấy bản ghi tồn kho"], 404);
            }
            $tonKho->delete();
            return response()->json(["success" => true, "message" => "Xóa thành công"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    public function hangCanNhap()
    {
        try {
            $items = TonKho::with(['phuTung', 'sanPham', 'kho'])
                ->where('so_luong_dang_giu', '>', 0)
                ->orderBy('so_luong_dang_giu', 'desc')
                ->get()
                ->map(function ($t) {
                    $ten = $t->loai_san_pham === 'san_pham' && $t->sanPham 
                        ? $t->sanPham->ten_san_pham 
                        : ($t->phuTung ? $t->phuTung->ten_phu_tung : 'Sản phẩm ID ' . $t->id_san_pham);
                    $ma = $t->loai_san_pham === 'san_pham' && $t->sanPham 
                        ? $t->sanPham->ma_san_pham 
                        : ($t->phuTung ? $t->phuTung->ma_phu_tung : 'SKU-' . $t->id_san_pham);
                    $giaNhap = $t->loai_san_pham === 'san_pham' && $t->sanPham 
                        ? (float) ($t->sanPham->gia_nhap ?? ($t->sanPham->gia_ban * 0.7))
                        : ($t->phuTung ? (float) ($t->phuTung->gia_nhap ?? ($t->phuTung->gia_ban * 0.7)) : 0);

                    return [
                        'id_ton_kho' => $t->id,
                        'id_san_pham' => $t->id_san_pham,
                        'loai_san_pham' => $t->loai_san_pham ?? 'phu_tung',
                        'ten_san_pham' => $ten,
                        'ma_san_pham' => $ma,
                        'so_luong_ton' => $t->so_luong_ton,
                        'so_luong_can_nhap' => $t->so_luong_dang_giu,
                        'don_gia_du_kien' => $giaNhap,
                        'kho' => $t->kho ? $t->kho->ten_kho : 'Kho Tổng',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}