<?php

namespace App\Domain\Sales\Services;

use App\Domain\Communication\Services\NotificationService;
use App\Domain\Core\Models\CauHinh;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\GiaoDichThanhToan;
use App\Domain\Sales\Models\YeuCauBaoGia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VietQRService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Lấy cấu hình tài khoản ngân hàng (Ưu tiên đọc từ CSDL Cấu Hình Hệ Thống, fallback về .env)
     */
    public function getBankConfig(): array
    {
        try {
            $dbConfigs = CauHinh::whereIn('khoa_cau_hinh', [
                'vietqr_bank_id',
                'vietqr_bank_name',
                'vietqr_account_no',
                'vietqr_account_name',
                'vietqr_template',
                'vietqr_prefix',
            ])->pluck('gia_tri', 'khoa_cau_hinh')->toArray();
        } catch (\Throwable $e) {
            $dbConfigs = [];
        }

        return [
            'bank_id'      => !empty($dbConfigs['vietqr_bank_id']) ? $dbConfigs['vietqr_bank_id'] : config('services.vietqr.bank_id', 'ICB'),
            'bank_name'    => !empty($dbConfigs['vietqr_bank_name']) ? $dbConfigs['vietqr_bank_name'] : config('services.vietqr.bank_name', 'VietinBank'),
            'account_no'   => !empty($dbConfigs['vietqr_account_no']) ? $dbConfigs['vietqr_account_no'] : config('services.vietqr.account_no', '108876543210'),
            'account_name' => !empty($dbConfigs['vietqr_account_name']) ? $dbConfigs['vietqr_account_name'] : config('services.vietqr.account_name', 'CONG TY PHU TUNG MAY THUY JPM'),
            'template'     => !empty($dbConfigs['vietqr_template']) ? $dbConfigs['vietqr_template'] : config('services.vietqr.template', 'compact2'),
            'prefix'       => !empty($dbConfigs['vietqr_prefix']) ? trim($dbConfigs['vietqr_prefix']) : 'COC',
        ];
    }

    /**
     * Sinh thông tin mã VietQR cho Đơn hàng
     */
    public function generateDonHangQR(DonHang $donHang, string $type = 'auto'): array
    {
        $bankCfg = $this->getBankConfig();
        $bankId = $bankCfg['bank_id'];
        $bankName = $bankCfg['bank_name'];
        $accountNo = $bankCfg['account_no'];
        $accountName = $bankCfg['account_name'];
        $template = $bankCfg['template'];

        $totalOrder = (float)($donHang->tong_tien + ($donHang->phi_van_chuyen ?? 0));
        $daThu = (float)($donHang->da_thu ?? 0);
        $tienCoc = (float)($donHang->tien_coc_yeu_cau ?? 0);

        $isDeposit = false;
        $prefix = !empty($bankCfg['prefix']) ? trim($bankCfg['prefix']) : 'COC';
        if ($type === 'deposit' || ($type === 'auto' && $tienCoc > 0 && $daThu < $tienCoc)) {
            $amount = max(0, $tienCoc - $daThu);
            $isDeposit = true;
            $transferContent = "{$prefix} " . $donHang->ma_don_hang;
        } else {
            $amount = max(0, $totalOrder - $daThu);
            $transferContent = "{$prefix} " . $donHang->ma_don_hang;
        }

        $qrUrl = sprintf(
            "https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s",
            $bankId,
            $accountNo,
            $template,
            (int)$amount,
            urlencode($transferContent),
            urlencode($accountName)
        );

        $isPaid = ($daThu >= $totalOrder && $totalOrder > 0);
        $isDepositPaid = ($daThu >= $tienCoc && $tienCoc > 0);

        return [
            'success' => true,
            'qr_url' => $qrUrl,
            'bank_id' => $bankId,
            'bank_name' => $bankName,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'amount' => $amount,
            'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
            'transfer_content' => $transferContent,
            'is_deposit' => $isDeposit,
            'is_deposit_paid' => $isDepositPaid,
            'is_paid' => $isPaid,
            'order_code' => $donHang->ma_don_hang,
            'total_order' => $totalOrder,
            'deposit_required' => $tienCoc,
            'paid_amount' => $daThu,
            'remaining_amount' => max(0, $totalOrder - $daThu),
            'order_status' => $donHang->trang_thai,
            'payment_status' => $donHang->trang_thai_thanh_toan,
        ];
    }

    /**
     * Sinh thông tin mã VietQR tiền cọc cho Yêu cầu báo giá
     */
    public function generateBaoGiaQR(YeuCauBaoGia $quote): array
    {
        $bankCfg = $this->getBankConfig();
        $bankId = $bankCfg['bank_id'];
        $bankName = $bankCfg['bank_name'];
        $accountNo = $bankCfg['account_no'];
        $accountName = $bankCfg['account_name'];
        $template = $bankCfg['template'];

        $tienCoc = (float)($quote->tien_coc_yeu_cau ?? 0);
        $tongTien = (float)($quote->tong_tien ?? 0);
        $amount = $tienCoc > 0 ? $tienCoc : $tongTien;

        $prefix = !empty($bankCfg['prefix']) ? trim($bankCfg['prefix']) : 'COC';
        $transferContent = "{$prefix} " . $quote->ma_yeu_cau;

        $qrUrl = sprintf(
            "https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s",
            $bankId,
            $accountNo,
            $template,
            (int)$amount,
            urlencode($transferContent),
            urlencode($accountName)
        );

        $isDepositPaid = ((int)$quote->trang_thai === 2 || ($quote->tien_coc_yeu_cau > 0 && ($quote->da_thu ?? 0) >= $quote->tien_coc_yeu_cau));

        return [
            'success' => true,
            'qr_url' => $qrUrl,
            'bank_id' => $bankId,
            'bank_name' => $bankName,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'amount' => $amount,
            'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
            'transfer_content' => $transferContent,
            'quote_code' => $quote->ma_yeu_cau,
            'total_amount' => $tongTien,
            'deposit_required' => $tienCoc,
            'is_deposit_paid' => $isDepositPaid,
            'quote_status' => $quote->trang_thai,
        ];
    }

    /**
     * Webhook tự động đối soát tiền chuyển khoản
     */
    public function reconcileWebhook(array $payload): array
    {
        $amountIn = (float)($payload['amountIn'] ?? $payload['transferAmount'] ?? $payload['amount'] ?? 0);
        $content = (string)($payload['transactionContent'] ?? $payload['content'] ?? $payload['description'] ?? '');
        $refNumber = (string)($payload['referenceNumber'] ?? $payload['id'] ?? ('REF_' . time()));
        $accountNo = (string)($payload['accountNumber'] ?? $payload['account_no'] ?? '');
        $gateway = (string)($payload['gateway'] ?? 'VietQR Bank');
        $transactionDate = isset($payload['transactionDate']) ? date('Y-m-d H:i:s', strtotime($payload['transactionDate'])) : now();

        Log::info("[VIETQR RECONCILIATION] Received payload: Ref={$refNumber}, Amount={$amountIn}, Content='{$content}'");

        if ($amountIn <= 0) {
            return ['success' => false, 'message' => 'Số tiền giao dịch không hợp lệ'];
        }

        // Kiểm tra xem giao dịch ngân hàng này đã được ghi nhận trước đó chưa (Chống duplicate Webhook)
        $existingTx = GiaoDichThanhToan::where('ma_giao_dich', $refNumber)->first();
        if ($existingTx) {
            return [
                'success' => true,
                'message' => 'Giao dịch đã được đối soát trước đó',
                'transaction_id' => $existingTx->id,
            ];
        }

        // Bóc tách mã Đơn hàng (DH...) hoặc Mã Yêu cầu báo giá (YCBG...) từ nội dung chuyển khoản
        $matchedOrderCode = null;
        $matchedQuoteCode = null;

        // Pattern 1: Đơn hàng (VD: DH20261018001, DH-12345, JPM DH...)
        if (preg_match('/DH[0-9A-Z_-]+/i', $content, $m)) {
            $matchedOrderCode = strtoupper($m[0]);
        }

        // Pattern 2: Báo giá (VD: YCBG20261018001, COC YCBG...)
        if (preg_match('/YCBG[0-9A-Z_-]+/i', $content, $m2)) {
            $matchedQuoteCode = strtoupper($m2[0]);
        }

        return DB::transaction(function () use (
            $amountIn, $content, $refNumber, $accountNo, $gateway, $transactionDate, $matchedOrderCode, $matchedQuoteCode, $payload
        ) {
            // 1. Trường hợp khớp Đơn hàng (DonHang)
            if ($matchedOrderCode) {
                $order = DonHang::where('ma_don_hang', $matchedOrderCode)->first();
                if ($order) {
                    $order->da_thu = (float)($order->da_thu ?? 0) + $amountIn;
                    $totalRequired = (float)($order->tong_tien + ($order->phi_van_chuyen ?? 0));
                    $depositRequired = (float)($order->tien_coc_yeu_cau ?? 0);

                    if ($order->da_thu >= $totalRequired) {
                        $order->trang_thai_thanh_toan = 1; // Đã thanh toán đủ
                        if ((int)$order->trang_thai === 0) {
                            $order->trang_thai = 1; // Đang xử lý
                        }
                    } elseif (($depositRequired > 0 && $order->da_thu >= $depositRequired) || ($order->da_thu > 0 && $order->da_thu < $totalRequired)) {
                        $order->trang_thai_thanh_toan = 2; // Đã đặt cọc
                        if ((int)$order->trang_thai === 0) {
                            $order->trang_thai = 1; // Đang xử lý
                        }
                    }
                    $order->save();

                    // Ghi log giao dịch
                    $tx = GiaoDichThanhToan::create([
                        'gateway' => $gateway,
                        'ma_giao_dich' => $refNumber,
                        'so_tai_khoan' => $accountNo,
                        'so_tien' => $amountIn,
                        'noi_dung' => $content,
                        'loai_doi_tuong' => 'don_hang',
                        'id_doi_tuong' => $order->id,
                        'ma_tham_chieu' => $order->ma_don_hang,
                        'trang_thai' => 'Thành công',
                        'du_lieu_raw' => $payload,
                        'ngay_giao_dich' => $transactionDate,
                    ]);

                    // Tự động kích hoạt gửi SMS / Zalo ZNS xác nhận cho khách hàng
                    $this->notificationService->sendDepositConfirmedToCustomer($order, $amountIn);

                    return [
                        'success' => true,
                        'matched' => 'don_hang',
                        'order_code' => $order->ma_don_hang,
                        'paid_added' => $amountIn,
                        'total_paid' => $order->da_thu,
                        'payment_status' => $order->trang_thai_thanh_toan,
                        'order_status' => $order->trang_thai,
                        'transaction_id' => $tx->id,
                    ];
                }
            }

            // 2. Trường hợp khớp Yêu cầu báo giá (YeuCauBaoGia)
            if ($matchedQuoteCode) {
                $quote = YeuCauBaoGia::where('ma_yeu_cau', $matchedQuoteCode)->first();
                if ($quote) {
                    $quote->trang_thai = 2; // Đã chốt / Đã cọc
                    $quote->save();

                    $tx = GiaoDichThanhToan::create([
                        'gateway' => $gateway,
                        'ma_giao_dich' => $refNumber,
                        'so_tai_khoan' => $accountNo,
                        'so_tien' => $amountIn,
                        'noi_dung' => $content,
                        'loai_doi_tuong' => 'yeu_cau_bao_gia',
                        'id_doi_tuong' => $quote->id,
                        'ma_tham_chieu' => $quote->ma_yeu_cau,
                        'trang_thai' => 'Thành công',
                        'du_lieu_raw' => $payload,
                        'ngay_giao_dich' => $transactionDate,
                    ]);

                    return [
                        'success' => true,
                        'matched' => 'yeu_cau_bao_gia',
                        'quote_code' => $quote->ma_yeu_cau,
                        'quote_status' => $quote->trang_thai,
                        'transaction_id' => $tx->id,
                    ];
                }
            }

            // 3. Trường hợp không tìm thấy đơn khớp (Lưu lại để kế toán đối soát thủ công)
            $unmatchedTx = GiaoDichThanhToan::create([
                'gateway' => $gateway,
                'ma_giao_dich' => $refNumber,
                'so_tai_khoan' => $accountNo,
                'so_tien' => $amountIn,
                'noi_dung' => $content,
                'loai_doi_tuong' => 'khac',
                'id_doi_tuong' => null,
                'ma_tham_chieu' => $matchedOrderCode ?: ($matchedQuoteCode ?: 'UNKNOWN'),
                'trang_thai' => 'Không khớp',
                'du_lieu_raw' => $payload,
                'ngay_giao_dich' => $transactionDate,
            ]);

            return [
                'success' => false,
                'matched' => 'none',
                'message' => 'Không tìm thấy đơn hàng hoặc báo giá khớp với nội dung chuyển khoản',
                'transaction_id' => $unmatchedTx->id,
            ];
        });
    }
}
