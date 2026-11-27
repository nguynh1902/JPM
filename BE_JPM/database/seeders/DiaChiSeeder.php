<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiaChiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $addresses = [
            [
                'id' => 1,
                'id_tai_khoan' => 3, // Nguyễn Hải Dương
                'ten_nguoi_nhan' => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'so_dien_thoai' => '0988123456',
                'dia_chi_cu_the' => 'Bến Tàu Du Lịch Rạch Giá, Đường 3/2, TP. Rạch Giá, Tỉnh Kiên Giang',
                'la_mac_dinh' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'id_tai_khoan' => 4, // Trần Quang Minh
                'ten_nguoi_nhan' => 'Trần Quang Minh (Xưởng Cano)',
                'so_dien_thoai' => '0905987654',
                'dia_chi_cu_the' => 'Xưởng Sửa Chữa Tàu Thủy Marina, 128 Nguyễn Hữu Cảnh, P.22, Q. Bình Thạnh, TP.HCM',
                'la_mac_dinh' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'id_tai_khoan' => 3,
                'ten_nguoi_nhan' => 'Kho Phụ Tùng Trạm Cửa Đại - Hội An',
                'so_dien_thoai' => '0988123456',
                'dia_chi_cu_the' => 'Âu Thuyền Thọ Quang, Đường Yết Kiêu, Q. Sơn Trà, TP. Đà Nẵng',
                'la_mac_dinh' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($addresses as $addr) {
            DB::table('dia_chis')->updateOrInsert(['id' => $addr['id']], $addr);
        }
    }
}
