<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\DonHang;

class HistoryController extends Controller
{
    /**
     * Get quote history for current user
     */
    public function getQuotes(Request $request)
    {
        $user = $request->user();
        $khachHang = \App\Domain\IAM\Models\KhachHang::where('id_tai_khoan', $user->id)->first();
        
        $quotes = YeuCauBaoGia::with('chiTiet')
            ->where(function($q) use ($user, $khachHang) {
                $q->where('user_id', $user->id);
                if (!empty($user->email)) {
                    $q->orWhere('khach_hang_email', $user->email);
                }
                if (!empty($user->phone)) {
                    $q->orWhere('khach_hang_sdt', $user->phone);
                }
                if ($khachHang && !empty($khachHang->so_dien_thoai)) {
                    $q->orWhere('khach_hang_sdt', $khachHang->so_dien_thoai);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
            
        return response()->json($quotes);
    }
    
    /**
     * Get order history for current user
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();
        $khachHang = \App\Domain\IAM\Models\KhachHang::where('id_tai_khoan', $user->id)->first();
        
        $orders = DonHang::with(['chiTiet.phuTung', 'chiTiet.sanPham'])
            ->where(function($q) use ($user, $khachHang) {
                if ($khachHang) {
                    $q->where('id_khach_hang', $khachHang->id);
                }
                if (!empty($user->email)) {
                    $q->orWhere('khach_hang_email', $user->email);
                }
                if (!empty($user->phone)) {
                    $q->orWhere('khach_hang_sdt', $user->phone);
                }
                if ($khachHang && !empty($khachHang->so_dien_thoai)) {
                    $q->orWhere('khach_hang_sdt', $khachHang->so_dien_thoai);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orders as $order) {
            $items = [];
            foreach ($order->chiTiet as $detail) {
                $items[] = [
                    'id'            => $detail->id,
                    'id_san_pham'   => $detail->id_san_pham,
                    'id_phu_tung'   => $detail->id_phu_tung,
                    'ten_san_pham'  => $detail->sanPham->ten_san_pham ?? $detail->phuTung->ten_phu_tung ?? $detail->ten_phu_tung ?? 'Phụ tùng chính hãng',
                    'ma_phu_tung'   => $detail->phuTung->ma_phu_tung ?? $detail->ma_phu_tung ?? '',
                    'so_luong'      => $detail->so_luong,
                    'don_gia'       => $detail->don_gia,
                    'thanh_tien'    => $detail->thanh_tien ?? ($detail->so_luong * $detail->don_gia),
                ];
            }
            $order->items = $items;
        }
            
        return response()->json($orders);
    }
}
