<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonNhapHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = \Carbon\Carbon::now();

        // Xóa dữ liệu cũ nếu có
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('chi_tiet_don_nhap_hangs')->truncate();
        \Illuminate\Support\Facades\DB::table('don_nhap_hangs')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        for ($i = 1; $i <= 2; $i++) {
            $idPO = \Illuminate\Support\Facades\DB::table('don_nhap_hangs')->insertGetId([
                'ma_po' => 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                'id_nha_cung_cap' => $i,
                'id_kho' => 1,
                'trang_thai' => rand(0, 1),
                'ngay_nhap' => $now->copy()->subDays(rand(1, 5)),
                'ngay_du_kien' => $now->copy()->addDays(rand(3, 10)),
                'tong_tien' => 0,
                'ghi_chu' => $i === 1 ? 'Nhập bổ sung phụ tùng động cơ' : 'Nhập hàng định kỳ',
                'created_at' => $now->copy()->subDays(rand(1, 5)),
                'updated_at' => $now,
            ]);

            $tongTien = 0;
            for ($j = 1; $j <= rand(2, 4); $j++) {
                $donGia = rand(500000, 3000000);
                $soLuong = rand(5, 20);
                $thanhTien = $donGia * $soLuong;
                $tongTien += $thanhTien;

                \Illuminate\Support\Facades\DB::table('chi_tiet_don_nhap_hangs')->insert([
                    'id_don_nhap_hang' => $idPO,
                    'id_san_pham' => rand(1, 10),
                    'loai_san_pham' => 'phu_tung',
                    'so_luong_nhap' => $soLuong,
                    'don_gia_nhap' => $donGia,
                    'thanh_tien' => $thanhTien,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            \Illuminate\Support\Facades\DB::table('don_nhap_hangs')->where('id', $idPO)->update(['tong_tien' => $tongTien]);
        }
    }
}


