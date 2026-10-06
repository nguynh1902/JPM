<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonNhapHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_po' => $this->ma_po,
            'id_nha_cung_cap' => $this->id_nha_cung_cap,
            'nha_cung_cap' => $this->nhaCungCap ? $this->nhaCungCap->ten_nha_cung_cap : 'N/A',
            'id_kho' => $this->id_kho,
            'ten_kho' => $this->kho ? $this->kho->ten_kho : 'N/A',
            'ngay_tao' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'ngay_du_kien' => $this->ngay_du_kien,
            'tong_tien' => $this->tong_tien,
            'trang_thai' => $this->trang_thai,
            'ghi_chu' => $this->ghi_chu,
        ];
    }
}
