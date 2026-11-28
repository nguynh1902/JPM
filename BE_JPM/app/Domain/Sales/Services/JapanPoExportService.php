<?php

namespace App\Domain\Sales\Services;

use App\Domain\PIM\Models\PhuTung;
use App\Domain\Procurement\Models\TonKho;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Cell\NumericCell;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class JapanPoExportService
{
    /**
     * Tạo file .xlsx chuẩn PO đặt hàng Nhật gồm 2 sheet:
     * - Sheet 1: Tổng hợp gom theo Mã phụ tùng OEM (gửi nhà cung cấp Nhật, không lộ thông tin khách lẻ)
     * - Sheet 2: Chi tiết theo từng đơn hàng (nội bộ JPM đối chiếu chia hàng khi nhập kho)
     *
     * @return string Đường dẫn file .xlsx tạm
     */
    public function buildWorkbook(Collection $orders): string
    {
        $partByCode = $this->loadFallbackPartsByCode($orders);
        $stockMap = $this->loadStockSummaryMap($orders, $partByCode);

        $aggregated = [];
        $detailRows = [];
        $totalOrderedQty = 0;

        foreach ($orders as $order) {
            foreach ($order->chiTiet as $item) {
                $qty = max(1, (int) ($item->so_luong ?? 1));
                $totalOrderedQty += $qty;

                $resolved = $this->resolveItemMeta($item, $partByCode, $stockMap);
                $groupKey = strtoupper($resolved['brand'] . '|' . $resolved['part_no'] . '|' . $resolved['part_name']);

                if (!isset($aggregated[$groupKey])) {
                    $aggregated[$groupKey] = [
                        'brand'        => $resolved['brand'],
                        'part_no'      => $resolved['part_no'],
                        'part_name'    => $resolved['part_name'],
                        'models'       => [],
                        'qty'          => 0,
                        'stock_ton'    => $resolved['stock_ton'],
                        'stock_giu'    => $resolved['stock_giu'],
                        'order_refs'   => [],
                    ];
                }

                $aggregated[$groupKey]['qty'] += $qty;
                if ($resolved['model_may'] !== '' && !in_array($resolved['model_may'], $aggregated[$groupKey]['models'], true)) {
                    $aggregated[$groupKey]['models'][] = $resolved['model_may'];
                }

                $orderCode = (string) ($order->ma_don_hang ?? ('#' . $order->id));
                $aggregated[$groupKey]['order_refs'][$orderCode] = ($aggregated[$groupKey]['order_refs'][$orderCode] ?? 0) + $qty;

                $detailRows[] = [
                    'order_code'   => $orderCode,
                    'order_date'   => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                    'customer'     => (string) ($order->khach_hang_ten ?: ($order->khachHang?->ho_ten ?? 'Khach le')),
                    'phone'        => (string) ($order->khach_hang_sdt ?: ($order->khachHang?->so_dien_thoai ?? '')),
                    'brand'        => $resolved['brand'],
                    'part_no'      => $resolved['part_no'],
                    'part_name'    => $resolved['part_name'],
                    'model_may'    => $resolved['model_may'],
                    'qty'          => $qty,
                    'order_note'   => $this->cleanOrderNote((string) ($order->ghi_chu_khach_hang ?? '')),
                ];
            }
        }

        // Sắp xếp danh sách gom PO theo Hãng -> Mã phụ tùng cho gọn đẹp khi gửi nhà cung cấp
        $summaryRows = array_values($aggregated);
        usort($summaryRows, static function (array $a, array $b): int {
            $cmpBrand = strcmp($a['brand'], $b['brand']);
            return $cmpBrand !== 0 ? $cmpBrand : strcmp($a['part_no'], $b['part_no']);
        });

        // Tạo styles
        $thinBorder = new Border(
            new BorderPart(Border::TOP, 'CBD5E1', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, 'CBD5E1', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::LEFT, 'CBD5E1', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, 'CBD5E1', Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        $titleStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor('0A1128')
            ->setCellAlignment(CellAlignment::LEFT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        $metaLabelStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('475569');

        $metaValStyle = (new Style())
            ->setFontSize(10)
            ->setFontColor('0F172A');

        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('0A1128')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText(true)
            ->setBorder($thinBorder);

        $textLeftStyle = (new Style())
            ->setFontSize(10)
            ->setCellAlignment(CellAlignment::LEFT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText(true)
            ->setBorder($thinBorder);

        $codeStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('0369A1')
            ->setCellAlignment(CellAlignment::LEFT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($thinBorder);

        $centerStyle = (new Style())
            ->setFontSize(10)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($thinBorder);

        $qtyStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('0A1128')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($thinBorder);

        $totalRowStyle = (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('0A1128')
            ->setBackgroundColor('F1F5F9')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($thinBorder);

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $xlsxPath = $tempDir . DIRECTORY_SEPARATOR . 'jpm_po_' . uniqid('', true) . '.xlsx';

        $options = new Options();
        if (is_dir($tempDir) && is_writable($tempDir)) {
            $options->setTempFolder($tempDir);
        }
        $options->mergeCells(0, 1, 8, 1, 0);
        $options->mergeCells(0, 1, 10, 1, 1);

        $writer = new Writer($options);
        $writer->openToFile($xlsxPath);

        // ============================================================
        // SHEET 1: PO Gui Nhat (Tong hop gom theo Ma OEM)
        // ============================================================
        $sheet1 = $writer->getCurrentSheet();
        $sheet1->setName('PO Gui Nhat (Tong hop)');
        $sheet1->setColumnWidth(7, 1);   // No.
        $sheet1->setColumnWidth(18, 2);  // Brand
        $sheet1->setColumnWidth(24, 3);  // Part Number OEM
        $sheet1->setColumnWidth(38, 4);  // Part Name
        $sheet1->setColumnWidth(26, 5);  // Engine Model
        $sheet1->setColumnWidth(16, 6);  // Order Qty
        $sheet1->setColumnWidth(16, 7);  // Stock On Hand
        $sheet1->setColumnWidth(16, 8);  // Reserved Qty
        $sheet1->setColumnWidth(34, 9);  // Order Refs

        $writer->addRow(new Row([
            new StringCell('JPM MARINE - PURCHASE ORDER (PO) TO JAPAN SUPPLIER', $titleStyle),
        ]));
        $writer->addRow(new Row([
            new StringCell('Ngay xuat:', $metaLabelStyle),
            new StringCell(date('d/m/Y H:i'), $metaValStyle),
            new StringCell('So don hang:', $metaLabelStyle),
            new NumericCell($orders->count(), $metaValStyle),
            new StringCell('So ma hang:', $metaLabelStyle),
            new NumericCell(count($summaryRows), $metaValStyle),
            new StringCell('Tong SL dat:', $metaLabelStyle),
            new NumericCell($totalOrderedQty, $metaValStyle),
        ]));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(new Row([
            new StringCell('No.', $headerStyle),
            new StringCell('Brand (Hang)', $headerStyle),
            new StringCell('Part Number (OEM)', $headerStyle),
            new StringCell('Part Name (Ten phu tung)', $headerStyle),
            new StringCell('Engine Model (Doi may)', $headerStyle),
            new StringCell('Order Qty (Tong SL)', $headerStyle),
            new StringCell('Stock (Ton kho)', $headerStyle),
            new StringCell('Reserved (Dang giu)', $headerStyle),
            new StringCell('Order Refs (Ma don)', $headerStyle),
        ]));

        foreach ($summaryRows as $idx => $row) {
            $orderRefParts = [];
            foreach ($row['order_refs'] as $oCode => $oQty) {
                $orderRefParts[] = "{$oCode} (x{$oQty})";
            }

            $writer->addRow(new Row([
                new NumericCell($idx + 1, $centerStyle),
                new StringCell($row['brand'], $textLeftStyle),
                new StringCell($row['part_no'], $codeStyle),
                new StringCell($row['part_name'], $textLeftStyle),
                new StringCell(implode(', ', $row['models']), $textLeftStyle),
                new NumericCell($row['qty'], $qtyStyle),
                new NumericCell($row['stock_ton'], $centerStyle),
                new NumericCell($row['stock_giu'], $centerStyle),
                new StringCell(implode(', ', $orderRefParts), $textLeftStyle),
            ]));
        }

        $writer->addRow(new Row([
            new StringCell('', $totalRowStyle),
            new StringCell('TONG CONG', $totalRowStyle),
            new StringCell(count($summaryRows) . ' ma', $totalRowStyle),
            new StringCell('', $totalRowStyle),
            new StringCell('', $totalRowStyle),
            new NumericCell($totalOrderedQty, $totalRowStyle),
            new StringCell('', $totalRowStyle),
            new StringCell('', $totalRowStyle),
            new StringCell($orders->count() . ' don hang', $totalRowStyle),
        ]));

        // ============================================================
        // SHEET 2: Chi Tiet Theo Don (Noi bo JPM chia hang ve kho)
        // ============================================================
        $sheet2 = $writer->addNewSheetAndMakeItCurrent();
        $sheet2->setName('Chi Tiet Theo Don');
        $sheet2->setColumnWidth(7, 1);   // STT
        $sheet2->setColumnWidth(18, 2);  // Ma don
        $sheet2->setColumnWidth(17, 3);  // Ngay dat
        $sheet2->setColumnWidth(24, 4);  // Khach hang
        $sheet2->setColumnWidth(15, 5);  // SDT
        $sheet2->setColumnWidth(16, 6);  // Hang
        $sheet2->setColumnWidth(22, 7);  // Ma OEM
        $sheet2->setColumnWidth(36, 8);  // Ten phu tung
        $sheet2->setColumnWidth(24, 9);  // Model may
        $sheet2->setColumnWidth(10, 10); // SL
        $sheet2->setColumnWidth(32, 11); // Ghi chu

        $writer->addRow(new Row([
            new StringCell('JPM MARINE - BANG KE CHI TIET PHU TUNG THEO DON HANG (NOI BO)', $titleStyle),
        ]));
        $writer->addRow(new Row([
            new StringCell('Ngay xuat:', $metaLabelStyle),
            new StringCell(date('d/m/Y H:i'), $metaValStyle),
            new StringCell('So don hang:', $metaLabelStyle),
            new NumericCell($orders->count(), $metaValStyle),
            new StringCell('Tong dong hang:', $metaLabelStyle),
            new NumericCell(count($detailRows), $metaValStyle),
        ]));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(new Row([
            new StringCell('STT', $headerStyle),
            new StringCell('Ma Don Hang', $headerStyle),
            new StringCell('Ngay Tao Don', $headerStyle),
            new StringCell('Khach Hang', $headerStyle),
            new StringCell('So Dien Thoai', $headerStyle),
            new StringCell('Hang', $headerStyle),
            new StringCell('Ma Phu Tung (OEM)', $headerStyle),
            new StringCell('Ten Phu Tung', $headerStyle),
            new StringCell('Model May', $headerStyle),
            new StringCell('SL', $headerStyle),
            new StringCell('Ghi Chu Don Hang', $headerStyle),
        ]));

        foreach ($detailRows as $idx => $dRow) {
            $writer->addRow(new Row([
                new NumericCell($idx + 1, $centerStyle),
                new StringCell($dRow['order_code'], $codeStyle),
                new StringCell($dRow['order_date'], $centerStyle),
                new StringCell($dRow['customer'], $textLeftStyle),
                new StringCell($dRow['phone'], $centerStyle),
                new StringCell($dRow['brand'], $textLeftStyle),
                new StringCell($dRow['part_no'], $codeStyle),
                new StringCell($dRow['part_name'], $textLeftStyle),
                new StringCell($dRow['model_may'], $textLeftStyle),
                new NumericCell($dRow['qty'], $qtyStyle),
                new StringCell($dRow['order_note'], $textLeftStyle),
            ]));
        }

        $writer->close();

        return $xlsxPath;
    }

    private function loadFallbackPartsByCode(Collection $orders): Collection
    {
        $codes = [];
        foreach ($orders as $order) {
            foreach ($order->chiTiet as $item) {
                $code = trim((string) ($item->ma_phu_tung ?? ''));
                if ($code !== '' && strtoupper($code) !== 'N/A' && !$item->phuTung) {
                    $codes[] = $code;
                }
            }
        }
        $codes = array_values(array_unique($codes));
        if (empty($codes)) {
            return collect();
        }

        return PhuTung::with('thuongHieu')
            ->whereIn('ma_phu_tung', $codes)
            ->get()
            ->keyBy(fn($pt) => strtoupper((string) $pt->ma_phu_tung));
    }

    private function loadStockSummaryMap(Collection $orders, Collection $partByCode): array
    {
        $partIds = [];
        $productIds = [];

        foreach ($orders as $order) {
            foreach ($order->chiTiet as $item) {
                $loai = $item->loai_san_pham ?? 'phu_tung';
                if ($loai === 'san_pham' && $item->id_san_pham) {
                    $productIds[] = (int) $item->id_san_pham;
                } elseif ($loai === 'phu_tung' && $item->id_san_pham) {
                    $partIds[] = (int) $item->id_san_pham;
                } else {
                    $code = strtoupper(trim((string) ($item->ma_phu_tung ?? '')));
                    if ($code !== '' && $partByCode->has($code)) {
                        $partIds[] = (int) $partByCode->get($code)->id;
                    }
                }
            }
        }

        $partIds = array_values(array_unique($partIds));
        $productIds = array_values(array_unique($productIds));
        if (empty($partIds) && empty($productIds)) {
            return [];
        }

        $rows = TonKho::query()
            ->select([
                'loai_san_pham',
                'id_san_pham',
                DB::raw('SUM(so_luong_ton) as ton'),
                DB::raw('SUM(COALESCE(so_luong_dang_giu, 0)) as giu'),
            ])
            ->whereHas('kho', fn($q) => $q->where('trang_thai', 1))
            ->where(function ($q) use ($partIds, $productIds) {
                if (!empty($partIds)) {
                    $q->orWhere(function ($sub) use ($partIds) {
                        $sub->where('loai_san_pham', 'phu_tung')->whereIn('id_san_pham', $partIds);
                    });
                }
                if (!empty($productIds)) {
                    $q->orWhere(function ($sub) use ($productIds) {
                        $sub->where('loai_san_pham', 'san_pham')->whereIn('id_san_pham', $productIds);
                    });
                }
            })
            ->groupBy('loai_san_pham', 'id_san_pham')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $key = $r->loai_san_pham . ':' . $r->id_san_pham;
            $map[$key] = [
                'ton' => (int) $r->ton,
                'giu' => (int) $r->giu,
            ];
        }
        return $map;
    }

    private function resolveItemMeta($item, Collection $partByCode, array $stockMap): array
    {
        $loai = $item->loai_san_pham ?? 'phu_tung';
        $partNo = trim((string) ($item->ma_phu_tung ?? ''));
        $partName = trim((string) ($item->ten_phu_tung ?? ''));

        $matchedPart = $loai === 'phu_tung' ? $item->phuTung : null;
        if (!$matchedPart && $partNo !== '' && $partByCode->has(strtoupper($partNo))) {
            $matchedPart = $partByCode->get(strtoupper($partNo));
        }

        if ($partNo === '' || strtoupper($partNo) === 'N/A') {
            $partNo = $matchedPart?->ma_phu_tung ?? ($item->sanPham?->ma_san_pham ?? 'N/A');
        }
        if ($partName === '') {
            $partName = $matchedPart?->ten_phu_tung ?? ($item->sanPham?->ten_san_pham ?? 'Khong ro');
        }

        if ($loai === 'san_pham') {
            $brand = 'San pham pho thong';
            $stockKey = $item->id_san_pham ? ('san_pham:' . $item->id_san_pham) : null;
        } elseif ($matchedPart) {
            $brand = (string) ($matchedPart->thuongHieu?->ten_thuong_hieu ?? 'Chua phan hang');
            $stockKey = 'phu_tung:' . $matchedPart->id;
        } else {
            $brand = 'Chua phan hang';
            $stockKey = null;
        }

        $stockInfo = ($stockKey && isset($stockMap[$stockKey])) ? $stockMap[$stockKey] : ['ton' => 0, 'giu' => 0];

        return [
            'brand'     => $brand,
            'part_no'   => $partNo,
            'part_name' => $partName,
            'model_may' => trim((string) ($item->model_may ?? '')),
            'stock_ton' => $stockInfo['ton'],
            'stock_giu' => $stockInfo['giu'],
        ];
    }

    private function cleanOrderNote(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        return trim((string) preg_replace('/^Báo giá\s+#YCBG-[^\s|]+\s*\|?\s*/i', '', $raw));
    }
}
