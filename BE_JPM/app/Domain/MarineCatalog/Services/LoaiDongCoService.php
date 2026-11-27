<?php

namespace App\Domain\MarineCatalog\Services;

use App\Domain\MarineCatalog\Models\LoaiDongCo;
use Illuminate\Support\Facades\Cache;
use Exception;

class LoaiDongCoService
{
    private function clearPublicCache()
    {
        app(CatalogCacheService::class)->clearAllCatalogCache();
    }

    public function getAll()
    {
        return LoaiDongCo::orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        try {
            if (empty($data['ma_loai_dong_co'])) {
                $data['ma_loai_dong_co'] = 'TMP_LDC_' . time() . uniqid();
            }
            $loaiDongCo = LoaiDongCo::create($data);
            
            if (str_starts_with($loaiDongCo->ma_loai_dong_co, 'TMP_LDC_')) {
                $loaiDongCo->update(['ma_loai_dong_co' => 'LDC' . str_pad($loaiDongCo->id, 2, '0', STR_PAD_LEFT)]);
            }
            
            $this->clearPublicCache();
            return $loaiDongCo;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi thêm loại động cơ: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        $loaiDongCo = LoaiDongCo::find($id);
        if (!$loaiDongCo) {
            throw new Exception("Không tìm thấy loại động cơ.");
        }
        return $loaiDongCo;
    }

    public function update($id, array $data)
    {
        try {
            $loaiDongCo = $this->getById($id);
            $loaiDongCo->update($data);
            $this->clearPublicCache();
            return $loaiDongCo;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi cập nhật loại động cơ: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $loaiDongCo = $this->getById($id);
            if ($loaiDongCo->dongCoModels()->exists()) {
                throw new Exception("Không thể xóa! Loại động cơ này đang được sử dụng trong các Model động cơ.");
            }
            $deleted = $loaiDongCo->delete();
            $this->clearPublicCache();
            return $deleted;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi xóa loại động cơ: " . $e->getMessage());
        }
    }
}
