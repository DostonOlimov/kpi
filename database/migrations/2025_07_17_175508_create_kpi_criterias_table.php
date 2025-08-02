<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_criterias', function (Blueprint $table) {
            $table->id();
            $table->integer('kpi_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('type')->default(1);
            $table->timestamps();
        });

        Schema::create('kpi_criteria_bands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('fine_ball')->default(0);
            $table->integer('type')->default(1);
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
        Schema::dropIfExists('kpi_criterias');
    }
}
