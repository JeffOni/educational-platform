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
        Schema::create('course_delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('delegated_by')->constrained('users')->comment('Profesor titular que delega');
            $table->foreignId('delegated_to')->constrained('users')->comment('Profesor reemplazo temporal');
            $table->json('permissions')->comment('Permisos asignados: grade_assignments, answer_questions, etc.');
            $table->timestamp('starts_at')->nullable()->comment('Inicio de la delegación');
            $table->timestamp('expires_at')->nullable()->comment('Fin de la delegación');
            $table->string('reason')->nullable()->comment('Razón de la delegación');
            $table->boolean('is_active')->default(true)->comment('Si la delegación está activa');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['course_id', 'delegated_to', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_delegations');
    }
};
