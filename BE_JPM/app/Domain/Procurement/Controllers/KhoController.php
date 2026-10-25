<?php

namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Procurement\Models\Kho;
use App\Domain\Procurement\Models\TonKho;

class KhoController extends Controller
{
    public function index()
    {
        $khos = Kho::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $khos
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_kho' => 'required|string',
            'dia_chi' => 'nullable|string',
            'trang_thai' => 'integer'
        ]);

        $lastKho = Kho::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastKho ? $lastKho->id + 1 : 1;
        $data['ma_kho'] = 'KHO' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $kho = Kho::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thêm kho thành công',
            'data' => $kho
        ]);
    }

    public function show(string $id)
    {
        $kho = Kho::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $kho
        ]);
    }

    public function update(Request $request, string $id)
    {
        $kho = Kho::findOrFail($id);
        
        $data = $request->validate([
            'ten_kho' => 'required|string',
            'dia_chi' => 'nullable|string',
            'trang_thai' => 'integer'
        ]);
        
        $kho->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kho thành công',
            'data' => $kho
        ]);
    }

    public function destroy(string $id)
    {
        $kho = Kho::findOrFail($id);

        if (TonKho::where('id_kho', $kho->id)->where(function ($q) {
            $q->where('so_luong_ton', '>', 0)->orWhere('so_luong_dang_giu', '>', 0);
        })->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa kho này vì vẫn còn hàng tồn kho hoặc hàng đang giữ trong kho!'
            ], 400);
        }

        $kho->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa kho thành công'
        ]);
    }
}
