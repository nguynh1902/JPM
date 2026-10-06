<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\Core\Requests\StoreChuyenMucRequest;
use App\Domain\Core\Requests\UpdateChuyenMucRequest;
use App\Domain\Core\Resources\ChuyenMucResource;
use App\Domain\CMS\Models\ChuyenMuc;
use Exception;
use Illuminate\Http\JsonResponse;

class ChuyenMucController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => ChuyenMucResource::collection(ChuyenMuc::orderBy('type', 'asc')->orderBy('id', 'asc')->get())
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreChuyenMucRequest $request): JsonResponse
    {
        try {
            $item = ChuyenMuc::create($request->validated());
            return response()->json([
                'success' => true,
                'data' => new ChuyenMucResource($item)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $item = ChuyenMuc::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new ChuyenMucResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateChuyenMucRequest $request, $id): JsonResponse
    {
        try {
            $item = ChuyenMuc::findOrFail($id);
            $item->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => new ChuyenMucResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            ChuyenMuc::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}



