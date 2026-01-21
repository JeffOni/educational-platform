<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Family;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tecnologia = Family::where('slug', 'tecnologia')->first();
        $negocios = Family::where('slug', 'negocios')->first();
        $diseno = Family::where('slug', 'diseno')->first();

        $categories = [
            ['name' => 'Desarrollo Web', 'family_id' => $tecnologia?->id],
            ['name' => 'Programación', 'family_id' => $tecnologia?->id],
            ['name' => 'Bases de Datos', 'family_id' => $tecnologia?->id],
            ['name' => 'Marketing Digital', 'family_id' => $negocios?->id],
            ['name' => 'Emprendimiento', 'family_id' => $negocios?->id],
            ['name' => 'Diseño Gráfico', 'family_id' => $diseno?->id],
            ['name' => 'UX/UI Design', 'family_id' => $diseno?->id],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'family_id' => $category['family_id'],
                'is_active' => true,
            ]);
        }
    }
}
