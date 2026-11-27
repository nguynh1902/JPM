<?php
namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChuyenMucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('chuyen_muc');
        return [
            'name' => 'required|string|max:255|unique:chuyen_mucs,name,' . $id,
            'type' => 'required|string|in:NEWS,FAQ',
            'status' => 'required|string|in:ACTIVE,HIDDEN',
        ];
    }
}

