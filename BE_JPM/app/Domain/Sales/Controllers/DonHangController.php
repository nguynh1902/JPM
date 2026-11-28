<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\Core\Services\ImageUploadService;
use App\Domain\Sales\Services\JapanPoExportService;
use App\Domain\Sales\Services\OrderService;

class DonHangController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_khach_hang' => 'required|integer',
            'id_dia_chi_giao_hang' => 'required|integer',
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
    public function index(Request $request) { 
        $query = DonHang::with(['khachHang', 'diaChi', 'chiTiet.phuTung', 'chiTiet.sanPham'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('ma_don_hang', 'like', "%{$s}%")
                  ->orWhere('khach_hang_ten', 'like', "%{$s}%")
                  ->orWhere('khach_hang_sdt', 'like', "%{$s}%")
                  ->orWhere('khach_hang_email', 'like', "%{$s}%")
                  ->orWhereHas('khachHang.user', function ($sub) use ($s) {
                      $sub->where('name', 'like', "%{$s}%")
                          ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->has('trang_thai') && $request->input('trang_thai') !== '' && $request->input('trang_thai') !== null) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        if ($request->has('trang_thai_thanh_toan') && $request->input('trang_thai_thanh_toan') !== '' && $request->input('trang_thai_thanh_toan') !== null) {
            $query->where('trang_thai_thanh_toan', (int) $request->input('trang_thai_thanh_toan'));
        }

        $hasPagination = $request->has('per_page') || $request->has('page');
        $all = $request->boolean('all', false) || !$hasPagination;
        $perPage = $all ? null : min(max((int) $request->input('per_page', 50), 1), 100);

        if ($all || !$perPage) {
            return response()->json([
                'success' => true,
                'data' => $query->limit(500)->get()
            ]);
        }

        $paginated = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ]
        ]);
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

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);

            $newStatus = $request->has('status')
                ? (int) $request->input('status')
                : ($request->has('trang_thai') ? (int) $request->input('trang_thai') : (int) $donHang->trang_thai);

            // Khi chuyển sang trạng thái Đã Hủy (4) lần đầu -> tự động hoàn kho / giảm giữ chỗ
            if ($newStatus === 4 && (int) $donHang->trang_thai !== 4) {
                $orderService = new OrderService();
                $orderService->cancelOrderInventory($donHang, auth()->id() ?? 1);
            }

            $donHang->trang_thai = $newStatus;

            if ($request->has('payment_status') || $request->has('trang_thai_thanh_toan')) {
                $donHang->trang_thai_thanh_toan = (int) $request->input('payment_status', $request->input('trang_thai_thanh_toan'));
            }
            if ($request->has('total_amount') || $request->has('tong_tien')) {
                $donHang->tong_tien = max(0, (float) $request->input('total_amount', $request->input('tong_tien')));
            }
            if ($request->has('shipping_fee') || $request->has('phi_van_chuyen')) {
                $donHang->phi_van_chuyen = max(0, (float) $request->input('shipping_fee', $request->input('phi_van_chuyen')));
            }
            if ($request->has('order_type') || $request->has('loai_don')) {
                $orderType = (string) $request->input('order_type', $request->input('loai_don'));
                if (in_array($orderType, ['b2b', 'b2c'], true)) {
                    $donHang->loai_don = $orderType;
                }
            }
            if ($request->has('freight_collect') || $request->has('khach_tu_tra_cuoc')) {
                $donHang->khach_tu_tra_cuoc = (int) (bool) $request->input('freight_collect', $request->input('khach_tu_tra_cuoc'));
            }
            $effectiveShipping = $donHang->khach_tu_tra_cuoc ? 0 : max(0, (float) $donHang->phi_van_chuyen);

            if ($request->has('deposit_required') || $request->has('tien_coc_yeu_cau')) {
                $rawDeposit = (float) $request->input('deposit_required', $request->input('tien_coc_yeu_cau'));
                if ($rawDeposit < 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số tiền cọc yêu cầu không được âm.'
                    ], 422);
                }
                $itemsSubtotal = (float) $donHang->chiTiet()->get()->sum(fn($ct) => max(1, (int) $ct->so_luong) * max(0, (float) $ct->don_gia));
                $maxGrandTotal = $itemsSubtotal > 0
                    ? max((float) $donHang->tong_tien, $itemsSubtotal + $effectiveShipping)
                    : ((float) $donHang->tong_tien + $effectiveShipping);

                if ($rawDeposit > ($maxGrandTotal + 0.01)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số tiền cọc yêu cầu không được vượt quá tổng giá trị đơn hàng (' . number_format($maxGrandTotal, 0, ',', '.') . ' đ).'
                    ], 422);
                }
                $donHang->tien_coc_yeu_cau = $rawDeposit;
            }
            if ($request->has('paid_amount') || $request->has('da_thu')) {
                $donHang->da_thu = max(0, (float) $request->input('paid_amount', $request->input('da_thu')));
            }
            if ($request->filled('dia_chi_text') || $request->filled('address')) {
                $donHang->dia_chi_text = $request->input('dia_chi_text', $request->input('address'));
            }
            if ($request->has('ten_don_vi_van_chuyen')) {
                $carrier = trim((string) $request->input('ten_don_vi_van_chuyen'));
                $donHang->ten_don_vi_van_chuyen = $carrier !== '' ? $carrier : null;
            }
            if ($request->has('ma_van_don')) {
                $tracking = trim((string) $request->input('ma_van_don'));
                $donHang->ma_van_don = $tracking !== '' ? $tracking : null;
            }
            if ($request->has('anh_chuyen_khoan') || $request->has('receipt_image')) {
                $donHang->anh_chuyen_khoan = $request->input('anh_chuyen_khoan', $request->input('receipt_image'));
            }
            if ($request->has('anh_chuyen_khoan_2') || $request->has('receipt_image_2')) {
                $donHang->anh_chuyen_khoan_2 = $request->input('anh_chuyen_khoan_2', $request->input('receipt_image_2'));
            }

            $donHang->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công',
                'data' => $donHang->fresh(['khachHang.user', 'diaChi', 'chiTiet.phuTung', 'chiTiet.sanPham'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật đơn hàng: ' . $e->getMessage()
            ], 500);
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
     * Admin: Xuất file Excel (.xlsx) gom đơn đặt hàng sang đối tác Nhật Bản (Purchase Order - PO)
     */
    public function exportJapanPO(Request $request)
    {
        $query = DonHang::with(['khachHang', 'chiTiet.phuTung.thuongHieu', 'chiTiet.sanPham']);

        $rawOrderIds = $request->input('order_ids', []);
        if (is_string($rawOrderIds) && $rawOrderIds !== '') {
            $rawOrderIds = array_filter(array_map('intval', explode(',', $rawOrderIds)));
        } elseif (is_array($rawOrderIds)) {
            $rawOrderIds = array_values(array_filter(array_map('intval', $rawOrderIds)));
        } else {
            $rawOrderIds = [];
        }

        // Nếu Admin có chọn đơn cụ thể -> xuất đúng các đơn đó (bỏ đơn đã hủy);
        // mặc định chỉ xuất các đơn Đang Xử Lý (trang_thai = 1, đã xác nhận / đã nhận cọc).
        if (!empty($rawOrderIds)) {
            $query->whereIn('id', $rawOrderIds)->where('trang_thai', '!=', 4);
        } else {
            $query->where('trang_thai', 1);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => !empty($rawOrderIds)
                    ? 'Không tìm thấy đơn hàng hợp lệ trong danh sách đã chọn để xuất PO.'
                    : 'Hiện chưa có đơn hàng Đang Xử Lý nào để xuất PO đặt hàng Nhật.'
            ], 422);
        }

        $xlsxPath = app(JapanPoExportService::class)->buildWorkbook($orders);
        $filename = 'JPM_PO_Nhat_' . date('Ymd_His') . '.xlsx';

        return response()->download($xlsxPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Admin: Xác nhận thủ công đã nhận tiền cọc cho Đơn Hàng
     */
    public function confirmDeposit(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            if ($request->filled('dia_chi_text')) {
                $donHang->dia_chi_text = $request->input('dia_chi_text');
            } elseif ($request->filled('address')) {
                $donHang->dia_chi_text = $request->input('address');
            } elseif ($request->filled('dia_chi_giao_hang')) {
                $donHang->dia_chi_text = $request->input('dia_chi_giao_hang');
            }
            if ($request->filled('anh_chuyen_khoan')) {
                $donHang->anh_chuyen_khoan = $request->input('anh_chuyen_khoan');
            } elseif ($request->filled('receipt_image')) {
                $donHang->anh_chuyen_khoan = $request->input('receipt_image');
            }
            if ($request->filled('anh_chuyen_khoan_2')) {
                $donHang->anh_chuyen_khoan_2 = $request->input('anh_chuyen_khoan_2');
            } elseif ($request->filled('receipt_image_2')) {
                $donHang->anh_chuyen_khoan_2 = $request->input('receipt_image_2');
            }
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
     * Khách hàng báo đã chuyển khoản cọc cho Đơn Hàng trực tiếp (kèm ảnh biên lai không bắt buộc)
     */
    public function reportDeposit(Request $request, $code)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->where('ma_don_hang', $code)->firstOrFail();

            // Xác thực quyền sở hữu: User đăng nhập khớp đơn HOẶC số điện thoại khớp
            $user = auth('sanctum')->user();
            $authorized = false;
            if ($user) {
                $khachHangId = KhachHang::where('id_tai_khoan', $user->id)->value('id');
                if (($khachHangId && (int) $donHang->id_khach_hang === (int) $khachHangId)
                    || (!empty($user->email) && strcasecmp((string) $donHang->khach_hang_email, (string) $user->email) === 0)
                    || $user->hasAnyRole(['admin', 'Super Admin'])) {
                    $authorized = true;
                }
            }
            if (!$authorized && $request->filled('phone')) {
                $inputPhone = preg_replace('/\D/', '', (string) $request->input('phone'));
                $orderPhone = preg_replace('/\D/', '', (string) $donHang->khach_hang_sdt);
                if ($inputPhone !== '' && $inputPhone === $orderPhone) {
                    $authorized = true;
                }
            }
            if (!$authorized) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền cập nhật thông tin chuyển khoản cho đơn hàng này.'
                ], 403);
            }

            if ((int) $donHang->trang_thai === 4) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này đã bị hủy.'
                ], 422);
            }

            if ((int) $donHang->trang_thai >= 1 && (float) ($donHang->da_thu ?? 0) > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Đơn hàng này đã được xác nhận thanh toán trước đó.',
                    'data' => $donHang
                ]);
            }

            $uploadedUrl = null;
            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120'
                ]);
                $uploadedUrl = ImageUploadService::storeUploadedFile(
                    $request->file('file'),
                    'uploads/receipts',
                    'receipt',
                    1600
                );
            } elseif ($request->filled('image_base64')) {
                $uploadedUrl = ImageUploadService::storeBase64(
                    (string) $request->input('image_base64'),
                    'uploads/receipts',
                    'receipt',
                    1600,
                    5 * 1024 * 1024
                );
            }

            if ($uploadedUrl) {
                $oldReceipt = $donHang->anh_chuyen_khoan;
                $donHang->anh_chuyen_khoan = $uploadedUrl;
                if ($oldReceipt && $oldReceipt !== $uploadedUrl) {
                    ImageUploadService::delete($oldReceipt);
                }
            }

            $donHang->da_bao_chuyen_khoan = true;
            $donHang->thoi_gian_bao_coc = now();
            $donHang->save();

            DB::commit();

            try {
                app(\App\Domain\Communication\Services\NotificationService::class)
                    ->notifyAdminDepositReported(
                        (string) $donHang->ma_don_hang,
                        (string) ($donHang->khach_hang_ten ?? 'Khách hàng'),
                        (string) ($donHang->khach_hang_sdt ?? ''),
                        (float) ($donHang->tien_coc_yeu_cau ?: $donHang->tong_tien ?: 0),
                        (bool) ($uploadedUrl || $donHang->anh_chuyen_khoan)
                    );
            } catch (\Throwable $ne) {
                // Ignore notification error
            }

            return response()->json([
                'success' => true,
                'message' => 'JPM Marine đã tiếp nhận thông báo chuyển khoản. Kỹ thuật viên sẽ kiểm tra và xác nhận đơn ngay.',
                'data' => $donHang
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'File ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, WebP tối đa 5MB).',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Đánh dấu chưa nhận được tiền cọc sau khi khách báo đã chuyển khoản
     */
    public function rejectDepositReport($id)
    {
        DB::beginTransaction();
        try {
            $donHang = DonHang::lockForUpdate()->findOrFail($id);
            if ((int) $donHang->trang_thai >= 1 && (float) ($donHang->da_thu ?? 0) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng này đã được xác nhận thu cọc trước đó.'
                ], 409);
            }

            $donHang->da_bao_chuyen_khoan = false;
            $donHang->thoi_gian_bao_coc = null;
            $donHang->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt lại trạng thái chờ khách chuyển cọc.',
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
            
            if ($request->has('ma_van_don')) {
                $tracking = trim((string) $request->input('ma_van_don'));
                $donHang->ma_van_don = $tracking !== '' ? $tracking : null;
            }
            if ($request->has('ten_don_vi_van_chuyen')) {
                $carrier = trim((string) $request->input('ten_don_vi_van_chuyen'));
                $donHang->ten_don_vi_van_chuyen = $carrier !== '' ? $carrier : 'Giao trực tiếp / Chành xe';
            } elseif (!$donHang->ten_don_vi_van_chuyen) {
                $donHang->ten_don_vi_van_chuyen = 'Giao trực tiếp / Chành xe';
            }
            if ($request->filled('dia_chi_text') || $request->filled('address')) {
                $donHang->dia_chi_text = $request->input('dia_chi_text', $request->input('address'));
            }
            if ($request->has('ten_don_vi_van_chuyen')) {
                $carrier = trim((string) $request->input('ten_don_vi_van_chuyen'));
                $donHang->ten_don_vi_van_chuyen = $carrier !== '' ? $carrier : null;
            }
            if ($request->has('ma_van_don')) {
                $tracking = trim((string) $request->input('ma_van_don'));
                $donHang->ma_van_don = $tracking !== '' ? $tracking : null;
            }
            
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

            // Cập nhật yêu cầu báo giá tương ứng nếu đơn hàng này sinh ra từ báo giá
            if (!empty($donHang->ghi_chu_khach_hang) && preg_match('/Báo giá #(YCBG-[^\s|]+)/', $donHang->ghi_chu_khach_hang, $m)) {
                $linkedQuote = YeuCauBaoGia::where('ma_yeu_cau', $m[1])->first();
                if ($linkedQuote) {
                    $linkedQuote->da_thu = $total;
                    $linkedQuote->trang_thai = 4; // Hoàn thành
                    $linkedQuote->save();
                }
            }

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
