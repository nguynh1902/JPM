<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Sales\Services\OrderService;

class DonHangController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_khach_hang' => 'required|integer',
            'id_dia_chi_giao_hang' => 'required|integer',
            'id_phuong_thuc_thanh_toan' => 'required|integer',
            'items' => 'required|array',
        ]);

        $idempotencyKey = $request->header('Idempotency-Key');
        $requestHash = md5(json_encode($request->all()));

        if ($idempotencyKey) {
            try {
                $existing = DB::table('idempotency_keys')->where('key', $idempotencyKey)->first();
                if ($existing) {
                    if ($existing->request_hash !== $requestHash) {
                        return response()->json(['success' => false, 'message' => 'Idempotency key mismatch body'], 409);
                    }
                    return response()->json(json_decode($existing->response_body, true), $existing->status_code);
                }
                
                DB::table('idempotency_keys')->insert([
                    'key' => $idempotencyKey,
                    'request_hash' => $requestHash,
                    'status_code' => 200,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Concurrent request detected'], 409);
            }
        }

        DB::beginTransaction();

        try {
            $maCode = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $donHang = DonHang::create([
                'ma_don_hang' => $maCode,
                'id_khach_hang' => $data['id_khach_hang'],
                'id_dia_chi_giao_hang' => $data['id_dia_chi_giao_hang'],
                'id_phuong_thuc_thanh_toan' => $data['id_phuong_thuc_thanh_toan'],
                'ghi_chu_khach_hang' => $request->ghi_chu_khach_hang,
                'tong_tien' => 0,
            ]);

            $orderService = new OrderService();
            $processedData = $orderService->processItemsAndInventory($data['items'], $donHang->id, false);

            foreach ($processedData['items'] as $itemData) {
                ChiTietDonHang::create($itemData);
            }

            $donHang->tong_tien = $processedData['tong_tien'];
            $donHang->save();

            DB::commit();
            
            $resBody = [
                'success' => true,
                'message' => 'Tạo đơn hàng thành công',
                'data' => $donHang
            ];
            
            if ($idempotencyKey) {
                DB::table('idempotency_keys')->where('key', $idempotencyKey)->update([
                    'response_body' => json_encode($resBody),
                    'status_code' => 200,
                ]);
            }

            return response()->json($resBody, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $status = 500;
            if (str_contains($e->getMessage(), 'Không đủ số lượng') || str_contains($e->getMessage(), 'Không thể hủy')) {
                $status = 409;
            } elseif (str_contains($e->getMessage(), 'Không tìm thấy') || str_contains($e->getMessage(), 'không hợp lệ')) {
                $status = 422;
            }
            
            $resBody = [
                'success' => false,
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()
            ];

            if ($idempotencyKey) {
                DB::table('idempotency_keys')->where('key', $idempotencyKey)->update([
                    'response_body' => json_encode($resBody),
                    'status_code' => $status,
                ]);
            }

            return response()->json($resBody, $status);
        }
    }

    public function index() { 
        return response()->json([
            'success' => true, 
            'data' => DonHang::with(['khachHang', 'diaChi', 'chiTiet.phuTung', 'chiTiet.sanPham'])->orderBy('id', 'desc')->get()
        ]); 
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            $oldStatus = (int) $donHang->trang_thai;
            $newStatus = isset($request->status) ? (int) $request->status : $oldStatus;
            
            if ($oldStatus == 3 && $newStatus == 4) {
                throw new \Exception("Không thể hủy đơn hàng đã hoàn thành. Vui lòng dùng quy trình đổi trả.");
            }
            
            $donHang->update([
                'trang_thai' => $newStatus,
                'trang_thai_thanh_toan' => $request->payment_status ?? $donHang->trang_thai_thanh_toan,
                'tong_tien' => $request->total_amount ?? $donHang->tong_tien,
                'phi_van_chuyen' => $request->shipping_fee ?? $donHang->phi_van_chuyen,
                'loai_don' => $request->order_type ?? $donHang->loai_don,
                'tien_coc_yeu_cau' => $request->deposit_required ?? $donHang->tien_coc_yeu_cau,
                'da_thu' => $request->paid_amount ?? $donHang->da_thu,
                'khach_tu_tra_cuoc' => $request->freight_collect ?? $donHang->khach_tu_tra_cuoc,
            ]);

            if ($oldStatus != 4 && $newStatus == 4) {
                $orderService = new OrderService();
                $orderService->cancelOrderInventory($donHang, auth()->id() ?? 1);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công',
                'data' => $donHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $status = 500;
            if (str_contains($e->getMessage(), 'Không thể hủy')) {
                $status = 409;
            }
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật đơn hàng: ' . $e->getMessage()
            ], $status);
        }
    }

    public function show($id)
    {
        try {
            $donHang = DonHang::with(['khachHang.user', 'diaChi', 'chiTiet.phuTung', 'chiTiet.sanPham'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $donHang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng: ' . $e->getMessage()
            ], 404);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            
            if ($donHang->trang_thai != 4) {
                $orderService = new OrderService();
                $orderService->cancelOrderInventory($donHang, auth()->id() ?? 1);
                $donHang->trang_thai = 4;
                $donHang->save();
            }
            
            $donHang->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Xóa đơn hàng thành công (Soft Delete)'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Xuất file Excel/CSV gom đơn đặt hàng sang đối tác Nhật Bản (Purchase Order - PO)
     */
    public function exportJapanPO(Request $request)
    {
        $query = DonHang::with(['chiTiet.phuTung.thuongHieu', 'chiTiet.sanPham']);

        // Cho phép lọc theo danh sách ID đơn cụ thể hoặc mặc định gom các đơn đã cọc/chờ nhập hàng
        if ($request->has('order_ids') && is_array($request->order_ids)) {
            $query->whereIn('id', $request->order_ids);
        } else {
            $query->whereIn('trang_thai', [0, 1]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'JPM_PO_JAPAN_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $output = fopen('php://output', 'w');

            // Ghi UTF-8 BOM để Excel hiển thị đúng dấu tiếng Việt / tiếng Nhật
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['JPM MARINE - PURCHASE ORDER (PO) TO JAPAN SUPPLIER']);
            fputcsv($output, ['Export Date', date('Y-m-d H:i:s'), 'Total Orders', $orders->count()]);
            fputcsv($output, []);

            fputcsv($output, [
                'STT (No.)',
                'Mã Đơn Hàng (Order Ref)',
                'Mã Phụ Tùng (Part Number OEM)',
                'Tên Phụ Tùng (Part Name Description)',
                'Số Lượng (Qty)',
                'Thương Hiệu / Hãng (Brand)',
                'Ghi Chú Đời Máy (Engine Model / Note)',
                'Tên Khách Hàng (Customer)',
                'Ngày Cọc (Deposit Date)'
            ]);

            $stt = 1;
            foreach ($orders as $order) {
                foreach ($order->chiTiet as $item) {
                    $partNo = $item->ma_phu_tung ?? ($item->phuTung->ma_phu_tung ?? $item->sanPham->ma_san_pham ?? 'N/A');
                    $partName = $item->ten_phu_tung ?? ($item->phuTung->ten_phu_tung ?? $item->sanPham->ten_san_pham ?? 'Không rõ');
                    $brand = $item->phuTung->thuongHieu->ten_thuong_hieu ?? 'Yamaha / Yanmar / Mercury';

                    fputcsv($output, [
                        $stt++,
                        $order->ma_don_hang,
                        $partNo,
                        $partName,
                        $item->so_luong,
                        $brand,
                        $order->ghi_chu_khach_hang ?? 'Theo tiêu chuẩn nhà sản xuất',
                        $order->khach_hang_ten,
                        $order->created_at ? $order->created_at->format('Y-m-d H:i') : ''
                    ]);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Admin: Xác nhận thủ công đã nhận tiền cọc cho Đơn Hàng
     */
    public function confirmDeposit(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            $amount = (float) $request->input('amount', $donHang->tien_coc_yeu_cau);
            if ($amount <= 0 && $donHang->tien_coc_yeu_cau > 0) {
                $amount = (float) $donHang->tien_coc_yeu_cau;
            }

            $donHang->da_thu = $amount;
            $donHang->trang_thai_thanh_toan = 1; // Đã cọc
            $donHang->trang_thai = 1; // Đang chuẩn bị / lên đơn Nhật
            $donHang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận nhận cọc ' . number_format($amount, 0, ',', '.') . ' đ thành công!',
                'data' => $donHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Cập nhật thông tin giao vận ViettelPost
     */
    public function updateShipping(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            
            $donHang->ma_van_don = $request->input('ma_van_don', $donHang->ma_van_don);
            $donHang->ten_don_vi_van_chuyen = $request->input('ten_don_vi_van_chuyen') ?: ($donHang->ten_don_vi_van_chuyen ?: null);
            
            $totalPayable = (float) ($donHang->tong_tien + ($donHang->khach_tu_tra_cuoc ? 0 : ($donHang->phi_van_chuyen ?? 0)));
            $daThu = (float) ($donHang->da_thu ?? 0);
            $calculatedCod = max(0, $totalPayable - $daThu);
            $donHang->tien_thu_ho_cod = (float) $request->input('tien_thu_ho_cod', $calculatedCod);

            if ($request->has('status')) {
                $donHang->trang_thai = (int) $request->input('status');
            } else {
                $donHang->trang_thai = 2; // Đang giao
            }
            $donHang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật vận chuyển thành công!',
                'data' => $donHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Xác nhận ViettelPost đã giao & nhận tiền COD đối soát -> Hoàn thành đơn hàng
     */
    public function completeOrder($request = null, $id = null)
    {
        if ($request instanceof Request) {
            $id = $id ?? (int)$request->route('id');
        } elseif (is_numeric($request)) {
            $id = (int)$request;
        } else {
            $id = (int)request()->route('id');
        }

        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            $total = (float) ($donHang->tong_tien + ($donHang->khach_tu_tra_cuoc ? 0 : ($donHang->phi_van_chuyen ?? 0)));

            $donHang->da_thu = $total;
            $donHang->trang_thai_thanh_toan = 2; // Đã thanh toán 100%
            $donHang->trang_thai = 3; // Hoàn thành
            $donHang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận hoàn thành đơn hàng!',
                'data' => $donHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
