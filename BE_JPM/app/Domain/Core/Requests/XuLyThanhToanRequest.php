<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XuLyThanhToanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_don_hang' => 'required|integer|exists:don_hangs,id_don_hang',
            'ma_giao_dich' => 'nullable|string|max:100',
            'so_tien' => 'required|numeric|min:0',
            'ngay_thanh_toan' => 'nullable|date',
            'ghi_chu' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_don_hang.required' => ':attribute không được để trống.',
            'id_don_hang.integer' => ':attribute phải là số nguyên.',
            'id_don_hang.exists' => ':attribute không tồn tại.',
            
            'ma_giao_dich.string' => ':attribute phải là chuỗi ký tự.',
            'ma_giao_dich.max' => ':attribute không được vượt quá :max ký tự.',
            
            'so_tien.required' => ':attribute không được để trống.',
            'so_tien.numeric' => ':attribute phải là số.',
            'so_tien.min' => ':attribute không hợp lệ.',
            
            'ngay_thanh_toan.date' => ':attribute không hợp lệ.',
            
            'ghi_chu.string' => ':attribute phải là chuỗi ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_don_hang' => 'Đơn hàng',
            'ma_giao_dich' => 'Mã giao dịch',
            'so_tien' => 'Số tiền',
            'ngay_thanh_toan' => 'Ngày thanh toán',
            'ghi_chu' => 'Ghi chú',
        ];
    }
}
