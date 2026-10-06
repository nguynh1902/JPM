<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \Illuminate\Support\Facades\DB::table('loai_dong_cos')->orderBy('id')->get();
foreach ($items as $b) {
    $newCode = 'LDC' . str_pad($b->id, 2, '0', STR_PAD_LEFT);
    \Illuminate\Support\Facades\DB::table('loai_dong_cos')->where('id', $b->id)->update(['ma_loai_dong_co' => $newCode]);
    echo "{$b->ma_loai_dong_co} -> {$newCode}\n";
}
