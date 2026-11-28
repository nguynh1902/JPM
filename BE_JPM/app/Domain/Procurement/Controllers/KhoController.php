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
            'ten_kho' => 'required|string|max:100',
            'dia_chi' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

        $data['ten_kho'] = trim($data['ten_kho']);
        $data['trang_thai'] = isset($data['trang_thai']) ? (int) $data['trang_thai'] : 1;

        $duplicate = Kho::whereRaw('LOWER(ten_kho) = ?', [mb_strtolower($data['ten_kho'], 'UTF-8')])->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Tên kho đã tồn tại trong hệ thống!'
            ], 422);
        }

        $nextNum = (int) (Kho::withTrashed()->max('id') ?? 0) + 1;
        do {
            $maKho = 'KHO' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (Kho::withTrashed()->where('ma_kho', $maKho)->exists());

        $data['ma_kho'] = $maKho;

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
            'ten_kho' => 'required|string|max:100',
            'dia_chi' => 'nullable|string|max:255',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

        $data['ten_kho'] = trim($data['ten_kho']);
        if (!array_key_exists('trang_thai', $data) || $data['trang_thai'] === null) {
            $data['trang_thai'] = (int) ($kho->trang_thai ?? 1);
        }

        $duplicate = Kho::where('id', '!=', $kho->id)
            ->whereRaw('LOWER(ten_kho) = ?', [mb_strtolower($data['ten_kho'], 'UTF-8')])
            ->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Tên kho đã tồn tại ở bản ghi khác!'
            ], 422);
        }

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
