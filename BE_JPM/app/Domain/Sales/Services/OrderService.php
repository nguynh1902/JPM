<?php

namespace App\Domain\Sales\Services;

use Illuminate\Support\Facades\DB;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;

class OrderService
{
    /**
     * Tnh ti?n t? DB v tr? t?n kho km lockForUpdate.
     */
    public function processItemsAndInventory(array $chiTiet, int $donHangId, bool $isCustomer = true)
    {
        $processedItems = [];
        $tongTien = 0;

        foreach ($chiTiet as $item) {
            $idPhuTung = $item['id_phu_tung'] ?? $item['id_san_pham'] ?? null;
            $soLuong = (int) ($item['so_luong'] ?? 1);

            if ($soLuong <= 0) {
                throw new \Exception("S? lu?ng hng khng h?p l?: {$soLuong}");
            }

            if (!$idPhuTung) {
                throw new \Exception("ID s?n ph?m khng du?c d? tr?ng.");
            }

            $phuTung = PhuTung::find($idPhuTung);
            if (!$phuTung) {
                throw new \Exception("Khng tm th?y s?n ph?m ID: {$idPhuTung}");
            }

            // Backend xc d?nh gi, khng tin client
            $donGia = (float) $phuTung->gia_ban;
            $tongTien += $donGia * $soLuong;

            // X? ly T?n Kho v?i Pessimistic Locking
            $tonKho = TonKho::where('id_san_pham', $idPhuTung)
                            // N?u c field loai_san_pham th c?n xc d?nh, m?c d?nh l phu_tung
                            // ->where('loai_san_pham', 'phu_tung')
                            ->lockForUpdate()
                            ->first();

            if (!$tonKho || $tonKho->so_luong_ton < $soLuong) {
                throw new \Exception("S?n ph?m '{$phuTung->ten_phu_tung}' khng d? s? lu?ng t?n kho (T?n: " . ($tonKho ? $tonKho->so_luong_ton : 0) . ")!");
            }

            $tonKho->so_luong_ton -= $soLuong;
            $tonKho->save();

            // Ghi Th? Kho
            TheKho::create([
                'id_kho' => $tonKho->id_kho,
                'id_san_pham' => $idPhuTung,
                'loai_giao_dich' => 'XUAT_BAN',
                'so_luong_thay_doi' => -$soLuong,
                'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                'id_tham_chieu' => $donHangId,
                'ma_tham_chieu' => 'DH' . str_pad($donHangId, 5, '0', STR_PAD_LEFT),
                'created_by' => auth()->id() ?? 1,
            ]);

            $processedItems[] = [
                'id_don_hang' => $donHangId,
                'id_san_pham' => $idPhuTung,
                'so_luong'    => $soLuong,
                'don_gia'     => $donGia,
                'thanh_tien'  => $donGia * $soLuong,
                'ten_phu_tung'=> $phuTung->ten_phu_tung,
                'ma_phu_tung' => $phuTung->ma_phu_tung,
            ];
        }

        return [
            'items' => $processedItems,
            'tong_tien' => $tongTien
        ];
    }

    /**
     * Hon l?i t?n kho n?u hu? don hng
     */
    public function cancelOrderInventory($donHang, int $actorId)
    {
        // Trnh hon kho nhi?u l?n
        // Check n?u d t?n t?i th? kho HOAN_NHAP cho d?n hng ny
        $exists = TheKho::where('id_tham_chieu', $donHang->id)
            ->where('loai_giao_dich', 'HOAN_NHAP')
            ->exists();

        if ($exists) {
            return; // D hon r?i, khng lm n?a (Idempotency)
        }

        $items = $donHang->chiTiet;
        foreach ($items as $item) {
            $idPhuTung = $item->id_san_pham;
            $soLuong = $item->so_luong;

            if (!$idPhuTung) continue;

            $tonKho = TonKho::where('id_san_pham', $idPhuTung)
                            ->lockForUpdate()
                            ->first();

            if ($tonKho) {
                $tonKho->so_luong_ton += $soLuong;
                $tonKho->save();

                TheKho::create([
                    'id_kho' => $tonKho->id_kho,
                    'id_san_pham' => $idPhuTung,
                    'loai_giao_dich' => 'HOAN_NHAP',
                    'so_luong_thay_doi' => $soLuong,
                    'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                    'id_tham_chieu' => $donHang->id,
                    'ma_tham_chieu' => 'DH' . str_pad($donHang->id, 5, '0', STR_PAD_LEFT),
                    'created_by' => $actorId,
                ]);
            }
        }
    }
}
