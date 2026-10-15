<?php
namespace App\Domain\Procurement\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Procurement\Models\CrossDocking;
use App\Domain\Procurement\Services\CrossDockingService;
use Exception;
use Illuminate\Http\JsonResponse;

class CrossDockingController extends Controller
{
    protected $crossDockingService;
    public function __construct(CrossDockingService $crossDockingService)
    {
        $this->crossDockingService = $crossDockingService;
    }
    public function index(): JsonResponse
    {
        try {
            // Fetch real data, map trang_thai to status for frontend compatibility
            $data = CrossDocking::orderBy('created_at', 'desc')->get()->map(function($item) {
                $item->status = $item->trang_thai;
                return $item;
            });
            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function store(Request $request): JsonResponse
    {
        $barcode = $request->input('barcode');
        if (!$barcode) { return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã vạch.'], 400); }
        $result = $this->crossDockingService->processScan($barcode);
        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 400);
        }
    }
}