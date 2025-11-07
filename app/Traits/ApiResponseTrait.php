<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(
        string $message = 'OK',
        int $status = 200,
        $data = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = 'Error',
        int $status = 400,
        $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
