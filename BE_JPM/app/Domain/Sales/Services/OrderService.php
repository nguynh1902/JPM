<?php

namespace App\Domain\Sales\Services;

use Illuminate\Support\Facades\DB;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\CMS\Models\SanPham;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;

class OrderService
{
    /**
     * Tính tiền từ DB và xử lý tồn kho / chờ gom đơn PO với Pessimistic Locking.
     *
     * @param array $chiTiet Danh sách món hàng
     * @param int $donHangId ID đơn hàng
     * @param bool $isCustomer Đặt từ Client hay Admin
     * @return array
     */
    public function processItemsAndInventory(array $chiTiet, int $donHangId, bool $isCustomer = true)
    {
        $processedItems = [];
        $tongTien = 0;

        foreach ($chiTiet as $item) {
            $idItem = $item['id_san_pham'] ?? $item['id_phu_tung'] ?? null;
            $soLuong = (int) ($item['so_luong'] ?? 1);

            if ($soLuong <= 0) {
                throw new \Exception("Số lượng hàng không hợp lệ: {$soLuong}");
            }

            if (!$idItem) {
                throw new \Exception("ID sản phẩm không được để trống.");
            }

            $loaiSanPham = $item['loai_san_pham'] ?? 'phu_tung';

            // 1. Xác thực thông tin sản phẩm và đơn giá authoritative từ Server
            if ($loaiSanPham === 'san_pham') {
                $product = SanPham::find($idItem);
                if (!$product) {
                    throw new \Exception("Không tìm thấy sản phẩm phổ thông ID: {$idItem}");
                }
                $tenItem = $product->ten_san_pham;
                $maItem = $product->ma_san_pham;
                $donGia = $product->is_gia_lien_he ? 0 : (float) $product->gia_ban;
            } else {
                $product = PhuTung::find($idItem);
                if (!$product) {
                    // Fallback thử tìm trong SanPham phòng trường hợp khách gửi nhầm loai_san_pham
                    $fallback = SanPham::find($idItem);
                    if ($fallback) {
                        $product = $fallback;
                        $loaiSanPham = 'san_pham';
                        $tenItem = $fallback->ten_san_pham;
                        $maItem = $fallback->ma_san_pham;
                        $donGia = $fallback->is_gia_lien_he ? 0 : (float) $fallback->gia_ban;
                    } else {
                        throw new \Exception("Không tìm thấy phụ tùng ID: {$idItem}");
                    }
                } else {
                    $tenItem = $product->ten_phu_tung;
                    $maItem = $product->ma_phu_tung;
                    $donGia = $product->is_gia_lien_he ? 0 : (float) $product->gia_ban;
                }
            }

            $tongTien += $donGia * $soLuong;

            // 2. Xử lý Tồn Kho & Gom Đơn (Order to PO Model)
            $tonKho = TonKho::where('id_san_pham', $idItem)
                            ->lockForUpdate()
                            ->first();

            if ($tonKho && $tonKho->so_luong_ton >= $soLuong) {
                // Hàng có sẵn trong kho -> Trừ tồn kho và ghi nhận Thẻ Kho
                $tonKho->so_luong_ton -= $soLuong;
                $tonKho->save();

                TheKho::create([
                    'id_kho' => $tonKho->id_kho,
                    'id_san_pham' => $idItem,
                    'loai_san_pham' => $loaiSanPham,
                    'loai_giao_dich' => 'XUAT_KHO',
                    'so_luong_thay_doi' => -$soLuong,
                    'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                    'id_tham_chieu' => $donHangId,
                    'ma_tham_chieu' => 'DH' . str_pad($donHangId, 5, '0', STR_PAD_LEFT),
                    'ghi_chu' => 'Xuất bán cho đơn hàng',
                    'created_by' => auth()->id() ?? 1,
                ]);
            } else {
                // Hàng chưa có sẵn trong kho (mô hình Gom đơn PO đi Nhật của JPM)
                // Cập nhật số lượng chờ giao thay vì chặn đứng khách đặt hàng
                if ($tonKho) {
                    $tonKho->so_luong_dang_giu = ($tonKho->so_luong_dang_giu ?? 0) + $soLuong;
                    $tonKho->save();
                } else {
                    TonKho::create([
                        'id_kho' => 1,
                        'id_san_pham' => $idItem,
                        'loai_san_pham' => $loaiSanPham,
                        'so_luong_ton' => 0,
                        'so_luong_dang_giu' => $soLuong,
                    ]);
                }
            }

            $processedItems[] = [
                'id_don_hang'   => $donHangId,
                'id_san_pham'   => $idItem,
                'loai_san_pham' => $loaiSanPham,
                'so_luong'      => $soLuong,
                'don_gia'       => $donGia,
                'thanh_tien'    => $donGia * $soLuong,
                'ten_phu_tung'  => $tenItem,
                'ma_phu_tung'   => $maItem,
            ];
        }

        return [
            'items' => $processedItems,
            'tong_tien' => $tongTien
        ];
    }

    /**
     * Hoàn lại tồn kho nếu hủy đơn hàng (Đảm bảo Idempotency).
     */
    public function cancelOrderInventory($donHang, int $actorId)
    {
        $exists = TheKho::where('id_tham_chieu', $donHang->id)
            ->where('ghi_chu', 'LIKE', '%Hoàn nhập do hủy đơn hàng%')
            ->exists();

        if ($exists) {
            return; // Đã hoàn rồi, không hoàn lần 2 (Idempotency)
        }

        $items = $donHang->chiTiet;
        if (!$items) return;

        foreach ($items as $item) {
            $idItem = $item->id_san_pham;
            $soLuong = $item->so_luong;

            if (!$idItem) continue;

            $tonKho = TonKho::where('id_san_pham', $idItem)
                            ->lockForUpdate()
                            ->first();

            // Kiểm tra xem đơn này lúc tạo có bị trừ tồn kho thực tế hay chỉ chờ giao
            $wasDeducted = TheKho::where('id_tham_chieu', $donHang->id)
                ->where('id_san_pham', $idItem)
                ->where('so_luong_thay_doi', '<', 0)
                ->exists();

            if ($wasDeducted && $tonKho) {
                $tonKho->so_luong_ton += $soLuong;
                $tonKho->save();

                TheKho::create([
                    'id_kho' => $tonKho->id_kho,
                    'id_san_pham' => $idItem,
                    'loai_san_pham' => $item->loai_san_pham ?? 'phu_tung',
                    'loai_giao_dich' => 'NHAP_KHO',
                    'so_luong_thay_doi' => $soLuong,
                    'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                    'id_tham_chieu' => $donHang->id,
                    'ma_tham_chieu' => 'DH' . str_pad($donHang->id, 5, '0', STR_PAD_LEFT),
                    'ghi_chu' => 'Hoàn nhập do hủy đơn hàng',
                    'created_by' => $actorId,
                ]);
            } elseif ($tonKho && ($tonKho->so_luong_dang_giu ?? 0) >= $soLuong) {
                $tonKho->so_luong_dang_giu -= $soLuong;
                $tonKho->save();
            }
        }
    }
}
