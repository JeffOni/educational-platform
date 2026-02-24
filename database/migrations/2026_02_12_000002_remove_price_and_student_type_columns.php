<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'price')) {
                $table->dropColumn('price');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_type')) {
                $table->dropColumn('student_type');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('enrollments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            $table->foreignId('enrollment_code_id')->nullable()->after('course_id')->constrained('enrollment_codes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0)->after('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('student_type')->nullable()->after('email');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0);
            $table->string('payment_method')->default('free');
            $table->dropForeign(['enrollment_code_id']);
            $table->dropColumn('enrollment_code_id');
        });
    }
};
