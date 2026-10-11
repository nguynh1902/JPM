<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\CMS\Models\SanPham;
use App\Domain\CMS\Models\DanhMucSanPham;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\CMS\Models\TinTuc;

class SitemapController extends Controller
{
    protected function getFrontendUrl(): string
    {
        return rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
    }

    protected function getBackendUrl(): string
    {
        return rtrim(config('app.url', 'http://127.0.0.1:8000'), '/');
    }

    /**
     * Sitemap Index tổng (/sitemap.xml)
     */
    public function index(): Response
    {
        $backendUrl = $this->getBackendUrl();
        $now = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        $sitemaps = [
            $backendUrl . '/sitemaps/phu-tung.xml',
            $backendUrl . '/sitemaps/so-do.xml',
            $backendUrl . '/sitemaps/san-pham.xml',
            $backendUrl . '/sitemaps/chung.xml',
        ];

        foreach ($sitemaps as $loc) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $now . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sitemap Phụ tùng OEM (/sitemaps/phu-tung.xml)
     */
    public function phuTung(): Response
    {
        $frontendUrl = $this->getFrontendUrl();
        $items = PhuTung::where('trang_thai', 1)
            ->select('id', 'ma_phu_tung', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($items as $item) {
            $loc = $frontendUrl . '/chi-tiet-phu-tung/' . rawurlencode($item->ma_phu_tung);
            $lastmod = $item->updated_at ? $item->updated_at->toAtomString() : now()->toAtomString();

            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.9</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sitemap Model động cơ & Sơ đồ rã máy (/sitemaps/so-do.xml)
     */
    public function soDo(): Response
    {
        $frontendUrl = $this->getFrontendUrl();
        $models = DongCoModel::where('trang_thai', 1)
            ->select('id', 'ma_model', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($models as $m) {
            $loc = $frontendUrl . '/so-do/' . rawurlencode($m->ma_model);
            $lastmod = $m->updated_at ? $m->updated_at->toAtomString() : now()->toAtomString();

            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.85</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sitemap Sản phẩm phổ thông (/sitemaps/san-pham.xml)
     */
    public function sanPham(): Response
    {
        $frontendUrl = $this->getFrontendUrl();
        $products = SanPham::where('trang_thai', 1)
            ->select('id', 'slug', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($products as $p) {
            $loc = $frontendUrl . '/chi-tiet-san-pham/' . ($p->slug ?: $p->id);
            $lastmod = $p->updated_at ? $p->updated_at->toAtomString() : now()->toAtomString();

            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sitemap Danh mục chung, Thương hiệu, Tin tức (/sitemaps/chung.xml)
     */
    public function chung(): Response
    {
        $frontendUrl = $this->getFrontendUrl();
        $now = now()->toAtomString();

        $staticPages = [
            ['path' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['path' => '/tra-cuu', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/san-pham', 'priority' => '0.85', 'changefreq' => 'daily'],
            ['path' => '/thuong-hieu', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['path' => '/tin-tuc', 'priority' => '0.75', 'changefreq' => 'weekly'],
            ['path' => '/ho-tro', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($staticPages as $page) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($frontendUrl . $page['path']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $now . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        // Thương hiệu
        $brands = ThuongHieu::where('trang_thai', 1)->select('id', 'code', 'updated_at')->get();
        foreach ($brands as $b) {
            $loc = $frontendUrl . '/thuong-hieu/' . strtolower($b->code);
            $lastmod = $b->updated_at ? $b->updated_at->toAtomString() : $now;
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        // Tin tức
        $news = TinTuc::where('trang_thai', 1)->select('id', 'updated_at')->get();
        foreach ($news as $n) {
            $loc = $frontendUrl . '/tin-tuc/' . $n->id;
            $lastmod = $n->updated_at ? $n->updated_at->toAtomString() : $now;
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.7</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * File robots.txt động (/robots.txt)
     */
    public function robotsTxt(): Response
    {
        $backendUrl = $this->getBackendUrl();
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Allow: /chi-tiet-phu-tung/\n";
        $content .= "Allow: /so-do/\n";
        $content .= "Allow: /chi-tiet-san-pham/\n";
        $content .= "Allow: /san-pham\n";
        $content .= "Allow: /thuong-hieu\n";
        $content .= "Allow: /tra-cuu\n";
        $content .= "Allow: /tin-tuc\n";
        $content .= "Allow: /ho-tro\n\n";

        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /tai-khoan\n";
        $content .= "Disallow: /gio-hang\n";
        $content .= "Disallow: /thanh-toan\n";
        $content .= "Disallow: /garage\n";
        $content .= "Disallow: /lich-su-don-hang\n";
        $content .= "Disallow: /api/\n\n";

        $content .= "Sitemap: " . $backendUrl . "/sitemap.xml\n";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
