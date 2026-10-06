<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDongCoModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'thuong_hieu_id' => 'sometimes|required|exists:thuong_hieus,id',
            'loai_dong_co_id' => 'sometimes|required|exists:loai_dong_cos,id',
            'ma_model' => 'sometimes|required|string|max:255|unique:dong_co_models,ma_model,' . $this->route('dong_co_model'),
            'ten_model' => 'sometimes|required|string|max:255',
            'hinh_anh' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nam_san_xuat' => 'nullable|integer',
            'ma_luc' => 'nullable|integer',
            'loai_nhip' => 'nullable|string|max:255',
            'trang_thai' => 'sometimes|required|integer|in:0,1',
        ];
    }
}


