<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(LevelSeeder::class);
        $this->call(FamilySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubcategorySeeder::class);

        // Admin
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        // Profesor
        User::factory()->create([
            'name' => 'Profesor',
            'email' => 'teacher@example.com',
            'password' => bcrypt('12345678')
        ])->assignRole('teacher');

        // Estudiantes Externos (Solo Cursos) - 2 usuarios
        User::factory()->create([
            'name' => 'Carlos Martínez',
            'email' => 'student@example.com',
            'password' => bcrypt('12345678'),
            'student_type' => 'external'
        ])->assignRole('student');

        User::factory()->create([
            'name' => 'María González',
            'email' => 'maria.gonzalez@example.com',
            'password' => bcrypt('12345678'),
            'student_type' => 'external'
        ])->assignRole('student');

        // Estudiantes de Academia (Cursos + Tareas) - 2 usuarios
        User::factory()->create([
            'name' => 'Roberto Sánchez',
            'email' => 'roberto.sanchez@academia.edu',
            'password' => bcrypt('12345678'),
            'student_type' => 'internal'
        ])->assignRole('student');

        User::factory()->create([
            'name' => 'Laura Fernández',
            'email' => 'laura.fernandez@academia.edu',
            'password' => bcrypt('12345678'),
            'student_type' => 'internal'
        ])->assignRole('student');

        // Crear cursos con contenido
        $this->call(CourseSeeder::class);

        // Crear delegaciones de ejemplo
        $this->call(CourseDelegationSeeder::class);

        // Crear tareas de ejemplo
        $this->call(AssignmentSeeder::class);
    }
}
