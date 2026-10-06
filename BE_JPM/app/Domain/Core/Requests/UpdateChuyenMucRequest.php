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
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:NEWS,FAQ,TUTORIAL',
            'status' => 'required|string|in:ACTIVE,HIDDEN',
        ];
    }
}

