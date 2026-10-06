<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoaiDongCoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'ten_loai_dong_co' => 'required|string|max:255',
            'trang_thai' => 'required|boolean',
        ];
    }
}
