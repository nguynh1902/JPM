<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\PIM\Requests\StoreDanhMucSanPhamRequest;
use App\Domain\PIM\Requests\UpdateDanhMucSanPhamRequest;
use App\Domain\PIM\Resources\DanhMucSanPhamResource;
use App\Domain\PIM\Services\DanhMucSanPhamService;
use Exception;
use Illuminate\Http\JsonResponse;
class DanhMucSanPhamController extends Controller
{
    protected $danhMucService;

    public function __construct(DanhMucSanPhamService $danhMucService)
    {
        $this->danhMucService = $danhMucService;
    }

    public function publicIndex(): JsonResponse
    {
        $data = \App\Domain\CMS\Models\DanhMucSanPham::where('trang_thai', 1)
                    
                    ->orderBy('id', 'desc')
                    ->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function publicGrouped(): JsonResponse
    {
        $categories = \App\Domain\CMS\Models\DanhMucSanPham::where('trang_thai', 1)
                    
                    ->orderBy('id', 'desc')
                    ->get();
        
        $data = [];
        foreach ($categories as $cat) {
            $products = \App\Domain\CMS\Models\SanPham::with('danhMuc')
                ->where('danh_muc_san_pham_id', $cat->id)
                ->where('trang_thai', 1)
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();
            
            if ($products->count() > 0) {
                $cat->setAttribute('products', $products);
                $data[] = $cat;
            }
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id): JsonResponse
    {
        try {
            $danhMuc = $this->danhMucService->getById($id);
            return response()->json([
                'success' => true,
                'data' => new DanhMucSanPhamResource($danhMuc)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function index(): JsonResponse
    {
        $data = $this->danhMucService->getAll();
        return response()->json([
            'success' => true,
            'data' => DanhMucSanPhamResource::collection($data)
        ]);
    }

    public function store(StoreDanhMucSanPhamRequest $request): JsonResponse
    {
        try {
            $danhMuc = $this->danhMucService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục thành công',
                'data' => new DanhMucSanPhamResource($danhMuc)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateDanhMucSanPhamRequest $request, $id): JsonResponse
    {
        try {
            $danhMuc = $this->danhMucService->update($id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công',
                'data' => new DanhMucSanPhamResource($danhMuc)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->danhMucService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}






