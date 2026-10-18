<?php

namespace App\Domain\MarineCatalog\Observers;

use App\Domain\MarineCatalog\Services\CatalogCacheService;

class CatalogModelObserver
{
    protected CatalogCacheService $cacheService;

    public function __construct(CatalogCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function saved($model): void
    {
        $this->cacheService->clearAllCatalogCache();
    }

    public function deleted($model): void
    {
        $this->cacheService->clearAllCatalogCache();
    }
}
