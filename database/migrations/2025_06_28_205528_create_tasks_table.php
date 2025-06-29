<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('type')->default(1); // 1 for task, 2 for category
            $table->integer('user_id');
            $table->integer('month');
            $table->integer('year')->default(date('Y'));
            $table->boolean('is_completed')->default(false);
            $table->text('file_path')->nullable(); // for task attachments
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
        Schema::dropIfExists('tasks');
    }
}
