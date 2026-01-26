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
        $ciencias = Family::where('slug', 'ciencias')->first();
        $marketing = Family::where('slug', 'marketing')->first();
        $idiomas = Family::where('slug', 'idiomas')->first();
        $desarrollo = Family::where('slug', 'desarrollo-personal')->first();
        $arte = Family::where('slug', 'arte-y-musica')->first();

        $categories = [
            // Tecnología
            ['name' => 'Desarrollo Web', 'family_id' => $tecnologia?->id],
            ['name' => 'Programación', 'family_id' => $tecnologia?->id],
            ['name' => 'Bases de Datos', 'family_id' => $tecnologia?->id],
            ['name' => 'Inteligencia Artificial', 'family_id' => $tecnologia?->id],
            ['name' => 'Ciberseguridad', 'family_id' => $tecnologia?->id],
            ['name' => 'DevOps', 'family_id' => $tecnologia?->id],

            // Negocios
            ['name' => 'Emprendimiento', 'family_id' => $negocios?->id],
            ['name' => 'Finanzas', 'family_id' => $negocios?->id],
            ['name' => 'Gestión de Proyectos', 'family_id' => $negocios?->id],
            ['name' => 'Recursos Humanos', 'family_id' => $negocios?->id],

            // Diseño
            ['name' => 'Diseño Gráfico', 'family_id' => $diseno?->id],
            ['name' => 'UX/UI Design', 'family_id' => $diseno?->id],
            ['name' => 'Diseño 3D', 'family_id' => $diseno?->id],
            ['name' => 'Animación', 'family_id' => $diseno?->id],

            // Ciencias
            ['name' => 'Matemáticas', 'family_id' => $ciencias?->id],
            ['name' => 'Física', 'family_id' => $ciencias?->id],
            ['name' => 'Química', 'family_id' => $ciencias?->id],

            // Marketing
            ['name' => 'Marketing Digital', 'family_id' => $marketing?->id],
            ['name' => 'Redes Sociales', 'family_id' => $marketing?->id],
            ['name' => 'SEO y SEM', 'family_id' => $marketing?->id],

            // Idiomas
            ['name' => 'Inglés', 'family_id' => $idiomas?->id],
            ['name' => 'Español', 'family_id' => $idiomas?->id],
            ['name' => 'Francés', 'family_id' => $idiomas?->id],

            // Desarrollo Personal
            ['name' => 'Productividad', 'family_id' => $desarrollo?->id],
            ['name' => 'Liderazgo', 'family_id' => $desarrollo?->id],

            // Arte y Música
            ['name' => 'Música', 'family_id' => $arte?->id],
            ['name' => 'Fotografía', 'family_id' => $arte?->id],
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
