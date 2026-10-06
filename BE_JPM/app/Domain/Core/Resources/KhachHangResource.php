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
        return [
            'id' => $this->id,
            'id_tai_khoan' => $this->id_tai_khoan,
            'user_code' => 'KH' . str_pad($this->id, 2, '0', STR_PAD_LEFT),
            'name' => $this->ho_ten,
            'phone' => $this->so_dien_thoai,
            'address' => 'N/A',
            'email' => $this->user ? $this->user->email : '',
            'avatar' => ($this->user && !empty($this->user->avatar)) ? (str_starts_with($this->user->avatar, 'http') ? $this->user->avatar : asset($this->user->avatar)) : '',
            'status' => $this->user ? $this->user->status : 1,
            'created_at' => $this->created_at,
        ];
    }
}

