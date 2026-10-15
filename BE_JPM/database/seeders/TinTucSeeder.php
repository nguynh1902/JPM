<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TinTucSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $imageUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';
        $tinTucs = [
            // BÀI VIẾT TIN TỨC CŨ
            [
                'title' => 'Cách bảo dưỡng động cơ Yamaha F150 hiệu quả nhất',
                'category' => 'Bảo Dưỡng Động Cơ',
                'thumbnail' => $imageUrl,
                'youtube_url' => 'https://www.youtube.com/watch?v=5CVzOqvqI38',
                'summary' => 'Hướng dẫn chi tiết cách bảo dưỡng và chăm sóc động cơ Yamaha F150 để kéo dài tuổi thọ.',
                'content' => '<p>Bảo dưỡng động cơ Yamaha F150 định kỳ giúp kéo dài tuổi thọ động cơ. Hãy thường xuyên thay nhớt, kiểm tra lọc gió và hệ thống làm mát...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Admin JPM',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'title' => 'Khuyến mãi 30% phụ tùng Suzuki tháng 8',
                'category' => 'Tin Tức Khuyến Mãi',
                'thumbnail' => $imageUrl,
                'summary' => 'Chương trình khuyến mãi lớn nhất trong năm dành cho các dòng phụ tùng chính hãng Suzuki.',
                'content' => '<p>Trong tháng 8 này, khi mua phụ tùng Suzuki chính hãng tại JPM, quý khách sẽ được giảm giá lên đến 30%...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Marketing',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            // 3 NEW ARTICLES ADDED VIA USER REQUEST
            [
                'title' => 'Cập nhật xu hướng thiết kế tàu du lịch năm 2026',
                'category' => 'Công Nghệ Hàng Hải',
                'thumbnail' => $imageUrl,
                'youtube_url' => 'https://www.youtube.com/watch?v=Y7f98aduVJ8',
                'summary' => 'Những xu hướng mới nhất về thiết kế nội ngoại thất cho tàu du lịch cỡ vừa.',
                'content' => '<p>Năm 2026 đánh dấu sự thay đổi lớn trong xu hướng thiết kế tàu du lịch với tiêu chí xanh và thông minh hơn...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Admin JPM',
                'created_at' => $now->copy()->subHours(12),
                'updated_at' => $now->copy()->subHours(12),
            ],
            [
                'title' => 'Lưu ý khi vận hành động cơ Diesel trên biển dài ngày',
                'category' => 'Bảo Dưỡng Động Cơ',
                'thumbnail' => $imageUrl,
                'summary' => 'Các mẹo giúp duy trì hoạt động ổn định của động cơ Diesel trong các chuyến đi dài.',
                'content' => '<p>Để động cơ Diesel hoạt động trơn tru trong thời gian dài trên biển, việc kiểm tra dầu nhờn và nhiệt độ nước làm mát là vô cùng quan trọng...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'title' => 'Ra mắt dòng phụ tùng chính hãng Honda Marine thế hệ mới',
                'category' => 'Công Nghệ Hàng Hải',
                'thumbnail' => $imageUrl,
                'summary' => 'Honda Marine vừa tung ra bộ phụ tùng thay thế với độ bền vượt trội và giá thành hợp lý.',
                'content' => '<p>Bộ phụ tùng mới từ Honda Marine sử dụng vật liệu chống ăn mòn thế hệ mới nhất...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Marketing',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'title' => 'Công nghệ hàng hải mới nhất áp dụng trên tàu cao tốc',
                'category' => 'Công Nghệ Hàng Hải',
                'thumbnail' => $imageUrl,
                'summary' => 'Tìm hiểu về các công nghệ hiện đại đang được áp dụng giúp tăng hiệu suất và an toàn cho tàu cao tốc.',
                'content' => '<p>Sự phát triển của công nghệ vật liệu và điện tử đã mang lại nhiều cải tiến đáng kể...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'title' => 'Lựa chọn động cơ thủy phù hợp cho tàu cá',
                'category' => 'Kiến thức Tàu Thuyền',
                'thumbnail' => $imageUrl,
                'summary' => 'Những lưu ý quan trọng khi chọn mua động cơ cho tàu đánh cá xa bờ.',
                'content' => '<p>Động cơ là trái tim của con tàu, vì thế việc lựa chọn động cơ phải dựa trên các yếu tố về công suất, mức tiêu hao nhiên liệu...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Admin JPM',
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(15),
            ],
            [
                'title' => 'JPM tổ chức sự kiện tri ân khách hàng 2026',
                'category' => 'Hoạt Động Công Ty',
                'thumbnail' => $imageUrl,
                'summary' => 'Sự kiện lớn thường niên nhằm tri ân những đối tác và khách hàng thân thiết.',
                'content' => '<p>Buổi tiệc được diễn ra tại trung tâm hội nghị với sự tham gia của hàng trăm khách mời...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Ban Giám Đốc',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(20),
            ],
            // BÀI VIẾT TIN TỨC BỔ SUNG
            [
                'title' => 'Những sai lầm thường gặp khi vệ sinh chân vịt tàu',
                'category' => 'Kiến thức Tàu Thuyền',
                'thumbnail' => $imageUrl,
                'summary' => 'Chân vịt (cánh quạt) là bộ phận dễ hư hỏng nếu không được vệ sinh đúng cách.',
                'content' => '<p>Việc xịt rửa nước áp lực quá mạnh hoặc dùng hóa chất sai loại có thể làm hỏng lớp sơn chống rỉ của chân vịt...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Admin JPM',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'title' => 'Lịch trình bảo dưỡng định kỳ cho động cơ Honda Marine',
                'category' => 'Bảo Dưỡng Động Cơ',
                'thumbnail' => $imageUrl,
                'summary' => 'Hướng dẫn chi tiết các cột mốc thời gian cần mang động cơ Honda đi bảo dưỡng.',
                'content' => '<p>Động cơ Honda Marine nổi tiếng bền bỉ nhưng bạn vẫn cần tuân thủ bảo dưỡng sau 20 giờ đầu tiên, và mỗi 100 giờ hoặc 1 năm sau đó...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'title' => 'Giới thiệu hệ thống Radar định vị Garmin thế hệ mới',
                'category' => 'Công Nghệ Hàng Hải',
                'thumbnail' => $imageUrl,
                'summary' => 'Công nghệ Radar tầm xa mới nhất giúp định vị chính xác và theo dõi thời tiết vượt trội.',
                'content' => '<p>Garmin vừa ra mắt dòng Radar mới với khả năng quét băng thông cực rộng, phát hiện tàu và chướng ngại vật ở cự ly xa hơn...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(100, 1000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(8),
            ],
            // BÀI VIẾT CHUYÊN SÂU & DỊCH VỤ
            [
                'title' => 'Hướng dẫn tra cứu sơ đồ rã máy và đặt phụ tùng OEM trên JPM',
                'category' => 'Kiến thức Tàu Thuyền',
                'thumbnail' => $imageUrl,
                'summary' => 'Các bước đơn giản để tra cứu đúng mã Part Number từ sơ đồ động cơ và đặt hàng chính hãng.',
                'content' => '<p>Bạn có thể sử dụng công cụ Tra Cứu nhanh trên trang chủ, nhập mã part number và thêm vào giỏ hàng. Sau đó điền thông tin nhận hàng...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'CSKH',
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
            [
                'title' => 'Chương trình hỗ trợ thanh toán & chiết khấu đơn hàng sỉ cho đội tàu',
                'category' => 'Tin Tức Khuyến Mãi',
                'thumbnail' => $imageUrl,
                'summary' => 'JPM triển khai gói ưu đãi chiết khấu và thanh toán linh hoạt dành riêng cho xưởng dịch vụ và chủ tàu.',
                'content' => '<p>Chúng tôi hiện chấp nhận chuyển khoản qua các ngân hàng lớn (Vietcombank, MB, Techcombank), thanh toán qua thẻ tín dụng và tiền mặt khi nhận hàng (COD đối với linh kiện nhỏ)...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'Marketing',
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(30),
            ],
            [
                'title' => 'Mạng lưới vận chuyển hỏa tốc thiết bị hàng hải tới các cảng biển',
                'category' => 'Hoạt Động Công Ty',
                'thumbnail' => $imageUrl,
                'summary' => 'Quy trình đóng gói tiêu chuẩn và giao nhận nhanh các mặt hàng động cơ, phụ tùng máy thủy toàn quốc.',
                'content' => '<p>Đối với phụ kiện nhỏ, thời gian giao hàng là 1-3 ngày. Đối với động cơ lớn, thời gian vận chuyển xe tải chuyên dụng có thể kéo dài 3-7 ngày tùy khu vực cảng biển...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'Admin JPM',
                'created_at' => $now->copy()->subDays(28),
                'updated_at' => $now->copy()->subDays(28),
            ],
            [
                'title' => 'Lưu ý bảo quản động cơ tránh vô nước biển và giữ trọn quyền lợi bảo hành',
                'category' => 'Bảo Dưỡng Động Cơ',
                'thumbnail' => $imageUrl,
                'summary' => 'Kinh nghiệm vận hành và lắp đặt đúng quy cách kỹ thuật cho các dòng máy thủy chính hãng.',
                'content' => '<p>Thiết bị được bảo hành miễn phí nếu lỗi do nhà sản xuất và còn trong thời hạn bảo hành. Lưu ý: Không bảo hành các trường hợp vô nước biển do lắp đặt sai quy cách...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(25),
            ],
            [
                'title' => 'Cách nhận biết phụ tùng máy thủy chính hãng và mã thay thế tương đương',
                'category' => 'Kiến thức Tàu Thuyền',
                'thumbnail' => $imageUrl,
                'summary' => 'Hướng dẫn kiểm tra tem mác OEM và sử dụng bảng quy đổi mã chéo (Cross-Reference) chuẩn xác.',
                'content' => '<p>Bước 1: Giữ nguyên tem mác. Bước 2: Chụp ảnh tình trạng lỗi gửi cho Hotline. Bước 3: Đóng gói gửi về trung tâm JPM gần nhất. Chúng tôi sẽ hoàn tiền trong 24h sau khi nhận được hàng...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(25),
            ],
            [
                'title' => 'Dịch vụ đội ngũ kỹ sư JPM chẩn đoán lỗi động cơ tận cảng cho tàu cá',
                'category' => 'Hoạt Động Công Ty',
                'thumbnail' => $imageUrl,
                'summary' => 'Dịch vụ cắm máy chẩn đoán chuyên dụng và bảo dưỡng động cơ trực tiếp tại bến cảng của JPM.',
                'content' => '<p>Đối với các lỗi lớn không thể tháo rời động cơ, JPM cung cấp dịch vụ cử kỹ thuật viên đến tận cảng để cắm máy tính chẩn đoán (chỉ áp dụng tại khu vực miền Nam và miền Trung)...</p>',
                'status' => 'PUBLISHED',
                'views' => rand(500, 2000),
                'author' => 'Kỹ Thuật',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(20),
            ]
        ];

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('tin_tucs')->truncate();
        DB::table('tin_tucs')->insert($tinTucs);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}



