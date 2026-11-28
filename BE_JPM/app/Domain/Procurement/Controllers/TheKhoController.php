<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Core\Resources\TheKhoResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class TheKhoController extends Controller
{
    /**
     * Chi tiết 1 giao dịch thẻ kho
     */
    public function show($id)
    {
        try {
            $item = TheKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
                'nguoiThucHien'
            ])->findOrFail($id);
            return response()->json(['success' => true, 'data' => new TheKhoResource($item)]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thẻ kho.'], 404);
        }
    }

    /**
     * Danh sách lịch sử giao dịch kho (Thẻ kho) có hỗ trợ lọc & phân trang chuẩn
     */
    public function index(Request $request)
    {
        try {
            $query = TheKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
                'nguoiThucHien'
            ])->orderBy('id', 'desc');

            if ($request->filled('warehouse_id')) {
                $query->where('id_kho', (int) $request->warehouse_id);
            }

            if ($request->filled('loai_giao_dich')) {
                $query->where('loai_giao_dich', $request->loai_giao_dich);
            }

            if ($request->filled('tu_ngay')) {
                $query->where('created_at', '>=', Carbon::parse($request->tu_ngay)->startOfDay());
            }

            if ($request->filled('den_ngay')) {
                $query->where('created_at', '<=', Carbon::parse($request->den_ngay)->endOfDay());
            }

            if ($request->filled('keyword')) {
                $kw = trim($request->keyword);
                $query->where(function ($q) use ($kw) {
                    $q->where('ma_tham_chieu', 'like', "%{$kw}%")
                      ->orWhere('ghi_chu', 'like', "%{$kw}%")
                      ->orWhereHas('phuTung', function ($sub) use ($kw) {
                          $sub->withTrashed()
                              ->where('ma_phu_tung', 'like', "%{$kw}%")
                              ->orWhere('ten_phu_tung', 'like', "%{$kw}%");
                      })
                      ->orWhereHas('sanPham', function ($sub) use ($kw) {
                          $sub->where('ma_san_pham', 'like', "%{$kw}%")
                              ->orWhere('ten_san_pham', 'like', "%{$kw}%");
                      });
                });
            }

            $perPage = $request->get('per_page', 50);
            if ($perPage === 'all') {
                $cappedAll = min(max((int) $request->get('limit', 2000), 1), 5000);
                $items = $query->limit($cappedAll)->get();
                return response()->json([
                    'success' => true,
                    'data' => TheKhoResource::collection($items),
                ]);
            }

            $capped = min(max((int) $perPage, 1), 200);
            $paginator = $query->paginate($capped);

            return response()->json([
                'success' => true,
                'data' => TheKhoResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Báo cáo tổng hợp Xuất - Nhập - Tồn theo kỳ (Tồn đầu kỳ, Nhập, Xuất, Điều chỉnh, Tồn cuối kỳ)
     */
    public function reportXNT(Request $request)
    {
        try {
            $start = $request->filled('tu_ngay')
                ? Carbon::parse($request->tu_ngay)->startOfDay()
                : Carbon::now()->startOfMonth()->startOfDay();
            $end = $request->filled('den_ngay')
                ? Carbon::parse($request->den_ngay)->endOfDay()
                : Carbon::now()->endOfDay();

            $warehouseId = $request->filled('warehouse_id') ? (int) $request->warehouse_id : null;
            $keyword = trim((string) $request->input('keyword', ''));

            $tonQuery = TonKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ]);
            if ($warehouseId) {
                $tonQuery->where('id_kho', $warehouseId);
            }
            $tonRows = $tonQuery->get();

            $ledgerQuery = TheKho::with([
                'kho' => fn($q) => $q->withTrashed(),
                'sanPham',
                'phuTung' => fn($q) => $q->withTrashed(),
            ])->where('created_at', '>=', $start);
            if ($warehouseId) {
                $ledgerQuery->where('id_kho', $warehouseId);
            }
            $ledgerRows = $ledgerQuery->get();

            $buckets = [];
            $ensureBucket = function ($row) use (&$buckets) {
                $loai = $row->loai_san_pham ?: 'phu_tung';
                $key = "{$row->id_kho}:{$loai}:{$row->id_san_pham}";
                if (isset($buckets[$key])) {
                    return $key;
                }

                $ma = $loai === 'san_pham'
                    ? ($row->sanPham->ma_san_pham ?? ('SP-' . $row->id_san_pham))
                    : ($row->phuTung->ma_phu_tung ?? ('PT-' . $row->id_san_pham));
                $ten = $loai === 'san_pham'
                    ? ($row->sanPham->ten_san_pham ?? ('Sản phẩm #' . $row->id_san_pham))
                    : ($row->phuTung->ten_phu_tung ?? ('Phụ tùng #' . $row->id_san_pham));
                $hinhAnh = $loai === 'san_pham'
                    ? ($row->sanPham->hinh_anh_chinh ?? null)
                    : ($row->phuTung->hinh_anh ?? null);

                $buckets[$key] = [
                    'key' => $key,
                    'warehouse_id' => (int) $row->id_kho,
                    'ten_kho' => $row->kho?->ten_kho ?? ('Kho #' . $row->id_kho),
                    'id_san_pham' => (int) $row->id_san_pham,
                    'loai_san_pham' => $loai,
                    'ma_san_pham' => $ma,
                    'ten_san_pham' => $ten,
                    'hinh_anh' => $hinhAnh,
                    'current_ton' => 0,
                    'so_luong_dang_giu' => 0,
                    'net_after_end' => 0,
                    'nhap_trong_ky' => 0,
                    'xuat_trong_ky' => 0,
                    'dieu_chinh_trong_ky' => 0,
                    'net_in_period' => 0,
                    'so_giao_dich' => 0,
                ];
                return $key;
            };

            foreach ($tonRows as $ton) {
                $key = $ensureBucket($ton);
                $buckets[$key]['current_ton'] = (int) $ton->so_luong_ton;
                $buckets[$key]['so_luong_dang_giu'] = (int) ($ton->so_luong_dang_giu ?? 0);
            }

            foreach ($ledgerRows as $tx) {
                $key = $ensureBucket($tx);
                $delta = (int) $tx->so_luong_thay_doi;
                $txTime = Carbon::parse($tx->created_at);

                if ($txTime->gt($end)) {
                    $buckets[$key]['net_after_end'] += $delta;
                    continue;
                }

                $buckets[$key]['net_in_period'] += $delta;
                $buckets[$key]['so_giao_dich']++;

                if ($tx->loai_giao_dich === 'NHAP_KHO') {
                    $buckets[$key]['nhap_trong_ky'] += max(0, $delta);
                } elseif ($tx->loai_giao_dich === 'XUAT_KHO') {
                    $buckets[$key]['xuat_trong_ky'] += abs($delta);
                } else {
                    $buckets[$key]['dieu_chinh_trong_ky'] += $delta;
                }
            }

            $kwLower = $keyword !== '' ? mb_strtolower($keyword, 'UTF-8') : '';
            $result = [];
            $totals = [
                'ton_dau_ky' => 0,
                'nhap_trong_ky' => 0,
                'xuat_trong_ky' => 0,
                'dieu_chinh_trong_ky' => 0,
                'ton_cuoi_ky' => 0,
                'so_luong_dang_giu' => 0,
            ];

            foreach ($buckets as $b) {
                if ($kwLower !== '') {
                    $matchCode = str_contains(mb_strtolower((string) $b['ma_san_pham'], 'UTF-8'), $kwLower);
                    $matchName = str_contains(mb_strtolower((string) $b['ten_san_pham'], 'UTF-8'), $kwLower);
                    if (!$matchCode && !$matchName) {
                        continue;
                    }
                }

                $tonCuoiKy = (int) $b['current_ton'] - (int) $b['net_after_end'];
                $tonDauKy = $tonCuoiKy - (int) $b['net_in_period'];

                $row = [
                    'key' => $b['key'],
                    'warehouse_id' => $b['warehouse_id'],
                    'ten_kho' => $b['ten_kho'],
                    'id_san_pham' => $b['id_san_pham'],
                    'loai_san_pham' => $b['loai_san_pham'],
                    'ma_san_pham' => $b['ma_san_pham'],
                    'ten_san_pham' => $b['ten_san_pham'],
                    'hinh_anh' => $b['hinh_anh'],
                    'ton_dau_ky' => $tonDauKy,
                    'nhap_trong_ky' => (int) $b['nhap_trong_ky'],
                    'xuat_trong_ky' => (int) $b['xuat_trong_ky'],
                    'dieu_chinh_trong_ky' => (int) $b['dieu_chinh_trong_ky'],
                    'ton_cuoi_ky' => $tonCuoiKy,
                    'so_luong_dang_giu' => (int) $b['so_luong_dang_giu'],
                    'so_giao_dich' => (int) $b['so_giao_dich'],
                ];

                $totals['ton_dau_ky'] += $tonDauKy;
                $totals['nhap_trong_ky'] += $row['nhap_trong_ky'];
                $totals['xuat_trong_ky'] += $row['xuat_trong_ky'];
                $totals['dieu_chinh_trong_ky'] += $row['dieu_chinh_trong_ky'];
                $totals['ton_cuoi_ky'] += $tonCuoiKy;
                $totals['so_luong_dang_giu'] += $row['so_luong_dang_giu'];

                $result[] = $row;
            }

            usort($result, static function ($a, $b) {
                $txDiff = $b['so_giao_dich'] <=> $a['so_giao_dich'];
                return $txDiff !== 0 ? $txDiff : strcmp((string) $a['ma_san_pham'], (string) $b['ma_san_pham']);
            });

            return response()->json([
                'success' => true,
                'data' => $result,
                'totals' => $totals,
                'period' => [
                    'tu_ngay' => $start->toDateString(),
                    'den_ngay' => $end->toDateString(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
