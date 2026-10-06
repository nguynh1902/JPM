<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$brands = App\Domain\MarineCatalog\Models\ThuongHieu::paginate(2);
$resource = App\Domain\MarineCatalog\Resources\ThuongHieuResource::collection($brands)->additional(['success' => true]);
echo json_encode($resource->toResponse(app('request'))->getData(true), JSON_PRETTY_PRINT);
