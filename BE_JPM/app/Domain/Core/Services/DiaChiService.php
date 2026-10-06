<?php

namespace App\Domain\Core\Services;

use App\Domain\IAM\Models\DiaChi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DiaChiService
{
    public function indexCuaKhachHang(int $idKhachHang): Collection
    {
        return DiaChi::where('user_id', $idKhachHang)->get();
    }

    public function show(int $id): DiaChi
    {
        return DiaChi::findOrFail($id);
    }

    /**
     * Thêm địa chỉ mới cho khách hàng.
     * Nếu đặt làm mặc định, tự động gỡ mặc định của các địa chỉ cũ.
     */
    public function themDiaChi(array $data): DiaChi
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['la_dia_chi_mac_dinh'])) {
                DiaChi::where('user_id', $data['user_id'])
                    ->update(['la_dia_chi_mac_dinh' => false]);
            }
            return DiaChi::create($data);
        });
    }

    /**
     * Cập nhật địa chỉ.
     * Nếu đặt làm mặc định, tự động gỡ mặc định của các địa chỉ cũ.
     */
    public function updateDiaChi(int $id, array $data): DiaChi
    {
        return DB::transaction(function () use ($id, $data) {
            $diaChi = $this->timTheoId($id);

            if (!empty($data['la_dia_chi_mac_dinh'])) {
                DiaChi::where('user_id', $diaChi->user_id)
                    ->where('dia_chi_id', '!=', $id)
                    ->update(['la_dia_chi_mac_dinh' => false]);
            }

            $diaChi->update($data);
            return $diaChi;
        });
    }

    public function destroyDiaChi(int $id): bool
    {
        return $this->timTheoId($id)->delete();
    }
}

