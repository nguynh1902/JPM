<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KhachHangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $addressText = 'Chưa cập nhật';
        if ($this->user) {
            $diaChis = $this->user->relationLoaded('diaChis')
                ? $this->user->diaChis
                : \App\Domain\IAM\Models\DiaChi::where('id_tai_khoan', $this->id_tai_khoan)->get();
            if ($diaChis && $diaChis->isNotEmpty()) {
                $defaultAddr = $diaChis->firstWhere('la_mac_dinh', 1) ?? $diaChis->first();
                if ($defaultAddr && !empty($defaultAddr->dia_chi_cu_the)) {
                    $addressText = $defaultAddr->dia_chi_cu_the;
                }
            }
        }

        return [
            'id' => $this->id,
            'id_tai_khoan' => $this->id_tai_khoan,
            'user_code' => 'KH' . str_pad($this->id, 2, '0', STR_PAD_LEFT),
            'name' => $this->ho_ten,
            'phone' => $this->so_dien_thoai,
            'address' => $addressText,
            'email' => $this->user ? $this->user->email : '',
            'avatar' => ($this->user && !empty($this->user->avatar)) ? (str_starts_with($this->user->avatar, 'http') ? $this->user->avatar : asset($this->user->avatar)) : '',
            'status' => $this->user ? $this->user->status : 1,
            'created_at' => $this->created_at,
        ];
    }
}
