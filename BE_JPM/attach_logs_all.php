<?php
$dir = new RecursiveDirectoryIterator('C:/xampp/htdocs/JPM/BE_JPM/app/Domain');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/\.php$/', RegexIterator::GET_MATCH);

foreach ($files as $file) {
    $path = $file[0];
    $c = file_get_contents($path);
    if (preg_match('/class\s+[A-Za-z0-9_]+\s+extends\s+Model/', $c)) {
        if (strpos($c, 'use \App\Domain\Core\Traits\LogsActivity;') === false) {
            $c = preg_replace('/(class\s+[A-Za-z0-9_]+\s+extends\s+Model[^{]*\{)/', "$1\n    use \App\Domain\Core\Traits\LogsActivity;\n", $c);
            file_put_contents($path, $c);
            echo "Patched $path\n";
        }
    }
}
