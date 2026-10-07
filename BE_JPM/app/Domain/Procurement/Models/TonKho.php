<?php namespace App\Domain\Procurement\Models; use Illuminate\Database\Eloquent\Model; use App\Domain\CMS\Models\SanPham;
use App\Domain\PIM\Models\PhuTung;

class TonKho extends Model {
    use \App\Domain\Core\Traits\LogsActivity;
 protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($tonKho) {
            $tonKho->syncTotalStock();
        });

        static::deleted(function ($tonKho) {
            $tonKho->syncTotalStock();
        });
    }

    public function syncTotalStock()
    {
        $total = self::where('id_san_pham', $this->id_san_pham)
                     ->where('loai_san_pham', $this->loai_san_pham)
                     ->sum('so_luong_ton');

        if ($this->loai_san_pham === 'san_pham' || $this->loai_san_pham === 'san-pham') {
            \App\Domain\CMS\Models\SanPham::where('id', $this->id_san_pham)->update(['so_luong_ton' => $total]);
        } else {
            \App\Domain\PIM\Models\PhuTung::where('id', $this->id_san_pham)->update(['so_luong_ton' => $total]);
        }
    } public function kho() { return $this->belongsTo(Kho::class, 'id_kho'); } public function sanPham() { return $this->belongsTo(SanPham::class, 'id_san_pham'); } public function phuTung() { return $this->belongsTo(PhuTung::class, 'id_san_pham'); } }
