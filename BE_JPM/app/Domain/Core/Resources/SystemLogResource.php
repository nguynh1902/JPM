<?php

namespace App\Domain\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemLogResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : 'System',
            'avatar' => $this->user ? $this->user->avatar : null,
            'role' => $this->user ? ($this->user->getRoleNames()->first() ?? 'Nhân viên') : 'Hệ thống',
            'action' => $this->action,
            'description' => $this->description,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
