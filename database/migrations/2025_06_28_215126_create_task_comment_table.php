<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskCommentTable extends Migration
{
    public function up()
    {
        // Add scoring fields to KPIs table
        Schema::create('kpi_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('kpi_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->integer('month');
            $table->integer('type')->default(1);
            $table->integer('year')->default(date('Y'));
            $table->text('feedback')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('scored_by')->nullable();
            $table->timestamp('scored_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('scored_by')->references('id')->on('users');

            $table->timestamps();
        });

        // Create task comments table
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_scores');
        Schema::dropIfExists('task_comments');
    }
}
