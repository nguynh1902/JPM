<?php

namespace App\Domain\PIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDanhMucPhuTungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'parent_id' => 'nullable|integer|exists:danh_muc_phu_tungs,id',
            'code' => 'required|string|max:100|unique:danh_muc_phu_tungs,code,' . $id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.integer' => 'Danh mục cha phải là số nguyên.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
            'code.required' => 'Mã danh mục không được để trống.',
            'code.unique' => 'Mã danh mục đã tồn tại.',
            'name.required' => 'Tên danh mục không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => 'Danh mục cha',
            'code' => 'Mã danh mục',
            'name' => 'Tên danh mục',
            'description' => 'Mô tả',
            'image' => 'Hình ảnh',
            'status' => 'Trạng thái',
        ];
    }
}
