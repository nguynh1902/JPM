<?php
namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTinTucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'status' => 'required|string',
            'thumbnail' => 'nullable',
            'gallery' => 'nullable|array',
            'gallery.*' => 'file',
            'existing_gallery' => 'nullable',
            'youtube_url' => 'nullable|url',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'author' => 'nullable|string',
        ];
    }
}

