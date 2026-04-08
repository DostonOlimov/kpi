<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('external_id'); // Ident. ustuni uchun
            $table->string('name');
            $table->string('department')->nullable();
            $table->date('date');
            $table->time('first_in')->nullable();
            $table->time('last_out')->nullable();
            $table->string('status')->nullable(); // "меҳнат таътилиda" kabi izohlar uchun
            $table->string('comment')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}
