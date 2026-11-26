<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = DB::table('companies')
            ->where('slug', env('VIGIX_COMPANY_SLUG', 'vigix'))
            ->value('id');

        if (!$companyId) {
            throw new \RuntimeException(
                'No existe la empresa principal de VIGIX.'
            );
        }

        $shifts = [
            [
                'name' => 'Matutino',
                'code' => 'MAT',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'crosses_midnight' => false,
            ],
            [
                'name' => 'Vespertino',
                'code' => 'VES',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'crosses_midnight' => false,
            ],
            [
                'name' => 'Nocturno',
                'code' => 'NOC',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'crosses_midnight' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            DB::table('shifts')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'code' => $shift['code'],
                ],
                [
                    'name' => $shift['name'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'crosses_midnight' => $shift['crosses_midnight'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}