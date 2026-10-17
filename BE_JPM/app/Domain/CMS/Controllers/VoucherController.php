<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\CMS\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        return response()->json(['success' => true, 'data' => $voucher]);
    }

    public function index()
    {
        $vouchers = Voucher::orderBy('id', 'desc')->cursorPaginate(50);
        return response()->json([
            'success' => true,
            'data' => $vouchers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'discount_type' => 'required|in:PERCENT,AMOUNT',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean'
        ]);

        $voucher = Voucher::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    public function update(Request $request, string $id)
    {
        $voucher = Voucher::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'discount_type' => 'required|in:PERCENT,AMOUNT',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean'
        ]);

        $voucher->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    public function destroy(string $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa voucher thành công'
        ]);
    }
}




