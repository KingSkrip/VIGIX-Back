<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateJwtSession
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            $sessionUuid = $payload->get('sid');

            if (! $sessionUuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'El token no contiene una sesión válida.',
                ], 401);
            }

            $session = DB::table('user_sessions')
                ->where('session_uuid', $sessionUuid)
                ->where('user_id', $payload->get('sub'))
                ->where('status', 'active')
                ->first();

            if (! $session) {
                return response()->json([
                    'success' => false,
                    'message' => 'La sesión ha sido cerrada o revocada.',
                ], 401);
            }

            DB::table('user_sessions')
                ->where('id', $session->id)
                ->update([
                    'last_activity_at' => now(),
                    'updated_at' => now(),
                ]);

            return $next($request);

        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado.',
            ], 401);
        }
    }
}
