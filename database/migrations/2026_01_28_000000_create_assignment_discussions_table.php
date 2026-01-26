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
        Schema::create('assignment_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('lesson_assignments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('assignment_discussions')->onDelete('cascade')
                ->comment('Para respuestas anidadas/hilos');
            $table->text('content');
            $table->boolean('is_solution')->default(false)
                ->comment('Marcado por profesor como solución/respuesta correcta');
            $table->integer('likes_count')->default(0);
            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index(['assignment_id', 'parent_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_discussions');
    }
};
