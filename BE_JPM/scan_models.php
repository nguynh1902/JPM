<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function scanModels($dir) {
    $models = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
                $namespace = $m[1];
                if (preg_match('/class\s+(\w+)\s+extends\s+Model/', $content, $c)) {
                    $className = $c[1];
                    $models[] = $namespace . '\\' . $className;
                }
            }
        }
    }
    return $models;
}

$modelDirs = [
    __DIR__.'/app/Domain/IAM/Models',
    __DIR__.'/app/Domain/Sales/Models',
    __DIR__.'/app/Domain/Procurement/Models',
    __DIR__.'/app/Domain/MarineCatalog/Models',
    __DIR__.'/app/Domain/Storefront/Models',
    __DIR__.'/app/Domain/PIM/Models',
    __DIR__.'/app/Domain/CMS/Models'
];

$models = [];
foreach ($modelDirs as $dir) {
    if (is_dir($dir)) {
        $models = array_merge($models, scanModels($dir));
    }
}

$errors = [];

foreach ($models as $class) {
    try {
        $reflection = new ReflectionClass($class);
        $model = $reflection->newInstance();
        $table = $model->getTable();
        
        if (!Schema::hasTable($table)) {
            $errors[] = "Table missing for Model: $class ($table)";
            continue;
        }
        
        // Scan methods for relationships
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== $class) continue;
            if ($method->getNumberOfParameters() > 0) continue;
            
            // Try invoking to see if it returns a Relation
            try {
                $returnType = $method->getReturnType();
                // Simple heuristic or just try catching
                $methodName = $method->getName();
                
                // Let's avoid mutating or triggering logic, just parse file content for hasMany/belongsTo
                $content = file_get_contents($reflection->getFileName());
                if (preg_match("/function\s+$methodName\s*\(\)/", $content)) {
                    if (preg_match("/return\s+\\$this->(hasMany|belongsTo|hasOne|belongsToMany)\s*\(([^)]+)\)/", $content, $m)) {
                        $relType = $m[1];
                        $args = explode(',', $m[2]);
                        $targetClassRaw = trim($args[0]);
                        
                        // Parse target class
                        preg_match('/([a-zA-Z0-9_\\\\]+)::class/', $targetClassRaw, $tcMatch);
                        if(isset($tcMatch[1])){
                            $targetClass = $tcMatch[1];
                            // Resolve full class name if necessary, skipping for now if it's too complex, just outputting relationships
                            // echo "Found $relType in $class -> $methodName \n";
                        }
                    }
                }
            } catch (\Exception $e) {}
        }
    } catch (\Exception $e) {
        // ignore abstract or non-instantiable
    }
}

// Specifically test Customer -> Delete relationship
try {
    $khController = file_get_contents(__DIR__.'/app/Domain/IAM/Services/KhachHangService.php');
    if (strpos($khController, 'khach_hang_id') !== false) {
        echo "Found khach_hang_id in KhachHangService.php\n";
    }
    
    // Check what columns are in don_hangs
    $cols = Schema::getColumnListing('don_hangs');
    echo "Columns in don_hangs: " . implode(", ", $cols) . "\n";
    
} catch (\Exception $e) {
    echo "Error checking specific bug: " . $e->getMessage() . "\n";
}

file_put_contents('model_errors.json', json_encode($errors, JSON_PRETTY_PRINT));
echo "Scan completed.\n";
