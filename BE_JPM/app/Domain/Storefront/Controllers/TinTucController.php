<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\CMS\Models\TinTuc;
use App\Domain\CMS\Models\ChuyenMuc;
use Illuminate\Support\Facades\Schema;

class TinTucController extends Controller
{
    /**
     * Lấy danh sách tên các chuyên mục không phải NEWS hoặc đang bị ẩn
     */
    private function getExcludedCategories()
    {
        return ChuyenMuc::where(function ($q) {
            $q->where('type', '!=', 'NEWS')
              ->orWhere('status', '!=', 'ACTIVE');
        })->pluck('name');
    }

    /**
     * Lấy tin tức mới nhất cho Trang chủ
     */
    public function moiNhat(Request $request)
    {
        $limit = (int) $request->get('limit', 6);
        $excluded = $this->getExcludedCategories();

        $query = TinTuc::where('status', 'PUBLISHED');
        if ($excluded->isNotEmpty()) {
            $query->whereNotIn('category', $excluded);
        }

        $news = $query->latest()
            ->limit($limit)
            ->get(['id', 'title', 'category', 'thumbnail', 'youtube_url', 'summary', 'views', 'author', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $news,
        ]);
    }

    /**
     * Danh sách đầy đủ + phân trang + lọc category + tìm kiếm cho trang /tin-tuc
     */
    public function danhSach(Request $request)
    {
        $perPage  = (int) $request->get('per_page', 9);
        $category = trim((string) $request->get('category', ''));
        $search   = trim((string) $request->get('search', ''));
        $excluded = $this->getExcludedCategories();

        $query = TinTuc::where('status', 'PUBLISHED')->latest();

        if ($excluded->isNotEmpty()) {
            $query->whereNotIn('category', $excluded);
        }

        if ($category !== '' && $category !== 'Tất cả') {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate($perPage, [
            'id', 'title', 'category', 'thumbnail', 'youtube_url', 'summary', 'views', 'author', 'created_at'
        ]);

        return response()->json([
            'success' => true,
            'data'    => $news,
        ]);
    }

    /**
     * Lấy danh sách category chuẩn từ bảng chuyen_mucs (type = NEWS, status = ACTIVE)
     */
    public function categories()
    {
        $cats = ChuyenMuc::where('type', 'NEWS')
            ->where('status', 'ACTIVE')
            ->orderBy('id', 'asc')
            ->pluck('name');

        return response()->json([
            'success' => true,
            'data'    => $cats,
        ]);
    }

    /**
     * Chi tiết tin tức (Hỗ trợ cả id và slug)
     */
    public function chiTiet($id)
    {
        $hasSlug = Schema::hasColumn('tin_tucs', 'slug');
        $news = TinTuc::where('status', 'PUBLISHED')
            ->where(function ($q) use ($id, $hasSlug) {
                if (is_numeric($id)) {
                    $q->where('id', $id);
                    if ($hasSlug) {
                        $q->orWhere('slug', $id);
                    }
                } elseif ($hasSlug) {
                    $q->where('slug', $id);
                } else {
                    $q->where('id', $id)->orWhere('title', $id);
                }
            })->firstOrFail();
        
        // Tăng view
        $news->increment('views');

        $excluded = $this->getExcludedCategories();

        // Lấy 3 bài liên quan cùng danh mục
        $related = TinTuc::where('status', 'PUBLISHED')
            ->where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->latest()
            ->limit(3)
            ->get(['id', 'title', 'category', 'thumbnail', 'summary', 'views', 'author', 'created_at']);

        // Nếu danh mục hiện tại chưa đủ 3 bài liên quan, lấy bù thêm bài mới nhất từ các chuyên mục NEWS khác
        if ($related->count() < 3) {
            $existingIds = $related->pluck('id')->push($news->id)->all();
            $moreQuery = TinTuc::where('status', 'PUBLISHED')
                ->whereNotIn('id', $existingIds);

            if ($excluded->isNotEmpty()) {
                $moreQuery->whereNotIn('category', $excluded);
            }

            $more = $moreQuery->latest()
                ->limit(3 - $related->count())
                ->get(['id', 'title', 'category', 'thumbnail', 'summary', 'views', 'author', 'created_at']);

            $related = $related->concat($more)->values();
        }

        return response()->json([
            'success' => true,
            'data'    => $news,
            'related' => $related,
        ]);
    }
}
