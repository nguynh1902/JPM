<?php

namespace App\Domain\MarineCatalog\Services;

use App\Domain\MarineCatalog\Models\ThuongHieu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class ThuongHieuService
{
    private function clearPublicCache()
    {
        Cache::forget('public_thuong_hieu');
    }

    public function getAll()
    {
        return ThuongHieu::orderBy('id', 'desc')->get();
    }

    public function getById(int $id): ThuongHieu
    {
        return ThuongHieu::findOrFail($id);
    }

    public function create(array $data): ThuongHieu
    {
        DB::beginTransaction();
        try {
            if (empty($data['code'])) {
                $data['code'] = 'TMP_' . time() . uniqid();
            }
            $brand = ThuongHieu::create($data);
            
            if (str_starts_with($brand->code, 'TMP_')) {
                $brand->update(['code' => 'TH' . str_pad($brand->id, 2, '0', STR_PAD_LEFT)]);
            }
            
            $this->clearPublicCache();
            DB::commit();
            return $brand;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): ThuongHieu
    {
        DB::beginTransaction();
        try {
            $thuongHieu = $this->getById($id);
            
            // If updating with a new logo, delete the old logo physically
            if (isset($data['logo_path']) && $thuongHieu->logo_path) {
                Storage::disk('public')->delete($thuongHieu->logo_path);
            }

            $thuongHieu->update($data);
            $this->clearPublicCache();
            DB::commit();
            return $thuongHieu;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $brand = $this->getById($id);
            
            if ($brand->dongCoModels()->exists()) {
                throw new \Exception('Không thể xóa! Thương hiệu này đang có Model Động Cơ trực thuộc.');
            }

            if ($brand->phuTungs()->exists()) {
                throw new \Exception('Không thể xóa! Thương hiệu này đang có Phụ Tùng trực thuộc.');
            }

            // Delete logo physical file if exists
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }

            $deleted = $brand->delete();
            $this->clearPublicCache();
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
