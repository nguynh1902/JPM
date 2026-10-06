<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$indexes = Illuminate\Support\Facades\DB::select("SHOW INDEXES FROM phu_tungs");
print_r($indexes);
