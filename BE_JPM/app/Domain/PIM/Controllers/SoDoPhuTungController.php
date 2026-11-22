<?php

namespace App\Domain\PIM\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\PIM\Models\SoDoPhuTung;
use App\Domain\PIM\Models\ChiTietSoDo;

class SoDoPhuTungController extends Controller
{
    public function index(Request $request)
    {
        $all = $request->boolean('all', false);
        $perPage = $all ? null : (int) $request->input('per_page', 50);
        $query = SoDoPhuTung::with(['model.thuongHieu', 'model.loaiDongCo', 'nhomPhuTung'])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('ten_so_do', 'like', "%{$search}%");
        }
        if ($request->filled('may_thuy_model_id')) {
            $query->where('may_thuy_model_id', $request->input('may_thuy_model_id'));
        }
        if ($request->filled('nhom_phu_tung_id')) {
            $query->where('nhom_phu_tung_id', $request->input('nhom_phu_tung_id'));
        }

        if ($all || !$perPage) {
            $soDos = $query->get();
            return response()->json([
                'success' => true,
                'data' => $soDos
            ]);
        }

        $paginated = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'ten_so_do' => 'required|string',
                'may_thuy_model_id' => 'required|integer',
                'nhom_phu_tung_id' => 'required|integer',
                'hinh_anh' => 'nullable|string',
                'hinh_anh_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,avif,heic,heif,tiff,tif,ico,jfif,eps,raw,cr2,nef,psd,ai|max:20480',
                'thu_tu' => 'nullable|integer',
                'trang_thai' => 'nullable|integer',
                'chiTiet' => 'nullable|array'
            ]);

            if ($request->hasFile('hinh_anh_file')) {
                $path = $request->file('hinh_anh_file')->store('so_dos', 'public');
                $data['hinh_anh'] = '/storage/' . $path;
            }
            $soDo = SoDoPhuTung::create($data);

            if (!empty($data['chiTiet'])) {
                foreach ($data['chiTiet'] as $item) {
                    if (empty($item['phu_tung_id'])) continue;
                    ChiTietSoDo::create([
                        'so_do_phu_tung_id' => $soDo->id,
                        'phu_tung_id' => $item['phu_tung_id'] ?? null,
                        'ref_number' => $item['ref_number'] ?? null,
                        'qty' => $item['qty'] ?? 1,
                        'x' => $item['x'] ?? null,
                        'y' => $item['y'] ?? null,
                        'width' => $item['width'] ?? null,
                        'height' => $item['height'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tạo sơ đồ thành công',
                'data' => $soDo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $soDo = SoDoPhuTung::with(['model.thuongHieu', 'model.loaiDongCo', 'chiTiets.phuTung'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $soDo
        ]);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $soDo = SoDoPhuTung::findOrFail($id);
            $data = $request->validate([
                'ten_so_do' => 'nullable|string',
                'may_thuy_model_id' => 'nullable|integer',
                'nhom_phu_tung_id' => 'nullable|integer',
                'hinh_anh' => 'nullable|string',
                'hinh_anh_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,avif,heic,heif,tiff,tif,ico,jfif,eps,raw,cr2,nef,psd,ai|max:20480',
                'thu_tu' => 'nullable|integer',
                'trang_thai' => 'nullable|integer',
                'chiTiet' => 'nullable|array'
            ]);

            if ($request->hasFile('hinh_anh_file')) {
                // Delete old image if needed (optional, keeping it simple)
                if ($soDo->hinh_anh && str_starts_with($soDo->hinh_anh, '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $soDo->hinh_anh);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('hinh_anh_file')->store('so_dos', 'public');
                $data['hinh_anh'] = '/storage/' . $path;
            }
            $soDo->update($data);

            if ($request->has('is_update_chi_tiet')) {
                ChiTietSoDo::where('so_do_phu_tung_id', $soDo->id)->delete();
                if (isset($data['chiTiet']) && is_array($data['chiTiet'])) {
                    foreach ($data['chiTiet'] as $item) {
                    if (empty($item['phu_tung_id'])) continue;
                    ChiTietSoDo::create([
                            'so_do_phu_tung_id' => $soDo->id,
                            'phu_tung_id' => $item['phu_tung_id'] ?? null,
                            'ref_number' => $item['ref_number'] ?? null,
                            'qty' => $item['qty'] ?? 1,
                            'x' => $item['x'] ?? null,
                            'y' => $item['y'] ?? null,
                            'width' => $item['width'] ?? null,
                            'height' => $item['height'] ?? null,
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật sơ đồ thành công',
                'data' => $soDo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $soDo = SoDoPhuTung::findOrFail($id);
            // Xóa các chi tiết sơ đồ trước
            ChiTietSoDo::where('so_do_phu_tung_id', $id)->delete();
            $soDo->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa sơ đồ và chi tiết thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa sơ đồ: ' . $e->getMessage()
            ], 500);
        }
    }
}





