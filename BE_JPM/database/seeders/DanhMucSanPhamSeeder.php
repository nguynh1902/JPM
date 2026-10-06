<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\CMS\Models\DanhMucSanPham;
use Illuminate\Support\Str;

class DanhMucSanPhamSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Dầu Nhớt',
            'Áo Phao',
            'Nước Mát',
            'Phụ Kiện',
            'Neo đậu và cập bến',
            'Vỏ bọc thuyền và động cơ',
            'Phụ kiện thuyền',
            'Phụ tùng động cơ thuyền',
            'Chỗ ngồi trên thuyền',
            'Đinh thuyền',
            'Điện',
            'Điện tử & Điều hướng',
            'Lắp đặt động cơ',
            'Câu cá',
            'Bảo trì & Sơn',
            'Phần cứng hàng hải',
            'Động cơ gắn ngoài cần bán',
            'Đồ dùng ngoài trời',
            'Hệ thống đường ống nước và thông gió',
            'Cánh quạt',
            'Phụ tùng xe RV',
            'An toàn & Sinh tồn',
            'Chèo thuyền',
            'Kéo rơ moóc',
            'Động cơ kéo',
            'Thể thao dưới nước',
            'Giám sát và an ninh tàu thuyền',
            'Máy ảnh & Phụ kiện',
            'Máy vẽ biểu đồ và máy dò cá',
            'Phụ kiện GPS & Radar',
            'Máy dò cá cầm tay',
            'GPS cầm tay',
            'Âm thanh hàng hải',
            'Hệ thống lái tự động hàng hải',
            'Thông tin liên lạc hàng hải',
            'Phụ kiện điện thoại và máy tính bảng',
            'Radar',
            'Sonar',
        ];

        $data = [];
        foreach ($categories as $index => $name) {
            $id = $index + 1;
            $maDanhMuc = 'DM' . str_pad($id, 2, '0', STR_PAD_LEFT);
            $loaiUngDung = ($name === 'Nước Mát' || $name === 'Phụ tùng xe RV') ? 'AUTO' : 'MARINE';
            
            $data[] = [
                'id' => $id,
                'ma_danh_muc' => $maDanhMuc,
                'ten_danh_muc' => $name,
                'slug' => Str::slug($name),
                'trang_thai' => 1
            ];
        }

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DanhMucSanPham::query()->truncate(); 
        DanhMucSanPham::insert($data);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}



