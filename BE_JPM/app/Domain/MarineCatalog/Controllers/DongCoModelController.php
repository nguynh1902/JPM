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
class DongCoModelController extends Controller
{
    protected $DongCoModelService;

    public function __construct(DongCoModelService $DongCoModelService)
    {
        $this->DongCoModelService = $DongCoModelService;
    }

    public function index(): JsonResponse
    {
        $data = $this->DongCoModelService->getAll();
        return response()->json([
            'success' => true,
            'data' => DongCoModelResource::collection($data)
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
            if ($request->hasFile('hinh_anh')) {
                $data['hinh_anh'] = $request->file('hinh_anh')->store('dong_co_models', 'public');
            }
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
            if ($request->hasFile('hinh_anh')) {
                $data['hinh_anh'] = $request->file('hinh_anh')->store('dong_co_models', 'public');
            }
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





