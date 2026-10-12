<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\TonKho;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. TẠO CÁC ĐƠN HÀNG (DON_HANGS) QUA CÁC THÁNG ĐẾN HÔM NAY
        $orders = [
            // Tháng 5 (5 tháng trước)
            [
                'ma_don_hang' => 'DH-260515-A101',
                'khach_hang_ten' => 'Garage Tàu Cửu Long',
                'khach_hang_sdt' => '0939112233',
                'loai_don' => 'b2b',
                'tong_tien' => 18500000,
                'trang_thai' => 3, // Hoàn thành
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(5)->setDay(15)->setHour(10),
                'updated_at' => $now->copy()->subMonths(5)->setDay(15)->setHour(10),
            ],
            [
                'ma_don_hang' => 'DH-260522-A102',
                'khach_hang_ten' => 'Nguyễn Văn Tuấn',
                'khach_hang_sdt' => '0912345678',
                'loai_don' => 'b2c',
                'tong_tien' => 6200000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(5)->setDay(22)->setHour(14),
                'updated_at' => $now->copy()->subMonths(5)->setDay(22)->setHour(14),
            ],

            // Tháng 6 (4 tháng trước)
            [
                'ma_don_hang' => 'DH-260608-B201',
                'khach_hang_ten' => 'Đại Lý Thủy Sản Miền Tây',
                'khach_hang_sdt' => '0988123456',
                'loai_don' => 'b2b',
                'tong_tien' => 45000000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(4)->setDay(8)->setHour(9),
                'updated_at' => $now->copy()->subMonths(4)->setDay(8)->setHour(9),
            ],
            [
                'ma_don_hang' => 'DH-260619-B202',
                'khach_hang_ten' => 'Trần Quang Minh',
                'khach_hang_sdt' => '0905987654',
                'loai_don' => 'b2c',
                'tong_tien' => 8400000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(4)->setDay(19)->setHour(11),
                'updated_at' => $now->copy()->subMonths(4)->setDay(19)->setHour(11),
            ],

            // Tháng 7 (3 tháng trước)
            [
                'ma_don_hang' => 'DH-260710-C301',
                'khach_hang_ten' => 'Xưởng Máy Thủy Hải Phòng',
                'khach_hang_sdt' => '0918776655',
                'loai_don' => 'b2b',
                'tong_tien' => 58000000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(3)->setDay(10)->setHour(13),
                'updated_at' => $now->copy()->subMonths(3)->setDay(10)->setHour(13),
            ],
            [
                'ma_don_hang' => 'DH-260725-C302',
                'khach_hang_ten' => 'Lê Hoàng Nam',
                'khach_hang_sdt' => '0977443322',
                'loai_don' => 'b2c',
                'tong_tien' => 12500000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(3)->setDay(25)->setHour(15),
                'updated_at' => $now->copy()->subMonths(3)->setDay(25)->setHour(15),
            ],

            // Tháng 8 (2 tháng trước)
            [
                'ma_don_hang' => 'DH-260805-D401',
                'khach_hang_ten' => 'Công Ty Hàng Hải Biển Đông',
                'khach_hang_sdt' => '0909554433',
                'loai_don' => 'b2b',
                'tong_tien' => 76000000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(2)->setDay(5)->setHour(8),
                'updated_at' => $now->copy()->subMonths(2)->setDay(5)->setHour(8),
            ],
            [
                'ma_don_hang' => 'DH-260820-D402',
                'khach_hang_ten' => 'Phạm Văn Hậu',
                'khach_hang_sdt' => '0933221100',
                'loai_don' => 'b2c',
                'tong_tien' => 9800000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonths(2)->setDay(20)->setHour(16),
                'updated_at' => $now->copy()->subMonths(2)->setDay(20)->setHour(16),
            ],

            // Tháng 9 (1 tháng trước)
            [
                'ma_don_hang' => 'DH-260904-E501',
                'khach_hang_ten' => 'Đại Lý Thủy Sản Miền Tây',
                'khach_hang_sdt' => '0988123456',
                'loai_don' => 'b2b',
                'tong_tien' => 92000000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonth()->setDay(4)->setHour(10),
                'updated_at' => $now->copy()->subMonth()->setDay(4)->setHour(10),
            ],
            [
                'ma_don_hang' => 'DH-260918-E502',
                'khach_hang_ten' => 'Võ Minh Trí',
                'khach_hang_sdt' => '0966889900',
                'loai_don' => 'b2c',
                'tong_tien' => 14200000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMonth()->setDay(18)->setHour(14),
                'updated_at' => $now->copy()->subMonth()->setDay(18)->setHour(14),
            ],

            // Tháng 10 (Hiện tại) - Các đơn đang xử lý & hoàn thành gần đây
            [
                'ma_don_hang' => 'DH-261002-F601',
                'khach_hang_ten' => 'Đại Lý Thủy Sản Miền Tây',
                'khach_hang_sdt' => '0988123456',
                'loai_don' => 'b2b',
                'tong_tien' => 64500000,
                'trang_thai' => 3,
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->setDay(2)->setHour(11),
                'updated_at' => $now->copy()->setDay(2)->setHour(11),
            ],
            [
                'ma_don_hang' => 'DH-261008-F602',
                'khach_hang_ten' => 'Xưởng Máy Thủy Hải Phòng',
                'khach_hang_sdt' => '0918776655',
                'loai_don' => 'b2b',
                'tong_tien' => 38000000,
                'trang_thai' => 2, // Đang giao
                'trang_thai_thanh_toan' => 1,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subDays(3)->setHour(9),
                'updated_at' => $now->copy()->subDays(3)->setHour(9),
            ],
            [
                'ma_don_hang' => 'DH-261010-F603',
                'khach_hang_ten' => 'Trần Quang Minh',
                'khach_hang_sdt' => '0905987654',
                'loai_don' => 'b2c',
                'tong_tien' => 7650000,
                'trang_thai' => 1, // Đang xử lý
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subDays(1)->setHour(14),
                'updated_at' => $now->copy()->subDays(1)->setHour(14),
            ],
            [
                'ma_don_hang' => 'DH-261011-F604',
                'khach_hang_ten' => 'Công Ty Hàng Hải Biển Đông',
                'khach_hang_sdt' => '0909554433',
                'loai_don' => 'b2b',
                'tong_tien' => 32500000,
                'trang_thai' => 3, // Hoàn tất hôm nay!
                'trang_thai_thanh_toan' => 2,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(4),
            ],
            [
                'ma_don_hang' => 'DH-261011-F605',
                'khach_hang_ten' => 'Nguyễn Hải Đăng',
                'khach_hang_sdt' => '0938445566',
                'loai_don' => 'b2c',
                'tong_tien' => 4200000,
                'trang_thai' => 0, // Chờ xác nhận
                'trang_thai_thanh_toan' => 0,
                'id_dia_chi_giao_hang' => 1,
                'id_phuong_thuc_thanh_toan' => 1,
                'created_at' => $now->copy()->subMinutes(25),
                'updated_at' => $now->copy()->subMinutes(25),
            ],
        ];

        foreach ($orders as $od) {
            DonHang::updateOrCreate(['ma_don_hang' => $od['ma_don_hang']], $od);
        }

        // 2. TẠO CÁC YÊU CẦU BÁO GIÁ (YEU_CAU_BAO_GIAS)
        $quotes = [
            [
                'ma_yeu_cau' => 'YCBG-261011-001',
                'khach_hang_ten' => 'Anh Tuấn (Rạch Giá)',
                'khach_hang_sdt' => '0912345678',
                'khach_hang_email' => 'tuan.rachgia@gmail.com',
                'ghi_chu_khach_hang' => 'Cần báo giá gấp bộ xéc-măng và bạc lót Yanmar 4JH',
                'ghi_chu_zalo' => 'Đã tư vấn qua Zalo, khách đợi báo giá có VAT',
                'tong_tien' => 14500000,
                'trang_thai' => 0, // Chờ xử lý
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'ma_yeu_cau' => 'YCBG-261011-002',
                'khach_hang_ten' => 'Chú Ba Cần Giờ',
                'khach_hang_sdt' => '0987654321',
                'khach_hang_email' => 'bacangio@marine.vn',
                'ghi_chu_khach_hang' => 'Hỏi giá bơm cao áp Cummins 6BT và kim phun',
                'ghi_chu_zalo' => 'Khách hỏi hàng bãi hay mới chính hãng',
                'tong_tien' => 28000000,
                'trang_thai' => 0, // Chờ xử lý
                'created_at' => $now->copy()->subMinutes(45),
                'updated_at' => $now->copy()->subMinutes(45),
            ],
            [
                'ma_yeu_cau' => 'YCBG-261010-003',
                'khach_hang_ten' => 'Nguyễn Văn Hưng',
                'khach_hang_sdt' => '0903112233',
                'khach_hang_email' => 'hung.nguyen@gmail.com',
                'ghi_chu_khach_hang' => 'Lọc dầu nhớt và cánh quạt đẩy Yamaha 200HP',
                'ghi_chu_zalo' => 'Đã báo giá 7.200.000 ₫ qua Zalo',
                'tong_tien' => 7200000,
                'trang_thai' => 1, // Đã báo giá
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'ma_yeu_cau' => 'YCBG-260925-004',
                'khach_hang_ten' => 'Xưởng Tàu Long Sơn',
                'khach_hang_sdt' => '0977889911',
                'khach_hang_email' => 'longson.shipyard@gmail.com',
                'ghi_chu_khach_hang' => 'Trọn bộ gioăng phớt đại tu máy Suzuki DF250',
                'ghi_chu_zalo' => 'Khách chốt đơn, đã chuyển khoản cọc 50%',
                'tong_tien' => 21500000,
                'trang_thai' => 2, // Đã chốt
                'created_at' => $now->copy()->subDays(16),
                'updated_at' => $now->copy()->subDays(16),
            ]
        ];

        foreach ($quotes as $qt) {
            YeuCauBaoGia::updateOrCreate(['ma_yeu_cau' => $qt['ma_yeu_cau']], $qt);
        }

        // 3. TẠO CÁC ĐƠN NHẬP HÀNG (DON_NHAP_HANGS / PO) CHO BIỂU ĐỒ DOANH THU & CHI PHÍ
        $pos = [
            [
                'ma_po' => 'PO-260510-01',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(5)->setDay(10)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(5)->setDay(25)->toDateString(),
                'tong_tien' => 12000000,
                'trang_thai' => 2, // Đã nhận hàng
                'created_at' => $now->copy()->subMonths(5)->setDay(10),
                'updated_at' => $now->copy()->subMonths(5)->setDay(10),
            ],
            [
                'ma_po' => 'PO-260615-02',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(4)->setDay(15)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(4)->setDay(30)->toDateString(),
                'tong_tien' => 31000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(4)->setDay(15),
                'updated_at' => $now->copy()->subMonths(4)->setDay(15),
            ],
            [
                'ma_po' => 'PO-260718-03',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(3)->setDay(18)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(3)->setDay(28)->toDateString(),
                'tong_tien' => 42000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(3)->setDay(18),
                'updated_at' => $now->copy()->subMonths(3)->setDay(18),
            ],
            [
                'ma_po' => 'PO-260812-04',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonths(2)->setDay(12)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonths(2)->setDay(26)->toDateString(),
                'tong_tien' => 54000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonths(2)->setDay(12),
                'updated_at' => $now->copy()->subMonths(2)->setDay(12),
            ],
            [
                'ma_po' => 'PO-260905-05',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->subMonth()->setDay(5)->toDateString(),
                'ngay_du_kien' => $now->copy()->subMonth()->setDay(20)->toDateString(),
                'tong_tien' => 65000000,
                'trang_thai' => 2,
                'created_at' => $now->copy()->subMonth()->setDay(5),
                'updated_at' => $now->copy()->subMonth()->setDay(5),
            ],
            [
                'ma_po' => 'PO-261005-06',
                'id_nha_cung_cap' => 1,
                'id_kho' => 1,
                'ngay_nhap' => $now->copy()->setDay(5)->toDateString(),
                'ngay_du_kien' => $now->copy()->setDay(22)->toDateString(),
                'tong_tien' => 45000000,
                'trang_thai' => 1, // Đang đặt hàng (PO đang mở)
                'created_at' => $now->copy()->setDay(5),
                'updated_at' => $now->copy()->setDay(5),
            ],
        ];

        foreach ($pos as $po) {
            DonNhapHang::updateOrCreate(['ma_po' => $po['ma_po']], $po);
        }

        // 4. CẬP NHẬT TỒN KHO CÓ NHU CẦU CẦN GOM PO (SO_LUONG_DANG_GIU > 0)
        $sampleInventory = TonKho::take(3)->get();
        if ($sampleInventory->count() >= 3) {
            $sampleInventory[0]->update(['so_luong_dang_giu' => 4]);
            $sampleInventory[1]->update(['so_luong_dang_giu' => 8]);
            $sampleInventory[2]->update(['so_luong_dang_giu' => 3]);
        } elseif (TonKho::count() == 0) {
            // Tạo 3 bản ghi mẫu tồn kho thiếu hàng nếu chưa có
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 1,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 0,
                'so_luong_dang_giu' => 5,
            ]);
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 2,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 1,
                'so_luong_dang_giu' => 6,
            ]);
            TonKho::create([
                'id_kho' => 1,
                'id_san_pham' => 3,
                'loai_san_pham' => 'phu_tung',
                'so_luong_ton' => 0,
                'so_luong_dang_giu' => 4,
            ]);
        }

        echo "Seeded Dashboard data successfully!\n";
    }
}
