<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhuongThucThanhToanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $methods = [
            [
                'id' => 1,
                'ten_phuong_thuc' => 'Chuyển khoản VietQR (Napas247)',
                'mo_ta' => 'Quét mã VietQR chuyển khoản tự động hoặc đặt cọc giữ hàng',
                'phi_xu_ly' => 0,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'ten_phuong_thuc' => 'Thu hộ COD khi nhận hàng',
                'mo_ta' => 'Thanh toán tiền mặt cho nhân viên giao hàng ViettelPost khi nhận kiện hàng',
                'phi_xu_ly' => 0,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'ten_phuong_thuc' => 'Tiền mặt tại kho/quầy',
                'mo_ta' => 'Thanh toán trực tiếp tại văn phòng đại diện hoặc kho phụ tùng JPM',
                'phi_xu_ly' => 0,
                'trang_thai' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($methods as $method) {
            DB::table('phuong_thuc_thanh_toans')->updateOrInsert(['id' => $method['id']], $method);
        }
    }
}
