<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    /**
     * Obtener categorías por familia
     */
    public function getCategoriesByFamily(Request $request)
    {
        $familyId = $request->query('family_id');

        $categories = Category::where('is_active', true)
            ->when($familyId, function ($query) use ($familyId) {
                return $query->where('family_id', $familyId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'family_id']);

        return response()->json($categories);
    }

    /**
     * Obtener subcategorías por categoría
     */
    public function getSubcategoriesByCategory(Request $request)
    {
        $categoryId = $request->query('category_id');

        $subcategories = Subcategory::where('is_active', true)
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'category_id']);

        return response()->json($subcategories);
    }
}
