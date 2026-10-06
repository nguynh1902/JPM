<?php

namespace App\Domain\PIM\Services;

use App\Domain\CMS\Models\DanhMucSanPham;
use Exception;

class DanhMucSanPhamService
{
    public function getAll()
    {
        // Trả về phẳng, FE buildTree - newest first theo chuẩn hệ thống
        return DanhMucSanPham::orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        try {
            // Tự sinh mã dựa trên số lớn nhất hiện có trong ma_danh_muc
            if (empty($data['ma_danh_muc'])) {
                $lastCode = DanhMucSanPham::where('ma_danh_muc', 'like', 'DM%')
                    ->orderByRaw('CAST(SUBSTRING(ma_danh_muc, 3) AS UNSIGNED) DESC')
                    ->value('ma_danh_muc');
                $nextNum = $lastCode ? ((int) preg_replace('/\D/', '', $lastCode)) + 1 : 1;
                $data['ma_danh_muc'] = 'DM' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
            }
            return DanhMucSanPham::create($data);
        } catch (Exception $e) {
            throw new Exception("Lỗi khi thêm danh mục: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        $danhMuc = DanhMucSanPham::find($id);
        if (!$danhMuc) {
            throw new Exception("Không tìm thấy danh mục.");
        }
        return $danhMuc;
    }

    public function update($id, array $data)
    {
        try {
            $danhMuc = $this->getById($id);
            // Ngăn tự chọn mình làm danh mục cha
            if (!empty($data['parent_id']) && $data['parent_id'] == $id) {
                throw new Exception("Danh mục không thể là cha của chính nó.");
            }
            $danhMuc->update($data);
            return $danhMuc;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi cập nhật danh mục: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $danhMuc = $this->getById($id);
            // RESTRICT: Không cho xóa nếu còn danh mục con
            if ($danhMuc->children()->exists()) {
                throw new Exception("Không thể xóa danh mục này vì vẫn còn danh mục con bên trong. Vui lòng xóa hoặc chuyển danh mục con trước.");
            }
            return $danhMuc->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}


