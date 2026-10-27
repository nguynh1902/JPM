<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\ChiTietBaoGia;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\CMS\Models\SanPham;

class DonHangVaBaoGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Đảm bảo có phương thức thanh toán, đơn vị vận chuyển và địa chỉ
        $this->call([
            PhuongThucThanhToanSeeder::class,
            DonViVanChuyenSeeder::class,
            DiaChiSeeder::class,
        ]);

        // Lấy danh sách phụ tùng và sản phẩm thực tế từ DB để gán chính xác
        $phuTungs = PhuTung::all()->keyBy('id');
        $sanPhams = SanPham::all()->keyBy('id');

        // =========================================================================
        // PHẦN A: YÊU CẦU BÁO GIÁ (YEU_CAU_BAO_GIAS & CHI_TIET_BAO_GIAS)
        // =========================================================================

        $quotesData = [
            // ---------------------------------------------------------------------
            // TAB 1: CHỜ BÁO GIÁ (trang_thai = 0)
            // ---------------------------------------------------------------------
            [
                'ma_yeu_cau'          => 'YCBG-20261027-01',
                'user_id'             => 3,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Nguyễn Văn Hùng (Chủ Tàu Kiên Giang)',
                'khach_hang_sdt'      => '0918234567',
                'khach_hang_email'    => 'hung.marine@gmail.com',
                'dia_chi_giao_hang'   => 'Cảng cá Tắc Cậu, Huyện Châu Thành, Kiên Giang',
                'ghi_chu_khach_hang'  => 'Cần báo giá gấp 3 món phụ tùng động cơ Honda BF225. Tàu đang neo tại bến chờ phụ tùng thay thế.',
                'ghi_chu_zalo'        => null,
                'tong_tien'           => 0,
                'phi_van_chuyen'      => 0,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 0,
                'da_thu'              => 0,
                'trang_thai'          => 0, // Chờ báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subHours(2),
                'items'               => [
                    ['ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 2, 'don_gia' => 0],
                    ['ten_phu_tung' => 'BEARING, RR. GENERATOR', 'ma_phu_tung' => '31111-PT0-003', 'so_luong' => 2, 'don_gia' => 0],
                    ['ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 1, 'don_gia' => 0],
                ]
            ],
            [
                'ma_yeu_cau'          => 'YCBG-20261027-02',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'HTX Vận Tải Thủy Cửa Đại (Hội An)',
                'khach_hang_sdt'      => '0905345678',
                'khach_hang_email'    => 'htx.cuadai@gmail.com',
                'dia_chi_giao_hang'   => 'Bến tàu Cửa Đại, TP. Hội An, Tỉnh Quảng Nam',
                'ghi_chu_khach_hang'  => 'Báo giá bộ bu-lông và ốc puly máy phát điện cano cao tốc chở khách Cù Lao Chàm. Cần xuất hóa đơn VAT.',
                'ghi_chu_zalo'        => null,
                'tong_tien'           => 0,
                'phi_van_chuyen'      => 0,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 0,
                'da_thu'              => 0,
                'trang_thai'          => 0, // Chờ báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subHours(5),
                'items'               => [
                    ['ten_phu_tung' => 'NUT, PULLEY LOCK', 'ma_phu_tung' => '31142-PD1-001', 'so_luong' => 4, 'don_gia' => 0],
                    ['ten_phu_tung' => 'BOLTS, STUDS (6X68)', 'ma_phu_tung' => '31145-PD1-004', 'so_luong' => 6, 'don_gia' => 0],
                    ['ten_phu_tung' => 'SCREW-WASHER', 'ma_phu_tung' => '31147-PT0-003', 'so_luong' => 10, 'don_gia' => 0],
                ]
            ],
            [
                'ma_yeu_cau'          => 'YCBG-20261026-03',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Bác Ba Vạn Gốc (Cà Mau)',
                'khach_hang_sdt'      => '0977891234',
                'khach_hang_email'    => 'baba.camau@gmail.com',
                'dia_chi_giao_hang'   => 'Thị trấn Sông Đốc, Huyện Trần Văn Thời, Cà Mau',
                'ghi_chu_khach_hang'  => 'Máy phát điện tàu vỏ gỗ bị rơ bạc đạn và kêu rè rè, cần tư vấn phụ tùng thay thế chuẩn Nhật.',
                'ghi_chu_zalo'        => null,
                'tong_tien'           => 0,
                'phi_van_chuyen'      => 0,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 0,
                'da_thu'              => 0,
                'trang_thai'          => 0, // Chờ báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subDays(1)->setHour(9),
                'items'               => [
                    ['ten_phu_tung' => 'BEARING, FR. GENERATOR', 'ma_phu_tung' => '31114-PT0-013', 'so_luong' => 1, 'don_gia' => 0],
                    ['ten_phu_tung' => 'BUSH, INSULATION', 'ma_phu_tung' => '31118-PT0-003', 'so_luong' => 2, 'don_gia' => 0],
                    ['ten_phu_tung' => 'COVER, BEARING', 'ma_phu_tung' => '31130-PD1-001', 'so_luong' => 1, 'don_gia' => 0],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 2: ĐÃ BÁO GIÁ (trang_thai = 1)
            // ---------------------------------------------------------------------
            [
                'ma_yeu_cau'          => 'YCBG-20261026-04',
                'user_id'             => 4,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'CLB Du Thuyền & Cano Sài Gòn Marina',
                'khach_hang_sdt'      => '0908777222',
                'khach_hang_email'    => 'saigon.marina.club@gmail.com',
                'dia_chi_giao_hang'   => 'Bến du thuyền Lan Anh, Đường số 45, P. Bình An, TP. Thủ Đức, TP.HCM',
                'ghi_chu_khach_hang'  => 'Yêu cầu phụ tùng chính hãng Honda Outboard dòng V6 250HP.',
                'ghi_chu_zalo'        => "Đã trao đổi qua Zalo với anh Tuấn quản lý bến. Khách đồng ý đơn giá, hẹn chiều nay kế toán chuyển cọc 30% giữ hàng.",
                'tong_tien'           => 14697000,
                'phi_van_chuyen'      => 65000,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 4500000,
                'da_thu'              => 0,
                'trang_thai'          => 1, // Đã báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subDays(1)->setHour(14),
                'items'               => [
                    ['ten_phu_tung' => 'ROTOR ASSY.', 'ma_phu_tung' => '31631-ZY3-003', 'so_luong' => 1, 'don_gia' => 11023000],
                    ['ten_phu_tung' => 'END COVER, RR.', 'ma_phu_tung' => '31639-ZY3-003', 'so_luong' => 1, 'don_gia' => 3674000],
                ]
            ],
            [
                'ma_yeu_cau'          => 'YCBG-20261025-05',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Công Ty Dịch Vụ Cảng Biển Vân Đồn (Quảng Ninh)',
                'khach_hang_sdt'      => '0983456789',
                'khach_hang_email'    => 'vandon.maritime@gmail.com',
                'dia_chi_giao_hang'   => 'Cảng quốc tế Ao Tiên, Huyện Vân Đồn, Quảng Ninh',
                'ghi_chu_khach_hang'  => 'Báo giá cụm máy phát điện và pát đỡ dòng máy thủy Honda.',
                'ghi_chu_zalo'        => "Đã gửi bảng báo giá có dấu mộc công ty qua Zalo anh Nam. Hiệu lực báo giá 72 giờ theo tỷ giá JPY.",
                'tong_tien'           => 30487000,
                'phi_van_chuyen'      => 95000,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 10000000,
                'da_thu'              => 0,
                'trang_thai'          => 1, // Đã báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subDays(2)->setHour(11),
                'items'               => [
                    ['ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 1, 'don_gia' => 30237000],
                    ['ten_phu_tung' => 'STAY, ALTERNATOR', 'ma_phu_tung' => '31637-ZY3-000', 'so_luong' => 2, 'don_gia' => 125000],
                ]
            ],
            [
                'ma_yeu_cau'          => 'YCBG-20261025-06',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Đội Tàu Dịch Vụ Câu Mực Đêm Phú Quốc',
                'khach_hang_sdt'      => '0913882119',
                'khach_hang_email'    => 'phuquoc.night@gmail.com',
                'dia_chi_giao_hang'   => 'Bến tàu Hàm Ninh, TP. Phú Quốc, Tỉnh Kiên Giang',
                'ghi_chu_khach_hang'  => 'Cần mua puly, ốc khóa và chổi than máy phát điện.',
                'ghi_chu_zalo'        => "Khách hẹn cuối tuần nhận tiền công tour du lịch sẽ chuyển cọc 1,500,000đ.",
                'tong_tien'           => 4386000,
                'phi_van_chuyen'      => 45000,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 1500000,
                'da_thu'              => 0,
                'trang_thai'          => 1, // Đã báo giá
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subDays(2)->setHour(16),
                'items'               => [
                    ['ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 1, 'don_gia' => 2978000],
                    ['ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 1, 'don_gia' => 1218000],
                    ['ten_phu_tung' => 'NUT, PULLEY LOCK', 'ma_phu_tung' => '31142-PD1-001', 'so_luong' => 1, 'don_gia' => 190000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 3: ĐANG XỬ LÝ & GIAO (trang_thai = 2: Báo giá đã chốt / đã cọc)
            // ---------------------------------------------------------------------
            [
                'ma_yeu_cau'          => 'YCBG-20261024-07',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Anh Đức (Cano Tuần Châu - Vịnh Hạ Long)',
                'khach_hang_sdt'      => '0945678123',
                'khach_hang_email'    => 'duc.tuanchau@gmail.com',
                'dia_chi_giao_hang'   => 'Bến Tuần Châu Marina, TP. Hạ Long, Tỉnh Quảng Ninh',
                'ghi_chu_khach_hang'  => 'Đặt hàng gấp 1 bộ Cụm máy phát điện ALTERNATOR ASSY chính hãng Honda.',
                'ghi_chu_zalo'        => "Khách đã quét VietQR chuyển cọc 10,000,000đ. Đã tạo phiếu yêu cầu gom PO Nhật Bản đặt hàng.",
                'tong_tien'           => 30237000,
                'phi_van_chuyen'      => 120000,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 10000000,
                'da_thu'              => 10000000,
                'trang_thai'          => 2, // Đã chốt -> Xuất hiện ở tab Đang Xử Lý & Giao
                'loai_yeu_cau'        => 'dat_hang',
                'created_at'          => $now->copy()->subDays(3)->setHour(10),
                'items'               => [
                    ['ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 1, 'don_gia' => 30237000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 5: ĐÃ HỦY (trang_thai = 3)
            // ---------------------------------------------------------------------
            [
                'ma_yeu_cau'          => 'YCBG-20261023-08',
                'user_id'             => null,
                'token'               => Str::random(32),
                'khach_hang_ten'      => 'Anh Tuấn Rạch Giá',
                'khach_hang_sdt'      => '0912443322',
                'khach_hang_email'    => 'tuan.rachgia@gmail.com',
                'dia_chi_giao_hang'   => 'Rạch Sỏi, TP. Rạch Giá, Tỉnh Kiên Giang',
                'ghi_chu_khach_hang'  => 'Cần mua lốc máy và rotor máy thủy đời 1995.',
                'ghi_chu_zalo'        => "Nhà cung cấp Nhật phản hồi model máy đã quá niên hạn (Discontinued) không còn linh kiện thay thế chính hãng. Đã thông báo hủy yêu cầu và gợi ý khách hoán cải cụm máy mới.",
                'tong_tien'           => 11023000,
                'phi_van_chuyen'      => 0,
                'khach_tu_tra_cuoc'   => false,
                'tien_coc_yeu_cau'    => 0,
                'da_thu'              => 0,
                'trang_thai'          => 3, // Đã hủy
                'loai_yeu_cau'        => 'bao_gia',
                'created_at'          => $now->copy()->subDays(4)->setHour(9),
                'items'               => [
                    ['ten_phu_tung' => 'ROTOR ASSY.', 'ma_phu_tung' => '31631-ZY3-003', 'so_luong' => 1, 'don_gia' => 11023000],
                ]
            ],
        ];

        foreach ($quotesData as $qData) {
            $items = $qData['items'];
            unset($qData['items']);

            $quote = YeuCauBaoGia::updateOrCreate(['ma_yeu_cau' => $qData['ma_yeu_cau']], $qData);

            // Xóa chi tiết cũ nếu có và tạo lại
            ChiTietBaoGia::where('yeu_cau_bao_gia_id', $quote->id)->delete();
            foreach ($items as $item) {
                ChiTietBaoGia::create([
                    'yeu_cau_bao_gia_id' => $quote->id,
                    'ten_phu_tung'       => $item['ten_phu_tung'],
                    'ma_phu_tung'        => $item['ma_phu_tung'],
                    'so_luong'           => $item['so_luong'],
                    'don_gia'            => $item['don_gia'],
                    'created_at'         => $quote->created_at,
                    'updated_at'         => $quote->updated_at,
                ]);
            }
        }

        // =========================================================================
        // PHẦN B: ĐƠN HÀNG (DON_HANGS & CHI_TIET_DON_HANGS)
        // =========================================================================

        $ordersData = [
            // ---------------------------------------------------------------------
            // TAB 3: ĐANG XỬ LÝ & GIAO - Đơn hàng Chờ Duyệt / Chờ Cọc (trang_thai = 0)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-20261027-001',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 1, // Nguyễn Hải Dương - Đại lý cấp 1
                'khach_hang_ten'           => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'khach_hang_sdt'           => '0988123456',
                'khach_hang_email'         => 'haiduong@abc-marine.com',
                'id_dia_chi_giao_hang'     => 1,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Rạch Giá, Đường 3/2, TP. Rạch Giá, Tỉnh Kiên Giang',
                'ghi_chu_khach_hang'       => 'Đơn đặt hàng đại lý lấy phụ tùng dự trữ bảo dưỡng cano cao tốc tháng 11.',
                'trang_thai'               => 0, // Chờ duyệt
                'trang_thai_thanh_toan'    => 0, // Chưa thanh toán
                'tong_tien'                => 16030000,
                'phi_van_chuyen'           => 120000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 5000000,
                'da_thu'                   => 0,
                'id_phuong_thuc_thanh_toan'=> 1, // VietQR
                'id_don_vi_van_chuyen'     => 1, // ViettelPost
                'ma_van_don'               => null,
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'tien_thu_ho_cod'          => 16150000,
                'created_at'               => $now->copy()->subHours(3),
                'items'                    => [
                    ['id_san_pham' => 1, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 5, 'don_gia' => 1218000, 'thanh_tien' => 6090000],
                    ['id_san_pham' => 2, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'BEARING, RR. GENERATOR', 'ma_phu_tung' => '31111-PT0-003', 'so_luong' => 4, 'don_gia' => 996000, 'thanh_tien' => 3984000],
                    ['id_san_pham' => 6, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 2, 'don_gia' => 2978000, 'thanh_tien' => 5956000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 3: ĐANG XỬ LÝ & GIAO - Đơn hàng Đang Gom PO Nhật (trang_thai = 1)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-20261026-002',
                'loai_don'                 => 'b2c',
                'id_khach_hang'            => 2, // Trần Quang Minh
                'khach_hang_ten'           => 'Trần Quang Minh (Xưởng Cano)',
                'khach_hang_sdt'           => '0905987654',
                'khach_hang_email'         => 'minh.tran@gmail.com',
                'id_dia_chi_giao_hang'     => 2,
                'dia_chi_text'             => 'Xưởng Sửa Chữa Tàu Thủy Marina, 128 Nguyễn Hữu Cảnh, P.22, Q. Bình Thạnh, TP.HCM',
                'ghi_chu_khach_hang'       => 'Phụ tùng chính hãng Honda Outboard cho cano thể thao Sea-Doo / Yamaha Marine.',
                'trang_thai'               => 1, // Lên đơn Nhật
                'trang_thai_thanh_toan'    => 1, // Đã cọc một phần
                'tong_tien'                => 22524000,
                'phi_van_chuyen'           => 80000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 12000000,
                'da_thu'                   => 12000000, // Đã cọc 12tr
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => null,
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'tien_thu_ho_cod'          => 10604000,
                'created_at'               => $now->copy()->subDays(1)->setHour(10),
                'items'                    => [
                    ['id_san_pham' => 15, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'ROTOR ASSY.', 'ma_phu_tung' => '31631-ZY3-003', 'so_luong' => 2, 'don_gia' => 11023000, 'thanh_tien' => 22046000],
                    ['id_san_pham' => 5, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'COVER, BEARING', 'ma_phu_tung' => '31130-PD1-001', 'so_luong' => 2, 'don_gia' => 239000, 'thanh_tien' => 478000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 3: ĐANG XỬ LÝ & GIAO - Đơn hàng Đang Giao ViettelPost (trang_thai = 2)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-20261025-003',
                'loai_don'                 => 'b2c',
                'id_khach_hang'            => null,
                'khach_hang_ten'           => 'Hoàng Quân (Bến Du Thuyền Cần Thơ)',
                'khach_hang_sdt'           => '0939889900',
                'khach_hang_email'         => 'quan.cantho@gmail.com',
                'id_dia_chi_giao_hang'     => null,
                'dia_chi_text'             => 'Bến Ninh Kiều, Đường Hai Bà Trưng, P. Tân An, Q. Ninh Kiều, Cần Thơ',
                'ghi_chu_khach_hang'       => 'Giao nhanh ViettelPost cho tàu kịp giờ xuất bến đi Châu Đốc.',
                'trang_thai'               => 2, // Giao ViettelPost
                'trang_thai_thanh_toan'    => 1, // Đã cọc một phần
                'tong_tien'                => 11318000,
                'phi_van_chuyen'           => 65000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 5000000,
                'da_thu'                   => 5000000,
                'id_phuong_thuc_thanh_toan'=> 2, // COD
                'id_don_vi_van_chuyen'     => 1, // ViettelPost
                'ma_van_don'               => 'VP772918239VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 6383000,
                'created_at'               => $now->copy()->subDays(2)->setHour(15),
                'items'                    => [
                    ['id_san_pham' => 17, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'END COVER, RR.', 'ma_phu_tung' => '31639-ZY3-003', 'so_luong' => 2, 'don_gia' => 3674000, 'thanh_tien' => 7348000],
                    ['id_san_pham' => 6, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 1, 'don_gia' => 2978000, 'thanh_tien' => 2978000],
                    ['id_san_pham' => 3, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'BEARING, FR. GENERATOR', 'ma_phu_tung' => '31114-PT0-013', 'so_luong' => 1, 'don_gia' => 996000, 'thanh_tien' => 992000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-20261024-004',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => null,
                'khach_hang_ten'           => 'Garage Tàu Thủy Miền Trung (Quy Nhơn)',
                'khach_hang_sdt'           => '0909123987',
                'khach_hang_email'         => 'garagemientrung@gmail.com',
                'id_dia_chi_giao_hang'     => null,
                'dia_chi_text'             => 'Cảng cá Quy Nhơn, Đường Trần Hưng Đạo, TP. Quy Nhơn, Bình Định',
                'ghi_chu_khach_hang'       => 'Đơn hàng thiết bị giám sát OnDeck và dụng cụ bảo dưỡng máy thủy.',
                'trang_thai'               => 2, // Giao ViettelPost
                'trang_thai_thanh_toan'    => 1,
                'tong_tien'                => 24799000,
                'phi_van_chuyen'           => 150000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 10000000,
                'da_thu'                   => 10000000,
                'id_phuong_thuc_thanh_toan'=> 2,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP882194821VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 14949000,
                'created_at'               => $now->copy()->subDays(3)->setHour(8),
                'items'                    => [
                    ['id_san_pham' => 4, 'loai_san_pham' => 'san_pham', 'ten_phu_tung' => 'TRUNG TÂM ONDECK', 'ma_phu_tung' => '010-02134-00', 'so_luong' => 1, 'don_gia' => 21250000, 'thanh_tien' => 21250000],
                    ['id_san_pham' => 10, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SCREW-WASHER', 'ma_phu_tung' => '31147-PT0-003', 'so_luong' => 20, 'don_gia' => 73000, 'thanh_tien' => 1460000],
                    ['id_san_pham' => 12, 'loai_san_pham' => 'san_pham', 'ten_phu_tung' => 'BỘ DỤNG CỤ THAY DẦU 10W-40 CHO TÀU THUYỀN', 'ma_phu_tung' => 'LUB-WTRCG-KT-00', 'so_luong' => 2, 'don_gia' => 1044500, 'thanh_tien' => 2089000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 4: HOÀN THÀNH (trang_thai = 3, trang_thai_thanh_toan = 2)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-20261020-005',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 1, // Nguyễn Hải Dương
                'khach_hang_ten'           => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'khach_hang_sdt'           => '0988123456',
                'khach_hang_email'         => 'haiduong@abc-marine.com',
                'id_dia_chi_giao_hang'     => 1,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Rạch Giá, Đường 3/2, TP. Rạch Giá, Tỉnh Kiên Giang',
                'ghi_chu_khach_hang'       => 'Đơn hàng phụ tùng máy phát điện ALTERNATOR ASSY chính hãng Honda.',
                'trang_thai'               => 3, // Hoàn thành
                'trang_thai_thanh_toan'    => 2, // Đã thanh toán đủ 100%
                'tong_tien'                => 60474000,
                'phi_van_chuyen'           => 150000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 30000000,
                'da_thu'                   => 60624000, // Thu đủ cả ship
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP661928472VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subDays(7)->setHour(14),
                'items'                    => [
                    ['id_san_pham' => 14, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 2, 'don_gia' => 30237000, 'thanh_tien' => 60474000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-20261018-006',
                'loai_don'                 => 'b2c',
                'id_khach_hang'            => null,
                'khach_hang_ten'           => 'Lê Đình Nam (Cano Cù Lao Chàm, Hội An)',
                'khach_hang_sdt'           => '0905789123',
                'khach_hang_email'         => 'nam.hoidan@gmail.com',
                'id_dia_chi_giao_hang'     => null,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Cửa Đại, Hội An, Quảng Nam',
                'ghi_chu_khach_hang'       => 'Cần phụ tùng puly và ốc khóa chính hãng cho cano tuần tra.',
                'trang_thai'               => 3, // Hoàn thành
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 7554000,
                'phi_van_chuyen'           => 50000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 3000000,
                'da_thu'                   => 7604000,
                'id_phuong_thuc_thanh_toan'=> 2,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP551829301VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subDays(9)->setHour(11),
                'items'                    => [
                    ['id_san_pham' => 6, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 2, 'don_gia' => 2978000, 'thanh_tien' => 5956000],
                    ['id_san_pham' => 1, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 1, 'don_gia' => 1218000, 'thanh_tien' => 1218000],
                    ['id_san_pham' => 7, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'NUT, PULLEY LOCK', 'ma_phu_tung' => '31142-PD1-001', 'so_luong' => 2, 'don_gia' => 190000, 'thanh_tien' => 380000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-20261015-007',
                'loai_don'                 => 'b2c',
                'id_khach_hang'            => null,
                'khach_hang_ten'           => 'Ngô Tấn Tài (Cảng Cá Hòn Rớ Nha Trang)',
                'khach_hang_sdt'           => '0979667788',
                'khach_hang_email'         => 'tai.honro@gmail.com',
                'id_dia_chi_giao_hang'     => null,
                'dia_chi_text'             => 'Khu hậu cần Cảng Cá Hòn Rớ, Phước Đồng, TP. Nha Trang, Khánh Hòa',
                'ghi_chu_khach_hang'       => 'Máy dò cá Striker Vivid 4CV phục vụ tàu đi biển dài ngày.',
                'trang_thai'               => 3, // Hoàn thành
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 5000000,
                'phi_van_chuyen'           => 60000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 2000000,
                'da_thu'                   => 5060000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP449102941VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subDays(12)->setHour(9),
                'items'                    => [
                    ['id_san_pham' => 2, 'loai_san_pham' => 'san_pham', 'ten_phu_tung' => 'STRIKER VIVID 4CV VỚI ĐẦU DÒ GT20-TM', 'ma_phu_tung' => '010-02550-01', 'so_luong' => 1, 'don_gia' => 5000000, 'thanh_tien' => 5000000],
                ]
            ],

            // ---------------------------------------------------------------------
            // TAB 5: ĐÃ HỦY (trang_thai = 4)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-20261022-008',
                'loai_don'                 => 'b2c',
                'id_khach_hang'            => null,
                'khach_hang_ten'           => 'Phạm Thanh Thắng (Bến Tre)',
                'khach_hang_sdt'           => '0948112990',
                'khach_hang_email'         => 'thang.bentre@gmail.com',
                'id_dia_chi_giao_hang'     => null,
                'dia_chi_text'             => 'Cảng cá Ba Tri, TT. Tiệm Tôm, Ba Tri, Bến Tre',
                'ghi_chu_khach_hang'       => 'Khách đổi ý muốn nâng cấp lên model GPS màn hình lớn hơn nên làm đơn mới.',
                'trang_thai'               => 4, // Đã hủy
                'trang_thai_thanh_toan'    => 0,
                'tong_tien'                => 2082000,
                'phi_van_chuyen'           => 0,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 0,
                'da_thu'                   => 0,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => null,
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subDays(5)->setHour(16),
                'items'                    => [
                    ['id_san_pham' => 3, 'loai_san_pham' => 'san_pham', 'ten_phu_tung' => 'FISHTRAX PORTABLE VỚI BIỂU TƯỢNG DSPLY', 'ma_phu_tung' => 'FT1P', 'so_luong' => 1, 'don_gia' => 2082000, 'thanh_tien' => 2082000],
                ]
            ],

            // ---------------------------------------------------------------------
            // PHẦN C: CÁC ĐƠN HÀNG LỊCH SỬ CÁC THÁNG TRƯỚC (PHỤC VỤ BIỂU ĐỒ DOANH THU DASHBOARD)
            // ---------------------------------------------------------------------
            [
                'ma_don_hang'              => 'DH-260515-09',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 1,
                'khach_hang_ten'           => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'khach_hang_sdt'           => '0988123456',
                'khach_hang_email'         => 'haiduong@abc-marine.com',
                'id_dia_chi_giao_hang'     => 1,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Rạch Giá, Kiên Giang',
                'ghi_chu_khach_hang'       => 'Đơn xuất bán sỉ phụ tùng máy thủy tháng 5/2026',
                'trang_thai'               => 3,
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 28500000,
                'phi_van_chuyen'           => 120000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 10000000,
                'da_thu'                   => 28620000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP260515091VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subMonths(5)->setDay(15),
                'items'                    => [
                    ['id_san_pham' => 14, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 1, 'don_gia' => 28500000, 'thanh_tien' => 28500000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-260620-10',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 2,
                'khach_hang_ten'           => 'Trần Quang Minh (Xưởng Cano)',
                'khach_hang_sdt'           => '0905987654',
                'khach_hang_email'         => 'minh.tran@gmail.com',
                'id_dia_chi_giao_hang'     => 2,
                'dia_chi_text'             => '128 Nguyễn Hữu Cảnh, P.22, Q. Bình Thạnh, TP.HCM',
                'ghi_chu_khach_hang'       => 'Đơn phụ tùng bảo dưỡng du thuyền tháng 6/2026',
                'trang_thai'               => 3,
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 45000000,
                'phi_van_chuyen'           => 100000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 15000000,
                'da_thu'                   => 45100000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP260620101VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subMonths(4)->setDay(20),
                'items'                    => [
                    ['id_san_pham' => 15, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'ROTOR ASSY.', 'ma_phu_tung' => '31631-ZY3-003', 'so_luong' => 4, 'don_gia' => 11023000, 'thanh_tien' => 44092000],
                    ['id_san_pham' => 6, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 1, 'don_gia' => 908000, 'thanh_tien' => 908000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-260722-11',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 1,
                'khach_hang_ten'           => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'khach_hang_sdt'           => '0988123456',
                'khach_hang_email'         => 'haiduong@abc-marine.com',
                'id_dia_chi_giao_hang'     => 1,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Rạch Giá, Kiên Giang',
                'ghi_chu_khach_hang'       => 'Đơn xuất kho định kỳ tháng 7/2026',
                'trang_thai'               => 3,
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 62000000,
                'phi_van_chuyen'           => 150000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 20000000,
                'da_thu'                   => 62150000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP260722111VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subMonths(3)->setDay(22),
                'items'                    => [
                    ['id_san_pham' => 14, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 2, 'don_gia' => 30237000, 'thanh_tien' => 60474000],
                    ['id_san_pham' => 1, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 1, 'don_gia' => 1526000, 'thanh_tien' => 1526000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-260818-12',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 2,
                'khach_hang_ten'           => 'Trần Quang Minh (Xưởng Cano)',
                'khach_hang_sdt'           => '0905987654',
                'khach_hang_email'         => 'minh.tran@gmail.com',
                'id_dia_chi_giao_hang'     => 2,
                'dia_chi_text'             => '128 Nguyễn Hữu Cảnh, P.22, Q. Bình Thạnh, TP.HCM',
                'ghi_chu_khach_hang'       => 'Đơn phụ tùng thay thế tháng 8/2026',
                'trang_thai'               => 3,
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 78000000,
                'phi_van_chuyen'           => 180000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 25000000,
                'da_thu'                   => 78180000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP260818121VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subMonths(2)->setDay(18),
                'items'                    => [
                    ['id_san_pham' => 14, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 2, 'don_gia' => 30237000, 'thanh_tien' => 60474000],
                    ['id_san_pham' => 15, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'ROTOR ASSY.', 'ma_phu_tung' => '31631-ZY3-003', 'so_luong' => 1, 'don_gia' => 11023000, 'thanh_tien' => 11023000],
                    ['id_san_pham' => 6, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'PULLEY', 'ma_phu_tung' => '31141-P13-003', 'so_luong' => 2, 'don_gia' => 3251500, 'thanh_tien' => 6503000],
                ]
            ],
            [
                'ma_don_hang'              => 'DH-260910-13',
                'loai_don'                 => 'b2b',
                'id_khach_hang'            => 1,
                'khach_hang_ten'           => 'Nguyễn Hải Dương (Đại lý Kiên Giang)',
                'khach_hang_sdt'           => '0988123456',
                'khach_hang_email'         => 'haiduong@abc-marine.com',
                'id_dia_chi_giao_hang'     => 1,
                'dia_chi_text'             => 'Bến Tàu Du Lịch Rạch Giá, Kiên Giang',
                'ghi_chu_khach_hang'       => 'Đơn xuất bán sỉ phụ tùng chuẩn Nhật tháng 9/2026',
                'trang_thai'               => 3,
                'trang_thai_thanh_toan'    => 2,
                'tong_tien'                => 92500000,
                'phi_van_chuyen'           => 200000,
                'khach_tu_tra_cuoc'        => false,
                'tien_coc_yeu_cau'         => 30000000,
                'da_thu'                   => 92700000,
                'id_phuong_thuc_thanh_toan'=> 1,
                'id_don_vi_van_chuyen'     => 1,
                'ma_van_don'               => 'VP260910131VN',
                'ten_don_vi_van_chuyen'    => 'ViettelPost',
                'don_vi_van_chuyen'        => 'ViettelPost',
                'tien_thu_ho_cod'          => 0,
                'created_at'               => $now->copy()->subMonth()->setDay(10),
                'items'                    => [
                    ['id_san_pham' => 14, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'SEE PART DETAILS - PRI; ALTERNATOR ASSY.', 'ma_phu_tung' => '31630-ZY3-013', 'so_luong' => 3, 'don_gia' => 30237000, 'thanh_tien' => 90711000],
                    ['id_san_pham' => 1, 'loai_san_pham' => 'phu_tung', 'ten_phu_tung' => 'HOLDER ASSY, BRUSH', 'ma_phu_tung' => '31105-PR7-A01', 'so_luong' => 1, 'don_gia' => 1789000, 'thanh_tien' => 1789000],
                ]
            ],
        ];

        foreach ($ordersData as $oData) {
            $items = $oData['items'];
            unset($oData['items']);

            $order = DonHang::updateOrCreate(['ma_don_hang' => $oData['ma_don_hang']], $oData);

            // Xóa chi tiết cũ nếu có và tạo lại
            ChiTietDonHang::where('id_don_hang', $order->id)->delete();
            foreach ($items as $item) {
                ChiTietDonHang::create([
                    'id_don_hang'   => $order->id,
                    'id_san_pham'   => $item['id_san_pham'],
                    'loai_san_pham' => $item['loai_san_pham'],
                    'so_luong'      => $item['so_luong'],
                    'don_gia'       => $item['don_gia'],
                    'thanh_tien'    => $item['thanh_tien'],
                    'ten_phu_tung'  => $item['ten_phu_tung'],
                    'ma_phu_tung'   => $item['ma_phu_tung'],
                    'ghi_chu'       => null,
                    'created_at'    => $order->created_at,
                    'updated_at'    => $order->updated_at,
                ]);
            }
        }

        echo "Seeded Sales & Orders (DonHangVaBaoGiaSeeder) successfully!\n";
    }
}
