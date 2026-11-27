<?php
namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChuyenMucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:chuyen_mucs,name',
            'type' => 'required|string|in:NEWS,FAQ',
            'status' => 'required|string|in:ACTIVE,HIDDEN',
        ];
    }
}

