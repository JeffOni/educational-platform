<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Section;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('video_type', ['youtube', 'vimeo', 'file', 'spaces'])->default('youtube');
            $table->string('video_url'); // URL de YouTube/Vimeo o path en storage
            $table->integer('duration')->nullable(); // Duración en segundos
            $table->foreignIdFor(Section::class)->constrained()->onDelete('cascade');
            $table->integer('order')->default(1);
            $table->boolean('is_preview')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
