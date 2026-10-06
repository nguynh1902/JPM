<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\Core\Requests\StoreDonNhapHangRequest;
use App\Domain\Core\Resources\DonNhapHangResource;
use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\ChiTietDonNhapHang;
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
            $donNhapHang = DonNhapHang::with(['nhaCungCap', 'kho', 'chiTiet.sanPham'])->findOrFail($id);
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
            $donNhapHang = DonNhapHang::findOrFail($id);
            $donNhapHang->update($request->only(['id_nha_cung_cap', 'id_kho', 'trang_thai', 'ngay_du_kien', 'ghi_chu']));
            
            // Complex update logic for chi_tiets would go here if needed
            
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
}



