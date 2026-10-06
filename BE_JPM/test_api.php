<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$garage = App\Domain\Storefront\Models\GaragePhuTung::with('phuTung.thuongHieu', 'phuTung.nhomPhuTung')->first();
echo json_encode($garage?->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
