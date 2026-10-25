<?php

namespace App\Domain\MarineCatalog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateThuongHieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('id') ?? $this->route('thuong_hieu');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('thuong_hieus', 'name')->ignore($brandId)
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:5120',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã thương hiệu là bắt buộc.',
            'code.unique' => 'Mã thương hiệu này đã tồn tại.',
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'name.unique' => 'Tên thương hiệu này đã tồn tại.',
            'loai_ung_dung.required' => 'Loại ứng dụng là bắt buộc.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'logo.image' => 'Logo phải là định dạng hình ảnh.',
            'logo.max' => 'Logo không được vượt quá 5MB.',
        ];
    }
}
