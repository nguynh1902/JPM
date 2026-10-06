<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$th = App\Domain\MarineCatalog\Models\ThuongHieu::create(['ten_thuong_hieu' => 'Test', 'code' => 'TMP_123']);
echo 'Was created: ' . ($th->wasRecentlyCreated ? 'yes' : 'no') . "\n";
$th->update(['code' => 'TH999']);
echo 'Was created after update: ' . ($th->wasRecentlyCreated ? 'yes' : 'no') . "\n";
$th->delete();
