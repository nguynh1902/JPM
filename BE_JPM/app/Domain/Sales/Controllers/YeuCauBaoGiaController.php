<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\ChiTietBaoGia;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\Sales\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class YeuCauBaoGiaController extends Controller
{
    /**
     * API gửi yêu cầu báo giá hoặc tạo đơn trực tiếp từ Client (Public & Guest & Member)
     */
    public function store(Request $request)
    {
        return $this->publicStore($request);
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'user_id'         => 'nullable|integer',
            'loai_yeu_cau'    => 'nullable|string|in:quote,order,bao_gia',
            'khach_hang_ten'  => 'required|string|max:255',
            'khach_hang_sdt'  => 'required|string|max:20',
            'chi_tiet'        => 'required|array|min:1',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
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
            $loaiYeuCau = $request->input('loai_yeu_cau', 'quote');

            // Nhận diện khách hàng B2B vs B2C
            $idKhachHang = null;
            $loaiDon = 'b2c';
            $khachTuTraCuoc = 1; // Mặc định B2C: Khách trả cước nội địa
            $verifiedUserId = auth('sanctum')->id();
            if (!$verifiedUserId && $request->user_id) {
                if (\App\Domain\IAM\Models\User::where('id', $request->user_id)->exists()) {
                    $verifiedUserId = (int) $request->user_id;
                }
            }

            if ($verifiedUserId) {
                $kh = KhachHang::where('id_tai_khoan', $verifiedUserId)->first();
                if ($kh) {
                    $idKhachHang = $kh->id;
                    if (!empty($kh->ten_cong_ty)) {
                        $loaiDon = 'b2b';
                        $khachTuTraCuoc = 0; // Khách B2B: JPM tài trợ miễn phí cước nội địa
                    }
                }
            }

            if ($loaiYeuCau === 'order') {
                $maCode = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $donHang = DonHang::create([
                    'ma_don_hang'           => $maCode,
                    'id_khach_hang'         => $idKhachHang,
                    'khach_hang_ten'        => trim($request->khach_hang_ten),
                    'khach_hang_sdt'        => trim($request->khach_hang_sdt),
                    'khach_hang_email'      => trim($request->khach_hang_email),
                    'dia_chi_text'          => $request->dia_chi_giao_hang,
                    'ghi_chu_khach_hang'    => $request->ghi_chu_khach_hang,
                    'tong_tien'             => 0,
                    'loai_don'              => $loaiDon,
                    'khach_tu_tra_cuoc'     => $khachTuTraCuoc,
                    'phi_van_chuyen'        => 0,
                    'trang_thai'            => 0, // Chờ xử lý / gom đơn
                    'trang_thai_thanh_toan' => 0,
                ]);

                $orderService = new OrderService();
                $processedData = $orderService->processItemsAndInventory($request->chi_tiet, $donHang->id, true);

                foreach ($processedData['items'] as $itemData) {
                    ChiTietDonHang::create($itemData);
                }

                $donHang->tong_tien = $processedData['tong_tien'];
                $donHang->save();

                $responseCode = $maCode;
            } else {
                $maCode = 'YCBG-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $quote = YeuCauBaoGia::create([
                    'ma_yeu_cau'         => $maCode,
                    'loai_yeu_cau'       => 'quote',
                    'khach_hang_ten'     => trim($request->khach_hang_ten),
                    'khach_hang_sdt'     => trim($request->khach_hang_sdt),
                    'dia_chi_giao_hang'  => $request->dia_chi_giao_hang,
                    'khach_hang_email'   => $request->khach_hang_email,
                    'ghi_chu_khach_hang' => $request->ghi_chu_khach_hang,
                    'tong_tien'          => 0,
                    'khach_tu_tra_cuoc'  => $khachTuTraCuoc,
                    'phi_van_chuyen'     => 0,
                    'tien_coc_yeu_cau'   => 0,
                    'trang_thai'         => 0, // 0 = Chờ báo giá
                    'user_id'            => $verifiedUserId,
                    'token'              => Str::random(32),
                ]);

                $tongTienTamTinh = 0;
                foreach ($request->chi_tiet as $item) {
                    $qty = (int) ($item['so_luong'] ?? 1);
                    $price = (float) ($item['gia_ban'] ?? 0);
                    $tenItem = $item['ten_phu_tung'] ?? 'Không rõ';
                    $maItem = $item['ma_phu_tung'] ?? 'N/A';

                    $idItem = $item['id_san_pham'] ?? $item['id_phu_tung'] ?? null;
                    $loaiSanPham = $item['loai_san_pham'] ?? 'phu_tung';
                    if ($idItem) {
                        if ($loaiSanPham === 'san_pham') {
                            $sp = \App\Domain\CMS\Models\SanPham::find($idItem);
                            if ($sp) {
                                $tenItem = $sp->ten_san_pham;
                                $maItem = $sp->ma_san_pham;
                                $price = $sp->is_gia_lien_he ? 0 : (float) $sp->gia_ban;
                            }
                        } else {
                            $pt = \App\Domain\PIM\Models\PhuTung::find($idItem);
                            if ($pt) {
                                $tenItem = $pt->ten_phu_tung;
                                $maItem = $pt->ma_phu_tung;
                                $price = $pt->is_gia_lien_he ? 0 : (float) $pt->gia_ban;
                            }
                        }
                    } elseif (!empty($maItem) && $maItem !== 'N/A') {
                        $pt = \App\Domain\PIM\Models\PhuTung::where('ma_phu_tung', $maItem)->first();
                        if ($pt) {
                            $tenItem = $pt->ten_phu_tung;
                            $price = $pt->is_gia_lien_he ? 0 : (float) $pt->gia_ban;
                        } else {
                            $sp = \App\Domain\CMS\Models\SanPham::where('ma_san_pham', $maItem)->first();
                            if ($sp) {
                                $tenItem = $sp->ten_san_pham;
                                $price = $sp->is_gia_lien_he ? 0 : (float) $sp->gia_ban;
                            }
                        }
                    }

                    $tongTienTamTinh += ($qty * $price);

                    ChiTietBaoGia::create([
                        'yeu_cau_bao_gia_id' => $quote->id,
                        'ten_phu_tung'       => $tenItem,
                        'ma_phu_tung'        => $maItem,
                        'so_luong'           => $qty,
                        'don_gia'            => $price
                    ]);
                }

                if ($tongTienTamTinh > 0) {
                    $quote->tong_tien = $tongTienTamTinh;
                    $quote->save();
                }

                $responseCode = $maCode;
            }

            DB::commit();

            $resBody = [
                'success' => true,
                'message' => 'Gửi yêu cầu thành công',
                'code'    => $responseCode,
                'data'    => [
                    'id'         => $loaiYeuCau === 'order' ? $donHang->id : $quote->id,
                    'code'       => $responseCode,
                    'ma_yeu_cau' => $responseCode,
                    'token'      => $loaiYeuCau === 'order' ? null : $quote->token,
                ]
            ];

            if ($idempotencyKey) {
                DB::table('idempotency_keys')->where('key', $idempotencyKey)->update([
                    'response_body' => json_encode($resBody),
                    'status_code'   => 200,
                ]);
            }

            return response()->json($resBody, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $resBody = [
                'success' => false,
                'message' => 'Lỗi xử lý: ' . $e->getMessage()
            ];

            return response()->json($resBody, 500);
        }
    }

    /**
     * Admin: Danh sách toàn bộ yêu cầu báo giá
     */
    public function index()
    {
        try {
            $quotes = YeuCauBaoGia::with('chiTiet')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $quotes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải danh sách báo giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Xem chi tiết 1 báo giá
     */
    public function show($id)
    {
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data'    => $quote
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy yêu cầu báo giá.'], 404);
        }
    }

    /**
     * Admin: Cập nhật thông tin báo giá & đơn giá từng món
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::findOrFail($id);

            $quote->update([
                'tong_tien'         => $request->input('tong_tien', $quote->tong_tien),
                'trang_thai'        => $request->input('trang_thai', $quote->trang_thai),
                'ghi_chu_zalo'      => $request->input('ghi_chu_zalo', $quote->ghi_chu_zalo),
                'phi_van_chuyen'    => $request->input('shipping_fee', $request->input('phi_van_chuyen', $quote->phi_van_chuyen)),
                'khach_tu_tra_cuoc' => $request->input('freight_collect', $request->input('khach_tu_tra_cuoc', $quote->khach_tu_tra_cuoc)),
                'tien_coc_yeu_cau'  => $request->input('deposit_required', $request->input('tien_coc_yeu_cau', $quote->tien_coc_yeu_cau)),
                'dia_chi_giao_hang' => $request->input('dia_chi_giao_hang', $request->input('address', $quote->dia_chi_giao_hang)),
            ]);

            // Cập nhật giá từng món hàng nếu có
            if ($request->has('chi_tiet') && is_array($request->input('chi_tiet'))) {
                foreach ($request->input('chi_tiet') as $item) {
                    if (isset($item['id']) && $item['id']) {
                        ChiTietBaoGia::where('id', $item['id'])
                            ->where('yeu_cau_bao_gia_id', $quote->id)
                            ->update([
                                'don_gia'  => (float) ($item['price'] ?? $item['don_gia'] ?? 0),
                                'so_luong' => (int) ($item['qty'] ?? $item['so_luong'] ?? 1),
                            ]);
                    } elseif (!empty($item['part_name']) || !empty($item['ten_phu_tung'])) {
                        ChiTietBaoGia::create([
                            'yeu_cau_bao_gia_id' => $quote->id,
                            'ten_phu_tung'      => $item['part_name'] ?? $item['ten_phu_tung'] ?? '',
                            'ma_phu_tung'       => $item['part_code'] ?? $item['ma_phu_tung'] ?? null,
                            'so_luong'          => (int) ($item['qty'] ?? $item['so_luong'] ?? 1),
                            'don_gia'           => (float) ($item['price'] ?? $item['don_gia'] ?? 0),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật báo giá thành công',
                'data'    => $quote->fresh('chiTiet')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật báo giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Xóa yêu cầu báo giá
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            ChiTietBaoGia::where('yeu_cau_bao_gia_id', $id)->delete();
            YeuCauBaoGia::destroy($id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa yêu cầu báo giá thành công.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa báo giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Khách hàng mở link xem báo giá qua Magic Token (Public)
     */
    public function showByToken($token)
    {
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->where('token', $token)->firstOrFail();

            // Thời hạn hiệu lực báo giá: 72 giờ (3 ngày) kể từ khi Admin báo giá
            $validHours = 72;
            $isExpired = false;
            $remainingSeconds = 0;
            $expiresAt = null;

            if ($quote->trang_thai == 1 && $quote->updated_at) {
                $expiresAt = $quote->updated_at->copy()->addHours($validHours);
                if (now()->greaterThan($expiresAt)) {
                    $isExpired = true;
                    // Tự động hủy báo giá quá hạn
                    $quote->trang_thai = 3;
                    $quote->ghi_chu_zalo = ($quote->ghi_chu_zalo ? $quote->ghi_chu_zalo . ' | ' : '') . 'Báo giá đã tự động hủy do quá hạn hiệu lực (' . $validHours . 'h).';
                    $quote->save();
                } else {
                    $remainingSeconds = max(0, now()->diffInSeconds($expiresAt, false));
                }
            } elseif ($quote->trang_thai == 3) {
                $isExpired = true;
            }

            $quoteData = $quote->toArray();
            $quoteData['is_expired'] = $isExpired;
            $quoteData['valid_hours'] = $validHours;
            $quoteData['expires_at'] = $expiresAt ? $expiresAt->toIso8601String() : null;
            $quoteData['remaining_seconds'] = $remainingSeconds;

            if ($quote->tien_coc_yeu_cau > 0) {
                try {
                    $vietQRService = app(\App\Domain\Sales\Services\VietQRService::class);
                    $quoteData['vietqr_data'] = $vietQRService->generateBaoGiaQR($quote);
                } catch (\Exception $qe) {
                    // Fallback
                }
            }

            return response()->json([
                'success' => true,
                'data'    => $quoteData
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Báo giá không tồn tại hoặc đã hết hạn.'], 404);
        }
    }

    /**
     * Khách hàng chốt / duyệt / hủy / yêu cầu báo giá lại từ link Zalo
     */
    public function customerAction(Request $request, $token)
    {
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->where('token', $token)->firstOrFail();
            $action = $request->input('action', 'accept');

            if ($action === 'accept') {
                // Kiểm tra nếu báo giá đã quá 72h thì không cho chốt giá cũ
                $validHours = 72;
                if ($quote->updated_at && now()->diffInHours($quote->updated_at) > $validHours) {
                    $quote->trang_thai = 3;
                    $quote->save();
                    DB::commit();

                    return response()->json([
                        'success' => false,
                        'is_expired' => true,
                        'message' => 'Báo giá này đã quá thời hạn giữ giá (' . $validHours . ' giờ). Vui lòng bấm "Yêu Cầu Báo Giá Lại" để nhận giá cập nhật mới nhất.'
                    ], 422);
                }

                if ($request->filled('dia_chi_giao_hang')) {
                    $quote->dia_chi_giao_hang = $request->input('dia_chi_giao_hang');
                } elseif ($request->filled('address')) {
                    $quote->dia_chi_giao_hang = $request->input('address');
                }

                $quote->trang_thai = 2; // Khách đã chốt đơn hàng (Chờ cọc)
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Quý khách đã đồng ý chốt đơn thành công! Vui lòng chuyển khoản tiền cọc để kho tiến hành đặt hàng bên Nhật.'
                ]);
            } elseif ($action === 're_quote') {
                // Khách yêu cầu báo giá lại khi đơn cũ quá hạn
                $quote->trang_thai = 0; // Chuyển về Chờ báo giá
                $quote->updated_at = now();
                $quote->ghi_chu_khach_hang = ($quote->ghi_chu_khach_hang ? $quote->ghi_chu_khach_hang . ' | ' : '') . 'Khách yêu cầu cập nhật lại báo giá vào ' . now()->format('d/m/Y H:i');
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã gửi yêu cầu báo giá lại tới JPM Marine. Kỹ thuật viên sẽ kiểm tra lại giá và gửi bảng giá mới nhất qua Zalo cho quý khách.'
                ]);
            } else {
                $quote->trang_thai = 3; // Hủy
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã từ chối báo giá. Đơn yêu cầu này đã được đóng lại.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Chuyển Yêu Cầu Báo Giá thành Đơn Hàng chính thức (Convert to Order)
     */
    public function convertToOrder($id)
    {
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->findOrFail($id);

            // Kiểm tra nếu đã chuyển rồi
            if ($quote->trang_thai == 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu báo giá này đã được tạo thành đơn hàng trước đó.'
                ], 409);
            }

            // Nhận diện khách hàng B2B vs B2C
            $idKhachHang = null;
            $loaiDon = 'b2c';
            if ($quote->user_id) {
                $kh = KhachHang::where('id_tai_khoan', $quote->user_id)->first();
                if ($kh) {
                    $idKhachHang = $kh->id;
                    if (!empty($kh->ten_cong_ty)) {
                        $loaiDon = 'b2b';
                    }
                }
            }

            $maDonHang = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $donHang = DonHang::create([
                'ma_don_hang'           => $maDonHang,
                'id_khach_hang'         => $idKhachHang,
                'khach_hang_ten'        => $quote->khach_hang_ten,
                'khach_hang_sdt'        => $quote->khach_hang_sdt,
                'khach_hang_email'      => $quote->khach_hang_email,
                'dia_chi_text'          => $quote->dia_chi_giao_hang,
                'ghi_chu_khach_hang'    => $quote->ghi_chu_khach_hang . ($quote->ghi_chu_zalo ? ' | ' . $quote->ghi_chu_zalo : ''),
                'tong_tien'             => $quote->tong_tien,
                'phi_van_chuyen'        => $quote->phi_van_chuyen,
                'tien_coc_yeu_cau'      => $quote->tien_coc_yeu_cau,
                'khach_tu_tra_cuoc'     => $quote->khach_tu_tra_cuoc,
                'loai_don'              => $loaiDon,
                'trang_thai'            => 0, // Chờ xác nhận
                'trang_thai_thanh_toan' => 0, // Chờ thanh toán / cọc
            ]);

            // Sao chép chi tiết từ Báo giá sang Đơn hàng
            foreach ($quote->chiTiet as $ct) {
                $idSanPham = null;
                $loaiSanPham = 'phu_tung';
                if (!empty($ct->ma_phu_tung) && $ct->ma_phu_tung !== 'N/A') {
                    $pt = \App\Domain\PIM\Models\PhuTung::where('ma_phu_tung', $ct->ma_phu_tung)->first();
                    if ($pt) {
                        $idSanPham = $pt->id;
                        $loaiSanPham = 'phu_tung';
                    } else {
                        $sp = \App\Domain\CMS\Models\SanPham::where('ma_san_pham', $ct->ma_phu_tung)->first();
                        if ($sp) {
                            $idSanPham = $sp->id;
                            $loaiSanPham = 'san_pham';
                        }
                    }
                }

                // Fallback an toàn cho hàng đặt riêng (Custom / Bespoke SKU)
                if (!$idSanPham) {
                    $idSanPham = null;
                    $loaiSanPham = "custom";
                } else {
                    // Xử lý tồn kho & ghi nhận Thẻ Kho tương tự OrderService
                    $tonKho = \App\Domain\Procurement\Models\TonKho::where("id_san_pham", $idSanPham)
                        ->where("loai_san_pham", $loaiSanPham)
                        ->lockForUpdate()
                        ->first();
                    if ($tonKho && $tonKho->so_luong_ton >= $ct->so_luong) {
                        $tonKho->so_luong_ton -= $ct->so_luong;
                        $tonKho->save();

                        \App\Domain\Procurement\Models\TheKho::create([
                            'id_kho' => $tonKho->id_kho,
                            'id_san_pham' => $idSanPham,
                            'loai_san_pham' => $loaiSanPham,
                            'loai_giao_dich' => 'XUAT_KHO',
                            'so_luong_thay_doi' => -$ct->so_luong,
                            'so_luong_ton_cuoi' => $tonKho->so_luong_ton,
                            'id_tham_chieu' => $donHang->id,
                            'ma_tham_chieu' => 'DH' . str_pad($donHang->id, 5, '0', STR_PAD_LEFT),
                            'ghi_chu' => 'Xuất bán từ báo giá #' . $quote->ma_yeu_cau,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    } elseif ($tonKho) {
                        $tonKho->so_luong_dang_giu = ($tonKho->so_luong_dang_giu ?? 0) + $ct->so_luong;
                        $tonKho->save();
                    } else {
                        \App\Domain\Procurement\Models\TonKho::create([
                            'id_kho' => 1,
                            'id_san_pham' => $idSanPham,
                            'loai_san_pham' => $loaiSanPham,
                            'so_luong_ton' => 0,
                            'so_luong_dang_giu' => $ct->so_luong,
                        ]);
                    }
                }

                ChiTietDonHang::create([
                    'id_don_hang'   => $donHang->id,
                    'id_san_pham'   => $idSanPham,
                    'loai_san_pham' => $loaiSanPham,
                    'ten_phu_tung'  => $ct->ten_phu_tung,
                    'ma_phu_tung'   => $ct->ma_phu_tung,
                    'so_luong'      => $ct->so_luong,
                    'don_gia'       => $ct->don_gia,
                    'thanh_tien'    => $ct->so_luong * $ct->don_gia,
                ]);
            }

            // Đổi trạng thái báo giá sang Đã chuyển đơn (2)
            $quote->trang_thai = 2;
            $quote->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã chuyển đổi thành công sang Đơn hàng {$maDonHang}!",
                'data'    => $donHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi chuyển đổi đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Xác nhận thủ công đã nhận tiền cọc cho Yêu Cầu Báo Giá
     */
    public function confirmDeposit($request = null, $id = null)
    {
        if ($request instanceof Request) {
            $id = $id ?? (int)$request->route('id');
        } elseif (is_numeric($request)) {
            $id = (int)$request;
            $request = request();
        } else {
            $id = (int)request()->route('id');
            $request = request();
        }
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->findOrFail($id);
            if ($request->filled('dia_chi_giao_hang')) {
                $quote->dia_chi_giao_hang = $request->input('dia_chi_giao_hang');
            } elseif ($request->filled('address')) {
                $quote->dia_chi_giao_hang = $request->input('address');
            }
            $amount = (float) $request->input('amount', $quote->tien_coc_yeu_cau);
            if ($amount <= 0 && $quote->tien_coc_yeu_cau > 0) {
                $amount = (float) $quote->tien_coc_yeu_cau;
            }

            $quote->da_thu = $amount;
            $quote->trang_thai = 2; // Đã chốt & đã cọc
            $quote->save();

            // Tự động tạo/cập nhật Đơn hàng chính thức để nhân viên lên đơn Nhật
            $existingOrder = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')->first();
            if (!$existingOrder) {
                $idKhachHang = null;
                $loaiDon = 'b2c';
                if ($quote->user_id) {
                    $kh = KhachHang::where('id_tai_khoan', $quote->user_id)->first();
                    if ($kh) {
                        $idKhachHang = $kh->id;
                        if (!empty($kh->ten_cong_ty)) {
                            $loaiDon = 'b2b';
                        }
                    }
                }

                $maDonHang = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $donHang = DonHang::create([
                    'ma_don_hang'           => $maDonHang,
                    'id_khach_hang'         => $idKhachHang,
                    'khach_hang_ten'        => $quote->khach_hang_ten,
                    'khach_hang_sdt'        => $quote->khach_hang_sdt,
                    'khach_hang_email'      => $quote->khach_hang_email,
                    'dia_chi_text'          => $quote->dia_chi_giao_hang,
                    'ghi_chu_khach_hang'    => 'Báo giá #' . $quote->ma_yeu_cau . ($quote->ghi_chu_khach_hang ? ' | ' . $quote->ghi_chu_khach_hang : ''),
                    'tong_tien'             => $quote->tong_tien,
                    'phi_van_chuyen'        => $quote->phi_van_chuyen,
                    'tien_coc_yeu_cau'      => $quote->tien_coc_yeu_cau,
                    'da_thu'                => $amount,
                    'khach_tu_tra_cuoc'     => $quote->khach_tu_tra_cuoc,
                    'loai_don'              => $loaiDon,
                    'trang_thai'            => 1, // 1 = Đang chuẩn bị / Lên đơn Nhật
                    'trang_thai_thanh_toan' => 1, // 1 = Đã đặt cọc
                ]);

                foreach ($quote->chiTiet as $ct) {
                    $idSanPham = null;
                    $loaiSanPham = 'phu_tung';
                    if (!empty($ct->ma_phu_tung) && $ct->ma_phu_tung !== 'N/A') {
                        $pt = \App\Domain\PIM\Models\PhuTung::where('ma_phu_tung', $ct->ma_phu_tung)->first();
                        if ($pt) {
                            $idSanPham = $pt->id;
                            $loaiSanPham = 'phu_tung';
                        } else {
                            $sp = \App\Domain\CMS\Models\SanPham::where('ma_san_pham', $ct->ma_phu_tung)->first();
                            if ($sp) {
                                $idSanPham = $sp->id;
                                $loaiSanPham = 'san_pham';
                            }
                        }
                    }

                    ChiTietDonHang::create([
                        'id_don_hang'   => $donHang->id,
                        'id_san_pham'   => $idSanPham,
                        'loai_san_pham' => $loaiSanPham,
                        'ten_phu_tung'  => $ct->ten_phu_tung,
                        'ma_phu_tung'   => $ct->ma_phu_tung,
                        'so_luong'      => $ct->so_luong,
                        'don_gia'       => $ct->don_gia,
                        'thanh_tien'    => $ct->so_luong * $ct->don_gia,
                    ]);
                }
            } else {
                $existingOrder->da_thu = $amount;
                $existingOrder->trang_thai_thanh_toan = 1;
                $existingOrder->trang_thai = 1;
                if ($quote->dia_chi_giao_hang && !$existingOrder->dia_chi_text) {
                    $existingOrder->dia_chi_text = $quote->dia_chi_giao_hang;
                }
                $existingOrder->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận nhận cọc ' . number_format($amount, 0, ',', '.') . ' đ thành công! Đơn hàng đã chuyển sang trạng thái Đang Chuẩn Bị & Lên Đơn Nhật.',
                'data'    => $quote->fresh('chiTiet')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xác nhận khách đã nhận hàng và thanh toán đủ số tiền còn lại (Hoàn tất giao dịch báo giá)
     */
    public function completeQuote($request = null, $id = null)
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
            $quote = YeuCauBaoGia::lockForUpdate()->findOrFail($id);
            $total = (float) ($quote->tong_tien + ($quote->khach_tu_tra_cuoc ? 0 : ($quote->phi_van_chuyen ?? 0)));

            $quote->da_thu = $total;
            $quote->trang_thai = 4; // 4 = Hoàn thành
            $quote->save();

            // Nếu có đơn hàng liên kết tương ứng, cũng cập nhật hoàn thành
            $linkedOrder = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')->first();
            if ($linkedOrder) {
                $linkedOrder->da_thu = $total;
                $linkedOrder->trang_thai_thanh_toan = 2; // Đã thanh toán 100%
                $linkedOrder->trang_thai = 3; // Hoàn thành
                $linkedOrder->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận hoàn thành đơn hàng báo giá #' . $quote->ma_yeu_cau . '!',
                'data'    => $quote->fresh('chiTiet')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
