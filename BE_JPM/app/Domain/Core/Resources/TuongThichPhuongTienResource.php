<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TuongThichPhuongTienResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tuong_thich' => $this->id_tuong_thich,
            'phu_tung_id' => $this->phu_tung_id,
            'model_id' => $this->model_id,
            'id_phien_ban' => $this->id_phien_ban,
            'id_dong_co' => $this->id_dong_co,
            'nam_bat_dau' => $this->nam_bat_dau ? (int) $this->nam_bat_dau : null,
            'nam_ket_thuc' => $this->nam_ket_thuc ? (int) $this->nam_ket_thuc : null,
            'ghi_chu' => $this->ghi_chu,

            // Relationships
            'dong_xe' => new VehicleModelResource($this->whenLoaded('dongXe')),
            'phien_ban' => new PhienBanResource($this->whenLoaded('phienBan')),
            'dong_co' => new DongCoResource($this->whenLoaded('dongCo')),
        ];
    }
}
