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
        Schema::create('discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('assignment_discussions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Un usuario solo puede dar like una vez por comentario
            $table->unique(['discussion_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_likes');
    }
};
