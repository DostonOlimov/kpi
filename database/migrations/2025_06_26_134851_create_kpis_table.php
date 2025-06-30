<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('kpis', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('parent_id')->nullable()->constrained('kpis')->onDelete('cascade');
//            $table->string('name');
//            $table->integer('max_score')->nullable(); // null for categories
//            $table->timestamps();
//        });
//
//        // Seed hierarchical KPI data
//        $categories = [
//            [
//                'name' => 'Марказ ходимининг таркибий бўлинмага юклатилган энг муҳим самарадорлик кўрсаткичларига риоя қилганлиги ва вазифа ҳамда функцияларни бажарилишдаги ҳиссаси',
//                'children' => [
//                    ['name' => 'Таркибий бўлинмага юклатилган энг муҳим самарадорлик кўрсаткичларига эришилганлиги', 'max_score' => 30],
//                    ['name' => 'Таркибий бўлинмага юклатилган вазифа ва функцияларни бажарганлиги', 'max_score' => 30],
//                ]
//            ],
//            [
//                'name' => 'Ижро интизомига риоя қилинганлиги',
//                'children' => [
//                    ['name' => 'Топшириқларни сони, сифати ва ўз муддатида бажарилганлиги', 'max_score' => 10],
//                    ['name' => 'Фуқаролар мурожаатларига ўз вақтида муносабат билдирилганлиги', 'max_score' => 10],
//                ]
//            ],
//            [
//                'name' => 'Меҳнат интизомига риоя қилинганлиги',
//                'children' => [
//                    ['name' => 'Иш режими талабларига риоя этганлиги', 'max_score' => 7],
//                    ['name' => 'Одоб-ахлоқ қоидалари талабларига риоя этганлиги', 'max_score' => 3],
//                ]
//            ],
//            [
//                'name' => 'Ташаббускорлиги',
//                'children' => [
//                    ['name' => 'Таклифлар ишлаб чиққанлиги, амалиятга жорий этганлиги ва ўз ташаббуси билан қўшимча вазифаларни бажарганлиги', 'max_score' => 10],
//                ]
//            ],
//        ];
//
//        foreach ($categories as $category) {
//            $parentId = DB::table('kpis')->insertGetId([
//                'name' => $category['name'],
//                'max_score' => null,
//                'parent_id' => null,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//
//            foreach ($category['children'] as $item) {
//                DB::table('kpis')->insert([
//                    'name' => $item['name'],
//                    'max_score' => $item['max_score'],
//                    'parent_id' => $parentId,
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ]);
//            }
//        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpis');
    }
}
