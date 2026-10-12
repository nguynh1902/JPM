<?php

namespace App\Domain\Analytics\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Procurement\Models\DonNhapHang;
use App\Domain\Procurement\Models\TonKho;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        try {
            $period = $request->query('period', 'year');
            $now = Carbon::now();

            // Xác định mốc thời gian lọc
            switch ($period) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $prevStart = $now->copy()->subDay()->startOfDay();
                    $prevEnd = $now->copy()->subDay()->endOfDay();
                    break;
                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $prevStart = $now->copy()->subWeek()->startOfWeek();
                    $prevEnd = $now->copy()->subWeek()->endOfWeek();
                    break;
                case 'year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    $prevStart = $now->copy()->subYear()->startOfYear();
                    $prevEnd = $now->copy()->subYear()->endOfYear();
                    break;
                case 'all':
                    $startDate = Carbon::createFromTimestamp(0);
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

            // 1. KPI: TỔNG DOANH THU (Total Sales)
            $totalSales = (float) DonHang::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', '!=', 4) // Bỏ qua đơn hủy
                ->sum('tong_tien');

            $prevSales = (float) DonHang::whereBetween('created_at', [$prevStart, $prevEnd])
                ->where('trang_thai', '!=', 4)
                ->sum('tong_tien');

            $todaySales = (float) DonHang::whereDate('created_at', Carbon::today())
                ->where('trang_thai', '!=', 4)
                ->sum('tong_tien');

            $salesGrowth = $prevSales > 0 
                ? round((($totalSales - $prevSales) / $prevSales) * 100, 1) 
                : ($totalSales > 0 ? 100 : 0);

            // 2. KPI: TỔNG ĐƠN HÀNG (Total Orders)
            $totalOrders = (int) DonHang::whereBetween('created_at', [$startDate, $endDate])->count();
            $prevOrders = (int) DonHang::whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $todayOrders = (int) DonHang::whereDate('created_at', Carbon::today())->count();
            $ordersGrowth = $prevOrders > 0 
                ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1) 
                : ($totalOrders > 0 ? 100 : 0);

            // 3. KPI: YÊU CẦU BÁO GIÁ (Quotes)
            $totalQuotes = (int) YeuCauBaoGia::whereBetween('created_at', [$startDate, $endDate])->count();
            $pendingQuotes = (int) YeuCauBaoGia::where('trang_thai', 0)->count();
            $todayQuotes = (int) YeuCauBaoGia::whereDate('created_at', Carbon::today())->count();
            $prevQuotes = (int) YeuCauBaoGia::whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $quotesGrowth = $prevQuotes > 0
                ? round((($totalQuotes - $prevQuotes) / $prevQuotes) * 100, 1)
                : ($totalQuotes > 0 ? 100 : 0);

            // 4. KPI: CẢNH BÁO THIẾU HÀNG / GOM PO (Inventory Demands)
            $itemsNeedReplenish = (int) TonKho::where('so_luong_dang_giu', '>', 0)->count();
            $totalQtyNeedReplenish = (int) TonKho::where('so_luong_dang_giu', '>', 0)->sum('so_luong_dang_giu');
            $openPOCount = (int) DonNhapHang::whereIn('trang_thai', [0, 1])->count();

            // 5. BIỂU ĐỒ DOANH THU & CHI PHÍ NHẬP HÀNG THEO KỲ LỌC ($period)
            $chartCategories = [];
            $salesData = [];
            $procurementData = [];

            switch ($period) {
                case 'today':
                    // Chia theo 4 khung giờ trong ngày
                    $slots = [
                        ['label' => '00h - 06h', 'start' => 0, 'end' => 6],
                        ['label' => '06h - 12h', 'start' => 6, 'end' => 12],
                        ['label' => '12h - 18h', 'start' => 12, 'end' => 18],
                        ['label' => '18h - 24h', 'start' => 18, 'end' => 24],
                    ];
                    foreach ($slots as $slot) {
                        $chartCategories[] = $slot['label'];
                        $slotStart = $now->copy()->startOfDay()->addHours($slot['start']);
                        $slotEnd = $slot['end'] === 24
                            ? $now->copy()->endOfDay()
                            : $now->copy()->startOfDay()->addHours($slot['end'])->subSecond();

                        $sVal = (float) DonHang::whereBetween('created_at', [$slotStart, $slotEnd])
                            ->where('trang_thai', '!=', 4)
                            ->sum('tong_tien');
                        $pVal = (float) DonNhapHang::whereBetween('created_at', [$slotStart, $slotEnd])
                            ->where('trang_thai', '!=', 3)
                            ->sum('tong_tien');

                        $salesData[] = round($sVal / 1000000, 2);
                        $procurementData[] = round($pVal / 1000000, 2);
                    }
                    break;

                case 'week':
                    // Chia theo 7 ngày trong tuần hiện tại (Thứ 2 -> Chủ nhật)
                    $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
                    $startOfWeek = $now->copy()->startOfWeek();
                    for ($d = 0; $d < 7; $d++) {
                        $targetDate = $startOfWeek->copy()->addDays($d);
                        $chartCategories[] = $dayNames[$d] . ' (' . $targetDate->format('d/m') . ')';

                        $dStart = $targetDate->copy()->startOfDay();
                        $dEnd = $targetDate->copy()->endOfDay();

                        $sVal = (float) DonHang::whereBetween('created_at', [$dStart, $dEnd])
                            ->where('trang_thai', '!=', 4)
                            ->sum('tong_tien');
                        $pVal = (float) DonNhapHang::whereBetween('created_at', [$dStart, $dEnd])
                            ->where('trang_thai', '!=', 3)
                            ->sum('tong_tien');

                        $salesData[] = round($sVal / 1000000, 2);
                        $procurementData[] = round($pVal / 1000000, 2);
                    }
                    break;

                case 'month':
                    // Chia theo các tuần trong tháng hiện tại (Tuần 1 -> Tuần 4/5)
                    $daysInMonth = $now->daysInMonth;
                    $currentMonthStr = $now->format('m');
                    $weeks = [
                        ['label' => 'Tuần 1 (01-07)', 'start' => 1, 'end' => 7],
                        ['label' => 'Tuần 2 (08-14)', 'start' => 8, 'end' => 14],
                        ['label' => 'Tuần 3 (15-21)', 'start' => 15, 'end' => 21],
                        ['label' => 'Tuần 4 (22-28)', 'start' => 22, 'end' => 28],
                    ];
                    if ($daysInMonth > 28) {
                        $weeks[] = [
                            'label' => 'Tuần 5 (29-' . $daysInMonth . ')',
                            'start' => 29,
                            'end' => $daysInMonth
                        ];
                    }

                    foreach ($weeks as $w) {
                        $chartCategories[] = $w['label'];
                        $wStart = $now->copy()->day($w['start'])->startOfDay();
                        $wEnd = $now->copy()->day($w['end'])->endOfDay();

                        $sVal = (float) DonHang::whereBetween('created_at', [$wStart, $wEnd])
                            ->where('trang_thai', '!=', 4)
                            ->sum('tong_tien');
                        $pVal = (float) DonNhapHang::whereBetween('created_at', [$wStart, $wEnd])
                            ->where('trang_thai', '!=', 3)
                            ->sum('tong_tien');

                        $salesData[] = round($sVal / 1000000, 2);
                        $procurementData[] = round($pVal / 1000000, 2);
                    }
                    break;

                case 'all':
                    // Hiển thị theo từng năm (từ 2024 đến năm hiện tại)
                    $currentYear = $now->year;
                    $startYear = max(2024, $currentYear - 2);
                    for ($y = $startYear; $y <= $currentYear; $y++) {
                        $chartCategories[] = 'Năm ' . $y;
                        $yStart = Carbon::create($y, 1, 1)->startOfYear();
                        $yEnd = Carbon::create($y, 12, 31)->endOfYear();

                        $sVal = (float) DonHang::whereBetween('created_at', [$yStart, $yEnd])
                            ->where('trang_thai', '!=', 4)
                            ->sum('tong_tien');
                        $pVal = (float) DonNhapHang::whereBetween('created_at', [$yStart, $yEnd])
                            ->where('trang_thai', '!=', 3)
                            ->sum('tong_tien');

                        $salesData[] = round($sVal / 1000000, 2);
                        $procurementData[] = round($pVal / 1000000, 2);
                    }
                    break;

                case 'year':
                default:
                    // Từ Tháng 01 đến Tháng hiện tại của năm (tự thêm cột khi qua tháng mới đến khi đủ 12 tháng)
                    $currentMonthNum = $now->month;
                    $currentYear = $now->year;
                    for ($m = 1; $m <= $currentMonthNum; $m++) {
                        $chartCategories[] = 'Tháng ' . str_pad($m, 2, '0', STR_PAD_LEFT);
                        $mStart = Carbon::create($currentYear, $m, 1)->startOfMonth();
                        $mEnd = Carbon::create($currentYear, $m, 1)->endOfMonth();

                        $sVal = (float) DonHang::whereBetween('created_at', [$mStart, $mEnd])
                            ->where('trang_thai', '!=', 4)
                            ->sum('tong_tien');
                        $pVal = (float) DonNhapHang::whereBetween('created_at', [$mStart, $mEnd])
                            ->where('trang_thai', '!=', 3)
                            ->sum('tong_tien');

                        $salesData[] = round($sVal / 1000000, 2);
                        $procurementData[] = round($pVal / 1000000, 2);
                    }
                    break;
            }

            // 6. PHÂN BỔ LUỒNG GIAO DỊCH (DISTRIBUTION: ĐƠN HOÀN TẤT, ĐANG XỬ LÝ, YÊU CẦU BÁO GIÁ)
            $completedOrderCount = (int) DonHang::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', 3)
                ->count();
            $processingOrderCount = (int) DonHang::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('trang_thai', [0, 1, 2])
                ->count();
            $quoteCount = (int) YeuCauBaoGia::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', '!=', 3)
                ->count();

            $totalDist = $completedOrderCount + $processingOrderCount + $quoteCount;
            $distPercentages = [
                'completed' => $totalDist > 0 ? round(($completedOrderCount / $totalDist) * 100, 1) : 0,
                'processing' => $totalDist > 0 ? round(($processingOrderCount / $totalDist) * 100, 1) : 0,
                'quote' => $totalDist > 0 ? round(($quoteCount / $totalDist) * 100, 1) : 0,
            ];

            // 7. GIAO DỊCH GẦN ĐÂY (RECENT ACTIVITIES - CHUẨN ĐỒNG BỘ VỚI TRANG ĐƠN HÀNG)
            $recentOrders = DonHang::with('khachHang')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($order) {
                    $statusMap = [
                        0 => ['label' => 'CHỜ XÁC NHẬN', 'class' => 'bg-pastel-warning'],
                        1 => ['label' => 'ĐANG XỬ LÝ', 'class' => 'bg-pastel-info'],
                        2 => ['label' => 'ĐANG GIAO', 'class' => 'bg-pastel-primary'],
                        3 => ['label' => 'HOÀN TẤT', 'class' => 'bg-pastel-success'],
                        4 => ['label' => 'ĐÃ HỦY', 'class' => 'bg-pastel-danger'],
                    ];
                    $st = $statusMap[$order->trang_thai] ?? ['label' => 'KHÔNG RÕ', 'class' => 'bg-pastel-secondary'];
                    Carbon::setLocale('vi');
                    $name = $order->khachHang ? $order->khachHang->ho_ten : ($order->khach_hang_ten ?? ($order->ten_khach_hang ?? 'Khách mua trực tiếp'));
                    $phone = $order->khachHang ? $order->khachHang->so_dien_thoai : ($order->khach_hang_sdt ?? ($order->so_dien_thoai ?? 'N/A'));

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
                    ];
                    $st = $statusMap[$quote->trang_thai] ?? ['label' => 'CHỜ XỬ LÝ', 'class' => 'bg-pastel-warning'];
                    Carbon::setLocale('vi');

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
                    ];
                });

            $allActivities = collect($recentOrders)->concat($recentQuotes)
                ->sortByDesc('created_at')
                ->values()
                ->take(6);

            return response()->json([
                'success' => true,
                'data' => [
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
                            ]
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
                        ]
                    ],
                    'recent_activities' => $allActivities
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải dữ liệu Dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}
