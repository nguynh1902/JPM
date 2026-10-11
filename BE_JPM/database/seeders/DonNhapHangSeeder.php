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

        // Xóa dữ liệu cũ nếu có (Không tạo dữ liệu mẫu / seeder cho PO)
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('chi_tiet_don_nhap_hangs')->truncate();
        \Illuminate\Support\Facades\DB::table('don_nhap_hangs')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}


