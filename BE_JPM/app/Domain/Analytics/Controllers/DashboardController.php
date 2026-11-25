<?php

namespace App\Domain\Analytics\Controllers;

use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        try {
            $period = (string) $request->query('period', 'year');
            $startDateParam = $request->query('start_date');
            $endDateParam = $request->query('end_date');

            if ($period === 'custom' && $startDateParam && $endDateParam) {
                $parsedStart = Carbon::parse($startDateParam)->startOfDay();
                $parsedEnd = Carbon::parse($endDateParam)->endOfDay();
                if ($parsedStart->gt($parsedEnd)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.',
                    ], 422);
                }
                if ($parsedStart->diffInDays($parsedEnd) > 366) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Khoảng thời gian thống kê tối đa là 366 ngày.',
                    ], 422);
                }
            }

            $cacheKey = 'dashboard_stats:' . md5($period . '|' . ($startDateParam ?? '') . '|' . ($endDateParam ?? '') . '|' . now()->format('Y-m-d-H-i'));

            $payload = Cache::remember($cacheKey, 60, function () use ($period, $startDateParam, $endDateParam) {
                return $this->buildStatsPayload($period, $startDateParam, $endDateParam);
            });

            return response()->json([
                'success' => true,
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải dữ liệu Dashboard.',
            ], 500);
        }
    }

    private function buildStatsPayload(string $period, ?string $startDateParam, ?string $endDateParam): array
    {
        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        if ($period === 'custom' && $startDateParam && $endDateParam) {
            $startDate = Carbon::parse($startDateParam)->startOfDay();
            $endDate = Carbon::parse($endDateParam)->endOfDay();
            $daysDiff = $startDate->diffInDays($endDate) + 1;
            $prevStart = $startDate->copy()->subDays($daysDiff)->startOfDay();
            $prevEnd = $startDate->copy()->subSecond();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $prevStart = $now->copy()->subDay()->startOfDay();
                    $prevEnd = $now->copy()->subDay()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = $now->copy()->subDay()->startOfDay();
                    $endDate = $now->copy()->subDay()->endOfDay();
                    $prevStart = $now->copy()->subDays(2)->startOfDay();
                    $prevEnd = $now->copy()->subDays(2)->endOfDay();
                    break;
                case 'last_7_days':
                    $startDate = $now->copy()->subDays(6)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $prevStart = $now->copy()->subDays(13)->startOfDay();
                    $prevEnd = $now->copy()->subDays(7)->endOfDay();
                    break;
                case 'last_30_days':
                    $startDate = $now->copy()->subDays(29)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $prevStart = $now->copy()->subDays(59)->startOfDay();
                    $prevEnd = $now->copy()->subDays(30)->endOfDay();
                    break;
                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $prevStart = $now->copy()->subWeek()->startOfWeek();
                    $prevEnd = $now->copy()->subWeek()->endOfWeek();
                    break;
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    $prevStart = $now->copy()->subMonths(2)->startOfMonth();
                    $prevEnd = $now->copy()->subMonths(2)->endOfMonth();
                    break;
                case 'year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    $prevStart = $now->copy()->subYear()->startOfYear();
                    $prevEnd = $now->copy()->subYear()->endOfYear();
                    break;
                case 'all':
                    $startDate = Carbon::create(max(2024, $now->year - 2), 1, 1)->startOfYear();
                    $endDate = $now->copy()->endOfDay();
                    $prevStart = Carbon::createFromTimestamp(0);
                    $prevEnd = Carbon::createFromTimestamp(0);
                    break;
                case 'month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $prevStart = $now->copy()->subMonth()->startOfMonth();
                    $prevEnd = $now->copy()->subMonth()->endOfMonth();
                    break;
            }
        }

        // 1. KPI: TỔNG DOANH THU (Chỉ tính đơn hợp lệ: bỏ đơn hủy trang_thai = 4)
        $totalSales = (float) DonHang::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', '!=', 4)
            ->sum('tong_tien');

        $prevSales = (float) DonHang::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('trang_thai', '!=', 4)
            ->sum('tong_tien');

        $todaySales = (float) DonHang::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('trang_thai', '!=', 4)
            ->sum('tong_tien');

        $salesGrowth = $prevSales > 0
            ? round((($totalSales - $prevSales) / $prevSales) * 100, 1)
            : ($totalSales > 0 ? 100 : 0);

        // 2. KPI: TỔNG ĐƠN HÀNG (Loại trừ đơn đã hủy để khớp doanh thu)
        $totalOrders = (int) DonHang::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', '!=', 4)
            ->count();
        $prevOrders = (int) DonHang::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('trang_thai', '!=', 4)
            ->count();
        $todayOrders = (int) DonHang::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('trang_thai', '!=', 4)
            ->count();
        $ordersGrowth = $prevOrders > 0
            ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        // 3. KPI: YÊU CẦU BÁO GIÁ (Loại trừ báo giá đã hủy trang_thai = 3)
        $totalQuotes = (int) YeuCauBaoGia::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', '!=', 3)
            ->count();
        $pendingQuotes = (int) YeuCauBaoGia::where('trang_thai', 0)->count();
        $todayQuotes = (int) YeuCauBaoGia::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('trang_thai', '!=', 3)
            ->count();
        $prevQuotes = (int) YeuCauBaoGia::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('trang_thai', '!=', 3)
            ->count();
        $quotesGrowth = $prevQuotes > 0
            ? round((($totalQuotes - $prevQuotes) / $prevQuotes) * 100, 1)
            : ($totalQuotes > 0 ? 100 : 0);

        // 4. KPI: CẢNH BÁO THIẾU HÀNG / GOM PO
        $replenishStats = TonKho::where('so_luong_dang_giu', '>', 0)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(so_luong_dang_giu), 0) as total_qty')
            ->first();
        $itemsNeedReplenish = (int) ($replenishStats->cnt ?? 0);
        $totalQtyNeedReplenish = (int) ($replenishStats->total_qty ?? 0);
        $openPOCount = (int) DonNhapHang::whereIn('trang_thai', [0, 1])->count();

        // 5. BIỂU ĐỒ DOANH THU & CHI PHÍ NHẬP HÀNG — Gom chỉ trong 2 truy vấn nhẹ (thay cho 62-648 queries trong vòng lặp)
        $buckets = $this->buildChartBuckets($period, $startDate, $endDate, $now);

        $chartStart = $buckets[0]['start'] ?? $startDate;
        $chartEnd = $buckets[count($buckets) - 1]['end'] ?? $endDate;

        $orderRows = DonHang::whereBetween('created_at', [$chartStart, $chartEnd])
            ->where('trang_thai', '!=', 4)
            ->get(['created_at', 'tong_tien']);

        $poRows = DonNhapHang::whereBetween('created_at', [$chartStart, $chartEnd])
            ->where('trang_thai', '!=', 3)
            ->get(['created_at', 'tong_tien']);

        $chartCategories = [];
        $salesData = [];
        $procurementData = [];

        foreach ($buckets as $bucket) {
            $chartCategories[] = $bucket['label'];
            $bStartTs = $bucket['start']->timestamp;
            $bEndTs = $bucket['end']->timestamp;

            $sVal = 0.0;
            foreach ($orderRows as $row) {
                $ts = $row->created_at ? $row->created_at->timestamp : 0;
                if ($ts >= $bStartTs && $ts <= $bEndTs) {
                    $sVal += (float) $row->tong_tien;
                }
            }

            $pVal = 0.0;
            foreach ($poRows as $row) {
                $ts = $row->created_at ? $row->created_at->timestamp : 0;
                if ($ts >= $bStartTs && $ts <= $bEndTs) {
                    $pVal += (float) $row->tong_tien;
                }
            }

            $salesData[] = round($sVal / 1000000, 2);
            $procurementData[] = round($pVal / 1000000, 2);
        }

        // 6. PHÂN BỔ LUỒNG GIAO DỊCH (1 query groupBy cho DonHang)
        $orderDist = DonHang::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', '!=', 4)
            ->selectRaw('trang_thai, COUNT(*) as cnt')
            ->groupBy('trang_thai')
            ->pluck('cnt', 'trang_thai');

        $completedOrderCount = (int) ($orderDist[3] ?? 0);
        $processingOrderCount = (int) (($orderDist[0] ?? 0) + ($orderDist[1] ?? 0) + ($orderDist[2] ?? 0));
        $quoteCount = $totalQuotes;

        $totalDist = $completedOrderCount + $processingOrderCount + $quoteCount;
        $distPercentages = [
            'completed' => $totalDist > 0 ? round(($completedOrderCount / $totalDist) * 100, 1) : 0,
            'processing' => $totalDist > 0 ? round(($processingOrderCount / $totalDist) * 100, 1) : 0,
            'quote' => $totalDist > 0 ? round(($quoteCount / $totalDist) * 100, 1) : 0,
        ];

        // 7. GIAO DỊCH GẦN ĐÂY — Sắp xếp bằng Unix timestamp trước khi format d/m/Y H:i
        Carbon::setLocale('vi');
        $recentOrders = DonHang::with('khachHang')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($order) {
                $statusMap = [
                    0 => ['label' => 'CHỜ XÁC NHẬN', 'class' => 'bg-pastel-warning'],
                    1 => ['label' => 'ĐANG XỬ LÝ', 'class' => 'bg-pastel-secondary'],
                    2 => ['label' => 'ĐANG GIAO', 'class' => 'bg-pastel-info'],
                    3 => ['label' => 'HOÀN TẤT', 'class' => 'bg-pastel-success'],
                    4 => ['label' => 'ĐÃ HỦY', 'class' => 'bg-pastel-danger'],
                ];
                $st = $statusMap[$order->trang_thai] ?? ['label' => 'KHÔNG RÕ', 'class' => 'bg-pastel-secondary'];
                $name = $order->khachHang ? $order->khachHang->ho_ten : ($order->khach_hang_ten ?? 'Khách mua trực tiếp');
                $phone = $order->khachHang ? $order->khachHang->so_dien_thoai : ($order->khach_hang_sdt ?? 'N/A');

                return [
                    'id' => $order->id,
                    'type' => 'order',
                    'type_label' => 'ĐƠN HÀNG',
                    'type_badge' => 'bg-pastel-warning',
                    'code' => $order->ma_don_hang ?? ('DH-' . $order->id),
                    'customer_name' => $name,
                    'customer_phone' => $phone,
                    'amount' => (float) $order->tong_tien,
                    'status_label' => $st['label'],
                    'status_class' => $st['class'],
                    'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                    'time_ago' => $order->created_at ? $order->created_at->diffForHumans() : '',
                    '_sort_ts' => $order->created_at ? $order->created_at->timestamp : 0,
                ];
            });

        $recentQuotes = YeuCauBaoGia::orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($quote) {
                $statusMap = [
                    0 => ['label' => 'CHỜ BÁO GIÁ', 'class' => 'bg-pastel-warning'],
                    1 => ['label' => 'CHỜ KHÁCH CHỐT', 'class' => 'bg-pastel-info'],
                    2 => ['label' => 'ĐÃ CHỐT', 'class' => 'bg-pastel-success'],
                    3 => ['label' => 'ĐÃ HỦY', 'class' => 'bg-pastel-danger'],
                    4 => ['label' => 'HOÀN TẤT', 'class' => 'bg-pastel-success'],
                ];
                $st = $statusMap[$quote->trang_thai] ?? ['label' => 'CHỜ XỬ LÝ', 'class' => 'bg-pastel-warning'];

                return [
                    'id' => $quote->id,
                    'type' => 'quote',
                    'type_label' => 'BÁO GIÁ',
                    'type_badge' => 'bg-pastel-info',
                    'code' => $quote->ma_yeu_cau ?? ('BG-' . $quote->id),
                    'customer_name' => $quote->khach_hang_ten ?? 'Khách yêu cầu báo giá',
                    'customer_phone' => $quote->khach_hang_sdt ?? 'N/A',
                    'amount' => (float) ($quote->tong_tien ?? 0),
                    'status_label' => $st['label'],
                    'status_class' => $st['class'],
                    'created_at' => $quote->created_at ? $quote->created_at->format('d/m/Y H:i') : '',
                    'time_ago' => $quote->created_at ? $quote->created_at->diffForHumans() : '',
                    '_sort_ts' => $quote->created_at ? $quote->created_at->timestamp : 0,
                ];
            });

        $allActivities = collect($recentOrders)
            ->concat($recentQuotes)
            ->sortByDesc('_sort_ts')
            ->values()
            ->take(6)
            ->map(function ($item) {
                unset($item['_sort_ts']);
                return $item;
            })
            ->all();

        return [
            'kpis' => [
                'total_sales' => $totalSales,
                'sales_today' => $todaySales,
                'sales_growth' => $salesGrowth,

                'total_orders' => $totalOrders,
                'orders_today' => $todayOrders,
                'orders_growth' => $ordersGrowth,

                'total_quotes' => $totalQuotes,
                'pending_quotes' => $pendingQuotes,
                'quotes_today' => $todayQuotes,
                'quotes_growth' => $quotesGrowth,

                'items_need_replenish' => $itemsNeedReplenish,
                'total_qty_need_replenish' => $totalQtyNeedReplenish,
                'open_po_count' => $openPOCount,
            ],
            'revenue_chart' => [
                'categories' => $chartCategories,
                'series' => [
                    [
                        'name' => 'Doanh Thu Bán Hàng (Tr.đ)',
                        'data' => $salesData,
                    ],
                    [
                        'name' => 'Chi Phí Nhập Kho PO (Tr.đ)',
                        'data' => $procurementData,
                    ],
                ],
                'current_period_revenue' => $totalSales,
                'growth_rate' => $salesGrowth,
            ],
            'distribution_chart' => [
                'series' => [$completedOrderCount, $processingOrderCount, $quoteCount],
                'labels' => ['Đơn Hoàn Tất', 'Đơn Đang Xử Lý', 'Yêu Cầu Báo Giá'],
                'percentages' => $distPercentages,
                'counts' => [
                    'completed' => $completedOrderCount,
                    'processing' => $processingOrderCount,
                    'quote' => $quoteCount,
                ],
            ],
            'recent_activities' => $allActivities,
        ];
    }

    private function buildChartBuckets(string $period, Carbon $startDate, Carbon $endDate, Carbon $now): array
    {
        $buckets = [];

        if ($period === 'custom' || in_array($period, ['last_7_days', 'last_30_days', 'last_month', 'yesterday'], true)) {
            $days = $startDate->diffInDays($endDate);
            if ($days === 0) {
                foreach ([['00h - 06h', 0, 6], ['06h - 12h', 6, 12], ['12h - 18h', 12, 18], ['18h - 24h', 18, 24]] as [$label, $hStart, $hEnd]) {
                    $buckets[] = [
                        'label' => $label,
                        'start' => $startDate->copy()->addHours($hStart),
                        'end' => $hEnd === 24 ? $startDate->copy()->endOfDay() : $startDate->copy()->addHours($hEnd)->subSecond(),
                    ];
                }
            } elseif ($days <= 31) {
                for ($d = 0; $d <= $days; $d++) {
                    $day = $startDate->copy()->addDays($d);
                    $buckets[] = [
                        'label' => $day->format('d/m'),
                        'start' => $day->copy()->startOfDay(),
                        'end' => $day->copy()->endOfDay(),
                    ];
                }
            } elseif ($days <= 120) {
                $cursor = $startDate->copy();
                $weekIdx = 1;
                while ($cursor->lte($endDate)) {
                    $wEnd = $cursor->copy()->addDays(6);
                    if ($wEnd->gt($endDate)) {
                        $wEnd = $endDate->copy();
                    }
                    $buckets[] = [
                        'label' => 'T' . $weekIdx . ' (' . $cursor->format('d/m') . '-' . $wEnd->format('d/m') . ')',
                        'start' => $cursor->copy()->startOfDay(),
                        'end' => $wEnd->copy()->endOfDay(),
                    ];
                    $cursor = $wEnd->copy()->addDay();
                    $weekIdx++;
                }
            } else {
                $cursor = $startDate->copy()->startOfMonth();
                while ($cursor->lte($endDate)) {
                    $mEnd = $cursor->copy()->endOfMonth();
                    if ($mEnd->gt($endDate)) {
                        $mEnd = $endDate->copy();
                    }
                    $buckets[] = [
                        'label' => 'T' . $cursor->format('m/y'),
                        'start' => $cursor->copy()->startOfDay(),
                        'end' => $mEnd->copy()->endOfDay(),
                    ];
                    $cursor = $cursor->copy()->addMonth()->startOfMonth();
                }
            }

            return $buckets;
        }

        switch ($period) {
            case 'today':
                foreach ([['00h - 06h', 0, 6], ['06h - 12h', 6, 12], ['12h - 18h', 12, 18], ['18h - 24h', 18, 24]] as [$label, $hStart, $hEnd]) {
                    $buckets[] = [
                        'label' => $label,
                        'start' => $now->copy()->startOfDay()->addHours($hStart),
                        'end' => $hEnd === 24 ? $now->copy()->endOfDay() : $now->copy()->startOfDay()->addHours($hEnd)->subSecond(),
                    ];
                }
                break;

            case 'week':
                $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
                $startOfWeek = $now->copy()->startOfWeek();
                for ($d = 0; $d < 7; $d++) {
                    $target = $startOfWeek->copy()->addDays($d);
                    $buckets[] = [
                        'label' => $dayNames[$d] . ' (' . $target->format('d/m') . ')',
                        'start' => $target->copy()->startOfDay(),
                        'end' => $target->copy()->endOfDay(),
                    ];
                }
                break;

            case 'month':
                $daysInMonth = $now->daysInMonth;
                $weeks = [
                    ['Tuần 1 (01-07)', 1, 7],
                    ['Tuần 2 (08-14)', 8, 14],
                    ['Tuần 3 (15-21)', 15, 21],
                    ['Tuần 4 (22-28)', 22, 28],
                ];
                if ($daysInMonth > 28) {
                    $weeks[] = ['Tuần 5 (29-' . $daysInMonth . ')', 29, $daysInMonth];
                }
                foreach ($weeks as [$label, $dStart, $dEnd]) {
                    $buckets[] = [
                        'label' => $label,
                        'start' => $now->copy()->day($dStart)->startOfDay(),
                        'end' => $now->copy()->day($dEnd)->endOfDay(),
                    ];
                }
                break;

            case 'all':
                $currentYear = $now->year;
                $startYear = max(2024, $currentYear - 2);
                for ($y = $startYear; $y <= $currentYear; $y++) {
                    $buckets[] = [
                        'label' => 'Năm ' . $y,
                        'start' => Carbon::create($y, 1, 1)->startOfYear(),
                        'end' => Carbon::create($y, 12, 31)->endOfYear(),
                    ];
                }
                break;

            case 'year':
            default:
                $currentMonthNum = $now->month;
                $currentYear = $now->year;
                for ($m = 1; $m <= $currentMonthNum; $m++) {
                    $buckets[] = [
                        'label' => 'Tháng ' . str_pad((string) $m, 2, '0', STR_PAD_LEFT),
                        'start' => Carbon::create($currentYear, $m, 1)->startOfMonth(),
                        'end' => Carbon::create($currentYear, $m, 1)->endOfMonth(),
                    ];
                }
                break;
        }

        return $buckets;
    }
}
