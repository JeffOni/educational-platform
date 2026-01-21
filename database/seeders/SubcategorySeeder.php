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
        $diseno = Category::where('slug', 'diseno-grafico')->first();

        $subcategories = [
            ['name' => 'Frontend', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Backend', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Full Stack', 'category_id' => $desarrolloWeb?->id],
            ['name' => 'Python', 'category_id' => $programacion?->id],
            ['name' => 'JavaScript', 'category_id' => $programacion?->id],
            ['name' => 'Java', 'category_id' => $programacion?->id],
            ['name' => 'Branding', 'category_id' => $diseno?->id],
            ['name' => 'Ilustración', 'category_id' => $diseno?->id],
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
