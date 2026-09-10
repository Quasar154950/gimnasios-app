<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMctandilSupportKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = env('MCTANDIL_SUPPORT_API_KEY');
        $providedKey = $request->header('X-MCTANDIL-SUPPORT-KEY');

        if (
            !$expectedKey ||
            !$providedKey ||
            !hash_equals($expectedKey, $providedKey)
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'No autorizado.',
            ], 401);
        }

        return $next($request);
    }
}
