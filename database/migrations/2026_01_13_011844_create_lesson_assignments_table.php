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
        Schema::create('lesson_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->timestamp('due_date')->nullable();
            $table->integer('max_points')->default(100);

            // Nuevos campos para tipos de entrega
            $table->enum('submission_type', ['file', 'text', 'link', 'file_and_text', 'forum'])
                ->default('file')
                ->comment('Tipo de entrega requerida');
            $table->json('allowed_file_types')->nullable()
                ->comment('Tipos de archivo permitidos: ["pdf","docx","xlsx"]');
            $table->integer('max_file_size')->default(10)
                ->comment('Tamaño máximo de archivo en MB');
            $table->integer('max_files')->default(1)
                ->comment('Cantidad máxima de archivos');
            $table->boolean('requires_text')->default(false)
                ->comment('Si requiere descripción adicional');
            $table->boolean('enable_comments')->default(false)
                ->comment('Si permite comentarios/foro');
            $table->json('config')->nullable()
                ->comment('Configuración adicional JSON');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_assignments');
    }
};
