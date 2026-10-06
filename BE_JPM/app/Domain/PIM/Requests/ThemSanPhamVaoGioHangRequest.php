<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemSanPhamVaoGioHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_phu_tung' => 'required|integer|exists:phu_tungs,id_phu_tung',
            'so_luong' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_phu_tung.required' => ':attribute không được để trống.',
            'id_phu_tung.integer' => ':attribute phải là số nguyên.',
            'id_phu_tung.exists' => ':attribute không tồn tại hoặc đã ngừng kinh doanh.',
            
            'so_luong.required' => ':attribute không được để trống.',
            'so_luong.integer' => ':attribute phải là số nguyên.',
            'so_luong.min' => ':attribute phải lớn hơn hoặc bằng :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_phu_tung' => 'Phụ tùng',
            'so_luong' => 'Số lượng',
        ];
    }
}
