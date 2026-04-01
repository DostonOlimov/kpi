<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('work_zones')->insert(['name' => "Markaziy aparat", 'sort_order' => 1]);
        $id = DB::table('work_zones')->where('sort_order',1)->first()?->id;
        DB::table('work_zones')->whereNull('sort_order')->update([
            'parent_id'=>$id
        ]);
        $regions = [
            ['name' => "Andijon viloyati", 'sort_order' => 2],
            ['name' => "Buxoro viloyati", 'sort_order' => 3],
            ['name' => "Farg‘ona viloyati", 'sort_order' => 4],
            ['name' => "Jizzax viloyati", 'sort_order' => 5],
            ['name' => "Namangan viloyati", 'sort_order' => 6],
            ['name' => "Navoiy viloyati", 'sort_order' => 7],
            ['name' => "Qashqadaryo viloyati", 'sort_order' => 8],
            ['name' => "Samarqand viloyati", 'sort_order' => 9],
            ['name' => "Sirdaryo viloyati", 'sort_order' => 10],
            ['name' => "Surxondaryo viloyati", 'sort_order' => 11],
            ['name' => "Toshkent viloyati", 'sort_order' => 12],
            ['name' => "Xorazm viloyati", 'sort_order' => 13],
            ['name' => "Qoraqalpog‘iston Respublikasi", 'sort_order' => 14],
        ];

        DB::table('work_zones')->insert($regions);
    }
}
