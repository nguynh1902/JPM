<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\CMS\Models\SanPham;
use App\Domain\CMS\Models\DanhMucSanPham;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SanPhamSeeder extends Seeder
{
    public function run(): void
    {
        $imageUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';

        $danhMucMap = DanhMucSanPham::pluck('id', 'ten_danh_muc')->toArray();
        $fallbackId = array_values($danhMucMap)[0] ?? 1;

        $products = [
            // Image 1: Máy dò cá cầm tay
            ['ten' => 'FISHTRAX 1C MÀU DI ĐỘNG', 'ma' => 'FT1PXC', 'thuong_hieu' => 'NORCORSS', 'gia' => 173.83, 'danh_muc' => 'Máy dò cá cầm tay'],
            ['ten' => 'STRIKER VIVID 4CV VỚI ĐẦU DÒ GT20-TM', 'ma' => '010-02550-01', 'thuong_hieu' => 'GARMIN', 'gia' => 199.99, 'danh_muc' => 'Máy dò cá cầm tay'],
            ['ten' => 'FISHTRAX PORTABLE VỚI BIỂU TƯỢNG DSPLY', 'ma' => 'FT1P', 'thuong_hieu' => 'NORCORSS', 'gia' => 83.27, 'danh_muc' => 'Máy dò cá cầm tay'],
            
            // Image 2: Giám sát và an ninh tàu thuyền
            ['ten' => 'TRUNG TÂM ONDECK', 'ma' => '010-02134-00', 'thuong_hieu' => 'GARMIN', 'gia' => 849.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            ['ten' => 'CÔNG TẮC RƠLE KẾT NỐI/GIÁM SÁT TỪ XA ONDECK (12V)', 'ma' => '010-13009-01', 'thuong_hieu' => 'GARMIN', 'gia' => 59.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            ['ten' => 'CẢM BIẾN GIÁM SÁT/BẢO MẬT KẾT NỐI TỪ XA ONDECK', 'ma' => '010-13009-03', 'thuong_hieu' => 'GARMIN', 'gia' => 29.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            ['ten' => 'CẢM BIẾN NGUỒN ĐIỆN BỜ GIÁM SÁT/KẾT NỐI TỪ XA ONDECK', 'ma' => '010-13009-00', 'thuong_hieu' => 'GARMIN', 'gia' => 59.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            ['ten' => 'CÔNG TẮC RƠLE KẾT NỐI/GIÁM SÁT TỪ XA ONDECK (24V)', 'ma' => '010-13009-07', 'thuong_hieu' => 'GARMIN', 'gia' => 69.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            ['ten' => 'CẢM BIẾN NGUỒN ĐIỆN BỜ ONDECK REMOTE', 'ma' => '010-13009-08', 'thuong_hieu' => 'GARMIN', 'gia' => 59.99, 'danh_muc' => 'Giám sát và an ninh tàu thuyền'],
            
            // Image 3: GPS cầm tay
            ['ten' => 'GPS 73 CẦM TAY NỔI', 'ma' => '010-01504-00', 'thuong_hieu' => 'GARMIN', 'gia' => 179.99, 'danh_muc' => 'GPS cầm tay'],

            // Image 4/5: Dầu Nhớt / Bảo trì & Sơn (Maintenance kits)
            ['ten' => 'BỘ DỤNG CỤ BẢO TRÌ DF9.9B/DF15A/DF20A', 'ma' => '17400-89811', 'thuong_hieu' => 'SUZUKI MARINE', 'gia' => 91.02, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => 'BỘ DỤNG CỤ THAY DẦU QUICKSILVER 10W-30', 'ma' => '8M0107511', 'thuong_hieu' => 'MERCURY', 'gia' => 80.99, 'danh_muc' => 'Dầu Nhớt'],
            ['ten' => 'BỘ DỤNG CỤ THAY DẦU 10W-40 CHO TÀU THUYỀN', 'ma' => 'LUB-WTRCG-KT-00', 'thuong_hieu' => 'YAMAHA MARINE', 'gia' => 41.78, 'danh_muc' => 'Dầu Nhớt'],
            ['ten' => 'BỘ DỤNG CỤ BẢO DƯỠNG ĐỘNG CƠ NGOÀI MERCURY 200-400HP', 'ma' => '8M0170715', 'thuong_hieu' => 'MERCURY', 'gia' => 142.99, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => 'BỘ DỤNG CỤ BẢO DƯỠNG ĐỘNG CƠ NGOÀI MERCURY 2.5/3.5HP', 'ma' => '8M0181314', 'thuong_hieu' => 'MERCURY', 'gia' => 69.04, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => 'BỘ DỤNG CỤ BẢO DƯỠNG 300 GIỜ CHO ĐỘNG CƠ BỐN THÌ 25-30HP', 'ma' => '8M0120839', 'thuong_hieu' => 'MERCURY', 'gia' => 277.99, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => 'SUZUKI DF25-60A OIL CHANGE KIT', 'ma' => '18-9227', 'thuong_hieu' => 'SIERRA', 'gia' => 67.87, 'danh_muc' => 'Dầu Nhớt'],
            ['ten' => 'QUICKSILVER 100 HOUR MAINTENANCE KIT FOR YAMAHA VF115', 'ma' => '8M0222223', 'thuong_hieu' => 'MERCURY', 'gia' => 54.79, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => '300 HOUR MAINTENANCE KIT MERCRUISER BRAVO', 'ma' => '8M0215917', 'thuong_hieu' => 'MERCURY', 'gia' => 594.99, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => '100 HOUR MAINTENANCE KIT 9.9-20HP', 'ma' => '8M0151469', 'thuong_hieu' => 'MERCURY', 'gia' => 60.99, 'danh_muc' => 'Bảo trì & Sơn'],
            ['ten' => 'QUICKSILVER 25W-40 V6 OIL CHANGE KIT', 'ma' => '8M0182225', 'thuong_hieu' => 'MERCURY', 'gia' => 80.99, 'danh_muc' => 'Dầu Nhớt'],
        ];

        $data = [];
        foreach ($products as $p) {
            $vndPrice = round($p['gia'] * 25000);
            $data[] = [
                'danh_muc_san_pham_id' => $danhMucMap[$p['danh_muc']] ?? $fallbackId,
                'ma_san_pham' => $p['ma'],
                'ten_san_pham' => $p['ten'],
                'slug' => Str::slug($p['ten'] . '-' . $p['ma']),
                'mo_ta_chi_tiet' => 'Sản phẩm chính hãng từ ' . $p['thuong_hieu'],
                'hinh_anh_chinh' => $imageUrl,
                'gia_ban' => $vndPrice,
                'so_luong_ton' => rand(10, 100),
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        SanPham::query()->truncate();
        SanPham::insert($data);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}



