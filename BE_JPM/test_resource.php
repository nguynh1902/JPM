<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dh = \App\Domain\Sales\Models\DonHang::create(['ma_don_hang'=>uniqid('test_'), 'id_khach_hang'=>1, 'tong_tien'=>100]);
try {
    $res = \App\Domain\Core\Resources\DonHangResource::make($dh)->resolve();
    print_r($res);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
$dh->delete();
