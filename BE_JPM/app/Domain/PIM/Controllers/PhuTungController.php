<?php

namespace App\Domain\PIM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\PIM\Requests\StorePhuTungRequest;
use App\Domain\PIM\Requests\UpdatePhuTungRequest;
use App\Domain\PIM\Resources\AdminPhuTungResource;
use App\Domain\PIM\Services\PhuTungService;
use Exception;
use Illuminate\Http\JsonResponse;

class PhuTungController extends Controller
{
    protected $phuTungService;

    public function __construct(PhuTungService $phuTungService)
    {
        $this->phuTungService = $phuTungService;
    }

    public function index(): JsonResponse
    {
        try {
            $phuTungs = $this->phuTungService->getAll();
            return response()->json([
                'success' => true,
                'data'    => AdminPhuTungResource::collection($phuTungs),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $phuTung = $this->phuTungService->getById($id);
            return response()->json([
                'success' => true,
                'data'    => new AdminPhuTungResource($phuTung),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(StorePhuTungRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle main image upload
            if ($request->hasFile('hinh_anh_file')) {
                $path = $request->file('hinh_anh_file')->store('phu_tungs', 'public');
                $data['hinh_anh'] = '/storage/' . $path;
            }

            // Handle gallery upload
            if ($request->hasFile('thu_vien_anh_files')) {
                $gallery = [];
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $path = $file->store('phu_tungs', 'public');
                    $gallery[] = '/storage/' . $path;
                }
                $data['thu_vien_anh'] = $gallery;
            }

            $phuTung = $this->phuTungService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tạo phụ tùng thành công',
                'data'    => new AdminPhuTungResource($phuTung),
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdatePhuTungRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle main image upload
            if ($request->hasFile('hinh_anh_file')) {
                $path = $request->file('hinh_anh_file')->store('phu_tungs', 'public');
                $data['hinh_anh'] = '/storage/' . $path;
            } elseif ($request->exists('hinh_anh_file')) {
                $data['hinh_anh'] = null;
            }

            // Handle gallery — keep existing + add new
            $gallery = [];
            if (!empty($data['thu_vien_anh_kept'])) {
                $gallery = json_decode($data['thu_vien_anh_kept'], true) ?? [];
            }
            if ($request->hasFile('thu_vien_anh_files')) {
                foreach ($request->file('thu_vien_anh_files') as $file) {
                    $path = $file->store('phu_tungs', 'public');
                    $gallery[] = '/storage/' . $path;
                }
            }
            if (isset($data['thu_vien_anh_kept']) || $request->hasFile('thu_vien_anh_files')) {
                $data['thu_vien_anh'] = $gallery;
            }
            unset($data['thu_vien_anh_kept']);

            $phuTung = $this->phuTungService->update($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phụ tùng thành công',
                'data'    => new AdminPhuTungResource($phuTung),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->phuTungService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa phụ tùng thành công',
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
