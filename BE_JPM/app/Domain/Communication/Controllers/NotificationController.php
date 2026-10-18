<?php

namespace App\Domain\Communication\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Communication\Models\LichSuThongBao;
use App\Domain\Communication\Services\NotificationService;
use App\Domain\Sales\Models\DonHang;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Admin: Lấy danh sách lịch sử tin nhắn ZNS & SMS
     */
    public function adminNotifications(Request $request)
    {
        $query = LichSuThongBao::orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $kw = $request->search;
            $query->where(function($q) use ($kw) {
                $q->where('so_dien_thoai', 'like', "%{$kw}%")
                  ->orWhere('ten_nguoi_nhan', 'like', "%{$kw}%")
                  ->orWhere('ma_tham_chieu', 'like', "%{$kw}%")
                  ->orWhere('noi_dung', 'like', "%{$kw}%");
            });
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('loai_tin', $request->type);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('trang_thai', $request->status);
        }

        $perPage = (int)$request->get('per_page', 15);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Admin: Gửi tin nhắn SMS / Zalo ZNS thủ công
     */
    public function sendCustom(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:zalo_zns,sms_brandname',
            'content' => 'required|string',
            'reference_code' => 'nullable|string',
        ]);

        $res = $this->notificationService->dispatchNotification([
            'loai_tin' => $data['type'],
            'so_dien_thoai' => $data['phone'],
            'ten_nguoi_nhan' => $data['name'] ?? 'Khách hàng',
            'noi_dung' => $data['content'],
            'ma_tham_chieu' => $data['reference_code'] ?? null,
            'mau_thong_bao' => 'tin_tuy_bien',
        ]);

        return response()->json($res);
    }

    /**
     * Admin: Gửi tin nhắn thông tin giao hàng Chành xe cho Khách hàng & Tài xế
     */
    public function sendOrderShippingNotice(Request $request, $orderId)
    {
        $order = DonHang::find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $shippingData = $request->validate([
            'ten_nha_xe' => 'required|string|max:255',
            'bien_so_xe' => 'nullable|string|max:50',
            'ten_tai_xe' => 'nullable|string|max:100',
            'sdt_tai_xe' => 'nullable|string|max:20',
            'so_kien_hang' => 'required|integer|min:1',
            'dia_diem_tra_hang' => 'nullable|string|max:255',
            'notify_customer' => 'boolean',
            'notify_driver' => 'boolean',
        ]);

        $results = [];

        // 1. Gửi thông báo cho Khách hàng / Chủ tàu
        if ($request->get('notify_customer', true)) {
            $results['customer'] = $this->notificationService->sendShippingInfoToCustomer($order, $shippingData);
        }

        // 2. Gửi thông báo cho Tài xế chành xe
        if ($request->get('notify_driver', false) && !empty($shippingData['sdt_tai_xe'])) {
            $results['driver'] = $this->notificationService->sendDispatchInfoToDriver($order, $shippingData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo chành xe thành công',
            'results' => $results,
        ]);
    }
}
