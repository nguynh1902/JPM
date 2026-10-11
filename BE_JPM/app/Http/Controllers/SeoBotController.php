<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\CMS\Models\SanPham;

class SeoBotController extends Controller
{
    protected function getFrontendUrl(): string
    {
        return rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
    }

    /**
     * Nhận diện Crawler / Googlebot / Social Bot
     */
    public static function isCrawler(Request $request): bool
    {
        if ($request->has('bot')) {
            return true;
        }

        $userAgent = strtolower($request->header('User-Agent', ''));
        $crawlers = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'facebookexternalhit', 'twitterbot', 'rogerbot',
            'linkedinbot', 'embedly', 'quora link preview', 'showyoubot',
            'outbrain', 'pinterest', 'slackbot', 'vkshare', 'w3c_validator',
            'redditbot', 'applebot', 'whatsapp', 'flipboard', 'tumblr',
            'bitlybot', 'skypeuripreview', 'nuzzel', 'discordbot',
            'google page speed', 'qwantify', 'zalobot', 'screaming frog'
        ];

        foreach ($crawlers as $c) {
            if (str_contains($userAgent, $c)) {
                return true;
            }
        }

        return false;
    }

    /**
     * SEO Landing & Prerender cho Phụ tùng OEM (/chi-tiet-phu-tung/{code})
     */
    public function phuTung(string $code, Request $request)
    {
        $frontendUrl = $this->getFrontendUrl();
        $targetUrl = $frontendUrl . '/chi-tiet-phu-tung/' . rawurlencode(strtoupper($code));

        $phuTung = PhuTung::with([
            'thuongHieu',
            'nhomPhuTung',
            'dongCoModels',
            'soDoPhuTungs.model.thuongHieu',
            'maThayThes.phuTungMoi',
            'thayTheChos.phuTungCu'
        ])->where('ma_phu_tung', strtoupper($code))->first();

        if (!$phuTung) {
            return redirect($frontendUrl . '/tra-cuu?keyword=' . urlencode($code));
        }

        if (!self::isCrawler($request)) {
            return redirect($targetUrl);
        }

        $brandName = $phuTung->thuongHieu?->name ?? 'Marine OEM';
        $title = "{$phuTung->ma_phu_tung} - {$phuTung->ten_phu_tung} | Phụ tùng máy thủy {$brandName} chính hãng - JPM Marine";
        $description = "Tra cứu và mua phụ tùng máy thủy chính hãng mã {$phuTung->ma_phu_tung} ({$phuTung->ten_phu_tung}) dùng cho động cơ {$brandName}. Sơ đồ rã máy, mã thay thế tương đương tại JPM Marine.";
        $canonical = $targetUrl;

        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $phuTung->ma_phu_tung . ' - ' . $phuTung->ten_phu_tung,
            'image' => $phuTung->hinh_anh ?: null,
            'description' => $description,
            'sku' => $phuTung->ma_phu_tung,
            'mpn' => $phuTung->ma_phu_tung,
            'brand' => [
                '@type' => 'Brand',
                'name' => $brandName
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $canonical,
                'priceCurrency' => 'VND',
                'price' => $phuTung->is_gia_lien_he ? 0 : (float)($phuTung->gia_ban ?? 0),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => ($phuTung->so_luong_ton > 0) ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'JPM Marine'
                ]
            ]
        ];
        $schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $breadcrumb = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang chủ',
                    'item' => $frontendUrl
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Tra cứu phụ tùng ' . $brandName,
                    'item' => $frontendUrl . '/thuong-hieu/' . strtolower($phuTung->thuongHieu?->code ?? '')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $phuTung->ma_phu_tung,
                    'item' => $canonical
                ]
            ]
        ];
        $breadcrumbJson = json_encode($breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return view('seo.phu_tung', compact('phuTung', 'brandName', 'title', 'description', 'canonical', 'targetUrl', 'schemaJson', 'breadcrumbJson', 'frontendUrl'));
    }

    /**
     * SEO Landing & Prerender cho Model Động cơ & Sơ đồ (/so-do/{model})
     */
    public function soDo(string $model, Request $request)
    {
        $frontendUrl = $this->getFrontendUrl();
        $targetUrl = $frontendUrl . '/so-do/' . rawurlencode($model);

        $dongCo = DongCoModel::with(['thuongHieu', 'loaiDongCo', 'soDoPhuTungs'])
            ->where('ma_model', $model)
            ->orWhere('id', $model)
            ->first();

        if (!$dongCo) {
            return redirect($frontendUrl . '/tra-cuu');
        }

        if (!self::isCrawler($request)) {
            return redirect($targetUrl);
        }

        $brandName = $dongCo->thuongHieu?->name ?? 'Marine';
        $modelName = $dongCo->ten_model ?: $dongCo->ma_model;
        $title = "Sơ đồ phụ tùng {$modelName} ({$brandName}) | Bản vẽ rã máy tra cứu JPM Marine";
        $description = "Bản vẽ sơ đồ phụ tùng rã máy chi tiết động cơ thủy {$brandName} {$modelName}. Tra cứu mã OEM, phụ tùng lắp đặt và mua hàng trực tiếp tại JPM Marine.";
        $canonical = $targetUrl;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical
        ];
        $schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $breadcrumb = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang chủ',
                    'item' => $frontendUrl
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Model ' . $brandName,
                    'item' => $frontendUrl . '/thuong-hieu/' . strtolower($dongCo->thuongHieu?->code ?? '')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $modelName,
                    'item' => $canonical
                ]
            ]
        ];
        $breadcrumbJson = json_encode($breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return view('seo.so_do', compact('dongCo', 'brandName', 'modelName', 'title', 'description', 'canonical', 'targetUrl', 'schemaJson', 'breadcrumbJson', 'frontendUrl'));
    }

    /**
     * SEO Landing & Prerender cho Sản phẩm phổ thông (/chi-tiet-san-pham/{slug})
     */
    public function sanPham(string $slug, Request $request)
    {
        $frontendUrl = $this->getFrontendUrl();
        $targetUrl = $frontendUrl . '/chi-tiet-san-pham/' . $slug;

        $sanPham = SanPham::with(['danhMuc'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$sanPham) {
            return redirect($frontendUrl . '/san-pham');
        }

        if (!self::isCrawler($request)) {
            return redirect($targetUrl);
        }

        $categoryName = $sanPham->danhMuc?->ten_danh_muc ?? 'Vật tư máy thủy';
        $brandName = 'JPM Marine';
        $title = "{$sanPham->ten_san_pham} - Giá tốt chính hãng | JPM Marine";
        $description = "Mua {$sanPham->ten_san_pham} thuộc danh mục {$categoryName} tại JPM Marine. Phụ tùng và vật tư máy thủy chất lượng cao, giao hàng nhanh toàn quốc.";
        $canonical = $targetUrl;

        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $sanPham->ten_san_pham,
            'image' => $sanPham->hinh_anh_chinh ?: null,
            'description' => $description,
            'sku' => $sanPham->ma_san_pham ?: $sanPham->slug,
            'brand' => [
                '@type' => 'Brand',
                'name' => $brandName
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $canonical,
                'priceCurrency' => 'VND',
                'price' => $sanPham->is_gia_lien_he ? 0 : (float)($sanPham->gia_ban ?? 0),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => ($sanPham->so_luong_ton > 0) ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'JPM Marine'
                ]
            ]
        ];
        $schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return view('seo.san_pham', compact('sanPham', 'brandName', 'categoryName', 'title', 'description', 'canonical', 'targetUrl', 'schemaJson', 'frontendUrl'));
    }
}
