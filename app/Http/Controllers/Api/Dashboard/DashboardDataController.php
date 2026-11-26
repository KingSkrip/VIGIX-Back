<?php

namespace App\Http\Controllers\Api\Dashboard;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardDataController
{
    /**
     * Recupera los datos del usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'No hay un usuario autenticado.',
            ], 401);
        }

        $user->makeHidden([
            'password',
            'remember_token',
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }
}