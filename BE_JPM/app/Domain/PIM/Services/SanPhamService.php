<?php

namespace App\Domain\PIM\Services;

use App\Domain\CMS\Models\SanPham;
use App\Domain\Core\Services\ImageUploadService;
use Exception;

class SanPhamService
{
    public function getAll(array $filters = [], ?int $perPage = null)
    {
        $query = SanPham::with('danhMuc')->orderBy('id', 'desc');

        if (!empty($filters['keyword'])) {
            $kw = trim($filters['keyword']);
            $query->where(function ($q) use ($kw) {
                $q->where('ten_san_pham', 'like', "%{$kw}%")
                  ->orWhere('ma_san_pham', 'like', "%{$kw}%");
            });
        }

        if (!empty($filters['danh_muc_id'])) {
            $query->where('danh_muc_san_pham_id', $filters['danh_muc_id']);
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function create(array $data)
    {
        try {
            return SanPham::create($data);
        } catch (Exception $e) {
            throw new Exception("Lỗi khi thêm sản phẩm: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        $sanPham = SanPham::with('danhMuc')->find($id);
        if (!$sanPham) {
            throw new Exception("Không tìm thấy sản phẩm.");
        }
        return $sanPham;
    }

    public function update($id, array $data)
    {
        try {
            $sanPham = $this->getById($id);
            $sanPham->update($data);
            return $sanPham->fresh('danhMuc');
        } catch (Exception $e) {
            throw new Exception("Lỗi khi cập nhật sản phẩm: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sanPham = $this->getById($id);
            $oldMain = $sanPham->hinh_anh_chinh;
            $oldGallery = is_array($sanPham->thu_vien_anh) ? $sanPham->thu_vien_anh : (json_decode((string) $sanPham->thu_vien_anh, true) ?? []);
            $deleted = $sanPham->delete();

            if ($deleted) {
                ImageUploadService::delete($oldMain);
                foreach ($oldGallery as $path) {
                    ImageUploadService::delete((string) $path);
                }
            }
            return $deleted;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi xóa sản phẩm: " . $e->getMessage());
        }
    }
}

