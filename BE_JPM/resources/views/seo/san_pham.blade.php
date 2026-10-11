<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $sanPham->ten_san_pham }}, {{ $categoryName }}, {{ $sanPham->ma_san_pham }}, vật tư máy thủy, phụ tùng máy thủy, JPM Marine">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">

    <!-- Open Graph -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="JPM Marine">
    @if(!empty($sanPham->hinh_anh_chinh))
    <meta property="og:image" content="{{ $sanPham->hinh_anh_chinh }}">
    @endif

    <!-- Schema.org Product -->
    <script type="application/ld+json">
    {!! $schemaJson !!}
    </script>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; line-height: 1.6; color: #1e293b; padding: 20px; max-width: 900px; margin: 0 auto; }
        h1 { color: #0f172a; font-size: 1.6rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; }
        th { background: #f8fafc; font-weight: 600; width: 220px; }
        .highlight { color: #dc2626; font-weight: bold; }
        .btn-view { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <article>
        <h1>{{ $sanPham->ten_san_pham }} - Phụ Kiện Máy Thủy JPM Marine</h1>
        <p>Sản phẩm và vật tư máy thủy chính hãng phân phối bởi <strong>JPM Marine</strong>.</p>

        <table>
            <tr>
                <th>Tên sản phẩm:</th>
                <td><strong>{{ $sanPham->ten_san_pham }}</strong></td>
            </tr>
            <tr>
                <th>Mã sản phẩm / SKU:</th>
                <td><strong class="highlight font-monospace">{{ $sanPham->ma_san_pham ?? $sanPham->slug }}</strong></td>
            </tr>
            <tr>
                <th>Danh mục ngành hàng:</th>
                <td>{{ $categoryName }}</td>
            </tr>
            <tr>
                <th>Giá bán niêm yết:</th>
                <td>
                    @if($sanPham->is_gia_lien_he)
                        <span class="highlight">Liên hệ nhận báo giá</span>
                    @else
                        <span class="highlight">{{ number_format($sanPham->gia_ban ?? 0, 0, ',', '.') }} VNĐ</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tình trạng:</th>
                <td>{{ ($sanPham->so_luong_ton > 0) ? 'Có sẵn trong kho' : 'Đặt hàng' }}</td>
            </tr>
        </table>

        @if($sanPham->mo_ta_chi_tiet)
        <h2>Mô Tả Chi Tiết:</h2>
        <div>{!! $sanPham->mo_ta_chi_tiet !!}</div>
        @endif

        <p><a href="{{ $targetUrl }}" class="btn-view">Xem chi tiết & Mua ngay trên JPM Marine &rarr;</a></p>
    </article>
</body>
</html>
