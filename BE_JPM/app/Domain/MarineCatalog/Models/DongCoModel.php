<?php

namespace App\Domain\MarineCatalog\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Domain\PIM\Models\PhuTung;

class DongCoModel extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;

    use Searchable;

    protected $fillable = ['thuong_hieu_id', 'loai_dong_co_id', 'ma_model', 'ten_model', 'hinh_anh', 'nam_san_xuat', 'ma_luc', 'loai_nhip', 'trang_thai'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['thuong_hieu_ten'] = $this->thuongHieu ? $this->thuongHieu->name : null;
        $array['loai_dong_co_ten'] = $this->loaiDongCo ? $this->loaiDongCo->name : null;

        return $array;
    }

    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'thuong_hieu_id');
    }

    public function loaiDongCo()
    {
        return $this->belongsTo(LoaiDongCo::class, 'loai_dong_co_id');
    }

    public function phuTungs()
    {
        return $this->belongsToMany(PhuTung::class, 'phu_tung_models', 'dong_co_model_id', 'phu_tung_id')
                    ->withPivot('nam_ap_dung')
                    ->withTimestamps();
    }
}


