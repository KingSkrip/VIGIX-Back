<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    /**
     * Iniciar sesión mediante correo y contraseña.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'max:255'],

            'device.uuid' => ['required', 'uuid'],
            'device.name' => ['nullable', 'string', 'max:150'],
            'device.brand' => ['nullable', 'string', 'max:100'],
            'device.model' => ['nullable', 'string', 'max:150'],
            'device.device_type' => ['nullable', 'string', 'max:40'],
            'device.operating_system' => ['nullable', 'string', 'max:80'],
            'device.os_version' => ['nullable', 'string', 'max:80'],
            'device.browser' => ['nullable', 'string', 'max:100'],
            'device.browser_version' => ['nullable', 'string', 'max:80'],
            'device.app_version' => ['nullable', 'string', 'max:40'],
            'device.platform' => ['nullable', 'string', 'max:40'],
            'device.metadata' => ['nullable', 'array'],
        ]);

        $email = mb_strtolower($validated['email']);

        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $user = User::where('email', $email)->first();

        /*
        |--------------------------------------------------------------------------
        | Validar credenciales
        |--------------------------------------------------------------------------
        */

        if (!$user || !Hash::check($validated['password'], $user->password)) {

            $this->recordLoginAttempt(
                userId: $user?->id,
                email: $email,
                deviceId: null,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'invalid_credentials'
            );

            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar estado del usuario
        |--------------------------------------------------------------------------
        */

        if (!$user->is_active || $user->trashed()) {

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: null,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'inactive_user'
            );

            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta se encuentra desactivada.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar empresa
        |--------------------------------------------------------------------------
        */

        if (!$user->company_id || !$user->company?->is_active) {

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: null,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'inactive_company'
            );

            return response()->json([
                'success' => false,
                'message' => 'La empresa asociada a tu cuenta no está disponible.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Registrar o recuperar dispositivo
        |--------------------------------------------------------------------------
        */

        $deviceData = $validated['device'];

        $device = DB::table('devices')
            ->where('uuid', $deviceData['uuid'])
            ->first();

        if ($device && (int) $device->user_id !== (int) $user->id) {

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: null,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'device_belongs_to_another_user'
            );

            return response()->json([
                'success' => false,
                'message' => 'El dispositivo no está asociado a esta cuenta.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Registrar dispositivo nuevo
        |--------------------------------------------------------------------------
        */

        if (!$device) {

            $deviceId = DB::table('devices')->insertGetId([
                'user_id' => $user->id,
                'uuid' => $deviceData['uuid'],

                'name' => $deviceData['name'] ?? null,
                'brand' => $deviceData['brand'] ?? null,
                'model' => $deviceData['model'] ?? null,

                'device_type' => $deviceData['device_type'] ?? null,
                'operating_system' => $deviceData['operating_system'] ?? null,
                'os_version' => $deviceData['os_version'] ?? null,

                'browser' => $deviceData['browser'] ?? null,
                'browser_version' => $deviceData['browser_version'] ?? null,

                'app_version' => $deviceData['app_version'] ?? null,
                'platform' => $deviceData['platform'] ?? null,

                'last_ip' => $ip,
                'registration_ip' => $ip,

                'last_seen_at' => now(),

                'status' => 'pending',

                'metadata' => isset($deviceData['metadata'])
                    ? json_encode($deviceData['metadata'])
                    : null,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: $deviceId,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'device_pending_approval'
            );

            return response()->json([
                'success' => false,
                'code' => 'DEVICE_PENDING',
                'message' => 'Este dispositivo necesita autorización para acceder.',
                'device' => [
                    'id' => $deviceId,
                    'uuid' => $deviceData['uuid'],
                    'status' => 'pending',
                ],
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar autorización del dispositivo
        |--------------------------------------------------------------------------
        */

        if (in_array($device->status, ['rejected', 'revoked'], true)) {

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: $device->id,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'device_not_authorized'
            );

            return response()->json([
                'success' => false,
                'code' => 'DEVICE_REVOKED',
                'message' => 'Este dispositivo no tiene autorización para acceder.',
            ], 403);
        }

        if ($device->status !== 'approved') {

            $this->recordLoginAttempt(
                userId: $user->id,
                email: $email,
                deviceId: $device->id,
                ip: $ip,
                userAgent: $userAgent,
                successful: false,
                reason: 'device_pending_approval'
            );

            return response()->json([
                'success' => false,
                'code' => 'DEVICE_PENDING',
                'message' => 'Tu dispositivo está pendiente de autorización.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Crear sesión y generar JWT
        |--------------------------------------------------------------------------
        */

        try {
            $result = DB::transaction(function () use (
                $user,
                $device,
                $ip,
                $userAgent,
                $email
            ) {
                /*
                 * Bloqueamos el usuario para serializar todos
                 * sus intentos de inicio de sesión.
                 */
                $lockedUser = DB::table('users')
                    ->where('id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedUser) {
                    return [
                        'blocked' => true,
                        'code' => 'USER_NOT_FOUND',
                        'message' => 'La cuenta no está disponible.',
                        'status' => 403,
                    ];
                }

                /*
                 * Volvemos a comprobar el dispositivo dentro
                 * de la transacción para evitar cambios de estado.
                 */
                $lockedDevice = DB::table('devices')
                    ->where('id', $device->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedDevice || (int) $lockedDevice->user_id !== (int) $user->id) {
                    return [
                        'blocked' => true,
                        'code' => 'DEVICE_NOT_AUTHORIZED',
                        'message' => 'El dispositivo no está autorizado.',
                        'status' => 403,
                    ];
                }

                if ($lockedDevice->status !== 'approved') {
                    return [
                        'blocked' => true,
                        'code' => 'DEVICE_PENDING',
                        'message' => 'Tu dispositivo está pendiente de autorización.',
                        'status' => 403,
                    ];
                }

                /*
                 * Una sola sesión activa por usuario.
                 */
                $activeSession = DB::table('user_sessions')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if ($activeSession) {
                    DB::table('login_attempts')->insert([
                        'user_id' => $user->id,
                        'email' => $email,
                        'device_id' => $device->id,
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'authentication_method' => 'password',
                        'successful' => false,
                        'failure_reason' => 'session_already_active',
                        'attempted_at' => now(),
                    ]);

                    return [
                        'blocked' => true,
                        'code' => 'SESSION_ALREADY_ACTIVE',
                        'message' => 'Ya tienes una sesión abierta. Cierra tu sesión actual antes de intentar ingresar nuevamente.',
                        'status' => 409,
                    ];
                }

                /*
                 * No existe una sesión activa.
                 * Podemos crear una nueva.
                 */
                $sessionUuid = (string) Str::uuid();

                DB::table('devices')
                    ->where('id', $device->id)
                    ->update([
                        'last_ip' => $ip,
                        'last_seen_at' => now(),
                        'updated_at' => now(),
                    ]);

                $sessionId = DB::table('user_sessions')->insertGetId([
                    'user_id' => $user->id,
                    'device_id' => $device->id,
                    'session_uuid' => $sessionUuid,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'started_at' => now(),
                    'last_activity_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $user->update([
                    'last_login_at' => now(),
                ]);

                /*
                 * JWT con identificador de sesión.
                 * Nunca se almacena el token en MySQL.
                 */
                $token = JWTAuth::claims([
                    'sid' => $sessionUuid,
                ])->fromUser($user);

                DB::table('login_attempts')->insert([
                    'user_id' => $user->id,
                    'email' => $email,
                    'device_id' => $device->id,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'authentication_method' => 'password',
                    'successful' => true,
                    'failure_reason' => null,
                    'attempted_at' => now(),
                ]);

                return [
                    'blocked' => false,
                    'token' => $token,
                    'session_id' => $sessionId,
                    'session_uuid' => $sessionUuid,
                ];
            }, 3);

            /*
             * Respuestas de bloqueo.
             */
            if ($result['blocked']) {
                return response()->json([
                    'success' => false,
                    'code' => $result['code'],
                    'message' => $result['message'],
                ], $result['status']);
            }

            /*
             * Respuesta de autenticación exitosa.
             */
            $user->load([
                'company:id,name,slug,timezone',
                'roles:id,name,slug',
                'subroles:id,name,slug,role_id',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso.',
                'data' => [
                    'token' => $result['token'],
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,

                    'session' => [
                        'id' => $result['session_id'],
                        'uuid' => $result['session_uuid'],
                    ],

                    'user' => [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'employee_number' => $user->employee_number,
                        'must_change_password' => $user->must_change_password,
                        'company' => $user->company,
                        'roles' => $user->roles,
                        'subroles' => $user->subroles,
                    ],

                    'device' => [
                        'id' => $device->id,
                        'uuid' => $device->uuid,
                        'status' => 'approved',
                    ],
                ],
            ], 200);

        } catch (JWTException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'No fue posible generar la sesión de autenticación.',
            ], 500);

        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al iniciar sesión.',
            ], 500);
        }
    }

    /**
     * Registrar intentos de autenticación.
     */
    private function recordLoginAttempt(
        ?int $userId,
        ?string $email,
        ?int $deviceId,
        ?string $ip,
        ?string $userAgent,
        bool $successful,
        ?string $reason = null
    ): void {

        DB::table('login_attempts')->insert([
            'user_id' => $userId,
            'email' => $email,
            'device_id' => $deviceId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'authentication_method' => 'password',
            'successful' => $successful,
            'failure_reason' => $reason,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(): JsonResponse
    {
        try {

            $payload = JWTAuth::parseToken()->getPayload();

            $sessionUuid = $payload->get('sid');

            if (!$sessionUuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'La sesión no es válida.',
                ], 401);
            }

            DB::table('user_sessions')
                ->where('session_uuid', $sessionUuid)
                ->where('status', 'active')
                ->update([
                    'status' => 'closed',
                    'ended_at' => now(),
                    'termination_reason' => 'user_logout',
                    'updated_at' => now(),
                ]);

            /*
             * No guardamos ni necesitamos borrar el JWT.
             * La sesión se invalida mediante session_uuid.
             */

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente.',
            ]);

        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'El token es inválido o ha expirado.',
            ], 401);
        }
    }
}