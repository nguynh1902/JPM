<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanhMucPhuTungResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'danh_muc_id' => $this->danh_muc_id,
            'danh_muc_id_cha' => $this->danh_muc_id_cha,
            'ten_danh_muc' => $this->ten_danh_muc,
            'mo_ta' => $this->mo_ta,
            'hinh_anh' => $this->hinh_anh,
            'trang_thai' => (int) $this->trang_thai,

            // Relationships
            'danh_muc_cha' => new DanhMucPhuTungResource($this->whenLoaded('danhMucCha')),
            'danh_muc_con' => DanhMucPhuTungResource::collection($this->whenLoaded('danhMucCon')),
        ];
    }
}
