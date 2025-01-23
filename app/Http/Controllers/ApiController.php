<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;

class ApiController
{
    protected function successResponse(string $message = 'Success', $result = null): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::success($message, $result);
    }

    protected function errorResponse(string $message = 'Error',  $result = null, int $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::error($message, $result, $statusCode);
    }

    protected function validationErrorResponse(string $message = 'Validation Error',  $errors = null): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::validationError($message, $errors);
    }

    protected function notFoundResponse(string $message = 'Resource not found'): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized access'): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }
}
