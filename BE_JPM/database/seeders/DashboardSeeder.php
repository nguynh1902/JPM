<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\TonKho;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. TẠO CÁC ĐƠN NHẬP HÀNG (DON_NHAP_HANGS / PO) CHO BIỂU ĐỒ CHI PHÍ NHẬP HÀNG
        $pos = [
            [
                'ma_po' => 'PO-260510-01',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(5)->setDay(10)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(5)->setDay(25)->toDateString(),
                'tong_tien' => 12000000,
                'trang_thai' => 2, // Đã nhận hàng
                'created_at' => $now->copy()->subMonths(5)->setDay(10),
                'updated_at' => $now->copy()->subMonths(5)->setDay(10),
            ],
            [
                'ma_po' => 'PO-260615-02',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(4)->setDay(15)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(4)->setDay(30)->toDateString(),
                'tong_tien' => 31000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(4)->setDay(15),
                'updated_at' => $now->copy()->subMonths(4)->setDay(15),
            ],
            [
                'ma_po' => 'PO-260718-03',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(3)->setDay(18)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(3)->setDay(28)->toDateString(),
                'tong_tien' => 42000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(3)->setDay(18),
                'updated_at' => $now->copy()->subMonths(3)->setDay(18),
            ],
            [
                'ma_po' => 'PO-260812-04',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(2)->setDay(12)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(2)->setDay(26)->toDateString(),
                'tong_tien' => 54000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(2)->setDay(12),
                'updated_at' => $now->copy()->subMonths(2)->setDay(12),
            ],
            [
                'ma_po' => 'PO-260905-05',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonth()->setDay(5)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonth()->setDay(20)->toDateString(),
                'tong_tien' => 65000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonth()->setDay(5),
                'updated_at' => $now->copy()->subMonth()->setDay(5),
            ],
            [
                'ma_po' => 'PO-261005-06',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->setDay(5)->toDateString(),
                'ngay_du_kien' => $now->copy()->setDay(22)->toDateString(),
                'tong_tien' => 45000000,
                'trang_thai' => 1, // Đang đặt hàng (PO đang mở)
                'created_at' => $now->copy()->setDay(5),
                'updated_at' => $now->copy()->setDay(5),
            ],
        ];

        foreach ($pos as $po) {
            DonNhapHang::updateOrCreate(['ma_po' => $po['ma_po']], $po);
        }

        // 4. CẬP NHẬT TỒN KHO CÓ NHU CẦU CẦN GOM PO (SO_LUONG_DANG_GIU > 0)
        $sampleInventory = TonKho::take(3)->get();
        if ($sampleInventory->count() >= 3) {
            $sampleInventory[0]->update(['so_luong_dang_giu' => 4]);
            $sampleInventory[1]->update(['so_luong_dang_giu' => 8]);
            $sampleInventory[2]->update(['so_luong_dang_giu' => 3]);
        } elseif (TonKho::count() == 0) {
            // Tạo 3 bản ghi mẫu tồn kho thiếu hàng nếu chưa có
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 1,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 0,
                'so_luong_dang_giu' => 5,
            ]);
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 2,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 1,
                'so_luong_dang_giu' => 6,
            ]);
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 3,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 0,
                'so_luong_dang_giu' => 4,
            ]);
        }

        echo "Seeded Dashboard data successfully!\n";
    }
}
