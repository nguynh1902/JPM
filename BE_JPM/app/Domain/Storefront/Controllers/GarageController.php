<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\IAM\Models\GarageMayThuy;
use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use Illuminate\Support\Facades\Validator;

class GarageController extends Controller
{
    /**
     * Lay danh sach may trong garage cua user hien tai
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $garage = GarageMayThuy::where('id_tai_khoan', $user->id)
            ->with(['dongCoModel.thuongHieu', 'dongCoModel.loaiDongCo'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($garage);
    }

    /**
     * Them may moi vao garage
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'dong_co_model_id' => 'required|exists:dong_co_models,id',
            'ten_may'          => 'nullable|string|max:255',
            'so_serial'        => 'nullable|string|max:100',
            'nam_mua'          => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'ghi_chu'          => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $may = GarageMayThuy::create([
            'id_tai_khoan'    => $user->id,
            'dong_co_model_id' => $request->dong_co_model_id,
            'ten_may'         => $request->ten_may,
            'so_serial'       => $request->so_serial,
            'nam_mua'         => $request->nam_mua,
            'ghi_chu'         => $request->ghi_chu,
        ]);

        $may->load(['dongCoModel.thuongHieu', 'dongCoModel.loaiDongCo']);

        return response()->json(['message' => 'Da them may vao garage!', 'may' => $may], 201);
    }

    /**
     * Cap nhat thong tin may
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $may = GarageMayThuy::where('id_tai_khoan', $user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'dong_co_model_id' => 'required|exists:dong_co_models,id',
            'ten_may'          => 'nullable|string|max:255',
            'so_serial'        => 'nullable|string|max:100',
            'nam_mua'          => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'ghi_chu'          => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $may->update([
            'dong_co_model_id' => $request->dong_co_model_id,
            'ten_may'         => $request->ten_may,
            'so_serial'       => $request->so_serial,
            'nam_mua'         => $request->nam_mua,
            'ghi_chu'         => $request->ghi_chu,
        ]);

        $may->load(['dongCoModel.thuongHieu', 'dongCoModel.loaiDongCo']);

        return response()->json(['message' => 'Cap nhat may thanh cong!', 'may' => $may]);
    }

    /**
     * Xoa may khoi garage
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $may = GarageMayThuy::where('id_tai_khoan', $user->id)->findOrFail($id);
        $may->delete();

        return response()->json(['message' => 'Da xoa may khoi garage!']);
    }

    /**
     * Lay danh sach thuong hieu MARINE de hien thi tren form
     */
    public function getThuongHieu()
    {
        $brands = ThuongHieu::where('status', 1)
            ->select('id', 'name', 'logo_path', 'code')
            ->orderBy('name')
            ->get();

        return response()->json($brands);
    }

    /**
     * Lay danh sach Model theo Thuong Hieu de filter trong dropdown
     */
    public function getModelsByBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'thuong_hieu_id' => 'required|exists:thuong_hieus,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $models = DongCoModel::where('thuong_hieu_id', $request->thuong_hieu_id)
            ->where('trang_thai', 1)
            
            ->select('id', 'ma_model', 'ten_model', 'ma_luc', 'nam_bat_dau', 'nam_ket_thuc')
            ->orderBy('ten_model')
            ->get();

        return response()->json($models);
    }
}

