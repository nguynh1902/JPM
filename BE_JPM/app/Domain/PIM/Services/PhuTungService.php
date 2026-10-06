<?php

namespace App\Domain\PIM\Services;

use App\Domain\PIM\Models\PhuTung;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Exception;

class PhuTungService
{
    private function clearCache(): void
    {
        Cache::forget('public_phu_tungs');
    }

    public function getAll()
    {
        return PhuTung::with(['nhomPhuTung', 'thuongHieu'])->orderBy('id', 'desc')->get();
    }

    public function getById($id): PhuTung
    {
        $item = PhuTung::with(['nhomPhuTung', 'thuongHieu'])->find($id);
        if (!$item) throw new Exception('Không tìm thấy phụ tùng.');
        return $item;
    }

    public function create(array $data): PhuTung
    {
        DB::beginTransaction();
        try {
            $phuTung = PhuTung::create($data);
            $this->clearCache();
            DB::commit();
            return $phuTung->load(['nhomPhuTung', 'thuongHieu']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Lỗi khi thêm phụ tùng: ' . $e->getMessage());
        }
    }

    public function update($id, array $data): PhuTung
    {
        DB::beginTransaction();
        try {
            $phuTung = $this->getById($id);
            // Delete old main image if replaced
            if (isset($data['hinh_anh']) && $phuTung->hinh_anh && $phuTung->hinh_anh !== $data['hinh_anh']) {
                $oldPath = str_replace('/storage/', '', $phuTung->hinh_anh);
                Storage::disk('public')->delete($oldPath);
            }
            $phuTung->update($data);
            $this->clearCache();
            DB::commit();
            return $phuTung->load(['nhomPhuTung', 'thuongHieu']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Lỗi khi cập nhật phụ tùng: ' . $e->getMessage());
        }
    }

    public function delete($id): bool
    {
        DB::beginTransaction();
        try {
            $phuTung = $this->getById($id);
            // Delete physical files
            if ($phuTung->hinh_anh) {
                $path = str_replace('/storage/', '', $phuTung->hinh_anh);
                Storage::disk('public')->delete($path);
            }
            if ($phuTung->thu_vien_anh && is_array($phuTung->thu_vien_anh)) {
                foreach ($phuTung->thu_vien_anh as $img) {
                    $path = str_replace('/storage/', '', $img);
                    Storage::disk('public')->delete($path);
                }
            }
            $deleted = $phuTung->delete();
            $this->clearCache();
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Lỗi khi xóa phụ tùng: ' . $e->getMessage());
        }
    }
}
