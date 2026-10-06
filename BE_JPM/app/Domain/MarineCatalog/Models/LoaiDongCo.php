<?php

namespace App\Domain\MarineCatalog\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiDongCo extends Model
{
    protected $table = 'loai_dong_cos';
    protected $fillable = ['ma_loai_dong_co', 'ten_loai_dong_co', 'trang_thai'];

    public function dongCoModels()
    {
        return $this->hasMany(DongCoModel::class, 'loai_dong_co_id');
    }
}

