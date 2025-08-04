<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortColumnKpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('kpis', function (Blueprint $table) {
            $table->string('sort')->nullable();
        });
    }

    /**
     * Reverse the migrations.1
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
