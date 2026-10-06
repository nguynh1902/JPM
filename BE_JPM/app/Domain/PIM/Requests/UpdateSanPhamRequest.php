<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'danh_muc_san_pham_id' => 'required|exists:danh_muc_san_phams,id',
            'ma_san_pham' => 'required|string|max:255|unique:san_phams,ma_san_pham,' . $this->route('san_pham'),
            'ten_san_pham' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:san_phams,slug,' . $this->route('san_pham'),
            'mo_ta_chi_tiet' => 'nullable|string',
            'thong_so_ky_thuat' => 'nullable|string',
            'video_url' => 'nullable|string',
            'gia_ban' => 'nullable|numeric|min:0',
            'is_gia_lien_he' => 'nullable|boolean',
            'so_luong_ton' => 'nullable|integer|min:0',
            'hinh_anh_chinh' => 'nullable|string',
            'hinh_anh_chinh_file' => 'nullable|file|image|max:5120',
            'thu_vien_anh_kept' => 'nullable|string',
            'thu_vien_anh_files' => 'nullable|array',
            'thu_vien_anh_files.*' => 'file|image|max:5120',
            'trang_thai' => 'required|integer|in:0,1',
        ];
    }
}
