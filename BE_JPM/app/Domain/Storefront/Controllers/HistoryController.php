<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Models\DonHang;

class HistoryController extends Controller
{
    /**
     * Get quote history for current user
     */
    public function getQuotes(Request $request)
    {
        $user = $request->user();
        
        $quotes = YeuCauBaoGia::with('chiTiet')
            ->where(function($q) use ($user) {
                $q->where('khach_hang_email', $user->email);
                if ($user->phone) {
                    $q->orWhere('khach_hang_sdt', $user->phone);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
            
        return response()->json($quotes);
    }
    
    /**
     * Get order history for current user
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();
        
        // Find customer ID
        $khachHang = \App\Domain\IAM\Models\KhachHang::where('id_tai_khoan', $user->id)->first();
        
        if (!$khachHang) {
            return response()->json([]);
        }
        
        // We'll need a DonHang model or just DB query if it doesn't exist
        $orders = \Illuminate\Support\Facades\DB::table('don_hangs')
            ->where('id_khach_hang', $khachHang->id)
            ->orderBy('id', 'desc')
            ->get();
            
        // Get items for each order
        foreach ($orders as $order) {
            $order->items = \Illuminate\Support\Facades\DB::table('chi_tiet_don_hangs')
                ->where('id_don_hang', $order->id)
                ->get();
        }
            
        return response()->json($orders);
    }
}
