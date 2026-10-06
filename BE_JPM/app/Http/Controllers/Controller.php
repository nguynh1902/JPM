<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Trả về Response thành công theo chuẩn JPM (mới).
     */
    protected function sendResponse($data = [], string $message = 'Thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Trả về Response lỗi theo chuẩn JPM (mới).
     */
    protected function sendError(string $message = 'Có lỗi xảy ra', $errors = [], int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}


