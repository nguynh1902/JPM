<?php

namespace App\Domain\Core\Services;

use App\Models\GioHang;
use App\Models\ChiTietGioHang;
use Illuminate\Support\Facades\DB;

class GioHangService
{
    /**
     * Lấy giỏ hàng của khách hàng, tạo mới nếu chưa có.
     */
    public function layGioHangCuaKhachHang(int $idKhachHang): GioHang
    {
        return GioHang::firstOrCreate(
            ['user_id' => $idKhachHang]
        )->load('chiTiets.phuTung');
    }

    /**
     * Thêm phụ tùng vào giỏ hàng.
     * Nếu đã có thì cộng dồn số lượng.
     *
     * @throws \Throwable
     */
    public function themSanPhamVaoGioHang(int $idKhachHang, int $idPhuTung, int $soLuong): GioHang
    {
        return DB::transaction(function () use ($idKhachHang, $idPhuTung, $soLuong) {
            $gioHang = $this->layGioHangCuaKhachHang($idKhachHang);

            $chiTiet = ChiTietGioHang::where('gio_hang_id', $gioHang->gio_hang_id)
                ->where('phu_tung_id', $idPhuTung)
                ->first();

            if ($chiTiet) {
                $chiTiet->so_luong += $soLuong;
                $chiTiet->save();
            } else {
                ChiTietGioHang::create([
                    'gio_hang_id' => $gioHang->gio_hang_id,
                    'phu_tung_id' => $idPhuTung,
                    'so_luong'    => $soLuong,
                ]);
            }

            return $gioHang->load('chiTiets.phuTung');
        });
    }

    /**
     * Cập nhật số lượng phụ tùng trong giỏ.
     * Nếu số lượng <= 0 sẽ tự động xóa sản phẩm đó.
     *
     * @throws \Throwable
     */
    public function updateSoLuongSanPham(int $idKhachHang, int $idPhuTung, int $soLuongMoi): GioHang
    {
        return DB::transaction(function () use ($idKhachHang, $idPhuTung, $soLuongMoi) {
            if ($soLuongMoi <= 0) {
                return $this->xoaSanPhamKhoiGioHang($idKhachHang, $idPhuTung);
            }

            $gioHang = $this->layGioHangCuaKhachHang($idKhachHang);

            ChiTietGioHang::where('gio_hang_id', $gioHang->gio_hang_id)
                ->where('phu_tung_id', $idPhuTung)
                ->firstOrFail()
                ->update(['so_luong' => $soLuongMoi]);

            return $gioHang->load('chiTiets.phuTung');
        });
    }

    /**
     * Xóa một phụ tùng khỏi giỏ hàng.
     *
     * @throws \Throwable
     */
    public function destroySanPhamKhoiGioHang(int $idKhachHang, int $idPhuTung): GioHang
    {
        return DB::transaction(function () use ($idKhachHang, $idPhuTung) {
            $gioHang = $this->layGioHangCuaKhachHang($idKhachHang);

            ChiTietGioHang::where('gio_hang_id', $gioHang->gio_hang_id)
                ->where('phu_tung_id', $idPhuTung)
                ->delete();

            return $gioHang->load('chiTiets.phuTung');
        });
    }

    /**
     * Xóa sạch toàn bộ giỏ hàng (gọi sau khi đặt hàng thành công).
     *
     * @throws \Throwable
     */
    public function lamSachGioHang(int $idKhachHang): bool
    {
        return DB::transaction(function () use ($idKhachHang) {
            $gioHang = GioHang::where('user_id', $idKhachHang)->first();
            if ($gioHang) {
                ChiTietGioHang::where('gio_hang_id', $gioHang->gio_hang_id)->delete();
            }
            return true;
        });
    }
}
