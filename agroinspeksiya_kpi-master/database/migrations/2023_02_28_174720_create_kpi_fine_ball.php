<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiFineBall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_fine_ball', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('kpi_id');
            $table->integer('count');
            $table->float('fine_ball');
            $table->string('order_file')->nullable();
            $table->integer('month_num');
            $table->integer('year');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_fine_ball');
    }
}
