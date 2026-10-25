<?php

namespace App\Domain\PIM\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\PIM\Models\NhomPhuTung;
use Illuminate\Http\Request;

class NhomPhuTungController extends Controller
{
    public function index(Request $request)
    {
        $query = NhomPhuTung::orderBy('id', 'desc');
        if ($request->boolean('all')) {
            return response()->json([
                'success' => true,
                'data' => \App\Domain\PIM\Resources\NhomPhuTungResource::collection($query->get())
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => \App\Domain\PIM\Resources\NhomPhuTungResource::collection($request->filled('cursor') ? $query->cursorPaginate(50) : $query->get())
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'trang_thai' => 'required|boolean',
        ]);

        $item = new NhomPhuTung();
        $item->ten_nhom = $validated['ten_nhom'];
        $item->trang_thai = $validated['trang_thai'];
        
        // Generate temporary code to bypass DB NOT NULL constraints before getting ID
        $item->ma_nhom = 'TEMP_' . time() . rand(100,999);
        $item->save();
        
        $item->ma_nhom = 'NPT' . str_pad($item->id, 2, '0', STR_PAD_LEFT);
        $item->save();

        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => NhomPhuTung::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = NhomPhuTung::findOrFail($id);
        
        $validated = $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'trang_thai' => 'required|boolean',
        ]);
        
        $item->update($validated);
        
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function destroy($id)
    {
        try {
            $item = NhomPhuTung::findOrFail($id);
            if ($item->phuTungs()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa! Nhóm phụ tùng này đang được sử dụng.'
                ], 400);
            }
            
            $item->delete();
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa nhóm phụ tùng: ' . $e->getMessage()
            ], 500);
        }
    }
}






