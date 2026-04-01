<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdWorkZones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_zones', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
            $table->integer('sort_order')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('pinfl')->nullable();
            $table->integer('ch_id')->nullable();
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
