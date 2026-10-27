<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonViVanChuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $units = [
            [
                'id' => 1,
                'ten_don_vi' => 'ViettelPost',
                'ma_don_vi' => 'VTP',
                'api_url' => 'https://partner.viettelpost.vn/v2',
                'api_key' => 'VTP_TEST_KEY_JPM',
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'ten_don_vi' => 'Chành Xe Liên Tỉnh & Bến Tàu Thủy',
                'ma_don_vi' => 'CHANH_XE',
                'api_url' => null,
                'api_key' => null,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'ten_don_vi' => 'Giao Hàng Hỏa Tốc Bến Cano',
                'ma_don_vi' => 'HOA_TOC',
                'api_url' => null,
                'api_key' => null,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($units as $unit) {
            DB::table('don_vi_van_chuyens')->updateOrInsert(['id' => $unit['id']], $unit);
        }
    }
}
