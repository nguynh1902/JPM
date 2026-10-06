<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ChiTietSoDoSeeder extends Seeder
{
    public function run(): void
    {
        // Xoá dữ liệu cũ để tránh trùng lặp
        DB::table('chi_tiet_so_dos')->truncate();

        $now = Carbon::now();
        $data = [];

        // Lấy ID sơ đồ
        $yamahaDiagram1 = DB::table('so_do_phu_tungs')->where('ten_so_do', 'like', '%Bottom Cowling 1%')->value('id');
        $yamahaDiagram2 = DB::table('so_do_phu_tungs')->where('ten_so_do', 'like', '%Bottom Cowling 2%')->value('id');
        $mercuryDiagram = DB::table('so_do_phu_tungs')->where('ten_so_do', 'like', '%Accent Panel%')->value('id');

        // YAMAHA: Map parts (thuong_hieu_id = 1)
        // Chúng ta chia list Yamaha parts làm 2 nửa, nửa map vào Sơ đồ 1, nửa vào sơ đồ 2.
        if ($yamahaDiagram1 || $yamahaDiagram2) {
            $yamahaParts = DB::table('phu_tungs')->where('thuong_hieu_id', 1)->orderBy('id')->get();
            $ref = 1;
            
            foreach ($yamahaParts as $index => $part) {
                // Nửa đầu vào diagram 1, nửa sau vào diagram 2
                $targetDiagram = ($index < count($yamahaParts) / 2) ? $yamahaDiagram1 : $yamahaDiagram2;
                
                if ($targetDiagram) {
                    $data[] = [
                        'so_do_phu_tung_id' => $targetDiagram,
                        'phu_tung_id' => $part->id,
                        'ref_number' => (string) $ref,
                        'qty' => 1,
                        'x' => null, 'y' => null, 'width' => null, 'height' => null,
                        'created_at' => $now, 'updated_at' => $now
                    ];
                    $ref++;
                }
            }
        }

        // MERCURY: Map theo list có sẵn vào Accent Panel
        if ($mercuryDiagram) {
            $mercuryMappings = [
                ['8M0142205', '1'],
                ['8M0142206', '1'],
                ['8M0142208', '1'],
                ['8M0142210', '1'],
                ['8M0142207', '1'],
                ['8M0142209', '1'],
                ['8M0147920', '2'],
                ['8M0147917', '2'],
                ['8M0043703', '3'],
            ];

            foreach ($mercuryMappings as $map) {
                $sku = $map[0];
                $refNumber = $map[1];
                $part = DB::table('phu_tungs')->where('ma_phu_tung', $sku)->first();
                if ($part) {
                    $data[] = [
                        'so_do_phu_tung_id' => $mercuryDiagram,
                        'phu_tung_id' => $part->id,
                        'ref_number' => $refNumber,
                        'qty' => 1,
                        'x' => null, 'y' => null, 'width' => null, 'height' => null,
                        'created_at' => $now, 'updated_at' => $now
                    ];
                }
            }
        }

        if (count($data) > 0) {
            DB::table('chi_tiet_so_dos')->insert($data);
        }
    }
}
