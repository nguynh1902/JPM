<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('faqs')->truncate();
        DB::table('faqs')->insert([
            // Mua hàng & Thanh toán
            [
                'question' => 'Tôi có thể đặt mua phụ tùng trực tuyến không?',
                'answer' => 'Có, bạn hoàn toàn có thể tìm kiếm mã phụ tùng (part number) trên website JPM và thêm vào giỏ hàng để đặt mua trực tuyến 24/7.',
                'category' => 'Mua hàng & Thanh toán',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'JPM chấp nhận những hình thức thanh toán nào?',
                'answer' => 'Chúng tôi hỗ trợ chuyển khoản ngân hàng (Vietcombank, MB Bank, Techcombank), thẻ tín dụng quốc tế và thanh toán tiền mặt khi nhận hàng (COD) cho các đơn hàng dưới 20 triệu đồng.',
                'category' => 'Mua hàng & Thanh toán',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Tôi có thể xuất hóa đơn VAT cho công ty không?',
                'answer' => 'Chắc chắn rồi. Tại bước thanh toán, vui lòng chọn "Yêu cầu xuất hóa đơn công ty" và điền đầy đủ Mã số thuế cùng Tên công ty. Hóa đơn điện tử sẽ được gửi qua email sau khi giao hàng thành công.',
                'category' => 'Mua hàng & Thanh toán',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Vận chuyển & Giao nhận
            [
                'question' => 'JPM có giao hàng toàn quốc không?',
                'answer' => 'Có, JPM hỗ trợ giao hàng trên toàn quốc. Đối với các khu vực biển đảo, chúng tôi sẽ phối hợp với các chành xe và tàu vận tải trung chuyển.',
                'category' => 'Vận chuyển & Giao nhận',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Thời gian giao hàng mất bao lâu?',
                'answer' => 'Phụ tùng và linh kiện nhỏ mất từ 1-3 ngày làm việc. Đối với động cơ lớn nguyên kiện (Outboard), thời gian vận chuyển xe cẩu chuyên dụng từ 3-7 ngày tùy khoảng cách.',
                'category' => 'Vận chuyển & Giao nhận',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Chi phí vận chuyển động cơ lớn được tính như thế nào?',
                'answer' => 'Phí vận chuyển sẽ được tự động tính toán dựa trên khối lượng (kg) hoặc kích thước quy đổi (cbm) của kiện hàng và khoảng cách từ kho JPM đến địa chỉ của bạn.',
                'category' => 'Vận chuyển & Giao nhận',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Chính sách Bảo hành
            [
                'question' => 'Sản phẩm mua tại JPM được bảo hành bao lâu?',
                'answer' => 'Thời gian bảo hành phụ thuộc vào nhà sản xuất. Động cơ outboard Yamaha/Honda thường được bảo hành 1 năm hoặc 1000 giờ chạy. Phụ tùng thay thế được bảo hành 3-6 tháng.',
                'category' => 'Chính sách Bảo hành',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Trường hợp nào bị từ chối bảo hành?',
                'answer' => 'JPM từ chối bảo hành các lỗi do sử dụng sai hướng dẫn, va đập vật lý, sử dụng sai loại dầu nhớt, hoặc động cơ bị ngâm nước biển do lắp đặt sai kỹ thuật.',
                'category' => 'Chính sách Bảo hành',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Đổi trả & Hoàn tiền
            [
                'question' => 'Làm sao để đổi trả phụ tùng nếu đặt nhầm mã?',
                'answer' => 'Vui lòng liên hệ CSKH trong vòng 7 ngày kể từ khi nhận hàng. Phụ tùng đổi trả phải còn nguyên tem, bao bì chưa bóc và chưa qua sử dụng hoặc dính dầu mỡ.',
                'category' => 'Đổi trả & Hoàn tiền',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Tôi sẽ nhận được tiền hoàn lại trong bao lâu?',
                'answer' => 'Sau khi JPM nhận và kiểm định hàng trả về đạt yêu cầu, tiền hoàn sẽ được chuyển khoản lại vào tài khoản của bạn trong vòng 24 đến 48 giờ làm việc.',
                'category' => 'Đổi trả & Hoàn tiền',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hỗ trợ Kỹ thuật
            [
                'question' => 'JPM có hỗ trợ lắp ráp động cơ lên tàu không?',
                'answer' => 'Có. Với các đơn hàng động cơ công suất lớn, chúng tôi có đội ngũ kỹ thuật viên đến tận xưởng đóng tàu hoặc cảng để trực tiếp lắp đặt và chạy thử nghiệm (Sea trial).',
                'category' => 'Hỗ trợ Kỹ thuật',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Động cơ báo lỗi trên màn hình, tôi phải làm sao?',
                'answer' => 'Quý khách vui lòng chụp lại mã lỗi hiển thị trên màn hình điều khiển (hoặc đèn chớp trên động cơ) và gửi cho hotline Kỹ Thuật của JPM. Chúng tôi sẽ chẩn đoán từ xa hoặc cử thợ cắm máy tính kiểm tra.',
                'category' => 'Hỗ trợ Kỹ thuật',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
