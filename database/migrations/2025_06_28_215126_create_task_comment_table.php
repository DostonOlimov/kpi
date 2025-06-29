<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskCommentTable extends Migration
{
    public function up()
    {
        // Add scoring fields to KPIs table
        Schema::table('kpis', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('scored_by')->nullable();
            $table->timestamp('scored_at')->nullable();

            $table->foreign('scored_by')->references('id')->on('users');
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
        Schema::table('kpis', function (Blueprint $table) {
            $table->dropForeign(['scored_by']);
            $table->dropColumn(['score', 'feedback', 'scored_by', 'scored_at']);
        });

        Schema::dropIfExists('task_comments');
    }
}
