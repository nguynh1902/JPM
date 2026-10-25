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
    use SoftDeletes;

    use Searchable;

    protected $table = 'phu_tungs';
    protected $fillable = ['thuong_hieu_id', 'nhom_phu_tung_id', 'ma_phu_tung', 'ten_phu_tung', 'gia_ban', 'is_gia_lien_he', 'so_luong_ton', 'trang_thai', 'don_vi_tinh', 'hinh_anh', 'mo_ta', 'thong_so_ky_thuat', 'thu_vien_anh', 'video_url'];


    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
        public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'ma_phu_tung' => (string) $this->ma_phu_tung,
            'ten_phu_tung' => (string) $this->ten_phu_tung,
            'mo_ta' => (string) ($this->mo_ta ?? ''),
            'thong_so_ky_thuat' => (string) ($this->thong_so_ky_thuat ?? ''),
        ];
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

    public function thayTheChos()
    {
        return $this->hasMany(MaThayThe::class, 'phu_tung_moi_id');
    }

}






