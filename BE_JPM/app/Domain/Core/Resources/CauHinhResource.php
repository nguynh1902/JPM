<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CauHinhResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_cau_hinh' => $this->id_cau_hinh,
            'khoa' => $this->khoa,
            'gia_tri' => $this->gia_tri,
            'nhom' => $this->nhom,
            'mo_ta' => $this->mo_ta,
            'trang_thai' => $this->trang_thai,
        ];
    }
}
