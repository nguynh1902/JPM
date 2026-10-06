<?php

namespace App\Domain\PIM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhuTungResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'phu_tung_id' => $this->phu_tung_id,
            'ma_phu_tung' => $this->ma_phu_tung,
            'ten_phu_tung' => $this->ten_phu_tung,
            'danh_muc_id' => $this->danh_muc_id,
            'thuong_hieu_id' => $this->thuong_hieu_id,
            'id_nha_san_xuat' => $this->id_nha_san_xuat,
            'id_don_vi_tinh' => $this->id_don_vi_tinh,
            'mo_ta' => $this->mo_ta,
            'thong_so_ky_thuat' => $this->thong_so_ky_thuat,
            
            // Ẩn giá nhập với client thường, có thể dùng when để xử lý, nhưng ở đây
            // ta chỉ map theo chuẩn. Controller/Service sẽ chịu trách nhiệm hide qua scope hoặc select,
            // hoặc dùng quyền (tạm thời trả ra hết nhưng giấu logic N+1).
            'gia_nhap' => $this->gia_nhap !== null ? (float) $this->gia_nhap : null,
            'gia_ban' => $this->gia_ban !== null ? (float) $this->gia_ban : null,
            
            'trong_luong' => $this->trong_luong !== null ? (float) $this->trong_luong : null,
            'kich_thuoc' => $this->kich_thuoc,
            'trang_thai' => (int) $this->trang_thai,

            // Relationships (Singular)
            'danh_muc' => new DanhMucPhuTungResource($this->whenLoaded('danhMuc')),
            'thuong_hieu' => new ThuongHieuResource($this->whenLoaded('thuongHieu')),
            'nha_san_xuat' => new NhaSanXuatResource($this->whenLoaded('nhaSanXuat')),
            'don_vi_tinh' => new DonViTinhResource($this->whenLoaded('donViTinh')),

            // Relationships (Collections)
            'hinh_anhs' => HinhAnhPhuTungResource::collection($this->whenLoaded('hinhAnhs')),
            'ma_oems' => MaOemResource::collection($this->whenLoaded('maOems')),
            'ma_tham_chieus' => MaThamChieuResource::collection($this->whenLoaded('maThamChieus')),
            'ma_thay_thes' => MaThayTheResource::collection($this->whenLoaded('maThayThes')),
            'tai_lieus' => TaiLieuPhuTungResource::collection($this->whenLoaded('taiLieus')),
            'tuong_thichs' => TuongThichPhuongTienResource::collection($this->whenLoaded('tuongThichs')),
        ];
    }
}
