<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\DonHang;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\CMS\Models\SanPham;

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

        $isPaginated = $request->has('per_page') || $request->has('page');
        $quotes = $isPaginated
            ? $query->paginate(min(max((int) $request->input('per_page', 20), 1), 50))
            : $query->limit(200)->get();

        $collection = $isPaginated ? $quotes->getCollection() : $quotes;

        $codes = $collection->flatMap(function ($quote) {
            return $quote->chiTiet->pluck('ma_phu_tung');
        })
            ->map(fn($c) => trim((string) $c))
            ->filter(fn($c) => $c !== '' && strtoupper($c) !== 'N/A')
            ->unique()
            ->values();

        $phuTungs = $codes->isNotEmpty()
            ? PhuTung::whereIn('ma_phu_tung', $codes)->get(['id', 'ma_phu_tung', 'hinh_anh'])->keyBy('ma_phu_tung')
            : collect();

        $sanPhams = $codes->isNotEmpty()
            ? SanPham::whereIn('ma_san_pham', $codes)->get(['id', 'ma_san_pham', 'hinh_anh_chinh', 'slug'])->keyBy('ma_san_pham')
            : collect();

        foreach ($collection as $quote) {
            foreach ($quote->chiTiet as $detail) {
                $code = trim((string) ($detail->ma_phu_tung ?? ''));
                $sp = $code !== '' ? $sanPhams->get($code) : null;
                $pt = $code !== '' ? $phuTungs->get($code) : null;

                $detail->setAttribute('loai_san_pham', $sp ? 'san_pham' : 'phu_tung');
                $detail->setAttribute('id_san_pham', $sp ? $sp->id : null);
                $detail->setAttribute('id_phu_tung', $pt ? $pt->id : null);
                $detail->setAttribute('slug', $sp ? $sp->slug : null);
                $detail->setAttribute('hinh_anh', $sp ? ($sp->hinh_anh_chinh ?? null) : ($pt->hinh_anh ?? null));
            }
        }

        return response()->json($quotes);
    }

    /**
     * Get order history for current user (strictly scoped to prevent cross-user IDOR)
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();
        $khachHang = KhachHang::where('id_tai_khoan', $user->id)->first();

        $query = DonHang::with(['chiTiet.phuTung', 'chiTiet.sanPham', 'diaChi', 'khachHang'])
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
                    ? ($detail->sanPham->hinh_anh_chinh ?? $detail->sanPham->hinh_anh ?? null)
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
                    'model_may'     => $detail->model_may ?? null,
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
