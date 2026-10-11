<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="sơ đồ phụ tùng {{ $modelName }}, bản vẽ rã máy {{ $modelName }}, phụ tùng động cơ {{ $brandName }} {{ $modelName }}, tra mã máy thủy, JPM Marine">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="JPM Marine">

    <!-- Schema.org ItemPage & Breadcrumb -->
    <script type="application/ld+json">
    {!! $schemaJson !!}
    </script>
    <script type="application/ld+json">
    {!! $breadcrumbJson !!}
    </script>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; line-height: 1.6; color: #1e293b; padding: 20px; max-width: 900px; margin: 0 auto; }
        h1 { color: #0f172a; font-size: 1.6rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .diagram-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #f8fafc; }
        .btn-view { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <article>
        <h1>Bản Vẽ Sơ Đồ Phụ Tùng Động Cơ Thủy {{ $brandName }}: {{ $modelName }}</h1>
        <p>Tra cứu sơ đồ exploded view (bản vẽ phân rã chi tiết), danh sách linh kiện và mã phụ tùng OEM chính hãng cho dòng máy <strong>{{ $brandName }} {{ $modelName }}</strong> tại JPM Marine.</p>

        <h2>Thông Tin Model Máy:</h2>
        <ul>
            <li><strong>Thương hiệu:</strong> {{ $brandName }}</li>
            <li><strong>Mã Model:</strong> {{ $dongCo->ma_model }}</li>
            <li><strong>Tên Model:</strong> {{ $dongCo->ten_model }}</li>
            <li><strong>Loại động cơ:</strong> {{ $dongCo->loaiDongCo?->ten_loai ?? 'Động cơ thủy' }}</li>
            @if($dongCo->nam_san_xuat)
            <li><strong>Năm sản xuất / Đời máy:</strong> {{ $dongCo->nam_san_xuat }}</li>
            @endif
        </ul>

        @if($dongCo->soDoPhuTungs && $dongCo->soDoPhuTungs->count() > 0)
        <h2>Các Cụm Sơ Đồ Bản Vẽ (Exploded Diagrams):</h2>
        <div class="diagram-list">
            @foreach($dongCo->soDoPhuTungs as $sd)
            <div class="diagram-card">
                <h3 style="margin: 0 0 8px 0; color: #0f172a;">{{ $sd->ten_so_do }}</h3>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Xem vị trí chi tiết từng con ốc, vòng bi, phớt dầu và mã phụ tùng OEM tương ứng.</p>
            </div>
            @endforeach
        </div>
        @endif

        <p><a href="{{ $targetUrl }}" class="btn-view">Mở bản vẽ tương tác đầy đủ trên JPM Marine &rarr;</a></p>
    </article>
</body>
</html>
