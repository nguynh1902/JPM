<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDanhMucSanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:danh_muc_san_phams,id',
            'ma_danh_muc' => 'nullable|string|max:100|unique:danh_muc_san_phams,ma_danh_muc',
            'ten_danh_muc' => 'required|string|max:255',
            'slug' => 'required|string|unique:danh_muc_san_phams,slug',
            'trang_thai' => 'required|integer|in:0,1',
        ];
    }
}
