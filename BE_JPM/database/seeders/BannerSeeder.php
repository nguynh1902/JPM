<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $imageUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';
        DB::table('banners')->insert([
            [
                'title' => 'Sale mùa hè linh kiện máy thủy',
                'image_url' => $imageUrl,
                'link_url' => 'https://jpm.com/marine-promo',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Động cơ mới ra mắt',
                'image_url' => $imageUrl,
                'link_url' => 'https://jpm.com/new-engine',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Phụ tùng ô tô giảm 20%',
                'image_url' => $imageUrl,
                'link_url' => 'https://jpm.com/auto-promo',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}


