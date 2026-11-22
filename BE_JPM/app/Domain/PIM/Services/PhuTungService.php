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

    public function getAll(array $filters = [], ?int $perPage = null)
    {
        $query = PhuTung::with(['nhomPhuTung', 'thuongHieu']);

        if (!empty($filters['keyword'])) {
            $kw = trim($filters['keyword']);
            $query->where(function ($q) use ($kw) {
                $q->where('ma_phu_tung', 'like', "%{$kw}%")
                  ->orWhere('ten_phu_tung', 'like', "%{$kw}%");
            });
        }

        if (!empty($filters['thuong_hieu_id'])) {
            $query->where('thuong_hieu_id', $filters['thuong_hieu_id']);
        }

        if (!empty($filters['nhom_phu_tung_id'])) {
            $query->where('nhom_phu_tung_id', $filters['nhom_phu_tung_id']);
        }

        if (isset($filters['trang_thai']) && $filters['trang_thai'] !== null && $filters['trang_thai'] !== '') {
            $query->where('trang_thai', (int) $filters['trang_thai']);
        }

        $query->orderBy('id', 'desc');

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
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
            // Vì PhuTung dùng SoftDeletes, KHÔNG xóa file ảnh vật lý trên ổ cứng và KHÔNG detach models()
            // để đơn hàng, báo giá, sơ đồ cũ không bị mất thông tin/hình ảnh.
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
