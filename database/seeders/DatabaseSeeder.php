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

        // Alumno
        User::factory()->create([
            'name' => 'Alumno',
            'email' => 'student@example.com',
            'password' => bcrypt('12345678')
        ])->assignRole('student');
    }
}
