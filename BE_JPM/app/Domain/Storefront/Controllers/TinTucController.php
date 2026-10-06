<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\CMS\Models\TinTuc;

class TinTucController extends Controller
{
    /**
     * Lấy tin tức mới nhất cho Trang chủ
     */
    public function moiNhat(Request $request)
    {
        $limit = (int) $request->get('limit', 6);

        $news = TinTuc::where('status', 'PUBLISHED')
            ->latest()
            ->limit($limit)
            ->get(['id', 'title', 'category', 'thumbnail', 'summary', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $news,
        ]);
    }

    /**
     * Danh sách đầy đủ + phân trang + lọc category cho trang /tin-tuc
     */
    public function danhSach(Request $request)
    {
        $perPage  = (int) $request->get('per_page', 9);
        $category = $request->get('category', '');

        $query = TinTuc::where('status', 'PUBLISHED')->latest();

        if ($category && $category !== 'Tất cả') {
            $query->where('category', $category);
        }

        $news = $query->paginate($perPage, ['id', 'title', 'category', 'thumbnail', 'summary', 'views', 'author', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $news,
        ]);
    }

    /**
     * Lấy danh sách category duy nhất (để render filter tabs)
     */
    public function categories()
    {
        $cats = TinTuc::where('status', 'PUBLISHED')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

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
            ->get(['id', 'title', 'category', 'thumbnail', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $news,
            'related' => $related,
        ]);
    }
}

