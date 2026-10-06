<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDongCoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_phien_ban' => 'required|integer|exists:phien_bans,id_phien_ban',
            'ten_dong_co' => 'required|string|max:150',
            'loai_nhien_lieu' => 'nullable|string|max:50',
            'dung_tich' => 'nullable|numeric|min:0',
            'ma_luc' => 'nullable|integer|min:0',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_phien_ban.required' => ':attribute không được để trống.',
            'id_phien_ban.exists' => ':attribute không tồn tại.',
            'ten_dong_co.required' => ':attribute không được để trống.',
            'ten_dong_co.string' => ':attribute phải là chuỗi ký tự.',
            'ten_dong_co.max' => ':attribute không được vượt quá :max ký tự.',
            'loai_nhien_lieu.string' => ':attribute phải là chuỗi ký tự.',
            'loai_nhien_lieu.max' => ':attribute không được vượt quá :max ký tự.',
            'dung_tich.numeric' => ':attribute phải là số.',
            'dung_tich.min' => ':attribute không được nhỏ hơn :min.',
            'ma_luc.integer' => ':attribute phải là số nguyên.',
            'ma_luc.min' => ':attribute không hợp lệ.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_phien_ban' => 'Phiên bản',
            'ten_dong_co' => 'Tên động cơ',
            'loai_nhien_lieu' => 'Loại nhiên liệu',
            'dung_tich' => 'Dung tích',
            'ma_luc' => 'Mã lực',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
