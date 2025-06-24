<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesSummaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees_summa', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->float('rating')->nullable();
            $table->double('summa')->nullable();
            $table->string('month')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->integer('current_ball');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_summa');
    }
}
