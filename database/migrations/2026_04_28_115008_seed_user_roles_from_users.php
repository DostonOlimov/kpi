<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedUserRolesFromUsers extends Migration
{
    public function up()
    {
        DB::statement('
            INSERT IGNORE INTO user_roles (user_id, role_id, created_at, updated_at)
            SELECT id, role_id, NOW(), NOW()
            FROM users
            WHERE role_id IS NOT NULL
        ');
    }

    public function down()
    {
        DB::table('user_roles')->truncate();
    }
}
