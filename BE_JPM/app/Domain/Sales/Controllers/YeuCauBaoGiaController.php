<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\ChiTietBaoGia;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\IAM\Models\User;
use App\Domain\Core\Services\ImageUploadService;
use App\Domain\Sales\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class YeuCauBaoGiaController extends Controller
{
    private function isB2BUser(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        $user = User::with('roles')->find($userId);
        if (!$user) {
            return false;
        }
        return $user->roles->contains(function ($role) {
            $name = mb_strtolower((string) ($role->name ?? ''), 'UTF-8');
            return str_contains($name, 'đại lý') || str_contains($name, 'b2b');
        });
    }
    /**
     * API gửi yêu cầu báo giá hoặc tạo đơn trực tiếp từ Client (Public & Guest & Member)
     */
    public function store(Request $request)
    {
        return $this->publicStore($request);
    }

    /**
     * API tạo đơn hàng / báo giá thủ công từ Admin / Staff (Zalo / Tại quầy)
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'khach_hang_ten'  => 'required|string|max:255',
            'khach_hang_sdt'  => 'required|string|max:20',
            'chi_tiet'        => 'required|array|min:1',
            'chi_tiet.*.ten_phu_tung' => 'required|string',
            'chi_tiet.*.so_luong'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $maCode = 'YCBG-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            
            $phiVanChuyen = max(0, (float) $request->input('phi_van_chuyen', 0));
            $tienCocYeuCau = max(0, (float) $request->input('tien_coc_yeu_cau', 0));
            $daThu = max(0, (float) $request->input('da_thu', 0));

            $tongTienTamTinh = 0;
            foreach ($request->chi_tiet as $item) {
                $qty = (int) ($item['so_luong'] ?? 1);
                $price = max(0, (float) ($item['don_gia'] ?? $item['gia_ban'] ?? $item['price'] ?? 0));
                $tongTienTamTinh += ($qty * $price);
            }
            $grandTotal = $tongTienTamTinh + $phiVanChuyen;
            if ($tienCocYeuCau > ($grandTotal + 0.01)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền cọc yêu cầu không được vượt quá tổng giá trị đơn hàng (' . number_format($grandTotal, 0, ',', '.') . ' đ).'
                ], 422);
            }
            if ($daThu > ($grandTotal + 0.01)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền đã thu không được vượt quá tổng giá trị đơn hàng (' . number_format($grandTotal, 0, ',', '.') . ' đ).'
                ], 422);
            }
            
            // Trạng thái:
            // 0: Chờ báo giá
            // 1: Đã báo giá
            // 2: Đã cọc & xử lý
            $targetStatus = (int) $request->input('trang_thai', 0);
            if ($daThu > 0 && $targetStatus < 2) {
                $targetStatus = 2; // Đã thu cọc thì chuyển thẳng sang Đã Cọc & Xử Lý
            }

            $quote = YeuCauBaoGia::create([
                'ma_yeu_cau'         => $maCode,
                'loai_yeu_cau'       => 'quote',
                'khach_hang_ten'     => trim($request->khach_hang_ten),
                'khach_hang_sdt'     => trim($request->khach_hang_sdt),
                'khach_hang_email'   => $request->khach_hang_email ? trim($request->khach_hang_email) : null,
                'dia_chi_giao_hang'  => $request->dia_chi_giao_hang,
                'ghi_chu_khach_hang' => $request->ghi_chu_khach_hang,
                'ghi_chu_zalo'       => $request->ghi_chu_zalo ?? 'Admin lên đơn qua Zalo / Tại quầy',
                'tong_tien'          => 0,
                'phi_van_chuyen'     => $phiVanChuyen,
                'tien_coc_yeu_cau'   => $tienCocYeuCau,
                'da_thu'             => $daThu,
                'anh_chuyen_khoan'   => $request->anh_chuyen_khoan,
                'anh_chuyen_khoan_2' => $request->anh_chuyen_khoan_2,
                'ten_don_vi_van_chuyen' => $request->ten_don_vi_van_chuyen,
                'ma_van_don'         => $request->ma_van_don,
                'trang_thai'         => $targetStatus,
                'token'              => Str::random(32),
            ]);

            $tongTienTamTinh = 0;
            foreach ($request->chi_tiet as $item) {
                $qty = (int) ($item['so_luong'] ?? 1);
                $price = (float) ($item['don_gia'] ?? $item['gia_ban'] ?? $item['price'] ?? 0);
                $tenItem = trim($item['ten_phu_tung'] ?? $item['part_name'] ?? 'Phụ tùng');
                $maItem = trim($item['ma_phu_tung'] ?? $item['part_code'] ?? 'N/A');
                $modelMay = trim((string) ($item['model_may'] ?? $item['engine_model'] ?? ''));

                $tongTienTamTinh += ($qty * $price);

                ChiTietBaoGia::create([
                    'yeu_cau_bao_gia_id' => $quote->id,
                    'ten_phu_tung'       => $tenItem,
                    'ma_phu_tung'        => $maItem,
                    'model_may'          => $modelMay !== '' ? $modelMay : null,
                    'so_luong'           => $qty,
                    'don_gia'            => $price,
                ]);
            }

            $quote->tong_tien = $tongTienTamTinh;
            $quote->save();

            // Nếu đơn đã cọc (trang_thai = 2), tự động tạo bản ghi DonHang chính thức để quy trình PO Nhật / kho hoạt động trơn tru
            if ($targetStatus === 2) {
                $maDonHang = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $donHang = DonHang::create([
                    'ma_don_hang'           => $maDonHang,
                    'khach_hang_ten'        => $quote->khach_hang_ten,
                    'khach_hang_sdt'        => $quote->khach_hang_sdt,
                    'khach_hang_email'      => $quote->khach_hang_email,
                    'dia_chi_text'          => $quote->dia_chi_giao_hang,
                    'anh_chuyen_khoan'      => $quote->anh_chuyen_khoan,
                    'anh_chuyen_khoan_2'    => $quote->anh_chuyen_khoan_2,
                    'ten_don_vi_van_chuyen' => $quote->ten_don_vi_van_chuyen,
                    'ma_van_don'            => $quote->ma_van_don,
                    'ghi_chu_khach_hang'    => 'Báo giá #' . $quote->ma_yeu_cau . ($quote->ghi_chu_khach_hang ? ' | ' . $quote->ghi_chu_khach_hang : ''),
                    'tong_tien'             => $quote->tong_tien,
                    'phi_van_chuyen'        => $quote->phi_van_chuyen,
                    'tien_coc_yeu_cau'      => $quote->tien_coc_yeu_cau,
                    'da_thu'                => $daThu,
                    'loai_don'              => 'b2c',
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
                    if (!$idSanPham) {
                        $loaiSanPham = 'custom';
                    }

                    ChiTietDonHang::create([
                        'id_don_hang'   => $donHang->id,
                        'id_san_pham'   => $idSanPham,
                        'loai_san_pham' => $loaiSanPham,
                        'ten_phu_tung'  => $ct->ten_phu_tung,
                        'ma_phu_tung'   => $ct->ma_phu_tung,
                        'model_may'     => $ct->model_may,
                        'so_luong'      => $ct->so_luong,
                        'don_gia'       => $ct->don_gia,
                        'thanh_tien'    => $ct->so_luong * $ct->don_gia,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $targetStatus === 2 ? 'Lên đơn hàng thành công (Đã cọc & Xử lý)' : 'Tạo yêu cầu báo giá thành công',
                'data'    => $quote->load('chiTiet')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo đơn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'loai_yeu_cau'    => 'nullable|string|in:quote,order,bao_gia',
            'khach_hang_ten'  => 'required|string|max:255',
            'khach_hang_sdt'  => 'required|string|max:20',
            'chi_tiet'        => 'required|array|min:1|max:200',
            'chi_tiet.*.so_luong' => 'required|integer|min:1|max:10000',
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

            // Nhận diện khách hàng B2B vs B2C — CHỈ tin cậy auth('sanctum')->id(), cấm spoof user_id từ request
            $idKhachHang = null;
            $loaiDon = 'b2c';
            $khachTuTraCuoc = 0; // Mặc định tắt khách tự trả cước; Admin chủ động bật khi cần
            $verifiedUserId = auth('sanctum')->id();

            if ($verifiedUserId) {
                $kh = KhachHang::where('id_tai_khoan', $verifiedUserId)->first();
                if ($kh) {
                    $idKhachHang = $kh->id;
                }
                if ($this->isB2BUser($verifiedUserId)) {
                    $loaiDon = 'b2b';
                    $khachTuTraCuoc = 0; // Khách B2B: JPM tài trợ miễn phí cước nội địa
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
                    $price = 0; // Chỉ lấy giá authoritative từ DB, không tin cậy gia_ban từ client
                    $tenItem = $item['ten_phu_tung'] ?? 'Không rõ';
                    $maItem = $item['ma_phu_tung'] ?? 'N/A';
                    $modelMay = trim((string) ($item['model_may'] ?? $item['engine_model'] ?? ''));

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
                        'model_may'          => $modelMay !== '' ? $modelMay : null,
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

            if ($idempotencyKey) {
                try {
                    DB::table('idempotency_keys')
                        ->where('key', $idempotencyKey)
                        ->whereNull('response_body')
                        ->delete();
                } catch (\Throwable $ignored) {}
            }

            $resBody = [
                'success' => false,
                'message' => 'Lỗi xử lý yêu cầu. Vui lòng thử lại.'
            ];

            return response()->json($resBody, 500);
        }
    }

    /**
     * Admin: Danh sách toàn bộ yêu cầu báo giá
     */
    public function index(Request $request)
    {
        try {
            $query = YeuCauBaoGia::with('chiTiet')->orderBy('id', 'desc');

            if ($request->filled('search')) {
                $s = trim($request->input('search'));
                $query->where(function ($q) use ($s) {
                    $q->where('ma_yeu_cau', 'like', "%{$s}%")
                      ->orWhere('khach_hang_ten', 'like', "%{$s}%")
                      ->orWhere('khach_hang_sdt', 'like', "%{$s}%")
                      ->orWhere('khach_hang_email', 'like', "%{$s}%");
                });
            }

            if ($request->has('trang_thai') && $request->input('trang_thai') !== '' && $request->input('trang_thai') !== null) {
                $query->where('trang_thai', (int) $request->input('trang_thai'));
            }

            $hasPagination = $request->has('per_page') || $request->has('page');
            $all = $request->boolean('all', false) || !$hasPagination;
            $perPage = $all ? null : min(max((int) $request->input('per_page', 50), 1), 100);

            if ($all || !$perPage) {
                return response()->json([
                    'success' => true,
                    'data'    => $query->limit(500)->get()
                ]);
            }

            $paginated = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'data'    => $paginated->items(),
                'meta'    => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                ]
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
            $quote = YeuCauBaoGia::with('chiTiet')->findOrFail($id);

            $freightCollect = (bool) $request->input('freight_collect', $request->input('khach_tu_tra_cuoc', $quote->khach_tu_tra_cuoc));
            $shippingFee = $freightCollect ? 0 : max(0, (float) $request->input('shipping_fee', $request->input('phi_van_chuyen', $quote->phi_van_chuyen)));
            $depositRequired = (float) $request->input('deposit_required', $request->input('tien_coc_yeu_cau', $quote->tien_coc_yeu_cau));
            $requestedStatus = (int) $request->input('trang_thai', $quote->trang_thai);

            if ((int) $quote->trang_thai >= 1 && $requestedStatus !== 3) {
                $existingItems = $quote->chiTiet->keyBy('id');
                $itemsChanged = false;
                if ($request->has('chi_tiet') && is_array($request->input('chi_tiet'))) {
                    foreach ($request->input('chi_tiet') as $item) {
                        $itemId = $item['id'] ?? null;
                        $existing = $itemId ? $existingItems->get($itemId) : null;
                        if (!$existing) {
                            $itemsChanged = true;
                            break;
                        }
                        $reqQty = max(1, (int) ($item['qty'] ?? $item['so_luong'] ?? $existing->so_luong));
                        $reqPrice = max(0, (float) ($item['price'] ?? $item['don_gia'] ?? $existing->don_gia));
                        if ($reqQty !== (int) $existing->so_luong || abs($reqPrice - (float) $existing->don_gia) > 0.01) {
                            $itemsChanged = true;
                            break;
                        }
                    }
                }

                $shippingChanged = $request->hasAny(['shipping_fee', 'phi_van_chuyen'])
                    && abs($shippingFee - (float) $quote->phi_van_chuyen) > 0.01;
                $freightChanged = $request->hasAny(['freight_collect', 'khach_tu_tra_cuoc'])
                    && ($freightCollect !== (bool) $quote->khach_tu_tra_cuoc);
                $depositChanged = $request->hasAny(['deposit_required', 'tien_coc_yeu_cau'])
                    && abs($depositRequired - (float) $quote->tien_coc_yeu_cau) > 0.01;
                $totalChanged = $request->has('tong_tien')
                    && abs((float) $request->input('tong_tien') - (float) $quote->tong_tien) > 0.01;

                if ($itemsChanged || $shippingChanged || $freightChanged || $depositChanged || $totalChanged) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Báo giá đã phát hành cho khách, không thể chỉnh sửa đơn giá, phí vận chuyển hoặc tiền cọc.'
                    ], 422);
                }
            }

            if ($depositRequired < 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền cọc yêu cầu không được âm.'
                ], 422);
            }

            $itemsSubtotal = 0;
            if ($request->has('chi_tiet') && is_array($request->input('chi_tiet'))) {
                foreach ($request->input('chi_tiet') as $item) {
                    $qty = max(1, (int) ($item['qty'] ?? $item['so_luong'] ?? 1));
                    $price = max(0, (float) ($item['price'] ?? $item['don_gia'] ?? 0));
                    $itemsSubtotal += ($qty * $price);
                }
            } else {
                foreach ($quote->chiTiet as $ct) {
                    $itemsSubtotal += (max(1, (int) $ct->so_luong) * max(0, (float) $ct->don_gia));
                }
            }

            $grandTotal = $itemsSubtotal > 0
                ? ($itemsSubtotal + $shippingFee)
                : max(0, (float) $request->input('tong_tien', $quote->tong_tien));

            if ($depositRequired > ($grandTotal + 0.01)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền cọc yêu cầu không được vượt quá tổng giá trị đơn hàng (' . number_format($grandTotal, 0, ',', '.') . ' đ).'
                ], 422);
            }

            $quote->update([
                'tong_tien'         => $request->input('tong_tien', $quote->tong_tien),
                'trang_thai'        => $request->input('trang_thai', $quote->trang_thai),
                'ghi_chu_zalo'      => $request->input('ghi_chu_zalo', $quote->ghi_chu_zalo),
                'phi_van_chuyen'    => $shippingFee,
                'khach_tu_tra_cuoc' => $freightCollect,
                'tien_coc_yeu_cau'  => $depositRequired,
                'dia_chi_giao_hang' => $request->input('dia_chi_giao_hang', $request->input('address', $quote->dia_chi_giao_hang)),
                'anh_chuyen_khoan'  => $request->input('anh_chuyen_khoan', $request->input('receipt_image', $quote->anh_chuyen_khoan)),
                'anh_chuyen_khoan_2'=> $request->input('anh_chuyen_khoan_2', $request->input('receipt_image_2', $quote->anh_chuyen_khoan_2)),
            ]);

            // Cập nhật giá từng món hàng nếu có
            if ($request->has('chi_tiet') && is_array($request->input('chi_tiet'))) {
                foreach ($request->input('chi_tiet') as $item) {
                    $hasModelMay = array_key_exists('model_may', $item) || array_key_exists('engine_model', $item);
                    $modelMay = $hasModelMay ? trim((string) ($item['model_may'] ?? $item['engine_model'] ?? '')) : '';

                    if (isset($item['id']) && $item['id']) {
                        $updateData = [
                            'don_gia'  => (float) ($item['price'] ?? $item['don_gia'] ?? 0),
                            'so_luong' => (int) ($item['qty'] ?? $item['so_luong'] ?? 1),
                        ];
                        if ($hasModelMay) {
                            $updateData['model_may'] = $modelMay !== '' ? $modelMay : null;
                        }
                        ChiTietBaoGia::where('id', $item['id'])
                            ->where('yeu_cau_bao_gia_id', $quote->id)
                            ->update($updateData);
                    } elseif (!empty($item['part_name']) || !empty($item['ten_phu_tung'])) {
                        ChiTietBaoGia::create([
                            'yeu_cau_bao_gia_id' => $quote->id,
                            'ten_phu_tung'      => $item['part_name'] ?? $item['ten_phu_tung'] ?? '',
                            'ma_phu_tung'       => $item['part_code'] ?? $item['ma_phu_tung'] ?? null,
                            'model_may'         => $modelMay !== '' ? $modelMay : null,
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
            ])->header('Cache-Control', 'no-store, private');
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
                if ((int) $quote->trang_thai === 3) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Báo giá này đã đóng hoặc bị từ chối.'
                    ], 422);
                }

                // Kiểm tra nếu báo giá đã quá 72h khi đang chờ chốt thì không cho chốt giá cũ
                $validHours = 72;
                if ((int) $quote->trang_thai === 1 && $quote->updated_at && now()->diffInHours($quote->updated_at) > $validHours) {
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

                $quote->khach_da_chot = true;
                $quote->thoi_gian_khach_chot = $quote->thoi_gian_khach_chot ?? now();

                // Nếu yêu cầu đặt cọc (> 0): giữ trang_thai = 1 để hiện VietQR cọc, chưa tạo đơn ảo cho tới khi đối soát cọc
                if ((float) ($quote->tien_coc_yeu_cau ?? 0) > 0) {
                    $quote->save();
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'requires_deposit' => true,
                        'message' => 'Đã xác nhận chốt đơn! Vui lòng quét mã VietQR bên dưới để chuyển khoản đặt cọc.',
                        'data' => $quote->fresh('chiTiet')
                    ]);
                }

                // Nếu không yêu cầu cọc (0đ - COD 100%): tự động tạo DonHang Đang xử lý ngay
                $existingOrder = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')->first();
                if (!$existingOrder) {
                    $existingOrder = $this->createOrderFromQuote($quote, [
                        'da_thu' => 0,
                        'trang_thai' => 1,
                        'trang_thai_thanh_toan' => 0,
                        'deduct_stock' => true,
                    ]);
                }

                $quote->trang_thai = 2;
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'requires_deposit' => false,
                    'order_code' => $existingOrder->ma_don_hang,
                    'message' => 'Quý khách đã chốt đơn thành công! Đơn hàng #' . $existingOrder->ma_don_hang . ' đã được chuyển xuống kho xử lý.',
                    'data' => $quote->fresh('chiTiet')
                ]);
            } elseif ($action === 're_quote') {
                // Khách yêu cầu báo giá lại khi đơn cũ quá hạn
                $quote->trang_thai = 0; // Chuyển về Chờ báo giá
                $quote->khach_da_chot = false;
                $quote->thoi_gian_khach_chot = null;
                $quote->da_bao_chuyen_khoan = false;
                $quote->thoi_gian_bao_coc = null;
                $quote->updated_at = now();
                $quote->ghi_chu_khach_hang = ($quote->ghi_chu_khach_hang ? $quote->ghi_chu_khach_hang . ' | ' : '') . 'Khách yêu cầu cập nhật lại báo giá vào ' . now()->format('d/m/Y H:i');
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã gửi yêu cầu báo giá lại tới JPM Marine. Kỹ thuật viên sẽ kiểm tra lại giá và gửi bảng giá mới nhất qua Zalo cho quý khách.'
                ]);
            } elseif ($action === 'report_deposit') {
                if ((int) $quote->trang_thai === 3) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Yêu cầu báo giá này đã đóng hoặc hết hạn.'
                    ], 422);
                }

                $validHours = 72;
                if ((int) $quote->trang_thai === 1 && $quote->updated_at && now()->diffInHours($quote->updated_at) > $validHours) {
                    $quote->trang_thai = 3;
                    $quote->save();
                    DB::commit();

                    return response()->json([
                        'success' => false,
                        'is_expired' => true,
                        'message' => 'Báo giá này đã quá thời hạn giữ giá (' . $validHours . ' giờ). Vui lòng liên hệ JPM để cập nhật báo giá mới.'
                    ], 422);
                }

                if ((int) $quote->trang_thai >= 2 && (float) ($quote->da_thu ?? 0) > 0) {
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Báo giá này đã được xác nhận nhận cọc trước đó.',
                        'data' => $quote->fresh('chiTiet')
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
                    $oldReceipt = $quote->anh_chuyen_khoan;
                    $quote->anh_chuyen_khoan = $uploadedUrl;
                    if ($oldReceipt && $oldReceipt !== $uploadedUrl) {
                        ImageUploadService::delete($oldReceipt);
                    }
                }

                if (!$quote->khach_da_chot) {
                    $quote->khach_da_chot = true;
                    $quote->thoi_gian_khach_chot = $quote->thoi_gian_khach_chot ?? now();
                }
                $quote->da_bao_chuyen_khoan = true;
                $quote->thoi_gian_bao_coc = now();
                $quote->save();

                $linkedOrder = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')
                    ->where('trang_thai', 0)
                    ->first();
                if ($linkedOrder) {
                    if ($uploadedUrl) {
                        $linkedOrder->anh_chuyen_khoan = $uploadedUrl;
                    }
                    $linkedOrder->da_bao_chuyen_khoan = true;
                    $linkedOrder->thoi_gian_bao_coc = $quote->thoi_gian_bao_coc;
                    $linkedOrder->save();
                }

                DB::commit();

                try {
                    app(\App\Domain\Communication\Services\NotificationService::class)
                        ->notifyAdminDepositReported(
                            (string) $quote->ma_yeu_cau,
                            (string) ($quote->khach_hang_ten ?? 'Khách hàng'),
                            (string) ($quote->khach_hang_sdt ?? ''),
                            (float) ($quote->tien_coc_yeu_cau ?: $quote->tong_tien ?: 0),
                            (bool) ($uploadedUrl || $quote->anh_chuyen_khoan)
                        );
                } catch (\Throwable $ne) {
                    // Không làm gián đoạn phản hồi của khách nếu ghi log thông báo lỗi
                }

                return response()->json([
                    'success' => true,
                    'message' => 'JPM Marine đã tiếp nhận thông báo chuyển khoản. Kỹ thuật viên sẽ đối soát ngân hàng và lên đơn ngay.',
                    'data' => $quote->fresh('chiTiet')
                ]);
            } else {
                if ((int) $quote->trang_thai >= 2 || (float) ($quote->da_thu ?? 0) > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Đơn hàng đã được xác nhận lên đơn hoặc đã thu cọc, vui lòng liên hệ JPM để được hỗ trợ.'
                    ], 422);
                }

                $wasAccepted = (bool) $quote->khach_da_chot;
                $quote->trang_thai = 3; // Hủy
                $quote->da_bao_chuyen_khoan = false;
                $quote->thoi_gian_bao_coc = null;
                $quote->save();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $wasAccepted
                        ? 'Đã hủy đơn hàng thành công.'
                        : 'Đã từ chối báo giá. Đơn yêu cầu này đã được đóng lại.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper chuyển Yêu Cầu Báo Giá thành Đơn Hàng chính thức (dùng chung cho accept COD, convertToOrder, confirmDeposit)
     */
    private function createOrderFromQuote(YeuCauBaoGia $quote, array $options = []): DonHang
    {
        $idKhachHang = null;
        $loaiDon = 'b2c';
        if ($quote->user_id) {
            $kh = KhachHang::where('id_tai_khoan', $quote->user_id)->first();
            if ($kh) {
                $idKhachHang = $kh->id;
            }
            if ($this->isB2BUser((int) $quote->user_id)) {
                $loaiDon = 'b2b';
            }
        }

        $daThu = (float) ($options['da_thu'] ?? $quote->da_thu ?? 0);
        $trangThai = (int) ($options['trang_thai'] ?? ($daThu > 0 ? 1 : 0));
        $payStatus = (int) ($options['trang_thai_thanh_toan'] ?? ($daThu > 0 ? 1 : 0));
        $deductStock = (bool) ($options['deduct_stock'] ?? true);

        $maDonHang = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $donHang = DonHang::create([
            'ma_don_hang'           => $maDonHang,
            'id_khach_hang'         => $idKhachHang,
            'khach_hang_ten'        => $quote->khach_hang_ten,
            'khach_hang_sdt'        => $quote->khach_hang_sdt,
            'khach_hang_email'      => $quote->khach_hang_email,
            'dia_chi_text'          => $quote->dia_chi_giao_hang,
            'anh_chuyen_khoan'      => $quote->anh_chuyen_khoan,
            'anh_chuyen_khoan_2'    => $quote->anh_chuyen_khoan_2,
            'da_bao_chuyen_khoan'   => (bool) $quote->da_bao_chuyen_khoan,
            'thoi_gian_bao_coc'     => $quote->thoi_gian_bao_coc,
            'ghi_chu_khach_hang'    => 'Báo giá #' . $quote->ma_yeu_cau . ($quote->ghi_chu_khach_hang ? ' | ' . $quote->ghi_chu_khach_hang : '') . ($quote->ghi_chu_zalo ? ' | ' . $quote->ghi_chu_zalo : ''),
            'tong_tien'             => $quote->tong_tien,
            'phi_van_chuyen'        => $quote->phi_van_chuyen,
            'tien_coc_yeu_cau'      => $quote->tien_coc_yeu_cau,
            'da_thu'                => $daThu,
            'khach_tu_tra_cuoc'     => $quote->khach_tu_tra_cuoc,
            'loai_don'              => $loaiDon,
            'trang_thai'            => $trangThai,
            'trang_thai_thanh_toan' => $payStatus,
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

            if (!$idSanPham) {
                $idSanPham = null;
                $loaiSanPham = 'custom';
            } elseif ($deductStock) {
                \App\Domain\Sales\Services\OrderService::deductOrReserveStock(
                    (int) $idSanPham,
                    $loaiSanPham,
                    (int) $ct->so_luong,
                    (int) $donHang->id,
                    'Xuất bán từ báo giá #' . $quote->ma_yeu_cau
                );
            }

            ChiTietDonHang::create([
                'id_don_hang'   => $donHang->id,
                'id_san_pham'   => $idSanPham,
                'loai_san_pham' => $loaiSanPham,
                'ten_phu_tung'  => $ct->ten_phu_tung,
                'ma_phu_tung'   => $ct->ma_phu_tung,
                'model_may'     => $ct->model_may,
                'so_luong'      => $ct->so_luong,
                'don_gia'       => $ct->don_gia,
                'thanh_tien'    => $ct->so_luong * $ct->don_gia,
            ]);
        }

        return $donHang;
    }

    /**
     * Admin: Chuyển Yêu Cầu Báo Giá thành Đơn Hàng chính thức (Convert to Order)
     */
    public function convertToOrder($id)
    {
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::with('chiTiet')->lockForUpdate()->findOrFail($id);

            // Kiểm tra đã tồn tại đơn hàng được chuyển từ báo giá này chưa
            $alreadyConverted = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')->exists();
            if ($alreadyConverted || (int) $quote->trang_thai === 4) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu báo giá này đã được tạo thành đơn hàng trước đó.'
                ], 409);
            }

            $donHang = $this->createOrderFromQuote($quote, ['deduct_stock' => true]);

            // Đổi trạng thái báo giá sang Đã chuyển đơn (2)
            $quote->khach_da_chot = true;
            $quote->thoi_gian_khach_chot = $quote->thoi_gian_khach_chot ?? now();
            $quote->trang_thai = 2;
            $quote->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã chuyển đổi thành công sang Đơn hàng {$donHang->ma_don_hang}!",
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
            if ($request->filled('anh_chuyen_khoan')) {
                $quote->anh_chuyen_khoan = $request->input('anh_chuyen_khoan');
            } elseif ($request->filled('receipt_image')) {
                $quote->anh_chuyen_khoan = $request->input('receipt_image');
            }
            if ($request->filled('anh_chuyen_khoan_2')) {
                $quote->anh_chuyen_khoan_2 = $request->input('anh_chuyen_khoan_2');
            } elseif ($request->filled('receipt_image_2')) {
                $quote->anh_chuyen_khoan_2 = $request->input('receipt_image_2');
            }
            $amount = (float) $request->input('amount', $quote->tien_coc_yeu_cau);
            if ($amount <= 0 && $quote->tien_coc_yeu_cau > 0) {
                $amount = (float) $quote->tien_coc_yeu_cau;
            }

            $quote->da_thu = $amount;
            $quote->khach_da_chot = true;
            $quote->thoi_gian_khach_chot = $quote->thoi_gian_khach_chot ?? now();
            $quote->trang_thai = 2; // Đã chốt & đã cọc
            $quote->save();

            // Tự động tạo/cập nhật Đơn hàng chính thức để nhân viên lên đơn Nhật
            $existingOrder = DonHang::where('ghi_chu_khach_hang', 'like', '%Báo giá #' . $quote->ma_yeu_cau . '%')->first();
            if (!$existingOrder) {
                $this->createOrderFromQuote($quote, [
                    'da_thu' => $amount,
                    'trang_thai' => 1,
                    'trang_thai_thanh_toan' => 1,
                    'deduct_stock' => false,
                ]);
            } else {
                $existingOrder->da_thu = $amount;
                $existingOrder->trang_thai_thanh_toan = 1;
                $existingOrder->trang_thai = 1;
                if ($quote->dia_chi_giao_hang && !$existingOrder->dia_chi_text) {
                    $existingOrder->dia_chi_text = $quote->dia_chi_giao_hang;
                }
                if ($quote->anh_chuyen_khoan && !$existingOrder->anh_chuyen_khoan) {
                    $existingOrder->anh_chuyen_khoan = $quote->anh_chuyen_khoan;
                }
                if ($quote->anh_chuyen_khoan_2 && !$existingOrder->anh_chuyen_khoan_2) {
                    $existingOrder->anh_chuyen_khoan_2 = $quote->anh_chuyen_khoan_2;
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

    /**
     * Upload ảnh chụp màn hình chuyển khoản / biên lai (Hỗ trợ cả File upload và Base64 khi paste Ctrl+V)
     */
    public function uploadReceiptImage(Request $request)
    {
        try {
            $url = null;

            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120'
                ]);
                $url = ImageUploadService::storeUploadedFile(
                    $request->file('file'),
                    'uploads/receipts',
                    'receipt',
                    1600
                );
            } elseif ($request->filled('image_base64')) {
                $url = ImageUploadService::storeBase64(
                    (string) $request->input('image_base64'),
                    'uploads/receipts',
                    'receipt',
                    1600,
                    5 * 1024 * 1024
                );
            }

            if (!$url) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu hình ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, WebP tối đa 5MB).'], 422);
            }

            // Nếu truyền type & id, tự động gắn vào record và dọn ảnh cũ
            $type = $request->input('type');
            $id = $request->input('id');
            $slot = (int) $request->input('slot', 1);
            $field = ($slot === 2) ? 'anh_chuyen_khoan_2' : 'anh_chuyen_khoan';

            if ($type === 'quote' && $id) {
                $quote = YeuCauBaoGia::find($id);
                if ($quote) {
                    $old = $quote->{$field};
                    $quote->{$field} = $url;
                    $quote->save();
                    ImageUploadService::delete($old);
                }
            } elseif ($type === 'order' && $id) {
                $order = DonHang::find($id);
                if ($order) {
                    $old = $order->{$field};
                    $order->{$field} = $url;
                    $order->save();
                    ImageUploadService::delete($old);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tải lên ảnh chụp màn hình chuyển khoản thành công!',
                'url' => $url,
                'slot' => $slot
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'File ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, WebP tối đa 5MB).',
                'errors' => $e->errors(),
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi tải ảnh biên lai.'], 500);
        }
    }

    /**
     * Gỡ ảnh chụp màn hình chuyển khoản
     */
    public function removeReceiptImage(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $slot = (int) $request->input('slot', 1);
        $field = ($slot === 2) ? 'anh_chuyen_khoan_2' : 'anh_chuyen_khoan';

        if ($type === 'quote' && $id) {
            $quote = YeuCauBaoGia::find($id);
            if ($quote) {
                $old = $quote->{$field};
                $quote->{$field} = null;
                $quote->save();
                ImageUploadService::delete($old);
            }
        } elseif ($type === 'order' && $id) {
            $order = DonHang::find($id);
            if ($order) {
                $old = $order->{$field};
                $order->{$field} = null;
                $order->save();
                ImageUploadService::delete($old);
            }
        }
        return response()->json(['success' => true, 'message' => 'Đã gỡ ảnh biên lai']);
    }

    /**
     * Admin: Đánh dấu chưa nhận được tiền cọc sau khi khách báo đã chuyển khoản
     */
    public function rejectDepositReport($id)
    {
        DB::beginTransaction();
        try {
            $quote = YeuCauBaoGia::lockForUpdate()->findOrFail($id);
            if ((int) $quote->trang_thai >= 2 && (float) ($quote->da_thu ?? 0) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Báo giá này đã được xác nhận thu cọc trước đó.'
                ], 409);
            }

            $quote->da_bao_chuyen_khoan = false;
            $quote->thoi_gian_bao_coc = null;
            $quote->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã đặt lại trạng thái chờ khách chuyển cọc.',
                'data' => $quote->fresh('chiTiet')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }
}
