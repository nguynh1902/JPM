<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\Core\Requests\StoreFaqRequest;
use App\Domain\Core\Requests\UpdateFaqRequest;
use App\Domain\Core\Resources\FaqResource;
use App\Domain\CMS\Models\Faq;
use Exception;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => FaqResource::collection(Faq::orderBy('id', 'desc')->get())
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        try {
            $item = Faq::create($request->validated());
            return response()->json([
                'success' => true,
                'data' => new FaqResource($item)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $item = Faq::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new FaqResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateFaqRequest $request, $id): JsonResponse
    {
        try {
            $item = Faq::findOrFail($id);
            $item->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => new FaqResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Faq::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}


