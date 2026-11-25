<?php

namespace App\Domain\Communication\Jobs;

use App\Domain\Communication\Models\LichSuThongBao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public int $logId,
        public array $payload
    ) {}

    public function handle(): void
    {
        $log = LichSuThongBao::find($this->logId);
        if (!$log) {
            return;
        }

        $loaiTin = $this->payload['loai_tin'] ?? 'sms_brandname';
        $soDienThoai = $this->payload['so_dien_thoai'] ?? '';
        $noiDung = $this->payload['noi_dung'] ?? '';
        $isZalo = ($loaiTin === 'zalo_zns');
        $apiKey = $isZalo ? config('services.zalo_zns.secret_key') : config('services.sms_brandname.api_key');

        $status = 'that_bai';
        $gatewayResponse = null;

        try {
            if ($isZalo) {
                $response = Http::withHeaders([
                    'access_token' => $apiKey,
                ])->timeout(8)->post('https://business.openapi.zalo.me/message/template', [
                    'phone' => preg_replace('/^0/', '84', $soDienThoai),
                    'template_id' => $this->payload['template_id'] ?? config('services.zalo_zns.template_quote_ready'),
                    'template_data' => $this->payload['template_data'] ?? [],
                ]);
                $gatewayResponse = $response->json();
                $status = ($response->successful() && ($gatewayResponse['error'] ?? 0) === 0) ? 'thanh_cong' : 'that_bai';
            } else {
                $response = Http::withBasicAuth($apiKey, 'x')
                    ->timeout(8)
                    ->post('https://api.speedsms.vn/index.php/sms/send', [
                        'to' => [$soDienThoai],
                        'content' => $noiDung,
                        'sms_type' => 2,
                        'sender' => config('services.sms_brandname.brandname', 'JPM_MARINE'),
                    ]);
                $gatewayResponse = $response->json();
                $status = ($response->successful() && ($gatewayResponse['status'] ?? '') === 'success') ? 'thanh_cong' : 'that_bai';
            }
        } catch (\Throwable $e) {
            $status = 'that_bai';
            $gatewayResponse = ['error' => $e->getMessage()];
            Log::error("Queued Notification Gateway Error (log #{$this->logId}): " . $e->getMessage());
        }

        $log->update([
            'trang_thai' => $status,
            'phan_hoi_gateway' => $gatewayResponse,
        ]);
    }
}
