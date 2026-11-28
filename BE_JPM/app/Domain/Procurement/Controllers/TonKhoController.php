<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\CMS\Models\SanPham;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\Procurement\Models\Kho;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Core\Resources\TonKhoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TonKhoController extends Controller
{
    public function show($id)
    {
        try {
            $item = TonKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ])->findOrFail($id);
            return response()->json(['success' => true, 'data' => new TonKhoResource($item)]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tồn kho.'], 404);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = TonKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ])->orderBy('id', 'desc');

            if ($request->filled('warehouse_id')) {
                $query->where('id_kho', (int) $request->warehouse_id);
            }

            if ($request->filled('loai_san_pham')) {
                $query->where('loai_san_pham', $request->loai_san_pham);
            }

            if ($request->boolean('has_pending')) {
                $query->where('so_luong_dang_giu', '>', 0);
            }

            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function ($q) use ($kw) {
                    $q->whereHas('phuTung', function ($sub) use ($kw) {
                        $sub->withTrashed()
                            ->where('ma_phu_tung', 'like', "%{$kw}%")
                            ->orWhere('ten_phu_tung', 'like', "%{$kw}%");
                    })->orWhereHas('sanPham', function ($sub) use ($kw) {
                        $sub->where('ma_san_pham', 'like', "%{$kw}%")
                            ->orWhere('ten_san_pham', 'like', "%{$kw}%");
                    });
                });
            }

            $items = $request->filled('cursor') ? $query->cursorPaginate(50) : $query->get();
            return response()->json([
                'success' => true,
                'data' => TonKhoResource::collection($items)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->id_kho || !$request->id_san_pham || !$request->loai_san_pham) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin bắt buộc'], 400);
            }

            $loaiSanPham = (string) $request->loai_san_pham;
            if (!in_array($loaiSanPham, ['phu_tung', 'san_pham'], true)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Loại sản phẩm không hợp lệ'], 422);
            }

            $kho = Kho::find((int) $request->id_kho);
            if (!$kho) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Kho chứa không tồn tại hoặc đã bị xóa'], 422);
            }
            if ((int) $kho->trang_thai !== 1) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Kho này đang tạm khóa, không thể thêm tồn kho mới'], 422);
            }

            $productExists = $loaiSanPham === 'san_pham'
                ? SanPham::where('id', (int) $request->id_san_pham)->exists()
                : PhuTung::where('id', (int) $request->id_san_pham)->exists();

            if (!$productExists) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Sản phẩm/Phụ tùng không tồn tại'], 422);
            }

            $exists = TonKho::where('id_kho', (int) $request->id_kho)
                ->where('id_san_pham', (int) $request->id_san_pham)
                ->where('loai_san_pham', $loaiSanPham)
                ->first();
            if ($exists) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Sản phẩm này đã có trong kho, vui lòng dùng chức năng Cập nhật'], 400);
            }

            $soLuongTon = (int) ($request->so_luong_ton ?? 0);
            if ($soLuongTon < 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Số lượng tồn kho không được âm'], 422);
            }

            $tonKho = new TonKho();
            $tonKho->id_kho = (int) $request->id_kho;
            $tonKho->id_san_pham = (int) $request->id_san_pham;
            $tonKho->loai_san_pham = $loaiSanPham;
            $tonKho->so_luong_ton = $soLuongTon;
            $tonKho->so_luong_dang_giu = 0;
            $tonKho->save();

            if ($soLuongTon > 0) {
                $ghiChu = trim((string) $request->input('ghi_chu', ''));
                TheKho::create([
                    'id_kho' => $tonKho->id_kho,
                    'id_san_pham' => $tonKho->id_san_pham,
                    'loai_san_pham' => $tonKho->loai_san_pham,
                    'loai_giao_dich' => 'NHAP_KHO',
                    'so_luong_thay_doi' => $soLuongTon,
                    'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                    'id_tham_chieu' => $tonKho->id,
                    'ma_tham_chieu' => 'TK-INIT-' . $tonKho->id,
                    'ghi_chu' => $ghiChu !== '' ? $ghiChu : 'Khởi tạo tồn kho ban đầu',
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            DB::commit();
            $tonKho->load([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ]);
            return response()->json(['success' => true, 'message' => 'Thêm tồn kho thành công', 'data' => new TonKhoResource($tonKho)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $tonKho = TonKho::lockForUpdate()->find($id);
            if (!$tonKho) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi tồn kho'], 404);
            }

            if ($request->filled('id_kho') && (int) $request->id_kho !== (int) $tonKho->id_kho) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đổi kho chứa trực tiếp trên dòng tồn kho. Vui lòng tạo dòng tồn mới tại kho đích.'
                ], 422);
            }

            $oldQty = (int) $tonKho->so_luong_ton;
            $newQty = $request->has('so_luong_ton') ? (int) $request->so_luong_ton : $oldQty;
            if ($newQty < 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Số lượng tồn kho không được âm'], 422);
            }
            $diff = $newQty - $oldQty;

            $tonKho->so_luong_ton = $newQty;
            $tonKho->save();

            if ($diff !== 0) {
                $ghiChu = trim((string) $request->input('ghi_chu', ''));
                TheKho::create([
                    'id_kho' => $tonKho->id_kho,
                    'id_san_pham' => $tonKho->id_san_pham,
                    'loai_san_pham' => $tonKho->loai_san_pham ?? 'phu_tung',
                    'loai_giao_dich' => 'DIEU_CHINH',
                    'so_luong_thay_doi' => $diff,
                    'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                    'id_tham_chieu' => $tonKho->id,
                    'ma_tham_chieu' => 'DC-' . date('ymd') . '-' . $tonKho->id,
                    'ghi_chu' => $ghiChu !== '' ? $ghiChu : ($diff > 0 ? 'Điều chỉnh tăng tồn kho trực tiếp từ Admin' : 'Điều chỉnh giảm tồn kho trực tiếp từ Admin'),
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            DB::commit();
            $tonKho->load([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ]);
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => new TonKhoResource($tonKho)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tonKho = TonKho::find($id);
            if (!$tonKho) {
                return response()->json(["success" => false, "message" => "Không tìm thấy bản ghi tồn kho"], 404);
            }
            if ((int)$tonKho->so_luong_ton > 0 || (int)$tonKho->so_luong_dang_giu > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Không thể xóa dòng tồn kho khi số lượng tồn hoặc đang giữ chỗ lớn hơn 0."
                ], 400);
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
