<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDongCoModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'thuong_hieu_id' => 'required|exists:thuong_hieus,id',
            'loai_dong_co_id' => 'required|exists:loai_dong_cos,id',
            'ma_model' => 'required|string|max:255|unique:dong_co_models,ma_model',
            'ten_model' => 'required|string|max:255',
            'nam_san_xuat' => 'nullable|integer',
            'ma_luc' => 'nullable|integer',
            'loai_nhip' => 'nullable|string|max:255',
            'trang_thai' => 'required|integer|in:0,1',
        ];
    }
}


