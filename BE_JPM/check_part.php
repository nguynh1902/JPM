<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pt = \App\Domain\PIM\Models\PhuTung::where('ma_phu_tung', '8M0142205')->first();
echo $pt ? 'FOUND: ' . $pt->ma_phu_tung : 'NOT FOUND';

// Also let's check how the part is shown in the first image
// In the first image, the part is listed in a table: "Danh sách Phụ Tùng", with Mã & Tên "8M0142205"
// Where does that list come from?
// Route: /so-do-by-id/{modelId} -> returns diagram and parts. Let's see what parts it returns.
