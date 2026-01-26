<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('lesson_assignments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();

            // Nuevos campos para múltiples tipos de entrega
            $table->text('submission_text')->nullable()
                ->comment('Respuesta de texto del estudiante');
            $table->string('submission_link')->nullable()
                ->comment('Enlace externo (GitHub, YouTube, etc.)');
            $table->json('submission_files')->nullable()
                ->comment('Array de archivos múltiples');
            $table->boolean('is_draft')->default(false)
                ->comment('Si es borrador (no entregado aún)');

            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
