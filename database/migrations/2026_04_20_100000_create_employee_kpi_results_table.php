<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeKpiResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_kpi_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_score', 8, 2)->default(0)->comment('Overall KPI score');
            $table->decimal('final_score', 8, 2)->default(0)->comment('Final calculated score after adjustments');
            $table->string('grade')->nullable()->comment('Performance grade/rating');
            $table->string('status')->default('pending')->comment('Status: pending, calculated, approved, rejected');
            $table->text('comments')->nullable()->comment('Additional comments or notes');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who evaluated/confirmed the result');
            $table->timestamp('evaluated_at')->nullable()->comment('When the evaluation was confirmed');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate results for same user/period
            $table->unique(['user_id', 'year', 'month']);
            
            // Indexes for better query performance
            $table->index(['year', 'month']);
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_kpi_results');
    }
}
