<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewRolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            "Kadrlar bo'limi",
            "Ijro bo'limi",
        ];

        foreach ($roles as $name) {
            DB::table('roles')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
