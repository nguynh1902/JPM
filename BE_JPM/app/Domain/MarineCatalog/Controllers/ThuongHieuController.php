<?php

namespace App\Domain\MarineCatalog\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\MarineCatalog\Requests\StoreThuongHieuRequest;
use App\Domain\MarineCatalog\Requests\UpdateThuongHieuRequest;
use App\Domain\MarineCatalog\Resources\ThuongHieuResource;
use App\Domain\MarineCatalog\Services\ThuongHieuService;
use Exception;
use Illuminate\Http\JsonResponse;

class ThuongHieuController extends Controller
{
    protected $thuongHieuService;

    public function __construct(ThuongHieuService $thuongHieuService)
    {
        $this->thuongHieuService = $thuongHieuService;
    }

    public function index(): JsonResponse
    {
        try {
            $brands = $this->thuongHieuService->getAll();
            return response()->json([
                'success' => true,
                'data' => ThuongHieuResource::collection($brands)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreThuongHieuRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('logo')) {
                $data['logo_path'] = $request->file('logo')->store('thuong_hieus', 'public');
            }
            $brand = $this->thuongHieuService->create($data);
            return response()->json([
                'success' => true,
                'message' => 'Thêm thương hiệu thành công',
                'data' => new ThuongHieuResource($brand)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $brand = $this->thuongHieuService->getById($id);
            return response()->json([
                'success' => true,
                'data' => new ThuongHieuResource($brand)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateThuongHieuRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('logo')) {
                $data['logo_path'] = $request->file('logo')->store('thuong_hieus', 'public');
            } elseif ($request->has('logo') && ($request->input('logo') === null || $request->input('logo') === '')) {
                $data['logo_path'] = null;
            }
            $brand = $this->thuongHieuService->update($id, $data);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thương hiệu thành công',
                'data' => new ThuongHieuResource($brand)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->thuongHieuService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa thương hiệu thành công'
            ]);
        } catch (Exception $e) {
            $status = str_contains($e->getMessage(), 'đang có') ? 409 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
        }
    }
}


