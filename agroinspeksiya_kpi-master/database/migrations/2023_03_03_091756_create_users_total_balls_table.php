<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTotalBallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_total_balls', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->float('personal_ball');
            $table->float('fine_ball');
            $table->float('current_ball');
            $table->float('max_ball');
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_total_balls');
    }
}
