<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\GiaoDichThanhToan;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Services\VietQRService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected VietQRService $vietQRService;

    public function __construct(VietQRService $vietQRService)
    {
        $this->vietQRService = $vietQRService;
    }

    /**
     * Lấy thông tin VietQR cho Đơn hàng
     */
    public function getDonHangQR($code, Request $request)
    {
        $donHang = DonHang::where('ma_don_hang', strtoupper(trim($code)))->first();
        if (!$donHang) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $type = $request->get('type', 'auto'); // auto, deposit, full
        $data = $this->vietQRService->generateDonHangQR($donHang, $type);

        return response()->json($data);
    }

    /**
     * Lấy thông tin VietQR cho Yêu cầu báo giá
     */
    public function getBaoGiaQR($code, Request $request)
    {
        $quote = YeuCauBaoGia::where('ma_yeu_cau', strtoupper(trim($code)))->first();
        if (!$quote) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy yêu cầu báo giá'], 404);
        }

        $data = $this->vietQRService->generateBaoGiaQR($quote);

        return response()->json($data);
    }

    /**
     * Polling kiểm tra trạng thái thanh toán thời gian thực khi đang quét QR
     */
    public function checkStatus($type, $code)
    {
        $code = strtoupper(trim($code));

        if ($type === 'order' || $type === 'don-hang') {
            $order = DonHang::where('ma_don_hang', $code)->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại'], 404);
            }

            $totalOrder = (float)($order->tong_tien + ($order->phi_van_chuyen ?? 0));
            $isPaid = ($order->da_thu >= $totalOrder && $totalOrder > 0);
            $isDepositPaid = ($order->da_thu >= $order->tien_coc_yeu_cau && $order->tien_coc_yeu_cau > 0);

            return response()->json([
                'success' => true,
                'type' => 'order',
                'order_code' => $order->ma_don_hang,
                'total_amount' => $totalOrder,
                'paid_amount' => (float)$order->da_thu,
                'deposit_required' => (float)$order->tien_coc_yeu_cau,
                'order_status' => $order->trang_thai,
                'payment_status' => $order->trang_thai_thanh_toan,
                'is_deposit_paid' => $isDepositPaid,
                'is_paid' => $isPaid,
            ]);
        }

        if ($type === 'quote' || $type === 'bao-gia') {
            $quote = YeuCauBaoGia::where('ma_yeu_cau', $code)->first();
            if (!$quote) {
                return response()->json(['success' => false, 'message' => 'Báo giá không tồn tại'], 404);
            }

            $isDepositPaid = ((int)$quote->trang_thai === 2 || ($quote->tien_coc_yeu_cau > 0 && ($quote->da_thu ?? 0) >= $quote->tien_coc_yeu_cau));

            return response()->json([
                'success' => true,
                'type' => 'quote',
                'quote_code' => $quote->ma_yeu_cau,
                'quote_status' => $quote->trang_thai,
                'is_deposit_paid' => $isDepositPaid,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Loại kiểm tra không hợp lệ'], 400);
    }

    /**
     * Webhook nhận dữ liệu thanh toán từ Ngân hàng / SePay / Casso
     */
    public function webhookReconciliation(Request $request)
    {
        $payload = $request->all();
        $result = $this->vietQRService->reconcileWebhook($payload);

        return response()->json($result);
    }

    /**
     * Admin: Xem danh sách giao dịch thanh toán ngân hàng
     */
    public function adminTransactions(Request $request)
    {
        $query = GiaoDichThanhToan::with(['donHang', 'yeuCauBaoGia'])->orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $kw = $request->search;
            $query->where(function($q) use ($kw) {
                $q->where('ma_tham_chieu', 'like', "%{$kw}%")
                  ->orWhere('ma_giao_dich', 'like', "%{$kw}%")
                  ->orWhere('noi_dung', 'like', "%{$kw}%");
            });
        }

        if ($request->has('gateway') && $request->gateway != '') {
            $query->where('gateway', $request->gateway);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('trang_thai', $request->status);
        }

        $perPage = (int)$request->get('per_page', 15);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Admin: Mô phỏng giao dịch chuyển khoản VietQR để kiểm thử
     */
    public function adminSimulatePayment(Request $request)
    {
        $data = $request->validate([
            'reference_code' => 'required|string', // Mã đơn hàng (DH...) hoặc báo giá (YCBG...)
            'amount' => 'required|numeric|min:1000',
            'content' => 'nullable|string',
        ]);

        $code = strtoupper(trim($data['reference_code']));
        $content = $data['content'] ?: ("JPM " . $code . " TEST CHUYEN TIEN");

        $fakePayload = [
            'gateway' => 'VietinBank (Sandbox Test)',
            'referenceNumber' => 'SIM_' . strtoupper(uniqid()),
            'accountNumber' => config('services.vietqr.account_no', '108876543210'),
            'amountIn' => (float)$data['amount'],
            'transactionContent' => $content,
            'transactionDate' => now()->toIso8601String(),
        ];

        $result = $this->vietQRService->reconcileWebhook($fakePayload);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi giao dịch mô phỏng đối soát thành công',
            'reconciliation_result' => $result,
        ]);
    }
}
