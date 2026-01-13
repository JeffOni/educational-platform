<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Level;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('description');
            $table->enum('status', [1, 2, 3])->default(1); // 1: Borrador, 2: Revisión, 3: Publicado
            $table->string('slug')->unique();
            $table->string('image_path')->nullable();
            
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Level::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(Category::class)->nullable()->constrained()->onDelete('set null');
            
            $table->decimal('price', 8, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
