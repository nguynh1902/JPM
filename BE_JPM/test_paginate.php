<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$brands = App\Domain\MarineCatalog\Models\ThuongHieu::paginate(2);
$resource = App\Domain\MarineCatalog\Resources\ThuongHieuResource::collection($brands);
echo json_encode(['success' => true, 'data' => $resource], JSON_PRETTY_PRINT);
