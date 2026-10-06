<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$allCols = [];
foreach($tables as $table) {
    $vals = array_values((array)$table);
    $tname = $vals[0];
    $cols = Schema::getColumnListing($tname);
    foreach($cols as $c) {
        if (str_starts_with($c, 'id_')) {
            $allCols[$tname][] = $c;
        }
    }
}

// Generate the list of dangerous "inverse" names
$dangerousNames = [];
foreach ($allCols as $tname => $cols) {
    foreach ($cols as $c) {
        $inverse = substr($c, 3) . '_id'; // id_khach_hang -> khach_hang_id
        $dangerousNames[$inverse] = $c;
    }
}

// Check all PHP files in app/ for dangerous names
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/app'));
$findings = [];
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        foreach ($dangerousNames as $wrong => $correct) {
            $pattern = "/(['\"]" . $wrong . "['\"]|->" . $wrong . "\b)/";
            if (preg_match($pattern, $content)) {
                $findings[] = [
                    'file' => str_replace(__DIR__, '', $file->getPathname()),
                    'wrong' => $wrong,
                    'correct' => $correct
                ];
            }
        }
    }
}

file_put_contents('mismatches.json', json_encode($findings, JSON_PRETTY_PRINT));
echo "Scan complete. Found " . count($findings) . " mismatches.\n";
