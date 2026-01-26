<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $desarrolloWeb = Category::where('slug', 'desarrollo-web')->first();
        $programacion = Category::where('slug', 'programacion')->first();
        $basesDatos = Category::where('slug', 'bases-de-datos')->first();
        $ia = Category::where('slug', 'inteligencia-artificial')->first();
        $disenoGrafico = Category::where('slug', 'diseno-grafico')->first();
        $uxui = Category::where('slug', 'ux-ui-design')->first();
        $marketingDigital = Category::where('slug', 'marketing-digital')->first();
        $emprendimiento = Category::where('slug', 'emprendimiento')->first();
        $devops = Category::where('slug', 'devops')->first();
        $diseno3d = Category::where('slug', 'diseno-3d')->first();
        $fotografia = Category::where('slug', 'fotografia')->first();
        $productividad = Category::where('slug', 'productividad')->first();

        $subcategories = [
            // Desarrollo Web
            ['name' => 'Frontend', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Backend', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Full Stack', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'React', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Vue.js', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Angular', 'category_id' => $desarrolloWeb?->id],

            // Programación
            ['name' => 'Python', 'category_id' => $programacion?->id],
            ['name' => 'JavaScript', 'category_id' => $programacion?->id],
            ['name' => 'Java', 'category_id' => $programacion?->id],
            ['name' => 'C#', 'category_id' => $programacion?->id],
            ['name' => 'PHP', 'category_id' => $programacion?->id],
            ['name' => 'Go', 'category_id' => $programacion?->id],
            ['name' => 'Rust', 'category_id' => $programacion?->id],

            // Bases de Datos
            ['name' => 'MySQL', 'category_id' => $basesDatos?->id],
            ['name' => 'PostgreSQL', 'category_id' => $basesDatos?->id],
            ['name' => 'MongoDB', 'category_id' => $basesDatos?->id],
            ['name' => 'Redis', 'category_id' => $basesDatos?->id],

            // Inteligencia Artificial
            ['name' => 'Machine Learning', 'category_id' => $ia?->id],
            ['name' => 'Deep Learning', 'category_id' => $ia?->id],
            ['name' => 'Procesamiento de Lenguaje Natural', 'category_id' => $ia?->id],

            // Diseño Gráfico
            ['name' => 'Branding', 'category_id' => $disenoGrafico?->id],
            ['name' => 'Ilustración', 'category_id' => $disenoGrafico?->id],
            ['name' => 'Tipografía', 'category_id' => $disenoGrafico?->id],

            // UX/UI
            ['name' => 'Diseño de Interfaces', 'category_id' => $uxui?->id],
            ['name' => 'Prototipado', 'category_id' => $uxui?->id],
            ['name' => 'Investigación de Usuarios', 'category_id' => $uxui?->id],

            // Marketing Digital
            ['name' => 'Email Marketing', 'category_id' => $marketingDigital?->id],
            ['name' => 'Content Marketing', 'category_id' => $marketingDigital?->id],
            ['name' => 'Marketing de Influencers', 'category_id' => $marketingDigital?->id],

            // Emprendimiento
            ['name' => 'Startups', 'category_id' => $emprendimiento?->id],
            ['name' => 'Modelo de Negocio', 'category_id' => $emprendimiento?->id],
            ['name' => 'Pitch e Inversión', 'category_id' => $emprendimiento?->id],

            // DevOps
            ['name' => 'DevOps', 'category_id' => $devops?->id],

            // Diseño 3D
            ['name' => 'Diseño 3D', 'category_id' => $diseno3d?->id],

            // Fotografía
            ['name' => 'Fotografía', 'category_id' => $fotografia?->id],

            // Productividad
            ['name' => 'Productividad', 'category_id' => $productividad?->id],
        ];

        foreach ($subcategories as $subcategory) {
            if ($subcategory['category_id']) {
                Subcategory::create([
                    'name' => $subcategory['name'],
                    'slug' => Str::slug($subcategory['name']),
                    'category_id' => $subcategory['category_id'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
