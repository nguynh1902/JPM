<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LoaiDongCoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['ma_loai_dong_co' => 'LDC01', 'ten_loai_dong_co' => 'Outboard', 'trang_thai' => 1],
        ];

        $now = Carbon::now();
        foreach ($data as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }

        DB::table('loai_dong_cos')->insert($data);
    }
}
