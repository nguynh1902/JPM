<?php

namespace App\Console\Commands;

use App\Domain\MarineCatalog\Services\CatalogCacheService;
use Illuminate\Console\Command;

class FlushCatalogCache extends Command
{
    protected $signature = 'jpm:cache-flush {--warmup : Làm ấm lại cache sau khi xóa}';
    protected $description = 'Xóa sạch cache danh mục phụ tùng/máy thủy và tùy chọn warm-up lại dữ liệu';

    public function handle(CatalogCacheService $cacheService)
    {
        $this->info("Đang xóa toàn bộ Cache Catalog (Redis/Application Cache)...");
        
        if ($this->option('warmup')) {
            $result = $cacheService->warmUpCatalogCache();
            $this->info("✓ Đã xóa và làm ấm lại cache thành công!");
            foreach ($result['stats'] as $key => $count) {
                $this->line("  - {$key}: {$count} bản ghi");
            }
        } else {
            $cacheService->clearAllCatalogCache();
            $this->info("✓ Đã xóa sạch Cache Catalog!");
        }

        return Command::SUCCESS;
    }
}
