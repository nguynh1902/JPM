<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Domain\PIM\Models\MaThayThe::with(['phuTungCu', 'phuTungMoi'])->orderBy('id', 'desc')->get();
echo json_encode($items[0]);
