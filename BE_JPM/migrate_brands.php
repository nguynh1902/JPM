<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$brands = \Illuminate\Support\Facades\DB::table('thuong_hieus')->orderBy('id')->get();
foreach ($brands as $b) {
    $newCode = 'TH' . str_pad($b->id, 2, '0', STR_PAD_LEFT);
    \Illuminate\Support\Facades\DB::table('thuong_hieus')->where('id', $b->id)->update(['code' => $newCode]);
    echo "{$b->code} -> {$newCode}\n";
}
