<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SoDoPhuTungSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get dynamic IDs to prevent mismatch after truncation/re-seeding
        $yamahaModelId = DB::table('dong_co_models')->where('ma_model', 'F250TUR')->value('id');
        $mercuryModelId = DB::table('dong_co_models')->where('ma_model', 'MERC-300-V8')->value('id');
        
        $nhomBotC1 = DB::table('nhom_phu_tungs')->where('ma_nhom', 'BOTC01')->value('id');
        $nhomBotC2 = DB::table('nhom_phu_tungs')->where('ma_nhom', 'BOTC02')->value('id');
        $nhomAccent = DB::table('nhom_phu_tungs')->where('ma_nhom', 'ACCENT')->value('id');

        $imageUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';

        $data = [
            [
                'may_thuy_model_id' => $yamahaModelId ?? 2,
                'nhom_phu_tung_id' => $nhomBotC1 ?? 100,
                'ten_so_do' => 'Yamaha F250TUR - Bottom Cowling 1',
                'hinh_anh' => $imageUrl,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'may_thuy_model_id' => $yamahaModelId ?? 2,
                'nhom_phu_tung_id' => $nhomBotC2 ?? 101,
                'ten_so_do' => 'Yamaha F250TUR - Bottom Cowling 2',
                'hinh_anh' => $imageUrl,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'may_thuy_model_id' => $mercuryModelId ?? 4,
                'nhom_phu_tung_id' => $nhomAccent ?? 111,
                'ten_so_do' => 'MERCURY MARINE 300HP V8 - ACCENT PANEL',
                'hinh_anh' => $imageUrl,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        DB::table('so_do_phu_tungs')->insert($data);
    }
}

