<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

// Columns in don_hangs
$cols = Schema::getColumnListing('don_hangs');
echo "Columns in don_hangs: " . implode(", ", $cols) . "\n";

// Check if khach_hang_id exists in any file in app/
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/app'));
$foundFiles = [];
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'khach_hang_id') !== false) {
            $foundFiles[] = $file->getPathname();
        }
    }
}
echo "\nFiles containing 'khach_hang_id':\n";
foreach ($foundFiles as $f) {
    echo $f . "\n";
}

