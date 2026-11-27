<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauHinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // 1. Trang chủ & Media
            [
                'khoa_cau_hinh' => 'homepage_youtube_url',
                'gia_tri'       => 'https://www.youtube.com/watch?v=5CVzOqvqI38',
                'mo_ta'         => 'Đường dẫn video YouTube chạy nền tại Hero Banner trang chủ',
                'loai_gia_tri'  => 'media',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'homepage_hero_title',
                'gia_tri'       => 'JPM MARINE',
                'mo_ta'         => 'Tiêu đề thương hiệu lớn hiển thị trên banner trang chủ',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'homepage_hero_subtitle',
                'gia_tri'       => 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN',
                'mo_ta'         => 'Slogan / Dòng chữ phụ trên banner trang chủ',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'store_banner_image',
                'gia_tri'       => '/client/image/shop_banner.png',
                'mo_ta'         => 'Đường dẫn ảnh nền banner trang Cửa hàng (Client Store)',
                'loai_gia_tri'  => 'media',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'store_banner_title',
                'gia_tri'       => 'Cửa Hàng Phụ Tùng',
                'mo_ta'         => 'Tiêu đề banner trang Cửa hàng',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'store_banner_subtitle',
                'gia_tri'       => 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.',
                'mo_ta'         => 'Mô tả phụ banner trang Cửa hàng',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],

            // 2. Thông tin liên hệ & Doanh nghiệp
            [
                'khoa_cau_hinh' => 'company_name',
                'gia_tri'       => 'JPM Marine',
                'mo_ta'         => 'Tên thương hiệu doanh nghiệp',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'company_address',
                'gia_tri'       => '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
                'mo_ta'         => 'Địa chỉ trụ sở / Cửa hàng hiển thị tại Footer và trang Hỗ trợ',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'company_hotline',
                'gia_tri'       => '0357 993 134',
                'mo_ta'         => 'Hotline hỗ trợ kỹ thuật và mua hàng',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'company_email',
                'gia_tri'       => 'tauthuyennhatbanjpvn@gmail.com',
                'mo_ta'         => 'Địa chỉ hòm thư điện tử liên hệ',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'business_hours',
                'gia_tri'       => 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
                'mo_ta'         => 'Thời gian mở cửa và hỗ trợ khách hàng',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],

            // 3. Mạng xã hội & Kênh kết nối
            [
                'khoa_cau_hinh' => 'social_facebook',
                'gia_tri'       => 'https://www.facebook.com/profile.php?id=61551777271332&mibextid=wwXIfr&rdid=9hcdbYOTV77YdCgO&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F18WWmNL1nX%2F%3Fmibextid%3DwwXIfr',
                'mo_ta'         => 'Liên kết Fanpage Facebook',
                'loai_gia_tri'  => 'url',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'social_youtube',
                'gia_tri'       => 'https://www.youtube.com/@linhkien-cano-tauthuynhatban?app=desktop',
                'mo_ta'         => 'Liên kết Kênh YouTube',
                'loai_gia_tri'  => 'url',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'social_zalo',
                'gia_tri'       => 'https://zalo.me/0357993134',
                'mo_ta'         => 'Liên kết tư vấn qua Zalo',
                'loai_gia_tri'  => 'url',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'social_instagram',
                'gia_tri'       => 'https://www.instagram.com/linhkiencano_tauthuynhatban?cnvx=MW82ODJuNnEwc3Z6NQ%3D%3D',
                'mo_ta'         => 'Liên kết Instagram',
                'loai_gia_tri'  => 'url',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'social_tiktok',
                'gia_tri'       => 'https://www.tiktok.com/@tauthuyennhatban',
                'mo_ta'         => 'Liên kết TikTok',
                'loai_gia_tri'  => 'url',
                'trang_thai'    => 1,
            ],

            // 4. Tài khoản ngân hàng & VietQR nhận cọc Napas 24/7
            [
                'khoa_cau_hinh' => 'vietqr_bank_id',
                'gia_tri'       => 'ICB',
                'mo_ta'         => 'Mã định danh ngân hàng Napas (ICB: VietinBank, VCB: Vietcombank, MB: MBBank...)',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'vietqr_bank_name',
                'gia_tri'       => 'VietinBank',
                'mo_ta'         => 'Tên viết tắt ngân hàng thụ hưởng',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'vietqr_account_no',
                'gia_tri'       => '108876543210',
                'mo_ta'         => 'Số tài khoản ngân hàng nhận tiền cọc',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'vietqr_account_name',
                'gia_tri'       => 'CONG TY PHU TUNG MAY THUY JPM',
                'mo_ta'         => 'Tên chủ tài khoản ngân hàng (in hoa không dấu)',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'vietqr_template',
                'gia_tri'       => 'compact2',
                'mo_ta'         => 'Kiểu hiển thị mã QR VietQR (compact, compact2, qr_only)',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'vietqr_prefix',
                'gia_tri'       => 'COC',
                'mo_ta'         => 'Tiền tố cú pháp nội dung chuyển khoản cọc (VD: COC, JPM COC...)',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],

            // 5. Chân trang & Pháp lý
            [
                'khoa_cau_hinh' => 'footer_about_text',
                'gia_tri'       => 'Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.',
                'mo_ta'         => 'Đoạn giới thiệu ngắn về sứ mệnh JPM Marine ở chân trang',
                'loai_gia_tri'  => 'textarea',
                'trang_thai'    => 1,
            ],
            [
                'khoa_cau_hinh' => 'copyright_text',
                'gia_tri'       => '© 2026 JPM Marine. Bảo lưu mọi quyền.',
                'mo_ta'         => 'Dòng thông tin bản quyền đáy chân trang',
                'loai_gia_tri'  => 'text',
                'trang_thai'    => 1,
            ],
        ];

        foreach ($configs as $config) {
            DB::table('cau_hinhs')->updateOrInsert(
                ['khoa_cau_hinh' => $config['khoa_cau_hinh']],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
