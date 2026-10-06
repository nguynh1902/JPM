<?php

namespace App\Domain\MarineCatalog\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\MarineCatalog\Models\LoaiDongCo;

use App\Domain\MarineCatalog\Requests\StoreLoaiDongCoRequest;
use App\Domain\MarineCatalog\Requests\UpdateLoaiDongCoRequest;
use App\Domain\MarineCatalog\Resources\LoaiDongCoResource;
use App\Domain\MarineCatalog\Services\LoaiDongCoService;
use Exception;
use Illuminate\Http\JsonResponse;
class LoaiDongCoController extends Controller
{
    protected $loaiDongCoService;

    public function __construct(LoaiDongCoService $loaiDongCoService)
    {
        $this->loaiDongCoService = $loaiDongCoService;
    }

    public function index(): JsonResponse
    {
        $data = $this->loaiDongCoService->getAll();
        return response()->json([
            'success' => true,
            'data' => LoaiDongCoResource::collection($data)
        ]);
    }

    public function show($id): JsonResponse
    {
        $item = LoaiDongCo::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }
    
    public function store(StoreLoaiDongCoRequest $request): JsonResponse
    {
        try {
            $loaiDongCo = $this->loaiDongCoService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Thêm loại động cơ thành công',
                'data' => new LoaiDongCoResource($loaiDongCo)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateLoaiDongCoRequest $request, $id): JsonResponse
    {
        try {
            $loaiDongCo = $this->loaiDongCoService->update($id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật loại động cơ thành công',
                'data' => new LoaiDongCoResource($loaiDongCo)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->loaiDongCoService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa loại động cơ thành công'
            ]);
        } catch (Exception $e) {
            $status = str_contains($e->getMessage(), 'đang được sử dụng') ? 409 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
        }
    }
}




