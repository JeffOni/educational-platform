<?php

namespace Database\Seeders;

use App\Models\Family;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $families = [
            'Tecnología',
            'Negocios',
            'Diseño',
            'Ciencias',
        ];

        foreach ($families as $family) {
            Family::create([
                'name' => $family,
                'slug' => Str::slug($family),
                'is_active' => true,
            ]);
        }
    }
}
