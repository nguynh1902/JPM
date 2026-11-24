<?php

namespace App\Domain\MarineCatalog\Services;

use App\Domain\MarineCatalog\Models\DongCoModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class DongCoModelService
{
    private function clearPublicCache()
    {
        Cache::forget('public_dong_co_models');
    }

    public function getAll(array $filters = [], ?int $perPage = null)
    {
        $query = DongCoModel::with(['thuongHieu', 'loaiDongCo']);

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('ten_model', 'like', "%{$s}%")
                  ->orWhere('ma_model', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['thuong_hieu_id'])) {
            $query->where('thuong_hieu_id', $filters['thuong_hieu_id']);
        }

        if (!empty($filters['loai_dong_co_id'])) {
            $query->where('loai_dong_co_id', $filters['loai_dong_co_id']);
        }

        if (!empty($filters['nam_san_xuat'])) {
            $query->where('nam_san_xuat', $filters['nam_san_xuat']);
        }

        $query->orderBy('id', 'desc');

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $model = DongCoModel::create($data);
            $this->clearPublicCache();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Lỗi khi thêm model: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        $model = DongCoModel::with(['thuongHieu', 'loaiDongCo'])->find($id);
        if (!$model) {
            throw new Exception("Không tìm thấy model.");
        }
        return $model;
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $model = $this->getById($id);
            $model->update($data);
            $this->clearPublicCache();
            DB::commit();
            return $this->getById($id); // Reload relations
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Lỗi khi cập nhật model: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $model = $this->getById($id);
            if ($model->phuTungs()->exists()) {
                throw new Exception("Không thể xóa! Model này đang được sử dụng bởi các Phụ tùng.");
            }
            if ($model->soDoPhuTungs()->exists()) {
                throw new Exception("Không thể xóa! Model này đang được sử dụng bởi Sơ đồ phụ tùng.");
            }
            $deleted = $model->delete();
            $this->clearPublicCache();
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
