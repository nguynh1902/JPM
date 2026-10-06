<?php

namespace App\Domain\Core\Services;

use App\Models\TuongThichPhuongTien;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FitmentService
{
    /**
     * Lấy danh sách xe tương thích của một phụ tùng
     */
    public function indexTuongThich(int $idPhuTung): Collection
    {
        return TuongThichPhuongTien::where('phu_tung_id', $idPhuTung)
            ->with(['dongXe', 'phienBan', 'dongCo'])
            ->get();
    }

    /**
     * Tìm phụ tùng tương thích với xe cụ thể
     */
    public function timPhuTungTuongThichVoiXe(int $idVehicleModel, ?int $idPhienBan = null, ?int $idDongCo = null): Collection
    {
        $query = TuongThichPhuongTien::with('phuTung')
            ->where('model_id', $idVehicleModel);

        if ($idPhienBan) {
            $query->where('id_phien_ban', $idPhienBan);
        }

        if ($idDongCo) {
            $query->where('id_dong_co', $idDongCo);
        }

        return $query->get();
    }

    /**
     * Kiểm tra phụ tùng có tương thích với xe không
     */
    public function kiemTraTuongThich(int $idPhuTung, int $idVehicleModel, ?int $idPhienBan = null): bool
    {
        $query = TuongThichPhuongTien::where('phu_tung_id', $idPhuTung)
            ->where('model_id', $idVehicleModel);

        if ($idPhienBan) {
            $query->where('id_phien_ban', $idPhienBan);
        }

        return $query->exists();
    }

    /**
     * Thêm xe tương thích cho phụ tùng
     */
    public function themTuongThich(array $data): TuongThichPhuongTien
    {
        return TuongThichPhuongTien::create($data);
    }

    /**
     * Xóa bản ghi tương thích
     */
    public function destroyTuongThich(int $id): bool
    {
        return TuongThichPhuongTien::findOrFail($id)->delete();
    }
}
