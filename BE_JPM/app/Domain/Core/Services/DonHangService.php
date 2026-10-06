<?php

namespace App\Domain\Core\Services;

use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Models\LichSuDonHang;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonHangService
{
    public function index(): Collection
    {
        return DonHang::with(['khachHang', 'nhanVien'])->latest()->get();
    }

    public function show(int $id): DonHang
    {
        return DonHang::with([
            'khachHang',
            'nhanVien',
            'diaChiGiaoHang',
            'phuongThucThanhToan',
            'donViVanChuyen',
            'chiTiets.phuTung',
            'lichSus',
        ])->findOrFail($id);
    }

    public function timTheoMa(string $maDonHang): DonHang
    {
        return DonHang::where('ma_don_hang', $maDonHang)->firstOrFail();
    }

    /**
     * Tạo đơn hàng mới.
     * Nhận $chiTiets gồm: [['phu_tung_id', 'so_luong', 'don_gia']...]
     *
     * @throws \Throwable
     */
    public function storeDonHang(array $data, array $chiTiets): DonHang
    {
        return DB::transaction(function () use ($data, $chiTiets) {
            $data['ma_don_hang'] = 'DH' . date('YmdHis') . rand(10, 99);
            $data['ngay_dat_hang'] = now();

            $tongTienHang = collect($chiTiets)->sum(fn($item) => $item['don_gia'] * $item['so_luong']);
            $thueVat = $tongTienHang * 0.1;
            $phiVanChuyen = $data['phi_van_chuyen'] ?? 0;
            $giamGia = $data['giam_gia'] ?? 0;

            $data['tong_tien_hang'] = $tongTienHang;
            $data['thue_vat'] = $thueVat;
            $data['tong_thanh_toan'] = $tongTienHang + $thueVat + $phiVanChuyen - $giamGia;

            $donHang = DonHang::create($data);

            foreach ($chiTiets as $item) {
                ChiTietDonHang::create([
                    'id_don_hang' => $donHang->id_don_hang,
                    'phu_tung_id' => $item['phu_tung_id'],
                    'so_luong'    => $item['so_luong'],
                    'don_gia'     => $item['don_gia'],
                    'thanh_tien'  => $item['don_gia'] * $item['so_luong'],
                    'ghi_chu'     => $item['ghi_chu'] ?? null,
                ]);
            }

            $this->ghiLichSuDonHang($donHang->id_don_hang, $data['trang_thai_don_hang'], 'Đơn hàng được tạo mới.', $data['user_id'] ?? null);

            Log::info('Tạo đơn hàng thành công', ['ma_don_hang' => $donHang->ma_don_hang]);

            return $donHang->load('chiTiets');
        });
    }

    /**
     * Cập nhật trạng thái đơn hàng.
     * Controller gọi Service này; Service không gọi ThanhToanService hay ThongBaoService ở đây.
     *
     * @throws \Throwable
     */
    public function doiTrangThaiDonHang(int $id, int $trangThaiMoi, ?string $ghiChu = null, ?int $idNguoiThaoTac = null): DonHang
    {
        return DB::transaction(function () use ($id, $trangThaiMoi, $ghiChu, $idNguoiThaoTac) {
            $donHang = $this->timTheoId($id);
            $donHang->trang_thai_don_hang = $trangThaiMoi;
            $donHang->save();

            $this->ghiLichSuDonHang($id, $trangThaiMoi, $ghiChu ?? 'Cập nhật trạng thái đơn hàng.', $idNguoiThaoTac);

            return $donHang;
        });
    }

    /**
     * Hủy đơn hàng.
     *
     * @throws \Throwable
     */
    public function huyDonHang(int $id, string $lyDo, int $idNguoiThaoTac): DonHang
    {
        return DB::transaction(function () use ($id, $lyDo, $idNguoiThaoTac) {
            $donHang = $this->timTheoId($id);
            $donHang->trang_thai_don_hang = 0; // 0 = Đã hủy
            $donHang->save();

            $this->ghiLichSuDonHang($id, 0, "Hủy đơn hàng: {$lyDo}", $idNguoiThaoTac);

            Log::warning('Hủy đơn hàng', ['ma_don_hang' => $donHang->ma_don_hang, 'ly_do' => $lyDo]);

            return $donHang;
        });
    }

    /**
     * Tính tổng tiền đơn hàng dựa trên chi tiết.
     */
    public function tinhTongTien(array $chiTiets, float $phiVanChuyen = 0, float $giamGia = 0): array
    {
        $tongTienHang = collect($chiTiets)->sum(fn($item) => $item['don_gia'] * $item['so_luong']);
        $thueVat = $tongTienHang * 0.1;
        $tongThanhToan = $tongTienHang + $thueVat + $phiVanChuyen - $giamGia;

        return [
            'tong_tien_hang'  => $tongTienHang,
            'thue_vat'        => $thueVat,
            'phi_van_chuyen'  => $phiVanChuyen,
            'giam_gia'        => $giamGia,
            'tong_thanh_toan' => $tongThanhToan,
        ];
    }

    /**
     * Ghi lịch sử trạng thái đơn hàng.
     */
    private function ghiLichSuDonHang(int $idDonHang, int $trangThai, string $ghiChu, ?int $nguoiTao): LichSuDonHang
    {
        return LichSuDonHang::create([
            'id_don_hang'         => $idDonHang,
            'trang_thai_don_hang' => $trangThai,
            'ghi_chu'             => $ghiChu,
            'nguoi_tao'           => $nguoiTao,
        ]);
    }
}

