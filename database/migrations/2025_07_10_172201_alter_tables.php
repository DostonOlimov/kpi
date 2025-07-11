<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->integer('user_kpi_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('type')->default(1);
            $table->text('feedback')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('scored_by')->nullable();
            $table->text('ai_extracted_text')->nullable();
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
        //
    }
}
