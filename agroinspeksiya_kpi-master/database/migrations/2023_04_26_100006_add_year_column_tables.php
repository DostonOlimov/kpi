<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearColumnTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpi_director', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month');
        });
        Schema::table('kpi_employee', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month');
        });
        Schema::table('bugalter_summa', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month');
        });
        Schema::table('employees_summa', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month');
        });
        Schema::table('employee_days', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month_id');
        });
        Schema::table('months', function (Blueprint $table) {
            $table->integer('year')->default(2023)->after('month_id');
        });
        //drop column
        Schema::table('kpi_director', function (Blueprint $table) {
            $table->dropColumn('has_lock');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
