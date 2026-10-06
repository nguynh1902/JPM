<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoaiDongCoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bỏ qua phân quyền ở đây để làm nhanh theo yêu cầu
    }

    public function rules(): array
    {
        return [
            'ma_loai_dong_co' => 'nullable|string|max:50|unique:loai_dong_cos,ma_loai_dong_co',
            'ten_loai_dong_co' => 'required|string|max:255',
            'trang_thai' => 'required|boolean',
        ];
    }
}
