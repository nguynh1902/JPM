<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sps = \App\Domain\CMS\Models\SanPham::all();
foreach($sps as $sp) {
    $total = \App\Domain\Procurement\Models\TonKho::where('id_san_pham', $sp->id)->whereIn('loai_san_pham', ['san_pham', 'san-pham'])->sum('so_luong_ton');
    $sp->update(['so_luong_ton' => $total]);
}
$pts = \App\Domain\PIM\Models\PhuTung::all();
foreach($pts as $pt) {
    $total = \App\Domain\Procurement\Models\TonKho::where('id_san_pham', $pt->id)->where('loai_san_pham', 'phu_tung')->sum('so_luong_ton');
    $pt->update(['so_luong_ton' => $total]);
}
echo 'Synced all stock';
