<?php

namespace App\Domain\Communication\Services;

use App\Domain\Communication\Models\LichSuThongBao;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * 1. Thông báo cho Khách hàng / Chủ tàu: Báo giá phụ tùng đã sẵn sàng duyệt
     */
    public function sendQuoteReadyToCustomer(YeuCauBaoGia $quote): array
    {
        $phone = $quote->khach_hang_sdt;
        $name = $quote->khach_hang_ten ?? 'Quý khách';
        $code = $quote->ma_yeu_cau;
        $total = number_format($quote->tong_tien ?? 0, 0, ',', '.') . ' đ';
        $deposit = number_format($quote->tien_coc_yeu_cau ?? 0, 0, ',', '.') . ' đ';
        $approveUrl = url("/tra-cuu-bao-gia?token={$quote->token}");

        $content = "JPM MARINE: Kính gửi {$name}, báo giá #{$code} phụ tùng máy thủy của quý khách đã hoàn tất. Tổng tiền: {$total}, tiền cọc yêu cầu: {$deposit}. Vui lòng xem và duyệt tại: {$approveUrl}";

        return $this->dispatchNotification([
            'loai_tin' => 'zalo_zns',
            'nguoi_nhan_loai' => 'khach_hang',
            'so_dien_thoai' => $phone,
            'ten_nguoi_nhan' => $name,
            'mau_thong_bao' => 'bao_gia_moi',
            'tieu_de' => "Báo giá phụ tùng JPM #{$code} đã sẵn sàng",
            'noi_dung' => $content,
            'ma_tham_chieu' => $code,
            'template_data' => [
                'customer_name' => $name,
                'quote_code' => $code,
                'total_amount' => $total,
                'deposit_amount' => $deposit,
                'url' => $approveUrl,
            ],
        ]);
    }

    /**
     * 2. Thông báo cho Khách hàng: Đã nhận tiền cọc hoặc thanh toán thành công
     */
    public function sendDepositConfirmedToCustomer(DonHang $order, float $amount): array
    {
        $phone = $order->khach_hang_sdt;
        $name = $order->khach_hang_ten ?? 'Quý khách';
        $code = $order->ma_don_hang;
        $amountFormatted = number_format($amount, 0, ',', '.') . ' đ';
        $statusText = $order->trang_thai_thanh_toan ?? 'Đã cọc';

        $content = "JPM MARINE: Đã nhận thành công {$amountFormatted} cho đơn hàng #{$code}. Trạng thái: {$statusText}. Kỹ thuật viên JPM đang chuẩn bị kiện hàng xuất kho.";

        return $this->dispatchNotification([
            'loai_tin' => 'sms_brandname',
            'nguoi_nhan_loai' => 'khach_hang',
            'so_dien_thoai' => $phone,
            'ten_nguoi_nhan' => $name,
            'mau_thong_bao' => 'xac_nhan_coc',
            'tieu_de' => "Xác nhận thanh toán đơn #{$code}",
            'noi_dung' => $content,
            'ma_tham_chieu' => $code,
            'template_data' => [
                'customer_name' => $name,
                'order_code' => $code,
                'paid_amount' => $amountFormatted,
                'payment_status' => $statusText,
            ],
        ]);
    }

    /**
     * 3. Thông báo cho Khách hàng: Đơn hàng phụ tùng đã giao cho Chành xe
     */
    public function sendShippingInfoToCustomer(DonHang $order, array $shippingDetails): array
    {
        $phone = $order->khach_hang_sdt;
        $name = $order->khach_hang_ten ?? 'Quý khách';
        $code = $order->ma_don_hang;

        $nhaXe = $shippingDetails['ten_nha_xe'] ?? 'Chành xe';
        $bienSo = $shippingDetails['bien_so_xe'] ?? 'Chưa rõ';
        $sdtTaiXe = $shippingDetails['sdt_tai_xe'] ?? '';
        $soKien = $shippingDetails['so_kien_hang'] ?? 1;
        $diaDiemNhan = $shippingDetails['dia_diem_tra_hang'] ?? ($order->dia_chi_text ?? 'Bến xe/Cảng');

        $content = "JPM MARINE: Đơn hàng #{$code} ({$soKien} kiện phụ tùng) đã được giao cho chành xe {$nhaXe} (BS: {$bienSo}" . ($sdtTaiXe ? ", SĐT: {$sdtTaiXe}" : "") . "). Nơi nhận: {$diaDiemNhan}.";

        return $this->dispatchNotification([
            'loai_tin' => 'zalo_zns',
            'nguoi_nhan_loai' => 'khach_hang',
            'so_dien_thoai' => $phone,
            'ten_nguoi_nhan' => $name,
            'mau_thong_bao' => 'giao_chanh_xe',
            'tieu_de' => "Thông tin chành xe giao hàng đơn #{$code}",
            'noi_dung' => $content,
            'ma_tham_chieu' => $code,
            'template_data' => [
                'customer_name' => $name,
                'order_code' => $code,
                'carrier_name' => $nhaXe,
                'license_plate' => $bienSo,
                'driver_phone' => $sdtTaiXe,
                'package_count' => $soKien,
                'dropoff_location' => $diaDiemNhan,
            ],
        ]);
    }

    /**
     * 4. Thông báo cho Tài xế / Nhà xe chành: Thông tin người nhận tại bến/cảng
     */
    public function sendDispatchInfoToDriver(DonHang $order, array $shippingDetails): array
    {
        $phoneDriver = $shippingDetails['sdt_tai_xe'] ?? null;
        if (!$phoneDriver) {
            return ['success' => false, 'message' => 'Không có số điện thoại tài xế'];
        }

        $code = $order->ma_don_hang;
        $customerName = $order->khach_hang_ten;
        $customerPhone = $order->khach_hang_sdt;
        $soKien = $shippingDetails['so_kien_hang'] ?? 1;
        $tienThuHo = ($order->tong_tien + $order->phi_van_chuyen - $order->da_thu);
        $codFormatted = $tienThuHo > 0 ? (number_format($tienThuHo, 0, ',', '.') . ' đ') : '0 đ (Đã thanh toán đủ)';

        $content = "JPM MARINE gửi xe: Kiện hàng #{$code} ({$soKien} kiện linh kiện máy thủy). Người nhận: {$customerName} - SĐT: {$customerPhone}. Tiền thu hộ COD: {$codFormatted}.";

        return $this->dispatchNotification([
            'loai_tin' => 'sms_brandname',
            'nguoi_nhan_loai' => 'tai_xe_chanh',
            'so_dien_thoai' => $phoneDriver,
            'ten_nguoi_nhan' => $shippingDetails['ten_tai_xe'] ?? 'Tài xế',
            'mau_thong_bao' => 'thong_tin_nha_xe',
            'tieu_de' => "Thông tin người nhận kiện hàng #{$code}",
            'noi_dung' => $content,
            'ma_tham_chieu' => $code,
            'template_data' => [
                'order_code' => $code,
                'receiver_name' => $customerName,
                'receiver_phone' => $customerPhone,
                'package_count' => $soKien,
                'cod_amount' => $codFormatted,
            ],
        ]);
    }

    /**
     * Xử lý điều phối và ghi log thông báo
     */
    public function dispatchNotification(array $data): array
    {
        $loaiTin = $data['loai_tin'] ?? 'sms_brandname';
        $soDienThoai = $data['so_dien_thoai'] ?? '';
        $noiDung = $data['noi_dung'] ?? '';

        if (empty($soDienThoai)) {
            return ['success' => false, 'message' => 'Số điện thoại không hợp lệ'];
        }

        $gatewayResponse = null;
        $status = 'mo_phong';
        $kenhGui = 'Mock/Log';

        $isZalo = ($loaiTin === 'zalo_zns');
        $apiKey = $isZalo ? config('services.zalo_zns.secret_key') : config('services.sms_brandname.api_key');
        $sandbox = $isZalo ? config('services.zalo_zns.sandbox', true) : config('services.sms_brandname.sandbox', true);

        if (!empty($apiKey) && !$sandbox) {
            try {
                if ($isZalo) {
                    $kenhGui = 'Zalo ZNS Official';
                    // Gọi Zalo Cloud API ZNS thực tế
                    $response = Http::withHeaders([
                        'access_token' => $apiKey,
                    ])->timeout(8)->post('https://business.openapi.zalo.me/message/template', [
                        'phone' => preg_replace('/^0/', '84', $soDienThoai),
                        'template_id' => $data['template_id'] ?? config('services.zalo_zns.template_quote_ready'),
                        'template_data' => $data['template_data'] ?? [],
                    ]);
                    $gatewayResponse = $response->json();
                    $status = ($response->successful() && ($gatewayResponse['error'] ?? 0) === 0) ? 'thanh_cong' : 'that_bai';
                } else {
                    $kenhGui = config('services.sms_brandname.provider', 'SpeedSMS');
                    // Gọi SMS Brandname API
                    $response = Http::withBasicAuth($apiKey, 'x')
                        ->timeout(8)
                        ->post('https://api.speedsms.vn/index.php/sms/send', [
                            'to' => [$soDienThoai],
                            'content' => $noiDung,
                            'sms_type' => 2, // Brandname CSKH
                            'sender' => config('services.sms_brandname.brandname', 'JPM_MARINE'),
                        ]);
                    $gatewayResponse = $response->json();
                    $status = ($response->successful() && ($gatewayResponse['status'] ?? '') === 'success') ? 'thanh_cong' : 'that_bai';
                }
            } catch (\Exception $e) {
                $status = 'that_bai';
                $gatewayResponse = ['error' => $e->getMessage()];
                Log::error("Notification Gateway Error: " . $e->getMessage());
            }
        } else {
            // Chế độ mô phỏng / Mock an toàn cho môi trường Dev & Test
            $kenhGui = $isZalo ? 'Mock Zalo ZNS' : 'Mock SMS Brandname';
            $status = 'mo_phong';
            $gatewayResponse = [
                'mode' => 'sandbox_simulation',
                'message' => 'Tin nhắn đã được mô phỏng gửi thành công chuẩn cú pháp viễn thông.',
                'timestamp' => now()->toIso8601String(),
            ];
            Log::info("[NOTIFICATION {$loaiTin}] Sent to {$soDienThoai}: {$noiDung}");
        }

        // Ghi nhật ký vào database
        $log = LichSuThongBao::create([
            'loai_tin' => $loaiTin,
            'kenh_gui' => $kenhGui,
            'nguoi_nhan_loai' => $data['nguoi_nhan_loai'] ?? 'khach_hang',
            'so_dien_thoai' => $soDienThoai,
            'ten_nguoi_nhan' => $data['ten_nguoi_nhan'] ?? null,
            'mau_thong_bao' => $data['mau_thong_bao'] ?? null,
            'tieu_de' => $data['tieu_de'] ?? null,
            'noi_dung' => $noiDung,
            'ma_tham_chieu' => $data['ma_tham_chieu'] ?? null,
            'trang_thai' => $status,
            'phan_hoi_gateway' => $gatewayResponse,
        ]);

        return [
            'success' => true,
            'status' => $status,
            'log_id' => $log->id,
            'channel' => $kenhGui,
            'recipient' => $soDienThoai,
            'message' => $noiDung,
        ];
    }
}
