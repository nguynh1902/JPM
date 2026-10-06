<?php

namespace App\Domain\PIM\Models;

use Illuminate\Database\Eloquent\Model;

class MaThayThe extends Model
{
    protected $table = 'ma_thay_thes';

    protected $fillable = [
        'phu_tung_cu_id',
        'phu_tung_moi_id',
        'loai_thay_the',
        'ghi_chu',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
    ];

    // Phụ tùng cũ bị thay thế
    public function phuTungCu()
    {
        return $this->belongsTo(PhuTung::class, 'phu_tung_cu_id');
    }

    // Phụ tùng mới thay thế
    public function phuTungMoi()
    {
        return $this->belongsTo(PhuTung::class, 'phu_tung_moi_id');
    }
}
