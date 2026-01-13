<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::create(['name' => 'Nivel Básico', 'slug' => Str::slug('Nivel Básico')]);
        Level::create(['name' => 'Nivel Intermedio', 'slug' => Str::slug('Nivel Intermedio')]);
        Level::create(['name' => 'Nivel Avanzado', 'slug' => Str::slug('Nivel Avanzado')]);
    }
}
