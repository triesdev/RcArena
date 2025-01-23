<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the Authorization header exists
        if (!$request->hasHeader('Authorization')) {
            return $this->unauthorizedResponse('Authorization header missing');
        }

        // Extract and clean up the Bearer token
        $bearerToken = $request->header('Authorization');
        if (!str_starts_with($bearerToken, 'Bearer ')) {
            return $this->unauthorizedResponse('Invalid token format');
        }

        $token = str_replace('Bearer ', '', $bearerToken);

        // Check if the token exists in the database
        $user = User::where('panel_token', $token)->first();
        if (!$user) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // Attach the authenticated user to the request for downstream usage
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }

    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'result' => null,
        ], 401);
    }
}
