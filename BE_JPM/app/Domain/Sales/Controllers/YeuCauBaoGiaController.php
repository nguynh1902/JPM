<?php

namespace App\Domain\Sales\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\ChiTietBaoGia;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class YeuCauBaoGiaController extends Controller
{
    public function publicStore(Request $request)
    {
        $request->validate([
            'user_id'         => 'nullable|integer',
            'loai_yeu_cau'    => 'nullable|string|in:quote,order,bao_gia',
            'khach_hang_ten'  => 'required|string|max:255',
            'khach_hang_sdt'  => 'required|string|max:20',
            'chi_tiet'        => 'required|array|min:1',
            'chi_tiet.*.id_phu_tung'  => 'nullable|integer',
            'chi_tiet.*.so_luong'     => 'required|integer|min:1',
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
                
                // C? g?ng insert d? ch?ng race condition
                DB::table('idempotency_keys')->insert([
                    'key' => $idempotencyKey,
                    'request_hash' => $requestHash,
                    'status_code' => 200, // s? update sau
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // N?u l?i constraint, c nghia l d c request khc insert
                return response()->json(['success' => false, 'message' => 'Concurrent request detected'], 409);
            }
        }

        DB::beginTransaction();
        try {
            $loaiYeuCau = $request->input('loai_yeu_cau', 'bao_gia');
            
            if ($loaiYeuCau === 'order') {
                $maCode = 'DH-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $donHang = DonHang::create([
                    'ma_don_hang' => $maCode,
                    'id_khach_hang' => $request->user_id ? \App\Domain\IAM\Models\KhachHang::where('id_tai_khoan', $request->user_id)->value('id') : null,
                    'khach_hang_ten' => trim($request->khach_hang_ten),
                    'khach_hang_sdt' => trim($request->khach_hang_sdt),
                    'khach_hang_email' => trim($request->khach_hang_email),
                    'dia_chi_text' => $request->dia_chi_giao_hang,
                    'ghi_chu_khach_hang' => $request->ghi_chu_khach_hang,
                    'tong_tien' => 0,
                    'loai_don' => 'b2c',
                    'trang_thai' => 0,
                    'trang_thai_thanh_toan' => 0,
                ]);

                $orderService = new OrderService();
                $processedData = $orderService->processItemsAndInventory($request->chi_tiet, $donHang->id, true);

                foreach ($processedData['items'] as $itemData) {
                    \App\Domain\Sales\Models\ChiTietDonHang::create($itemData);
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
                    'trang_thai'         => 0,
                    'user_id'            => $request->user_id,
                    'token'              => \Illuminate\Support\Str::random(32),
                ]);

                foreach ($request->chi_tiet as $item) {
                    ChiTietBaoGia::create([
                        'yeu_cau_bao_gia_id' => $quote->id,
                        'ten_phu_tung'       => $item['ten_phu_tung'] ?? 'Không rõ',
                        'ma_phu_tung'        => $item['ma_phu_tung'] ?? 'N/A',
                        'so_luong'           => $item['so_luong'],
                        'don_gia'            => 0
                    ]);
                }
                
                $responseCode = $maCode;
            }

            DB::commit();

            $resBody = [
                'success' => true,
                'message' => 'G?i yu c?u thnh cng',
                'code'    => $responseCode
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
            if (str_contains($e->getMessage(), 'Khng d? s? lu?ng') || str_contains($e->getMessage(), 'Khng th? h?y')) {
                $status = 409; // Conflict
            } elseif (str_contains($e->getMessage(), 'Khng tm th?y') || str_contains($e->getMessage(), 'khng h?p l?')) {
                $status = 422; // Unprocessable
            }
            
            $resBody = [
                'success' => false,
                'message' => 'L?i x? ly: ' . $e->getMessage()
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
}
