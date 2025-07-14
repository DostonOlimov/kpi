<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoreTaskScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('month');
            $table->dropColumn('year');
            $table->dropColumn('is_checked');
            $table->dropColumn('is_completed');

            $table->text('extracted_text')->nullable()->after('file_path');
            $table->decimal('score')->nullable()->after('type');
            $table->integer('task_score_id')->nullable()->after('type');

            $table->softDeletes();
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('ai_extracted_text');
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
