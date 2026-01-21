<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category.family')
            ->withCount('courses')
            ->latest()
            ->get();

        return Inertia::render('Admin/Subcategories/Index', [
            'subcategories' => $subcategories,
        ]);
    }

    public function create()
    {
        $categories = Category::with('family')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Admin/Subcategories/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subcategories,name',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Subcategory::create($validated);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoría creada exitosamente.');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::with('family')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Admin/Subcategories/Edit', [
            'subcategory' => $subcategory->load('category.family'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subcategories,name,' . $subcategory->id,
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $subcategory->update($validated);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoría actualizada exitosamente.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoría eliminada exitosamente.');
    }
}
