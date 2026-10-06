<?php namespace App\Domain\MarineCatalog\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory; class ThuongHieu extends Model {
    use \App\Domain\Core\Traits\LogsActivity;
 use HasFactory; protected $table = 'thuong_hieus';
    protected $fillable = ['code', 'name', 'logo_path', 'status'];

    public function phuTungs()
    {
        return $this->hasMany(\App\Domain\PIM\Models\PhuTung::class, 'thuong_hieu_id');
    }

    public function dongCoModels()
    {
        return $this->hasMany(DongCoModel::class, 'thuong_hieu_id');
    }
}

