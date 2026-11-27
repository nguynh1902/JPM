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
            $oldName = $item->name;
            $oldType = $item->type;

            $validated = $request->validated();

            if ($oldType !== $validated['type']) {
                $inUse = ($oldType === 'NEWS' && \App\Domain\CMS\Models\TinTuc::where('category', $oldName)->exists())
                    || ($oldType === 'FAQ' && \App\Domain\CMS\Models\Faq::where('category', $oldName)->exists());
                if ($inUse) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể đổi loại chuyên mục vì đang có bài viết/câu hỏi sử dụng chuyên mục này.'
                    ], 422);
                }
            }

            $item->update($validated);

            if ($oldName && $oldName !== $item->name) {
                if ($item->type === 'NEWS') {
                    \App\Domain\CMS\Models\TinTuc::where('category', $oldName)->update(['category' => $item->name]);
                } elseif ($item->type === 'FAQ') {
                    \App\Domain\CMS\Models\Faq::where('category', $oldName)->update(['category' => $item->name]);
                }
            }

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
            $item = ChuyenMuc::findOrFail($id);
            $inUse = ($item->type === 'NEWS' && \App\Domain\CMS\Models\TinTuc::where('category', $item->name)->exists())
                || ($item->type === 'FAQ' && \App\Domain\CMS\Models\Faq::where('category', $item->name)->exists());
            if ($inUse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa! Chuyên mục này đang có bài viết hoặc câu hỏi sử dụng.'
                ], 409);
            }
            $item->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}



