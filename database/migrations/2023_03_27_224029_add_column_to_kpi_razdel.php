<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddColumnToKpiRazdel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('kpi_razdel', function (Blueprint $table) {
////            $table->integer('parent_id')->default(0);
//        });
//        DB::table('kpi_razdel')->insert(
//            array(
//                'name' => 'Бевосита йуналишга таълуқли топшириқлар,хатлар ва бошқа ҳужжатларнинг ўз вақтида бажарилганлик даражаси',
//                'max_ball' => 10,
//                'weight' => 10,
//                'parent_id' => 2,
//            )
//        );
//        DB::table('kpi_razdel')->insert(
//            array(
//                'name' => 'Келиб тушган мурожаатларнинг ўз вақтида кўриб чиқилганлиги даражаси**',
//                'max_ball' => 10,
//                'weight' => 10,
//                'parent_id' => 2,
//            )
//        );
//        DB::table('kpi_razdel')->insert(
//            array(
//                'name' => 'Иш режими талабларига риоя этганлиги (тўлиқ риоя этганлик)',
//                'max_ball' => 5,
//                'weight' => 5,
//                'parent_id' => 3,
//            )
//        );
//        DB::table('kpi_razdel')->insert(
//            array(
//                'name' => 'Одоб-ахлоқ қоидалари талабларига тўлиқ риоя этганлиги',
//                'max_ball' => 5,
//                'weight' => 5,
//                'parent_id' => 3,
//            )
//        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_razdel', function (Blueprint $table) {
            //
        });
    }
}
