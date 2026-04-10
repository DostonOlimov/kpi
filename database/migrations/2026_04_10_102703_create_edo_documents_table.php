<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdoDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edo_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');           // Hujjat raqami (4b/184)
            $table->date('document_date');               // Hujjat sanasi (25 mar 2026)
            $table->string('document_type');             // Hujjat turi (Kiruvchi hujjat)
            $table->date('due_date');                    // Bajarish muddati (26 mar 2026)
            $table->string('sender')->nullable();        // Yuboruvchi
            $table->date('task_created_at');             // Topshiriq yaratilgan sana
            $table->text('summary')->nullable();         // Qisqacha mazmuni
            $table->string('status')->default('pending'); // Holat: pending, in_progress, vaqtida_bajarilgan, muddati_o_tib_bajarilgan
            $table->timestamp('completed_at')->nullable(); // Bajarilgan vaqt
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('document_number');
            $table->index('status');
            $table->index('due_date');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edo_documents');
    }
}
