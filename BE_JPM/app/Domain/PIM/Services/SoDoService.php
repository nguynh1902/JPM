<?php

namespace App\Domain\PIM\Services;

use App\Domain\PIM\Models\SoDo;
use App\Domain\PIM\Models\ChiTietSoDo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SoDoService
{
    public function index(): Collection
    {
        return SoDo::with(['dongXe', 'phienBan'])->get();
    }

    public function show(int $id): SoDo
    {
        return SoDo::with(['dongXe', 'phienBan', 'chiTiets.phuTung'])->findOrFail($id);
    }

    public function store(array $data): SoDo
    {
        return SoDo::create($data);
    }

    public function update(int $id, array $data): SoDo
    {
        $soDo = SoDo::findOrFail($id);
        $soDo->update($data);
        return $soDo;
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $soDo = SoDo::findOrFail($id);
            // Xóa chi tiết liên quan trước khi xóa sơ đồ
            ChiTietSoDo::where('so_do_id', $soDo->id)->delete();
            return $soDo->delete();
        });
    }

    /**
     * Thêm vị trí phụ tùng lên sơ đồ
     */
    public function themChiTietSoDo(array $data): ChiTietSoDo
    {
        return ChiTietSoDo::create($data);
    }

    /**
     * Cập nhật vị trí phụ tùng trên sơ đồ
     */
    public function capNhatChiTietSoDo(int $idChiTiet, array $data): ChiTietSoDo
    {
        $chiTiet = ChiTietSoDo::findOrFail($idChiTiet);
        $chiTiet->update($data);
        return $chiTiet;
    }

    /**
     * Xóa vị trí phụ tùng khỏi sơ đồ
     */
    public function xoaChiTietSoDo(int $idChiTiet): bool
    {
        return ChiTietSoDo::findOrFail($idChiTiet)->delete();
    }
}

