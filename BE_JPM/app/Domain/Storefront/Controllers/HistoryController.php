<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\DonHang;
use App\Domain\IAM\Models\KhachHang;

class HistoryController extends Controller
{
    /**
     * Get quote history for current user (strictly scoped to prevent cross-user IDOR)
     */
    public function getQuotes(Request $request)
    {
        $user = $request->user();

        $query = YeuCauBaoGia::with('chiTiet')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                // Chỉ ghép thêm báo giá khách vãng lai (user_id IS NULL) trùng email đã đăng ký của user
                if (!empty($user->email)) {
                    $q->orWhere(function ($guestQ) use ($user) {
                        $guestQ->whereNull('user_id')
                               ->where('khach_hang_email', $user->email);
                    });
                }
            })
            ->orderBy('id', 'desc');

        if ($request->has('per_page') || $request->has('page')) {
            $perPage = min(max((int) $request->input('per_page', 20), 1), 50);
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->limit(200)->get());
    }

    /**
     * Get order history for current user (strictly scoped to prevent cross-user IDOR)
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();
        $khachHang = KhachHang::where('id_tai_khoan', $user->id)->first();

        $query = DonHang::with(['chiTiet.phuTung', 'chiTiet.sanPham'])
            ->where(function ($q) use ($user, $khachHang) {
                if ($khachHang) {
                    $q->where('id_khach_hang', $khachHang->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
                // Chỉ ghép thêm đơn vãng lai (id_khach_hang IS NULL) trùng email tài khoản hiện tại
                if (!empty($user->email)) {
                    $q->orWhere(function ($guestQ) use ($user) {
                        $guestQ->whereNull('id_khach_hang')
                               ->where('khach_hang_email', $user->email);
                    });
                }
            })
            ->orderBy('id', 'desc');

        $isPaginated = $request->has('per_page') || $request->has('page');
        $orders = $isPaginated
            ? $query->paginate(min(max((int) $request->input('per_page', 20), 1), 50))
            : $query->limit(200)->get();

        $collection = $isPaginated ? $orders->getCollection() : $orders;

        foreach ($collection as $order) {
            $items = [];
            foreach ($order->chiTiet as $detail) {
                $isSanPham = ($detail->loai_san_pham === 'san_pham');
                $tenSanPham = $detail->ten_phu_tung
                    ?? ($isSanPham ? ($detail->sanPham->ten_san_pham ?? null) : ($detail->phuTung->ten_phu_tung ?? null))
                    ?? 'Phụ tùng chính hãng';
                $maPhuTung = $detail->ma_phu_tung
                    ?? ($isSanPham ? ($detail->sanPham->ma_san_pham ?? null) : ($detail->phuTung->ma_phu_tung ?? null))
                    ?? '';
                $hinhAnh = $isSanPham
                    ? ($detail->sanPham->hinh_anh ?? null)
                    : ($detail->phuTung->hinh_anh ?? null);
                $slug = $isSanPham
                    ? ($detail->sanPham->slug ?? null)
                    : null;

                $items[] = [
                    'id'            => $detail->id,
                    'id_san_pham'   => $detail->id_san_pham,
                    'id_phu_tung'   => $detail->id_phu_tung ?? ($isSanPham ? null : $detail->id_san_pham),
                    'loai_san_pham' => $detail->loai_san_pham ?? 'phu_tung',
                    'ten_san_pham'  => $tenSanPham,
                    'ma_phu_tung'   => $maPhuTung,
                    'hinh_anh'      => $hinhAnh,
                    'slug'          => $slug,
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
