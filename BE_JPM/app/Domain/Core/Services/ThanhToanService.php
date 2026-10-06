<?php

namespace App\Domain\Core\Services;

use App\Models\ThanhToan;
use App\Models\GiaoDichThanhToan;
use App\Domain\Sales\Models\DonHang;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThanhToanService
{
    public function index(): Collection
    {
        return ThanhToan::with(['donHang', 'phuongThucThanhToan'])->latest()->get();
    }

    public function show(int $id): ThanhToan
    {
        return ThanhToan::with(['donHang', 'phuongThucThanhToan', 'giaoDichThanhToans'])->findOrFail($id);
    }

    /**
     * Tạo bản ghi thanh toán cho đơn hàng.
     * Hỗ trợ cả thanh toán toàn phần và đặt cọc.
     *
     * @throws \Throwable
     */
    public function storeThanhToan(DonHang $donHang, array $data): ThanhToan
    {
        return DB::transaction(function () use ($donHang, $data) {
            $laDatCoc = !empty($data['la_dat_coc']);
            $tyLe = $data['ty_le_dat_coc'] ?? 0.3;

            $soTienDatCoc = $laDatCoc ? round($donHang->tong_thanh_toan * $tyLe, 0) : 0;
            $soTienConLai = $laDatCoc ? $donHang->tong_thanh_toan - $soTienDatCoc : 0;

            return ThanhToan::create([
                'id_don_hang'            => $donHang->id_don_hang,
                'id_phuong_thuc_thanh_toan' => $data['id_phuong_thuc_thanh_toan'],
                'ma_thanh_toan'          => 'TT' . date('YmdHis') . rand(10, 99),
                'tong_tien'              => $donHang->tong_thanh_toan,
                'la_dat_coc'             => $laDatCoc,
                'ty_le_dat_coc'          => $laDatCoc ? $tyLe : null,
                'so_tien_dat_coc'        => $soTienDatCoc,
                'so_tien_con_lai'        => $soTienConLai,
                'trang_thai_thanh_toan'  => 1, // 1 = Chờ thanh toán
            ]);
        });
    }

    /**
     * Xác nhận thanh toán thành công (nhận callback từ cổng thanh toán).
     * Cập nhật trạng thái ThanhToan và DonHang.
     *
     * @throws \Throwable
     */
    public function xacNhanThanhToan(int $idThanhToan, float $soTienGiaoDich, string $maGiaoDichDoiTac, ?array $phanHoiDoiTac = null): GiaoDichThanhToan
    {
        return DB::transaction(function () use ($idThanhToan, $soTienGiaoDich, $maGiaoDichDoiTac, $phanHoiDoiTac) {
            $thanhToan = $this->timTheoId($idThanhToan);

            $giaoDich = GiaoDichThanhToan::create([
                'id_thanh_toan'         => $idThanhToan,
                'ma_giao_dich_doi_tac'  => $maGiaoDichDoiTac,
                'so_tien'               => $soTienGiaoDich,
                'loai_giao_dich'        => 1, // 1 = Thanh toán
                'phan_hoi_tu_doi_tac'   => $phanHoiDoiTac ? json_encode($phanHoiDoiTac) : null,
                'ngay_giao_dich'        => now(),
                'trang_thai'            => 1, // 1 = Thành công
            ]);

            $tongDaThanhToan = GiaoDichThanhToan::where('id_thanh_toan', $idThanhToan)
                ->where('trang_thai', 1)
                ->sum('so_tien');

            if ($tongDaThanhToan >= $thanhToan->tong_tien) {
                $thanhToan->trang_thai_thanh_toan = 3; // 3 = Đã thanh toán toàn bộ
                $thanhToan->ngay_thanh_toan = now();
                $thanhToan->so_tien_con_lai = 0;

                $thanhToan->donHang()->update(['trang_thai_thanh_toan' => 3]);
            } elseif ($thanhToan->la_dat_coc && $tongDaThanhToan >= $thanhToan->so_tien_dat_coc) {
                $thanhToan->trang_thai_thanh_toan = 2; // 2 = Đã đặt cọc
                $thanhToan->so_tien_con_lai = $thanhToan->tong_tien - $tongDaThanhToan;
            }

            $thanhToan->save();

            Log::info('Xác nhận thanh toán', ['ma_thanh_toan' => $thanhToan->ma_thanh_toan, 'so_tien' => $soTienGiaoDich]);

            return $giaoDich;
        });
    }
}

