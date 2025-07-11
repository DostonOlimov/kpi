<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kpi_scores');
        Schema::create('task_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('feedback')->nullable();
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
        Schema::dropIfExists('task_scores');
    }
}
