<?php

namespace App\Domain\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\IAM\Models\SystemLog;
use Illuminate\Http\Request;
use App\Domain\Core\Resources\SystemLogResource;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with(['user', 'user.roles']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action_type')) {
            $query->where('action', $request->action_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Return cursorPaginate so we can load more without pages
        $logs = $query->orderBy('id', 'desc')->cursorPaginate(30);

        return response()->json([
            'success' => true,
            'data' => SystemLogResource::collection($logs)->response()->getData(true)
        ]);
    }
}
