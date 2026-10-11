<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $phuTung->ma_phu_tung }}, phụ tùng {{ $phuTung->ma_phu_tung }}, {{ $phuTung->ten_phu_tung }}, phụ tùng {{ $brandName }}, máy thủy, tra mã oem, sơ đồ rã máy, JPM Marine">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="JPM Marine">
    @if(!empty($phuTung->hinh_anh))
    <meta property="og:image" content="{{ $phuTung->hinh_anh }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">

    <!-- Schema.org Product Structured Data (Google Rich Results) -->
    <script type="application/ld+json">
    {!! $schemaJson !!}
    </script>

    <!-- Schema.org Breadcrumb -->
    <script type="application/ld+json">
    {!! $breadcrumbJson !!}
    </script>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; line-height: 1.6; color: #1e293b; padding: 20px; max-width: 900px; margin: 0 auto; }
        h1 { color: #0f172a; font-size: 1.6rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; }
        th { background: #f8fafc; font-weight: 600; width: 220px; }
        .highlight { color: #dc2626; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; margin-right: 6px; }
        .badge-brand { background: #fee2e2; color: #b91c1c; }
        .badge-stock { background: #dcfce7; color: #15803d; }
        .btn-view { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <article>
        <h1>Phụ Tùng Máy Thủy {{ $brandName }}: {{ $phuTung->ma_phu_tung }} - {{ $phuTung->ten_phu_tung }}</h1>
        <p>Tra cứu thông số kỹ thuật, bản vẽ sơ đồ phân rã linh kiện và mua hàng chính hãng tại đại lý <strong>JPM Marine</strong>.</p>

        <table>
            <tr>
                <th>Mã phụ tùng (OEM Part #):</th>
                <td><strong class="highlight font-monospace">{{ $phuTung->ma_phu_tung }}</strong></td>
            </tr>
            <tr>
                <th>Tên phụ tùng:</th>
                <td><strong>{{ $phuTung->ten_phu_tung }}</strong></td>
            </tr>
            <tr>
                <th>Thương hiệu:</th>
                <td><span class="badge badge-brand">{{ $brandName }}</span></td>
            </tr>
            <tr>
                <th>Nhóm linh kiện:</th>
                <td>{{ $phuTung->nhomPhuTung?->ten_nhom ?? 'Phụ tùng máy thủy' }}</td>
            </tr>
            <tr>
                <th>Giá bán niêm yết:</th>
                <td>
                    @if($phuTung->is_gia_lien_he)
                        <span class="highlight">Liên hệ nhận báo giá</span>
                    @else
                        <span class="highlight">{{ number_format($phuTung->gia_ban, 0, ',', '.') }} VNĐ</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tình trạng kho hàng:</th>
                <td><span class="badge badge-stock">{{ ($phuTung->so_luong_ton > 0) ? 'Có sẵn trong kho' : 'Đặt hàng chính hãng' }}</span></td>
            </tr>
        </table>

        @if($phuTung->maThayThes && $phuTung->maThayThes->count() > 0)
        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0; color: #b45309; font-size: 1.1rem;">⚠️ Lưu ý mã thay thế (Superseded Part):</h3>
            <p>Mã phụ tùng <strong>{{ $phuTung->ma_phu_tung }}</strong> đã có mã mới thay thế từ nhà sản xuất:</p>
            <ul>
                @foreach($phuTung->maThayThes as $tt)
                    @if($tt->phuTungMoi)
                    <li>Mã mới: <strong><a href="{{ $frontendUrl }}/chi-tiet-phu-tung/{{ $tt->phuTungMoi->ma_phu_tung }}">{{ $tt->phuTungMoi->ma_phu_tung }}</a></strong> ({{ $tt->phuTungMoi->ten_phu_tung }}) - Loại: {{ $tt->loai_thay_the }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        @if($phuTung->thayTheChos && $phuTung->thayTheChos->count() > 0)
        <div style="background: #eff6ff; border: 1px solid #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0; color: #1d4ed8; font-size: 1.1rem;">Mã thay thế tương đương cho các mã cũ:</h3>
            <p>Phụ tùng này thay thế trực tiếp cho các mã phụ tùng cũ đã ngưng sản xuất sau:</p>
            <ul>
                @foreach($phuTung->thayTheChos as $tc)
                    @if($tc->phuTungCu)
                    <li>Mã cũ: <strong>{{ $tc->phuTungCu->ma_phu_tung }}</strong> ({{ $tc->phuTungCu->ten_phu_tung }})</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        @if($phuTung->dongCoModels && $phuTung->dongCoModels->count() > 0)
        <h2>Các Dòng Động Cơ Tương Thích (Fitment):</h2>
        <ul>
            @foreach($phuTung->dongCoModels as $m)
            <li><strong>{{ $m->thuongHieu?->name }} {{ $m->ten_model ?: $m->ma_model }}</strong> (Năm áp dụng: {{ $m->pivot->nam_ap_dung ?? $m->nam_san_xuat ?? 'Tất cả các năm' }})</li>
            @endforeach
        </ul>
        @endif

        <p><a href="{{ $targetUrl }}" class="btn-view">Mở giao diện đầy đủ & Đặt hàng trên JPM Marine &rarr;</a></p>
    </article>
</body>
</html>
