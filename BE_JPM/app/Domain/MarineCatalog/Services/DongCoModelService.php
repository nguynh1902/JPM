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

    public function getAll()
    {
        return DongCoModel::with(['thuongHieu', 'loaiDongCo'])->orderBy('id', 'desc')->get();
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
            $deleted = $model->delete();
            $this->clearPublicCache();
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Lỗi khi xóa model: " . $e->getMessage());
        }
    }
}
