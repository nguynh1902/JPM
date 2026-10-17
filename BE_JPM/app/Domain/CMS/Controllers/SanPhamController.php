<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\PIM\Requests\StoreSanPhamRequest;
use App\Domain\PIM\Requests\UpdateSanPhamRequest;
use App\Domain\PIM\Resources\SanPhamResource;
use App\Domain\PIM\Services\SanPhamService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SanPhamController extends Controller
{
    protected $sanPhamService;

    public function __construct(SanPhamService $sanPhamService)
    {
        $this->sanPhamService = $sanPhamService;
    }

    public function show($id): JsonResponse
    {
        try {
            $sanPham = $this->sanPhamService->getById($id);
            return response()->json([
                'success' => true,
                'data' => new SanPhamResource($sanPham)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function index(): JsonResponse
    {
        $data = $this->sanPhamService->getAll();
        return response()->json([
            'success' => true,
            'data' => SanPhamResource::collection($data)
        ]);
    }

    public function store(StoreSanPhamRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            if ($request->hasFile('hinh_anh_chinh_file')) {
                $path = $request->file('hinh_anh_chinh_file')->store('san_phams', 'public');
                $data['hinh_anh_chinh'] = '/storage/' . $path;
            } elseif ($request->exists('hinh_anh_chinh_file')) {
                $data['hinh_anh_chinh'] = null;
            }

            if ($request->hasFile('thu_vien_anh_files')) {
                $gallery = [];
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $path = $file->store('san_phams', 'public');
                    $gallery[] = '/storage/' . $path;
                }
                $data['thu_vien_anh'] = $gallery;
            }

            $sanPham = $this->sanPhamService->create($data);
            $sanPham->load('danhMuc');
            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công',
                'data' => new SanPhamResource($sanPham)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateSanPhamRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            
            if ($request->hasFile('hinh_anh_chinh_file')) {
                $path = $request->file('hinh_anh_chinh_file')->store('san_phams', 'public');
                $data['hinh_anh_chinh'] = '/storage/' . $path;
            }

            $gallery = [];
            if (!empty($data['thu_vien_anh_kept'])) {
                $gallery = json_decode($data['thu_vien_anh_kept'], true) ?? [];
            }
            
            if ($request->hasFile('thu_vien_anh_files')) {
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $path = $file->store('san_phams', 'public');
                    $gallery[] = '/storage/' . $path;
                }
            }
            $data['thu_vien_anh'] = $gallery;

            $sanPham = $this->sanPhamService->update($id, $data);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công',
                'data' => new SanPhamResource($sanPham)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->sanPhamService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function publicIndex(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = \App\Domain\CMS\Models\SanPham::with('danhMuc')->where('trang_thai', 1);

        if ($request->has('danh_muc') && !empty($request->danh_muc)) {
            $slugs = explode(',', $request->danh_muc);
            $query->whereHas('danhMuc', function($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            });
        }

        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('ten_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('ma_san_pham', 'like', "%{$keyword}%");
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('gia_ban', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('gia_ban', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        if (request()->has('all')) {
            $data = $query->get();
        } else {
            $data = $query->paginate(12);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function publicShow($slug): JsonResponse
    {
        $sanPham = \App\Domain\CMS\Models\SanPham::with('danhMuc')
            ->where('slug', $slug)
            ->where('trang_thai', 1)
            ->first();

        if (!$sanPham) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Fetch related products (same category)
        $relatedProducts = \App\Domain\CMS\Models\SanPham::where('danh_muc_san_pham_id', $sanPham->danh_muc_san_pham_id)
            ->where('id', '!=', $sanPham->id)
            ->where('trang_thai', 1)
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'san_pham' => $sanPham,
                'related_products' => $relatedProducts
            ]
        ]);
    }
}





