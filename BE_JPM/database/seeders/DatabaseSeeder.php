<?php

namespace Database\Seeders;

use App\Domain\IAM\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            VaiTroSeeder::class,
            NhanVienSeeder::class,
            KhachHangSeeder::class,
            ThuongHieuSeeder::class,
            LoaiDongCoSeeder::class,
            DongCoModelSeeder::class,
            NhomPhuTungSeeder::class,
            PhuTungSeeder::class,
            SoDoPhuTungSeeder::class,
            ChiTietSoDoSeeder::class,
            DanhMucSanPhamSeeder::class,
            SanPhamSeeder::class,
            ChuyenMucSeeder::class,
            TinTucSeeder::class,
            FaqSeeder::class,
            NhaCungCapSeeder::class,
            KhoSeeder::class,
            CauHinhSeeder::class,
            DashboardSeeder::class,
            NotificationSeeder::class,
            SystemLogSeeder::class,
        ]);
    }
}
