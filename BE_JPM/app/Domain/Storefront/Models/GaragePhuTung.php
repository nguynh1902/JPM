<?php

namespace App\Domain\Storefront\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\PIM\Models\PhuTung;

class GaragePhuTung extends Model
{
    use HasFactory;

    protected $table = 'garage_phu_tungs';

    protected $fillable = [
        'garage_id',
        'phu_tung_id',
        'so_luong',
        'ghi_chu'
    ];

    public function garage()
    {
        return $this->belongsTo(GarageMayThuy::class, 'garage_id');
    }

    public function phuTung()
    {
        return $this->belongsTo(PhuTung::class, 'phu_tung_id');
    }
}
