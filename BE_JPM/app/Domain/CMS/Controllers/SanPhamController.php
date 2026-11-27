<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\PIM\Requests\StoreSanPhamRequest;
use App\Domain\PIM\Requests\UpdateSanPhamRequest;
use App\Domain\PIM\Resources\SanPhamResource;
use App\Domain\PIM\Services\SanPhamService;
use App\Domain\Core\Services\ImageUploadService;
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

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $all = $request->boolean('all', false);
        $perPage = $all ? null : min(max((int) $request->input('per_page', 50), 1), 100);
        $filters = $request->only(['keyword', 'danh_muc_id']);

        $data = $this->sanPhamService->getAll($filters, $perPage);

        if ($all || !$perPage) {
            return response()->json([
                'success' => true,
                'data' => SanPhamResource::collection($data)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => SanPhamResource::collection($data->items()),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    public function store(StoreSanPhamRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            if ($request->hasFile('hinh_anh_chinh_file')) {
                $data['hinh_anh_chinh'] = ImageUploadService::storeUploadedFile(
                    $request->file('hinh_anh_chinh_file'),
                    'uploads/san_phams',
                    'sp',
                    1600
                );
            } elseif ($request->exists('hinh_anh_chinh_file')) {
                $data['hinh_anh_chinh'] = null;
            }

            if ($request->hasFile('thu_vien_anh_files')) {
                $gallery = [];
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $gallery[] = ImageUploadService::storeUploadedFile(
                        $file,
                        'uploads/san_phams',
                        'sp_gal',
                        1600
                    );
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
            
            $existing = $this->sanPhamService->getById($id);
            $oldMain = $existing->hinh_anh_chinh;
            $oldGallery = is_array($existing->thu_vien_anh) ? $existing->thu_vien_anh : (json_decode((string) $existing->thu_vien_anh, true) ?? []);

            if ($request->hasFile('hinh_anh_chinh_file')) {
                $data['hinh_anh_chinh'] = ImageUploadService::storeUploadedFile(
                    $request->file('hinh_anh_chinh_file'),
                    'uploads/san_phams',
                    'sp',
                    1600
                );
            } elseif ($request->boolean('remove_main_image')) {
                $data['hinh_anh_chinh'] = null;
            }
            unset($data['remove_main_image']);

            $gallery = [];
            if (!empty($data['thu_vien_anh_kept'])) {
                $gallery = json_decode($data['thu_vien_anh_kept'], true) ?? [];
            }
            
            if ($request->hasFile('thu_vien_anh_files')) {
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $gallery[] = ImageUploadService::storeUploadedFile(
                        $file,
                        'uploads/san_phams',
                        'sp_gal',
                        1600
                    );
                }
            }
            $data['thu_vien_anh'] = $gallery;

            $sanPham = $this->sanPhamService->update($id, $data);

            // Dọn ảnh cũ bị thay thế/gỡ bỏ
            if (($request->hasFile('hinh_anh_chinh_file') || $request->boolean('remove_main_image')) && $oldMain && $oldMain !== $sanPham->hinh_anh_chinh) {
                ImageUploadService::delete($oldMain);
            }
            foreach (array_diff($oldGallery, $gallery) as $removedPath) {
                ImageUploadService::delete((string) $removedPath);
            }
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
        $query = \App\Domain\CMS\Models\SanPham::with('danhMuc.parent')
            ->where('trang_thai', 1)
            ->whereHas('danhMuc', function ($d) {
                $d->where('trang_thai', 1)
                  ->where(function ($p) {
                      $p->whereNull('parent_id')
                        ->orWhereHas('parent', fn($gp) => $gp->where('trang_thai', 1));
                  });
            });

        if ($request->has('danh_muc') && !empty($request->danh_muc)) {
            $slugs = array_filter(array_map('trim', explode(',', (string) $request->danh_muc)));
            $matchedIds = \App\Domain\CMS\Models\DanhMucSanPham::where('trang_thai', 1)
                ->whereIn('slug', $slugs)
                ->pluck('id')
                ->all();
            $childIds = !empty($matchedIds)
                ? \App\Domain\CMS\Models\DanhMucSanPham::where('trang_thai', 1)
                    ->whereIn('parent_id', $matchedIds)
                    ->pluck('id')
                    ->all()
                : [];
            $targetIds = array_values(array_unique(array_merge($matchedIds, $childIds)));
            if (!empty($targetIds)) {
                $query->whereIn('danh_muc_san_pham_id', $targetIds);
            } else {
                $query->whereRaw('1 = 0');
            }
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
            $query->orderBy('is_gia_lien_he', 'asc')->orderBy('gia_ban', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('is_gia_lien_he', 'asc')->orderBy('gia_ban', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = min(max((int) $request->input('per_page', 16), 1), 100);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function publicShow($slug): JsonResponse
    {
        $sanPham = \App\Domain\CMS\Models\SanPham::with('danhMuc.parent')
            ->where('slug', $slug)
            ->where('trang_thai', 1)
            ->whereHas('danhMuc', function ($d) {
                $d->where('trang_thai', 1)
                  ->where(function ($p) {
                      $p->whereNull('parent_id')
                        ->orWhereHas('parent', fn($gp) => $gp->where('trang_thai', 1));
                  });
            })
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





