<?php

namespace App\Domain\Communication\Services;

use App\Domain\Communication\Jobs\DispatchNotificationJob;
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
        $approveUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/') . "/bao-gia/{$quote->token}";

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
        $paymentStatusMap = [
            0 => 'Chưa thanh toán',
            1 => 'Đã đặt cọc',
            2 => 'Đã thanh toán đủ',
        ];
        $statusText = $paymentStatusMap[(int)($order->trang_thai_thanh_toan ?? 1)] ?? 'Đã đặt cọc';

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
        $shippingFee = $order->khach_tu_tra_cuoc ? 0 : (float)($order->phi_van_chuyen ?? 0);
        $tienThuHo = max(0, (float)$order->tong_tien + $shippingFee - (float)($order->da_thu ?? 0));
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
     * 5. Thông báo nội bộ cho Admin: Khách hàng vừa báo đã chuyển khoản cọc
     */
    public function notifyAdminDepositReported(
        string $code,
        string $customerName,
        string $phone,
        float $amount,
        bool $hasReceipt = false
    ): LichSuThongBao {
        $amountFormatted = number_format($amount, 0, ',', '.') . ' đ';
        $receiptNote = $hasReceipt ? ' (có đính kèm ảnh biên lai)' : ' (không đính kèm ảnh)';
        $content = "Khách {$customerName} ({$phone}) vừa báo đã chuyển khoản cọc {$amountFormatted} cho mã #{$code}{$receiptNote}. Vui lòng kiểm tra tài khoản ngân hàng và xác nhận.";

        Log::info("[DEPOSIT REPORT] {$content}");

        return LichSuThongBao::create([
            'loai_tin' => 'system_alert',
            'kenh_gui' => 'Admin Portal',
            'nguoi_nhan_loai' => 'admin',
            'so_dien_thoai' => $phone ?: 'N/A',
            'ten_nguoi_nhan' => $customerName ?: 'Khách hàng',
            'mau_thong_bao' => 'khach_bao_coc',
            'tieu_de' => "Khách báo đã chuyển cọc #{$code}",
            'noi_dung' => $content,
            'ma_tham_chieu' => $code,
            'trang_thai' => 'da_ghi_nhan',
            'phan_hoi_gateway' => [
                'has_receipt' => $hasReceipt,
                'amount' => $amount,
                'reported_at' => now()->toIso8601String(),
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
            $kenhGui = $isZalo ? 'Zalo ZNS Official' : config('services.sms_brandname.provider', 'SpeedSMS');
            $status = 'dang_gui';
            $gatewayResponse = ['mode' => 'queued', 'queued_at' => now()->toIso8601String()];

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

            DispatchNotificationJob::dispatch($log->id, $data);

            return [
                'success' => true,
                'status' => $status,
                'log_id' => $log->id,
                'channel' => $kenhGui,
                'recipient' => $soDienThoai,
                'message' => $noiDung,
            ];
        }

        // Chế độ mô phỏng / Mock an toàn cho môi trường Dev & Test
        $kenhGui = $isZalo ? 'Mock Zalo ZNS' : 'Mock SMS Brandname';
        $status = 'mo_phong';
        $gatewayResponse = [
            'mode' => 'sandbox_simulation',
            'message' => 'Tin nhắn đã được mô phỏng gửi thành công chuẩn cú pháp viễn thông.',
            'timestamp' => now()->toIso8601String(),
        ];
        Log::info("[NOTIFICATION {$loaiTin}] Sent to {$soDienThoai}: {$noiDung}");

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
