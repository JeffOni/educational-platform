<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Desarrollo Web', 'slug' => Str::slug('Desarrollo Web')]);
        Category::create(['name' => 'Diseño Web', 'slug' => Str::slug('Diseño Web')]);
        Category::create(['name' => 'Programación', 'slug' => Str::slug('Programación')]);
        Category::create(['name' => 'Algoritmos', 'slug' => Str::slug('Algoritmos')]);
        Category::create(['name' => 'Bases de Datos', 'slug' => Str::slug('Bases de Datos')]);
    }
}
