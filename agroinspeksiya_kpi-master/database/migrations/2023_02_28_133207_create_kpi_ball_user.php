<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiBallUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_ball_user', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('kpi_id');
            $table->integer('works_count');
            $table->float('current_ball');
            $table->string('order_file')->nullable();
            $table->integer('month_num');
            $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_ball_user');
    }
}
