<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThuongHieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã thương hiệu là bắt buộc',
            'code.unique' => 'Mã thương hiệu đã tồn tại',
            'name.required' => 'Tên thương hiệu là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
        ];
    }
}
