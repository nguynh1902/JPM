<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Domain\Sales\Models\YeuCauBaoGia;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('quotes:cancel-expired', function () {
    $validHours = 72;
    $threshold = now()->subHours($validHours);
    
    $expiredQuotes = YeuCauBaoGia::where('trang_thai', 1)
        ->where('updated_at', '<', $threshold)
        ->get();
        
    $count = 0;
    foreach ($expiredQuotes as $quote) {
        $quote->trang_thai = 3; // 3 = Đã hủy
        $quote->ghi_chu_zalo = ($quote->ghi_chu_zalo ? $quote->ghi_chu_zalo . ' | ' : '') . 'Tự động hủy do quá hạn hiệu lực 3 ngày (72h) không xác nhận.';
        $quote->save();
        $count++;
    }
    
    $this->info("Đã tự động hủy {$count} báo giá quá hạn 3 ngày.");
})->purpose('Tự động hủy các báo giá quá hạn 3 ngày (72h) mà khách chưa xác nhận hoặc đặt cọc');

Schedule::command('quotes:cancel-expired')->daily();
