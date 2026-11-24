<?php

namespace App\Domain\MarineCatalog\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\MarineCatalog\Models\DongCoModel;

use App\Domain\MarineCatalog\Requests\StoreDongCoModelRequest;
use App\Domain\MarineCatalog\Requests\UpdateDongCoModelRequest;
use App\Domain\MarineCatalog\Resources\DongCoModelResource;
use App\Domain\MarineCatalog\Services\DongCoModelService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DongCoModelController extends Controller
{
    protected $DongCoModelService;

    public function __construct(DongCoModelService $DongCoModelService)
    {
        $this->DongCoModelService = $DongCoModelService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'thuong_hieu_id', 'loai_dong_co_id', 'nam_san_xuat']);
        $hasPagination = $request->has('per_page') || $request->has('page');
        $all = $request->boolean('all', false) || !$hasPagination;
        $perPage = $all ? null : (int) $request->input('per_page', 50);

        $models = $this->DongCoModelService->getAll($filters, $perPage);

        if ($all || !$perPage) {
            return response()->json([
                'success' => true,
                'data' => DongCoModelResource::collection($models)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => DongCoModelResource::collection($models->items()),
            'meta' => [
                'current_page' => $models->currentPage(),
                'last_page'    => $models->lastPage(),
                'per_page'     => $models->perPage(),
                'total'        => $models->total(),
            ]
        ]);
    }

    public function show($id): JsonResponse
    {
        try {
            $item = $this->DongCoModelService->getById($id);
            return response()->json([
                'success' => true,
                'data' => new DongCoModelResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
    
    public function store(StoreDongCoModelRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $model = $this->DongCoModelService->create($data);
            $model->load(['thuongHieu', 'loaiDongCo']);
            return response()->json([
                'success' => true,
                'message' => 'Thêm model thành công',
                'data' => new DongCoModelResource($model)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateDongCoModelRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $model = $this->DongCoModelService->update($id, $data);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật model thành công',
                'data' => new DongCoModelResource($model)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->DongCoModelService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa model thành công'
            ]);
        } catch (Exception $e) {
            $status = str_contains($e->getMessage(), 'sử dụng') ? 409 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
        }
    }
}





