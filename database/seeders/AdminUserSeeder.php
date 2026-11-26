<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('VIGIX_ADMIN_EMAIL');
        $password = env('VIGIX_ADMIN_PASSWORD');

        if (!$email || !$password) {
            throw new RuntimeException(
                'Configura VIGIX_ADMIN_EMAIL y VIGIX_ADMIN_PASSWORD en .env'
            );
        }

        $companyId = DB::table('companies')
            ->where('slug', env('VIGIX_COMPANY_SLUG', 'vigix'))
            ->value('id');

        $roleId = DB::table('roles')
            ->where('slug', 'superadmin')
            ->where('guard_name', 'web')
            ->value('id');

        if (!$companyId || !$roleId) {
            throw new RuntimeException(
                'No se encontró la empresa o el rol superadmin.'
            );
        }

        $user = DB::table('users')
            ->where('email', $email)
            ->first();

        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'company_id' => $companyId,
                'employee_number' => 'VIGIX-ADMIN-001',
                'name' => 'Administrador',
                'last_name' => 'Principal',
                'email' => $email,
                'phone' => null,
                'password' => Hash::make($password),
                'is_active' => true,
                'must_change_password' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        DB::table('model_has_roles')->insertOrIgnore([
            'role_id' => $roleId,
            'model_type' => \App\Models\User::class,
            'model_id' => $userId,
        ]);
    }
}