<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChuyenMucSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $chuyenMucs = [
            ['name' => 'Kiến thức Tàu Thuyền', 'type' => 'NEWS', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bảo Dưỡng Động Cơ', 'type' => 'NEWS', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tin Tức Khuyến Mãi', 'type' => 'NEWS', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Công Nghệ Hàng Hải', 'type' => 'NEWS', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hoạt Động Công Ty', 'type' => 'NEWS', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            
            // Thêm các chuyên mục FAQ
            ['name' => 'Mua hàng & Thanh toán', 'type' => 'FAQ', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vận chuyển & Giao nhận', 'type' => 'FAQ', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chính sách Bảo hành', 'type' => 'FAQ', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Đổi trả & Hoàn tiền', 'type' => 'FAQ', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hỗ trợ Kỹ thuật', 'type' => 'FAQ', 'status' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
        ];

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('chuyen_mucs')->truncate();
        DB::table('chuyen_mucs')->insert($chuyenMucs);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}

