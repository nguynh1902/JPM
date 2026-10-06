<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
echo "Total Tables: " . count($tables) . "\n";
foreach($tables as $table) {
    $vals = array_values((array)$table);
    echo $vals[0] . ", ";
}
echo "\n";
