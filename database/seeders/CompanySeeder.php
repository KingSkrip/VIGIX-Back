<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $name = env('VIGIX_COMPANY_NAME', 'VIGIX');

        DB::table('companies')->updateOrInsert(
            ['slug' => env('VIGIX_COMPANY_SLUG', Str::slug($name))],
            [
                'name' => $name,
                'tax_identifier' => env('VIGIX_COMPANY_TAX_ID'),
                'address' => env('VIGIX_COMPANY_ADDRESS'),
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}