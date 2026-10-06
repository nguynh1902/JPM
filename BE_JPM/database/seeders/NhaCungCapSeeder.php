<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaCungCapSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['ten_nha_cung_cap' => 'Công ty TNHH Hưng Phát', 'ma_nha_cung_cap' => 'NCC001', 'so_dien_thoai' => '0901234567', 'dia_chi' => 'Hà Nội', 'trang_thai' => 1],
            ['ten_nha_cung_cap' => 'Cơ sở Cơ Khí Minh Tuấn', 'ma_nha_cung_cap' => 'NCC002', 'so_dien_thoai' => '0912345678', 'dia_chi' => 'Hải Phòng', 'trang_thai' => 1],
            ['ten_nha_cung_cap' => 'Yamaha Motor Việt Nam', 'ma_nha_cung_cap' => 'NCC003', 'so_dien_thoai' => '0987654321', 'dia_chi' => 'Hồ Chí Minh', 'trang_thai' => 1],
        ];
        
        foreach ($suppliers as $s) {
            DB::table('nha_cung_caps')->updateOrInsert(['ma_nha_cung_cap' => $s['ma_nha_cung_cap']], $s);
        }
    }
}
