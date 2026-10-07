<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/Procurement/Models/TonKho.php';
$c = file_get_contents($file);

$injection = "protected \$guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saved(function (\$tonKho) {
            \$tonKho->syncTotalStock();
        });

        static::deleted(function (\$tonKho) {
            \$tonKho->syncTotalStock();
        });
    }

    public function syncTotalStock()
    {
        \$total = self::where('id_san_pham', \$this->id_san_pham)
                     ->where('loai_san_pham', \$this->loai_san_pham)
                     ->sum('so_luong_ton');

        if (\$this->loai_san_pham === 'san_pham' || \$this->loai_san_pham === 'san-pham') {
            \App\Domain\CMS\Models\SanPham::where('id', \$this->id_san_pham)->update(['so_luong_ton' => \$total]);
        } else {
            \App\Domain\PIM\Models\PhuTung::where('id', \$this->id_san_pham)->update(['so_luong_ton' => \$total]);
        }
    }";

$c = str_replace('protected $guarded = [];', $injection, $c);

file_put_contents($file, $c);
echo "Injected total stock sync to TonKho Model\n";
