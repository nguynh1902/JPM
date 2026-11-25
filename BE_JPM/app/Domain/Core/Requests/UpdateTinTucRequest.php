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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120',
            'gallery' => 'nullable|array|max:20',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120',
            'existing_gallery' => 'nullable',
            'youtube_url' => 'nullable|url',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'author' => 'nullable|string',
        ];
    }
}

