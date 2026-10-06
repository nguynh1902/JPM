<?php

namespace App\Domain\IAM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaiTroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id') ?? $this->route('vai_tro');
        
        return [
            'name' => 'required|string|max:100|unique:roles,name,' . $roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.max' => 'Tên vai trò không được vượt quá 100 ký tự.',
            'name.unique' => 'Tên vai trò này đã tồn tại trong hệ thống.',
            'permissions.array' => 'Danh sách quyền không hợp lệ.',
            'permissions.*.exists' => 'Quyền không tồn tại trong hệ thống.',
        ];
    }
}
