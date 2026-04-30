<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => self::meta($meta),
        ], $status);
    }

    public static function paginated(array $data, array $pagination, string $message = 'OK', int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'meta' => self::meta($meta),
        ], $status);
    }

    public static function error(string $message, int $status = 400, string $code = 'ERROR', array $details = [], array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => [
                'code' => $code,
                'details' => $details,
            ],
            'meta' => self::meta($meta),
        ], $status);
    }

    private static function meta(array $meta = []): array
    {
        return array_merge([
            'request_id' => request()->headers->get('X-Request-Id', 'req_' . substr(md5((string) microtime(true)), 0, 12)),
            'timestamp' => now()->toIso8601String(),
        ], $meta);
    }
}
