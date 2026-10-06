<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoaiPhuongTienResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'loai_phuong_tien_id' => $this->loai_phuong_tien_id,
            'ten_loai' => $this->ten_loai,
            'mo_ta' => $this->mo_ta,
            'trang_thai' => (int) $this->trang_thai,
        ];
    }
}
