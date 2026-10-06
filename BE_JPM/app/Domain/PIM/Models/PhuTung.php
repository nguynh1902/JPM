<?php

namespace App\Domain\PIM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\MarineCatalog\Models\DongCoModel;

class PhuTung extends Model
{
    use \App\Domain\Core\Traits\LogsActivity;

    use Searchable;

    protected $table = 'phu_tungs';
    protected $fillable = ['thuong_hieu_id', 'nhom_phu_tung_id', 'ma_phu_tung', 'ten_phu_tung', 'gia_ban', 'is_gia_lien_he', 'trang_thai', 'don_vi_tinh', 'hinh_anh', 'mo_ta', 'thong_so_ky_thuat', 'thu_vien_anh', 'video_url'];


    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize the data array...
        // Tích hợp dữ liệu thuộc tính quan hệ (để search được bằng tên thương hiệu hoặc tên nhóm phụ tùng)
        $array['thuong_hieu_ten'] = $this->thuongHieu ? $this->thuongHieu->name : null;
        $array['nhom_phu_tung_ten'] = $this->nhomPhuTung ? $this->nhomPhuTung->ten_nhom : null;
        
        // Arrays for Meilisearch filtering
        $array['dong_co_model_ids'] = $this->dongCoModels->pluck('id')->toArray();
        
        $years = [];
        foreach ($this->dongCoModels as $model) {
             $year = $model->pivot->nam_ap_dung ?? $model->nam_san_xuat;
             if ($year) {
                 $years[] = (int)$year;
             }
        }
        $array['applicable_years'] = array_values(array_unique($years));

        return $array;
    }

        protected $casts = [
        'thu_vien_anh' => 'array',
    ];

    public function nhomPhuTung()
    {
        return $this->belongsTo(NhomPhuTung::class, 'nhom_phu_tung_id');
    }

    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'thuong_hieu_id');
    }

    public function dongCoModels()
    {
        return $this->belongsToMany(DongCoModel::class, 'phu_tung_models', 'phu_tung_id', 'dong_co_model_id')
                    ->withPivot('nam_ap_dung')
                    ->withTimestamps();
    }
    public function soDoPhuTungs()
    {
        return $this->belongsToMany(SoDoPhuTung::class, 'chi_tiet_so_dos', 'phu_tung_id', 'so_do_phu_tung_id')
                    ->withPivot('ref_number', 'qty', 'x', 'y');
    }

    public function maThayThes()
    {
        return $this->hasMany(MaThayThe::class, 'phu_tung_cu_id');
    }

}






