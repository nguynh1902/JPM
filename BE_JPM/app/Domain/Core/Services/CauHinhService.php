<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Models\CauHinh;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class CauHinhService
{
    const CACHE_KEY = 'public_site_settings';
    const CACHE_TTL_SECONDS = 86400; // 24 giờ

    /**
     * Lấy danh sách cấu hình chung phục vụ Client (Public).
     * Được cache trong RAM (Redis/File) với TTL 24h.
     *
     * @return array
     */
    public function getPublicSettings(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
                return $this->fetchSettingsFromDatabase();
            });
        } catch (\Throwable $e) {
            // Graceful fallback nếu cache không khả dụng
            return $this->fetchSettingsFromDatabase();
        }
    }

    /**
     * Lấy toàn bộ cấu hình chi tiết cho trang Admin.
     *
     * @return array
     */
    public function getAllForAdmin(): array
    {
        return CauHinh::orderBy('id', 'asc')->get()->toArray();
    }

    /**
     * Cập nhật hàng loạt cấu hình từ form Admin.
     *
     * @param array $settings - Mảng key-value ['homepage_youtube_url' => '...', ...]
     * @return array
     * @throws Exception
     */
    public function updateBatch(array $settings): array
    {
        DB::beginTransaction();
        try {
            foreach ($settings as $key => $value) {
                $valueToSave = is_string($value) ? trim($value) : $value;
                $group = str_starts_with((string) $key, 'seo_') ? 'seo' : 'general';
                $existing = CauHinh::where('khoa_cau_hinh', $key)->first();
                if ($existing) {
                    $existing->update([
                        'gia_tri'    => $valueToSave,
                        'updated_at' => now(),
                    ]);
                } else {
                    CauHinh::updateOrCreate(
                        ['khoa_cau_hinh' => $key],
                        [
                            'gia_tri'    => $valueToSave,
                            'nhom'       => $group,
                            'trang_thai' => 1,
                        ]
                    );
                }
            }

            DB::commit();

            // Xóa cache để Client nhận cấu hình mới ngay lập tức
            $this->clearSettingsCache();

            return $this->getPublicSettings();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Lỗi khi cập nhật cấu hình: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Xóa cache cài đặt.
     */
    public function clearSettingsCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Query dữ liệu từ Database và format thành dictionary key-value.
     */
    protected function fetchSettingsFromDatabase(): array
    {
        $configs = CauHinh::where('trang_thai', 1)->get();
        $result = [];

        foreach ($configs as $config) {
            $result[$config->khoa_cau_hinh] = $config->gia_tri ?? '';
        }

        // Bổ sung helper field homepage_youtube_id để Client dễ dùng trực tiếp
        if (!empty($result['homepage_youtube_url'])) {
            $result['homepage_youtube_id'] = $this->extractYoutubeId($result['homepage_youtube_url']);
        } else {
            $result['homepage_youtube_id'] = '5CVzOqvqI38'; // Default fallback
        }

        return $result;
    }

    /**
     * Trích xuất YouTube Video ID từ link bất kỳ.
     */
    public function extractYoutubeId(string $url): string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        // Nếu chuỗi nhập vào chính là ID 11 ký tự
        if (strlen(trim($url)) === 11 && !str_contains($url, '/')) {
            return trim($url);
        }

        return '5CVzOqvqI38';
    }
}
