<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\CMS\Models\TinTuc;
use App\Domain\CMS\Models\ChuyenMuc;

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
     * Chi tiết tin tức
     */
    public function chiTiet($id)
    {
        $news = TinTuc::where('status', 'PUBLISHED')->findOrFail($id);
        
        // Tăng view
        $news->increment('views');

        // Lấy 3 bài liên quan cùng danh mục
        $related = TinTuc::where('status', 'PUBLISHED')
            ->where('category', $news->category)
            ->where('id', '!=', $id)
            ->latest()
            ->limit(3)
            ->get(['id', 'title', 'category', 'thumbnail', 'summary', 'views', 'author', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $news,
            'related' => $related,
        ]);
    }
}

