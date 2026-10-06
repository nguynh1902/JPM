<?php

namespace App\Domain\PIM\Services;

use App\Domain\CMS\Models\SanPham;
use Exception;

class SanPhamService
{
    public function getAll()
    {
        return SanPham::with('danhMuc')->orderBy('id', 'desc')->get();
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
            return $sanPham->delete();
        } catch (Exception $e) {
            throw new Exception("Lỗi khi xóa sản phẩm: " . $e->getMessage());
        }
    }
}

