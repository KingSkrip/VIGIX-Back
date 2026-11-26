<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Superadministrador',
                'slug' => 'superadmin',
                'description' => 'Control total de VIGIX.',
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Administración de la empresa.',
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisión de guardias y recorridos.',
            ],
            [
                'name' => 'Guardia',
                'slug' => 'guard',
                'description' => 'Ejecución de rondines y registro de incidencias.',
            ],
        ];

        $roleIds = [];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                [
                    'slug' => $role['slug'],
                    'guard_name' => 'web',
                ],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $roleIds[$role['slug']] = DB::table('roles')
                ->where('slug', $role['slug'])
                ->where('guard_name', 'web')
                ->value('id');
        }

        $modules = [
            'dashboard' => [
                'view' => 'Ver dashboard',
            ],

            'companies' => [
                'view' => 'Ver empresas',
                'create' => 'Crear empresas',
                'edit' => 'Editar empresas',
                'delete' => 'Eliminar empresas',
            ],

            'users' => [
                'view' => 'Ver usuarios',
                'create' => 'Crear usuarios',
                'edit' => 'Editar usuarios',
                'delete' => 'Eliminar usuarios',
                'manage_roles' => 'Administrar roles de usuarios',
            ],

            'roles' => [
                'view' => 'Ver roles',
                'create' => 'Crear roles',
                'edit' => 'Editar roles',
                'delete' => 'Eliminar roles',
            ],

            'permissions' => [
                'view' => 'Ver permisos',
                'manage' => 'Administrar permisos',
            ],

            'wings' => [
                'view' => 'Ver alas',
                'create' => 'Crear alas',
                'edit' => 'Editar alas',
                'delete' => 'Eliminar alas',
            ],

            'qr_points' => [
                'view' => 'Ver puntos QR',
                'create' => 'Crear puntos QR',
                'edit' => 'Editar puntos QR',
                'delete' => 'Eliminar puntos QR',
                'regenerate' => 'Regenerar códigos QR',
            ],

            'devices' => [
                'view' => 'Ver dispositivos',
                'approve' => 'Aprobar dispositivos',
                'revoke' => 'Revocar dispositivos',
            ],

            'sessions' => [
                'view' => 'Ver sesiones',
                'revoke' => 'Cerrar sesiones',
            ],

            'shifts' => [
                'view' => 'Ver turnos',
                'create' => 'Crear turnos',
                'edit' => 'Editar turnos',
                'delete' => 'Eliminar turnos',
            ],

            'assignments' => [
                'view' => 'Ver asignaciones',
                'create' => 'Crear asignaciones',
                'edit' => 'Editar asignaciones',
                'delete' => 'Eliminar asignaciones',
            ],

            'patrol_policies' => [
                'view' => 'Ver políticas de rondines',
                'create' => 'Crear políticas',
                'edit' => 'Editar políticas',
                'delete' => 'Eliminar políticas',
            ],

            'patrol_schedules' => [
                'view' => 'Ver programaciones',
                'generate' => 'Generar programaciones',
                'edit' => 'Editar programaciones',
                'cancel' => 'Cancelar programaciones',
            ],

            'patrol_runs' => [
                'view' => 'Ver recorridos',
                'execute' => 'Ejecutar recorridos',
                'assign' => 'Asignar recorridos',
                'cancel' => 'Cancelar recorridos',
            ],

            'patrol_scans' => [
                'view' => 'Ver escaneos',
                'scan' => 'Registrar escaneos QR',
                'validate' => 'Validar escaneos',
            ],

            'incidents' => [
                'view' => 'Ver incidencias',
                'create' => 'Reportar incidencias',
                'resolve' => 'Resolver incidencias',
            ],

            'exceptions' => [
                'view' => 'Ver excepciones',
                'create' => 'Solicitar excepciones',
                'approve' => 'Aprobar excepciones',
                'reject' => 'Rechazar excepciones',
            ],

            'reports' => [
                'view' => 'Ver reportes',
                'export' => 'Exportar reportes',
                'deliver' => 'Enviar reportes',
            ],

            'audit' => [
                'view' => 'Consultar auditoría',
            ],
        ];

        $permissionIds = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action => $name) {
                $slug = "{$module}.{$action}";

                DB::table('permissions')->updateOrInsert(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'module' => $module,
                        'description' => null,
                        'guard_name' => 'web',
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $permissionIds[$slug] = DB::table('permissions')
                    ->where('slug', $slug)
                    ->value('id');
            }
        }

        // Superadministrador: todos los permisos.
        $allPermissionIds = array_values($permissionIds);

        // Administrador: administración operativa de su empresa.
        $adminPermissions = array_filter(
            $permissionIds,
            fn ($slug) => !in_array($slug, [
                'companies.delete',
                'permissions.manage',
            ], true)
        );

        // Supervisor: supervisión y consulta operativa.
        $supervisorModules = [
            'dashboard',
            'users',
            'wings',
            'qr_points',
            'devices',
            'sessions',
            'shifts',
            'assignments',
            'patrol_schedules',
            'patrol_runs',
            'patrol_scans',
            'incidents',
            'exceptions',
            'reports',
        ];

        $supervisorPermissions = array_filter(
            $permissionIds,
            function ($slug) use ($supervisorModules) {
                $module = explode('.', $slug)[0];

                return in_array($module, $supervisorModules, true);
            }
        );

        // Guardia: únicamente operación de sus recorridos.
        $guardPermissions = [
            'dashboard.view',
            'patrol_runs.view',
            'patrol_runs.execute',
            'patrol_scans.scan',
            'incidents.view',
            'incidents.create',
            'exceptions.view',
            'exceptions.create',
        ];

        $rolePermissions = [
            'superadmin' => $allPermissionIds,
            'admin' => array_values($adminPermissions),
            'supervisor' => array_values($supervisorPermissions),
            'guard' => array_values(
                array_intersect_key(
                    $permissionIds,
                    array_flip($guardPermissions)
                )
            ),
        ];

        foreach ($rolePermissions as $roleSlug => $permissions) {
            $roleId = $roleIds[$roleSlug];

            foreach ($permissions as $permissionId) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        // Subroles iniciales.
        $subroles = [
            'supervisor' => [
                [
                    'name' => 'Supervisor operativo',
                    'slug' => 'supervisor-operativo',
                    'description' => 'Supervisión de operaciones y recorridos.',
                ],
                [
                    'name' => 'Supervisor general',
                    'slug' => 'supervisor-general',
                    'description' => 'Supervisión general de la empresa.',
                ],
            ],

            'guard' => [
                [
                    'name' => 'Guardia rondinero',
                    'slug' => 'guardia-rondinero',
                    'description' => 'Responsable de ejecutar rondines.',
                ],
                [
                    'name' => 'Guardia fijo',
                    'slug' => 'guardia-fijo',
                    'description' => 'Responsable de vigilancia fija.',
                ],
            ],
        ];

        foreach ($subroles as $roleSlug => $items) {
            foreach ($items as $subrole) {
                DB::table('subroles')->updateOrInsert(
                    [
                        'role_id' => $roleIds[$roleSlug],
                        'slug' => $subrole['slug'],
                    ],
                    [
                        'name' => $subrole['name'],
                        'description' => $subrole['description'],
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}