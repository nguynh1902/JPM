<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$may = App\Domain\IAM\Models\GarageMayThuy::with('dongCoModel.thuongHieu')->first();
echo json_encode($may?->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
