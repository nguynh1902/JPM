<?php

namespace App\Domain\Core\Services;

use App\Models\ThongBao;
use Illuminate\Database\Eloquent\Collection;

class ThongBaoService
{
    public function indexCuaTaiKhoan(int $idTaiKhoan): Collection
    {
        return ThongBao::where('id_tai_khoan', $idTaiKhoan)
            ->orderByDesc('created_at')
            ->get();
    }

    public function indexChuaDoc(int $idTaiKhoan): Collection
    {
        return ThongBao::where('id_tai_khoan', $idTaiKhoan)
            ->where('trang_thai_doc', false)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Gửi thông báo đến tài khoản.
     */
    public function guiThongBao(int $idTaiKhoan, string $tieuDe, string $noiDung, int $loaiThongBao = 1): ThongBao
    {
        return ThongBao::create([
            'id_tai_khoan'    => $idTaiKhoan,
            'tieu_de'         => $tieuDe,
            'noi_dung'        => $noiDung,
            'loai_thong_bao'  => $loaiThongBao,
            'trang_thai_doc'  => false,
        ]);
    }

    /**
     * Đánh dấu một thông báo đã đọc.
     */
    public function danhDauDaDoc(int $id): ThongBao
    {
        $thongBao = ThongBao::findOrFail($id);
        $thongBao->trang_thai_doc = true;
        $thongBao->save();
        return $thongBao;
    }

    /**
     * Đánh dấu toàn bộ thông báo của tài khoản là đã đọc.
     */
    public function danhDauTatCaDaDoc(int $idTaiKhoan): int
    {
        return ThongBao::where('id_tai_khoan', $idTaiKhoan)
            ->where('trang_thai_doc', false)
            ->update(['trang_thai_doc' => true]);
    }

    public function destroyThongBao(int $id): bool
    {
        return ThongBao::findOrFail($id)->delete();
    }
}
