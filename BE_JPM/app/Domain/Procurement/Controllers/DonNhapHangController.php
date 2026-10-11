<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\Core\Requests\StoreDonNhapHangRequest;
use App\Domain\Core\Resources\DonNhapHangResource;
use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\ChiTietDonNhapHang;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonNhapHangController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = DonNhapHang::with(['nhaCungCap', 'kho'])->orderBy('id', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => DonNhapHangResource::collection($data)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreDonNhapHangRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            // Generate ma_po
            $validated['ma_po'] = 'PO' . strtoupper(substr(uniqid(), -6));
            $validated['trang_thai'] = 0; // 0 = Nháp
            
            $chiTiets = $validated['chi_tiets'] ?? [];
            unset($validated['chi_tiets']);
            
            $donNhapHang = DonNhapHang::create($validated);
            
            $tongTien = 0;
            if (!empty($chiTiets)) {
                foreach ($chiTiets as $ct) {
                    $thanhTien = $ct['so_luong'] * $ct['don_gia'];
                    $tongTien += $thanhTien;
                    
                    ChiTietDonNhapHang::create([
                        'id_don_nhap_hang' => $donNhapHang->id,
                        'id_san_pham' => $ct['id_phu_tung'],
                        'loai_san_pham' => 'phu_tung',
                        'so_luong_nhap' => $ct['so_luong'],
                        'don_gia_nhap' => $ct['don_gia'],
                        'thanh_tien' => $thanhTien,
                    ]);
                }
            }
            
            $donNhapHang->tong_tien = $tongTien;
            $donNhapHang->save();
            
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => new DonNhapHangResource($donNhapHang)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $donNhapHang = DonNhapHang::with(['nhaCungCap', 'kho', 'chiTiet.sanPham', 'chiTiet.phuTung'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'po' => new DonNhapHangResource($donNhapHang),
                    'chi_tiets' => $donNhapHang->chiTiet
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $donNhapHang = DonNhapHang::with('chiTiet')->findOrFail($id);
            $oldStatus = (int) $donNhapHang->trang_thai;
            $newStatus = $request->has('trang_thai') ? (int) $request->trang_thai : $oldStatus;
            
            $donNhapHang->update($request->only(['id_nha_cung_cap', 'id_kho', 'trang_thai', 'ngay_du_kien', 'ghi_chu']));
            
            // Xử lý tự động Nhập Kho khi PO hoàn thành (trang_thai == 2)
            if ($oldStatus !== 2 && $newStatus === 2) {
                $idKho = $donNhapHang->id_kho ?? 1;
                foreach ($donNhapHang->chiTiet as $ct) {
                    $idSanPham = $ct->id_san_pham;
                    $loaiSanPham = $ct->loai_san_pham ?? 'phu_tung';
                    $soLuongNhap = (int) $ct->so_luong_nhap;

                    if ($soLuongNhap <= 0 || !$idSanPham) continue;

                    $tonKho = TonKho::where('id_san_pham', $idSanPham)
                        ->where('id_kho', $idKho)
                        ->lockForUpdate()
                        ->first();

                    if (!$tonKho) {
                        $tonKho = TonKho::create([
                            'id_kho' => $idKho,
                            'id_san_pham' => $idSanPham,
                            'loai_san_pham' => $loaiSanPham,
                            'so_luong_ton' => 0,
                            'so_luong_dang_giu' => 0,
                        ]);
                    }

                    $tonKho->so_luong_ton += $soLuongNhap;

                    // Giảm số lượng đang giữ / chờ giao nếu có
                    if (($tonKho->so_luong_dang_giu ?? 0) > 0) {
                        $giaiToa = min($tonKho->so_luong_dang_giu, $soLuongNhap);
                        $tonKho->so_luong_dang_giu -= $giaiToa;
                    }

                    $tonKho->save();

                    // Ghi Thẻ Kho
                    TheKho::create([
                        'id_kho' => $idKho,
                        'id_san_pham' => $idSanPham,
                        'loai_san_pham' => $loaiSanPham,
                        'loai_giao_dich' => 'NHAP_KHO',
                        'so_luong_thay_doi' => $soLuongNhap,
                        'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                        'id_tham_chieu' => $donNhapHang->id,
                        'ma_tham_chieu' => $donNhapHang->ma_po,
                        'ghi_chu' => 'Nhập kho tự động từ PO ' . $donNhapHang->ma_po,
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => new DonNhapHangResource($donNhapHang)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            ChiTietDonNhapHang::where('id_don_nhap_hang', $id)->delete();
            DonNhapHang::destroy($id);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa PO thành công'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function gomPO(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_san_pham' => 'required|integer',
            'items.*.so_luong' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $maPO = 'PO' . strtoupper(substr(uniqid(), -6));
            $donNhapHang = DonNhapHang::create([
                'ma_po' => $maPO,
                'trang_thai' => 0, // Nháp
                'id_nha_cung_cap' => $request->id_nha_cung_cap ?? 1,
                'id_kho' => $request->id_kho ?? 1,
                'ngay_du_kien' => $request->ngay_du_kien ?? now()->addDays(14)->toDateString(),
                'tong_tien' => 0,
                'ghi_chu' => $request->ghi_chu ?? ('Gom đơn tổng hợp đặt hàng (' . count($request->items) . ' mặt hàng)'),
            ]);

            $tongTien = 0;
            foreach ($request->items as $item) {
                $donGia = (float) ($item['don_gia_nhap'] ?? 0);
                $soLuong = (int) $item['so_luong'];
                $thanhTien = $soLuong * $donGia;
                $tongTien += $thanhTien;

                ChiTietDonNhapHang::create([
                    'id_don_nhap_hang' => $donNhapHang->id,
                    'id_san_pham' => $item['id_san_pham'],
                    'loai_san_pham' => $item['loai_san_pham'] ?? 'phu_tung',
                    'so_luong_nhap' => $soLuong,
                    'don_gia_nhap' => $donGia,
                    'thanh_tien' => $thanhTien,
                ]);
            }

            $donNhapHang->tong_tien = $tongTien;
            $donNhapHang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã tạo thành công PO gom lô {$maPO} với " . count($request->items) . " mặt hàng!",
                'data' => new DonNhapHangResource($donNhapHang)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}



