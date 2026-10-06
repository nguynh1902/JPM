<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HinhAnhPhuTungResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_hinh_anh' => $this->id_hinh_anh,
            'phu_tung_id' => $this->phu_tung_id,
            'duong_dan' => $this->duong_dan,
            'la_anh_chinh' => (bool) $this->la_anh_chinh,
        ];
    }
}
