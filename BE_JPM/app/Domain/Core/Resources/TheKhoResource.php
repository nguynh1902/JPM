<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TheKhoResource extends JsonResource
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
            'id_kho' => $this->id_kho,
            'id_san_pham' => $this->id_san_pham,
            'loai_san_pham' => $this->loai_san_pham,
            'loai_giao_dich' => $this->loai_giao_dich,
            'so_luong_thay_doi' => (int) $this->so_luong_thay_doi,
            'so_luong_ton_cuoi' => (int) $this->so_luong_ton_cuoi,
            'id_tham_chieu' => $this->id_tham_chieu,
            'ma_tham_chieu' => $this->ma_tham_chieu,
            'ghi_chu' => $this->ghi_chu,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'kho' => $this->whenLoaded('kho'),
            'san_pham' => $this->when($this->loai_san_pham === 'san_pham', $this->whenLoaded('sanPham')),
            'phu_tung' => $this->when($this->loai_san_pham === 'phu_tung', $this->whenLoaded('phuTung')),
            'nguoi_thuc_hien' => $this->relationLoaded('nguoiThucHien') && $this->nguoiThucHien ? [
                'id' => $this->nguoiThucHien->id,
                'name' => $this->nguoiThucHien->name,
                'avatar' => $this->nguoiThucHien->avatar,
            ] : null,
        ];
    }
}
