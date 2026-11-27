<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Core\Requests\StoreTinTucRequest;
use App\Domain\Core\Requests\UpdateTinTucRequest;
use App\Domain\Core\Resources\TinTucResource;
use App\Domain\Core\Services\ImageUploadService;
use App\Domain\CMS\Models\TinTuc;
use Exception;
use Illuminate\Http\JsonResponse;

class TinTucController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $query = TinTuc::orderBy('id', 'desc');
            $items = request()->filled('cursor') ? $query->cursorPaginate(50) : $query->limit(500)->get();
            return response()->json([
                'success' => true,
                'data' => TinTucResource::collection($items)
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
                $data['thumbnail'] = ImageUploadService::storeUploadedFile(
                    $request->file('thumbnail'),
                    'uploads/tin_tuc',
                    'news',
                    1600
                );
            } elseif ($request->boolean('remove_thumbnail')) {
                $data['thumbnail'] = null;
            }
            unset($data['remove_thumbnail']);

            $galleryPaths = [];
            if ($request->has('existing_gallery')) {
                $existing = $request->input('existing_gallery');
                $galleryPaths = is_array($existing) ? $existing : (json_decode($existing, true) ?? []);
            }
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $f) {
                    $galleryPaths[] = ImageUploadService::storeUploadedFile(
                        $f,
                        'uploads/tin_tuc/gallery',
                        'news_gal',
                        1600
                    );
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
            $oldThumb = $item->thumbnail;
            $oldGallery = is_array($item->gallery) ? $item->gallery : (json_decode((string) $item->gallery, true) ?? []);

            $data = $request->validated();
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = ImageUploadService::storeUploadedFile(
                    $request->file('thumbnail'),
                    'uploads/tin_tuc',
                    'news',
                    1600
                );
            } elseif ($request->boolean('remove_thumbnail')) {
                $data['thumbnail'] = null;
            }
            unset($data['remove_thumbnail']);

            $galleryPaths = [];
            if ($request->has('existing_gallery')) {
                $existing = $request->input('existing_gallery');
                $galleryPaths = is_array($existing) ? $existing : (json_decode($existing, true) ?? []);
            }
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $f) {
                    $galleryPaths[] = ImageUploadService::storeUploadedFile(
                        $f,
                        'uploads/tin_tuc/gallery',
                        'news_gal',
                        1600
                    );
                }
            }
            unset($data['existing_gallery']);
            $data['gallery'] = json_encode(array_values($galleryPaths));

            $item->update($data);

            // Dọn ảnh thumbnail cũ nếu thay mới hoặc xóa & ảnh gallery đã bị gỡ
            if (($request->hasFile('thumbnail') || $request->boolean('remove_thumbnail')) && $oldThumb && $oldThumb !== $item->thumbnail) {
                ImageUploadService::delete($oldThumb);
            }
            foreach (array_diff($oldGallery, $galleryPaths) as $removedPath) {
                ImageUploadService::delete((string) $removedPath);
            }

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
            $item = TinTuc::findOrFail($id);
            $oldThumb = $item->thumbnail;
            $oldGallery = is_array($item->gallery) ? $item->gallery : (json_decode((string) $item->gallery, true) ?? []);

            $item->delete();

            ImageUploadService::delete($oldThumb);
            foreach ($oldGallery as $path) {
                ImageUploadService::delete((string) $path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
