<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\Core\Requests\StoreTinTucRequest;
use App\Domain\Core\Requests\UpdateTinTucRequest;
use App\Domain\Core\Resources\TinTucResource;
use App\Domain\CMS\Models\TinTuc;
use Exception;
use Illuminate\Http\JsonResponse;

class TinTucController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => TinTucResource::collection(request()->filled('cursor') ? TinTuc::orderBy('id', 'desc')->cursorPaginate(50) : TinTuc::orderBy('id', 'desc')->get())
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreTinTucRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('tin_tuc', 'public');
                $data['thumbnail'] = 'storage/' . $path;
            }

            // Handle Gallery
            $galleryPaths = [];
            if ($request->has('existing_gallery')) {
                $existing = $request->input('existing_gallery');
                $galleryPaths = is_array($existing) ? $existing : (json_decode($existing, true) ?? []);
            }
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $f) {
                    $path = $f->store('tin_tuc/gallery', 'public');
                    $galleryPaths[] = 'storage/' . $path;
                }
            }
            unset($data['existing_gallery']);
            $data['gallery'] = json_encode(array_values($galleryPaths));

            $item = TinTuc::create($data);
            return response()->json([
                'success' => true,
                'data' => new TinTucResource($item)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $item = TinTuc::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new TinTucResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateTinTucRequest $request, $id): JsonResponse
    {
        try {
            $item = TinTuc::findOrFail($id);
            $data = $request->validated();
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('tin_tuc', 'public');
                $data['thumbnail'] = 'storage/' . $path;
            }

            // Handle Gallery
            $galleryPaths = [];
            if ($request->has('existing_gallery')) {
                $existing = $request->input('existing_gallery');
                $galleryPaths = is_array($existing) ? $existing : (json_decode($existing, true) ?? []);
            }
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $f) {
                    $path = $f->store('tin_tuc/gallery', 'public');
                    $galleryPaths[] = 'storage/' . $path;
                }
            }
            unset($data['existing_gallery']);
            $data['gallery'] = json_encode(array_values($galleryPaths));

            $item->update($data);
            return response()->json([
                'success' => true,
                'data' => new TinTucResource($item)
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            TinTuc::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}




