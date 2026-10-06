<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThongBaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_thong_bao' => $this->id_thong_bao,
            'id_tai_khoan' => $this->id_tai_khoan,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'loai_thong_bao' => $this->loai_thong_bao,
            'trang_thai_doc' => (bool) $this->trang_thai_doc,

            // Relationships
            'tai_khoan' => new TaiKhoanResource($this->whenLoaded('taiKhoan')),
        ];
    }
}
