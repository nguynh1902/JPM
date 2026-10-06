<?php
$mismatches = json_decode(file_get_contents(__DIR__.'/mismatches.json'), true);

foreach ($mismatches as $m) {
    $filePath = __DIR__ . str_replace('\\', '/', $m['file']);
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // We have to be careful with replace. We'll replace exact string literals and properties.
        // For example: 'khach_hang_id' -> 'id_khach_hang'
        // ->khach_hang_id -> ->id_khach_hang
        // "khach_hang_id" -> "id_khach_hang"
        
        $wrong = $m['wrong'];
        $correct = $m['correct'];
        
        $content = str_replace("'$wrong'", "'$correct'", $content);
        $content = str_replace("\"$wrong\"", "\"$correct\"", $content);
        $content = str_replace("->$wrong", "->$correct", $content);
        
        file_put_contents($filePath, $content);
        echo "Fixed in: " . $m['file'] . "\n";
    }
}
echo "All fixes applied.\n";
