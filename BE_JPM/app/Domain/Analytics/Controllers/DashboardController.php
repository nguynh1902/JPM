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
            $period = $request->query('period', 'month');
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

            // 5. BIỂU ĐỒ DOANH THU & CHI PHÍ NHẬP HÀNG (6 THÁNG GẦN NHẤT)
            $months = [];
            $salesData = [];
            $procurementData = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = $now->copy()->subMonths($i);
                $mStart = $m->copy()->startOfMonth();
                $mEnd = $m->copy()->endOfMonth();
                $monthLabel = 'Tháng ' . $m->format('m');
                $months[] = $monthLabel;

                $monthlySale = (float) DonHang::whereBetween('created_at', [$mStart, $mEnd])
                    ->where('trang_thai', '!=', 4)
                    ->sum('tong_tien');
                $salesData[] = round($monthlySale / 1000000, 2); // Quy về triệu VNĐ cho biểu đồ gọn

                $monthlyProcurement = (float) DonNhapHang::whereBetween('created_at', [$mStart, $mEnd])
                    ->where('trang_thai', '!=', 3)
                    ->sum('tong_tien');
                $procurementData[] = round($monthlyProcurement / 1000000, 2); // Triệu VNĐ
            }

            // 6. PHÂN BỔ KÊNH BÁN (DISTRIBUTION)
            // 6. PHÂN BỔ LOẠI GIAO DỊCH (DISTRIBUTION: ĐƠN HÀNG VS BÁO GIÁ)
            $orderCount = (int) DonHang::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', '!=', 4)
                ->count();
            $quoteCount = (int) YeuCauBaoGia::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', '!=', 3)
                ->count();

            $totalDist = $orderCount + $quoteCount;
            $distPercentages = [
                'order' => $totalDist > 0 ? round(($orderCount / $totalDist) * 100, 1) : 0,
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
                        'categories' => $months,
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
                        'series' => [$orderCount, $quoteCount],
                        'labels' => ['Đơn Hàng Trực Tiếp', 'Yêu Cầu Báo Giá'],
                        'percentages' => $distPercentages,
                        'counts' => [
                            'order' => $orderCount,
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
