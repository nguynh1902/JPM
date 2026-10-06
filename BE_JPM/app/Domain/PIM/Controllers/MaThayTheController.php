<?php

namespace App\Domain\PIM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\PIM\Models\MaThayThe;
use Illuminate\Http\Request;

class MaThayTheController extends Controller
{
    public function index()
    {
        $items = MaThayThe::with(['phuTungCu', 'phuTungMoi'])->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function show($id)
    {
        $item = MaThayThe::with(['phuTungCu', 'phuTungMoi'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phu_tung_cu_id' => 'required|exists:phu_tungs,id',
            'phu_tung_moi_id' => 'required|exists:phu_tungs,id|different:phu_tung_cu_id',
            'loai_thay_the' => 'required|string',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|boolean',
        ], [
            'phu_tung_moi_id.different' => 'Phụ tùng mới không được trùng với phụ tùng cũ.'
        ]);

        // Kiểm tra xem đã tồn tại chưa
        $exists = MaThayThe::where('phu_tung_cu_id', $validated['phu_tung_cu_id'])
            ->where('phu_tung_moi_id', $validated['phu_tung_moi_id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Mã thay thế này đã tồn tại!'], 400);
        }

        $item = MaThayThe::create($validated);
        $item->load(['phuTungCu', 'phuTungMoi']);

        return response()->json([
            'success' => true,
            'message' => 'Tạo mã thay thế thành công',
            'data' => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = MaThayThe::findOrFail($id);

        $validated = $request->validate([
            'phu_tung_cu_id' => 'required|exists:phu_tungs,id',
            'phu_tung_moi_id' => 'required|exists:phu_tungs,id|different:phu_tung_cu_id',
            'loai_thay_the' => 'required|string',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|boolean',
        ], [
            'phu_tung_moi_id.different' => 'Phụ tùng mới không được trùng với phụ tùng cũ.'
        ]);

        // Check if duplicate for another ID
        $exists = MaThayThe::where('phu_tung_cu_id', $validated['phu_tung_cu_id'])
            ->where('phu_tung_moi_id', $validated['phu_tung_moi_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Mã thay thế này đã tồn tại!'], 400);
        }

        $item->update($validated);
        $item->load(['phuTungCu', 'phuTungMoi']);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật mã thay thế thành công',
            'data' => $item
        ]);
    }

    public function destroy($id)
    {
        $item = MaThayThe::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa mã thay thế thành công'
        ]);
    }
}
