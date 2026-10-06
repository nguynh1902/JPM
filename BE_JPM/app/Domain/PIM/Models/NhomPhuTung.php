<?php

namespace App\Domain\PIM\Models;

use Illuminate\Database\Eloquent\Model;

class NhomPhuTung extends Model
{
    protected $table = 'nhom_phu_tungs';
    protected $fillable = ['ma_nhom', 'ten_nhom', 'trang_thai'];

    public function phuTungs()
    {
        return $this->hasMany(PhuTung::class, 'nhom_phu_tung_id');
    }
}

