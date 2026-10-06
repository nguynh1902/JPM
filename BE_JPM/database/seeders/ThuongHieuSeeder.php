<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ThuongHieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['id' => 1, 'code' => 'YAM', 'name' => 'YAMAHA', 'img' => 'yamaha_thuonghieu.png'],
            ['id' => 2, 'code' => 'HON', 'name' => 'HONDA', 'img' => 'honda_marine_thuonghieu.png'],
            ['id' => 5, 'code' => 'SUZ', 'name' => 'SUZUKI', 'img' => 'suzuki_marine_thuonghieu.png'],
            ['id' => 7, 'code' => 'MER', 'name' => 'MERCURY', 'img' => 'mecury_thuonghieu.png'],
            ['id' => 8, 'code' => 'JET', 'name' => 'JETKI', 'img' => 'jetki_thuonghieu.png'],
            ['id' => 9, 'code' => 'TOH', 'name' => 'TOHATSU', 'img' => 'tohatsu_thuonghieu.png'],
            ['id' => 10, 'code' => 'SEA', 'name' => 'SEADOO', 'img' => 'seadoo_thuonghieu.png'],
            ['id' => 11, 'code' => 'YAN', 'name' => 'YANMAR', 'img' => 'yanmar_thuonghieu.png'],
        ];

        $data = [];
        $now = Carbon::now();

        foreach ($brands as $brand) {
            $data[] = [
                'id'              => $brand['id'],
                'code'            => $brand['code'],
                'name'            => $brand['name'],
                'logo_path'       => '/client/image/' . $brand['img'],
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('thuong_hieus')->upsert($data, ['id'], ['code', 'name', 'logo_path', 'status', 'updated_at']);
    }
}



