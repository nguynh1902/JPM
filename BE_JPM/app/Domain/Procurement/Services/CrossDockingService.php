<?php

namespace App\Domain\Procurement\Services;

use App\Domain\PIM\Models\PhuTung;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\CrossDocking;
use Illuminate\Support\Facades\DB;
use Exception;

class CrossDockingService
{
    /**
     * Xử lý quét mã vạch sản phẩm tại trạm Cross-Docking
     * 
     * @param string $barcode Mã vạch / Mã phụ tùng
     * @param int $idKho Kho thực hiện cross-docking
     * @param int $userId Người quét
     * @return array Kết quả xử lý
     */
    public function processScan(string $barcode, int $idKho = 1, int $userId = 1): array
    {
        DB::beginTransaction();
        try {
            // 1. Tìm sản phẩm qua mã vạch (Giả sử là mã phụ tùng)
            $phuTung = PhuTung::where('ma_phu_tung', $barcode)->first();
            
            if (!$phuTung) {
                throw new Exception("Mã vạch {$barcode} không tồn tại trong hệ thống.");
            }
            
            // 2. Thuật toán FIFO: Tìm đơn hàng (DonHang) cũ nhất đang chờ phụ tùng này
            // Ở phiên bản đơn giản, chúng ta tìm don_hang có trạng thái = "Đang xử lý" (VD: 1)
            $oldestOrder = DonHang::where('trang_thai', 1)
                ->whereHas('chiTiet', function($query) use ($phuTung) {
                    $query->where('id_san_pham', $phuTung->id)
                          ->where('loai_san_pham', 'phu_tung');
                })
                ->orderBy('created_at', 'asc')
                ->first();
                
            $allocatedOrderId = null;
            $maDonHang = "NHẬP KHO CHỜ XỬ LÝ";
            $message = "Đã nhập kho sản phẩm {$phuTung->ten_phu_tung}.";
            
            if ($oldestOrder) {
                $allocatedOrderId = $oldestOrder->id;
                $maDonHang = $oldestOrder->ma_don_hang ?? "DH-{$oldestOrder->id}";
                $message = "Đã phân bổ {$phuTung->ten_phu_tung} cho đơn hàng {$maDonHang}.";
                
                // Cập nhật trạng thái chi tiết đơn hàng (nếu có cột) - Tạm bỏ qua vì db chưa có
            }

            // 3. Ghi nhận giao dịch vào Thẻ Kho (Nhập Cross-Docking)
            TheKho::create([
                'id_kho' => $idKho,
                'id_san_pham' => $phuTung->id,
                'loai_san_pham' => 'phu_tung',
                'loai_giao_dich' => 'NHAP_CROSS_DOCKING',
                'so_luong_thay_doi' => 1,
                'so_luong_ton_cuoi' => 0, // Cần tính toán tồn kho thực tế nếu có
                'id_tham_chieu' => $allocatedOrderId,
                'ma_tham_chieu' => $maDonHang,
                'ghi_chu' => 'Quét mã Cross-Docking',
                'created_by' => $userId
            ]);
            
            // Nếu có đơn hàng nhận, xuất luôn
            if ($allocatedOrderId) {
                TheKho::create([
                    'id_kho' => $idKho,
                    'id_san_pham' => $phuTung->id,
                    'loai_san_pham' => 'phu_tung',
                    'loai_giao_dich' => 'XUAT_CROSS_DOCKING',
                    'so_luong_thay_doi' => -1,
                    'so_luong_ton_cuoi' => 0,
                    'id_tham_chieu' => $allocatedOrderId,
                    'ma_tham_chieu' => $maDonHang,
                    'ghi_chu' => 'Xuất ngay cho đơn hàng',
                    'created_by' => $userId
                ]);
            }
            
            // 4. Cập nhật trạng thái lô hàng Cross-Docking thực tế
            $cd = CrossDocking::where('trang_thai', 'WAITING')
                ->where(function($q) use ($barcode) {
                    $q->where('ma_van_don', $barcode)->orWhere('ten_san_pham', 'LIKE', "%{$barcode}%");
                })->first();
            
            if ($cd) {
                $cd->update(['trang_thai' => 'SCANNED']);
            } else {
                CrossDocking::create([
                    'ma_van_don' => 'BARCODE-'.$barcode,
                    'ten_san_pham' => $phuTung->ten_phu_tung,
                    'so_luong' => 1,
                    'khach_hang' => $maDonHang,
                    'trang_thai' => 'SCANNED'
                ]);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'phu_tung' => $phuTung->ten_phu_tung,
                    'don_hang' => $maDonHang,
                    'status' => 'SCANNED'
                ]
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

