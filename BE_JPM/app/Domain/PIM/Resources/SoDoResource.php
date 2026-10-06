<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoDoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'so_do_id' => $this->so_do_id,
            'ten_so_do' => $this->ten_so_do,
            'mo_ta' => $this->mo_ta,
            'hinh_anh' => $this->hinh_anh,
            'model_id' => $this->model_id,
            'id_phien_ban' => $this->id_phien_ban,
            'nam_ap_dung_tu' => $this->nam_ap_dung_tu ? (int) $this->nam_ap_dung_tu : null,
            'nam_ap_dung_den' => $this->nam_ap_dung_den ? (int) $this->nam_ap_dung_den : null,
            'trang_thai' => (int) $this->trang_thai,

            // Relationships
            'dong_xe' => new VehicleModelResource($this->whenLoaded('dongXe')),
            'phien_ban' => new PhienBanResource($this->whenLoaded('phienBan')),
            
            // Collection
            'chi_tiets' => ChiTietSoDoResource::collection($this->whenLoaded('chiTiets')),
        ];
    }
}
