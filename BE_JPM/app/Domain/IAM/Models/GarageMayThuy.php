<?php

namespace App\Domain\IAM\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\MarineCatalog\Models\DongCoModel;

class GarageMayThuy extends Model
{
    protected $table = 'garage_may_thuy';
    protected $fillable = [
        'id_tai_khoan', 'dong_co_model_id',
        'ten_may', 'so_serial', 'nam_mua', 'ghi_chu'
    ];

    public function dongCoModel()
    {
        return $this->belongsTo(DongCoModel::class, 'dong_co_model_id');
    }
}
