<?php

namespace App\Domain\PIM\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Domain\MarineCatalog\Models\DongCoModel;

class SoDoPhuTung extends Model
{
    use Searchable;

    protected $table = 'so_do_phu_tungs';
    protected $fillable = ['may_thuy_model_id', 'nhom_phu_tung_id', 'ten_so_do', 'hinh_anh', 'thu_tu', 'trang_thai'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['model_ten'] = $this->model ? $this->model->ten_model : null;
        $array['nhom_phu_tung_ten'] = $this->nhomPhuTung ? $this->nhomPhuTung->ten_nhom : null;

        return $array;
    }

    public function model()
    {
        return $this->belongsTo(DongCoModel::class, 'may_thuy_model_id');
    }

    public function nhomPhuTung()
    {
        return $this->belongsTo(NhomPhuTung::class, 'nhom_phu_tung_id');
    }

    public function phuTungs()
    {
        return $this->belongsToMany(PhuTung::class, 'chi_tiet_so_dos', 'so_do_phu_tung_id', 'phu_tung_id');
    }

    public function chiTiets()
    {
        return $this->hasMany(ChiTietSoDo::class, 'so_do_phu_tung_id');
    }
}

