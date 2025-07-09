<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgainUsersTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_kpis');
        Schema::create('user_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->float('target_score')->default(100);
            $table->float('current_score');
            $table->integer('year')->default(date('Y')); // Add year column with default value of current year
            $table->integer('month')->default(date('m')); // Add month column with default
            $table->text('ai_extracted_text')->nullable();
            $table->unsignedTinyInteger('ai_score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_kpis');
    }
}
