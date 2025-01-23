<?php

namespace App\Http\Response;

class ApiResponse
{
    private bool $success;
    private string $message;
    private  $result;

    public function __construct(bool $success = true, string $message = '',  $result = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->result = $result;
    }

    public static function success(string $message = 'Success',  $result = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'result' => $result
        ]);
    }

    public static function error(string $message = 'Error',  $result = null, int $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'result' => $result
        ], $statusCode);
    }

    // Helper method for validation errors
    public static function validationError(string $message = 'Validation Error',  $errors = null): \Illuminate\Http\JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    // Helper method for not found errors
    public static function notFound(string $message = 'Resource not found'): \Illuminate\Http\JsonResponse
    {
        return self::error($message, null, 404);
    }

    // Helper method for unauthorized access
    public static function unauthorized(string $message = 'Unauthorized access'): \Illuminate\Http\JsonResponse
    {
        return self::error($message, null, 401);
    }
}
