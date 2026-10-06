<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ChiTietSoDoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $details = [
            // so_do_id, phu_tung_id, ref, qty, x, y, w, h
            [1, 1, '1', 1, 10, 20, 15, 20],
            [1, 2, '2', 1, 30, 20, 15, 20],
            [1, 3, '3', 1, 50, 10, 5, 5],
            [1, 4, '4', 1, 50, 15, 5, 5],
            [1, 5, '5', 1, 50, 20, 5, 5],
            [1, 6, '6', 1, 50, 25, 5, 5],
            [1, 7, '7', 1, 50, 30, 8, 8],
            [1, 8, '8', 2, 20, 45, 5, 5],
            [1, 9, '9', 4, 30, 45, 5, 5],
        ];

        $data = [];
        foreach ($details as $d) {
            $data[] = [
                'so_do_phu_tung_id' => $d[0],
                'phu_tung_id' => $d[1],
                'ref_number' => $d[2],
                'qty' => $d[3],
                'x' => $d[4],
                'y' => $d[5],
                'width' => $d[6],
                'height' => $d[7],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('chi_tiet_so_dos')->insert($data);
    }
}