<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p1 = App\Domain\PIM\Models\PhuTung::first();
$p2 = App\Domain\PIM\Models\PhuTung::orderBy('id', 'desc')->first();
if ($p1 && $p2 && $p1->id !== $p2->id) {
    App\Domain\PIM\Models\MaThayThe::create(['phu_tung_cu_id' => $p1->id, 'phu_tung_moi_id' => $p2->id, 'loai_thay_the' => 'Thay thế trực tiếp (Direct)', 'ghi_chu' => 'Test', 'trang_thai' => 1]);
    echo "Created";
} else {
    echo "Failed";
}
