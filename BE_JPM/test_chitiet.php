<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $code = '8M0142205';
    $phuTung = \App\Domain\PIM\Models\PhuTung::with(['thuongHieu', 'nhomPhuTung', 'dongCoModels', 'soDoPhuTungs.model.thuongHieu', 'maThayThes.phuTungMoi'])
        ->where('ma_phu_tung', strtoupper($code))
        ->first();
    
    if (!$phuTung) {
        echo "NOT FOUND";
    } else {
        echo "FOUND: " . $phuTung->ma_phu_tung;
    }
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
