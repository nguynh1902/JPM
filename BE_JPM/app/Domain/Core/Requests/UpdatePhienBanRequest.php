<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhienBanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_dong_xe' => 'required|integer|exists:dong_xes,id_dong_xe',
            'ten_phien_ban' => 'required|string|max:150',
            'nam_san_xuat' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'kieu_dang' => 'nullable|string|max:50',
            'so_cho_ngoi' => 'nullable|integer|min:1',
            'trang_thai' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_dong_xe.required' => ':attribute không được để trống.',
            'id_dong_xe.exists' => ':attribute không tồn tại.',
            'ten_phien_ban.required' => ':attribute không được để trống.',
            'ten_phien_ban.string' => ':attribute phải là chuỗi ký tự.',
            'ten_phien_ban.max' => ':attribute không được vượt quá :max ký tự.',
            'nam_san_xuat.integer' => ':attribute phải là số nguyên.',
            'nam_san_xuat.min' => ':attribute không hợp lệ.',
            'nam_san_xuat.max' => ':attribute không hợp lệ.',
            'kieu_dang.string' => ':attribute phải là chuỗi ký tự.',
            'kieu_dang.max' => ':attribute không được vượt quá :max ký tự.',
            'so_cho_ngoi.integer' => ':attribute phải là số nguyên.',
            'so_cho_ngoi.min' => ':attribute không hợp lệ.',
            'trang_thai.integer' => ':attribute phải là số nguyên.',
            'trang_thai.in' => ':attribute không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_dong_xe' => 'Dòng xe',
            'ten_phien_ban' => 'Tên phiên bản',
            'nam_san_xuat' => 'Năm sản xuất',
            'kieu_dang' => 'Kiểu dáng',
            'so_cho_ngoi' => 'Số chỗ ngồi',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
