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
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,avif,heic,heif,tiff,tif,ico,jfif,eps,raw,cr2,nef,psd,ai|max:20480',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,avif,heic,heif,tiff,tif,ico,jfif,eps,raw,cr2,nef,psd,ai|max:20480',
            'existing_gallery' => 'nullable',
            'youtube_url' => 'nullable|url',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'author' => 'nullable|string',
        ];
    }
}

