<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FamilyController extends Controller
{
    public function index()
    {
        $families = Family::withCount('categories')->latest()->get();

        return Inertia::render('Admin/Families/Index', [
            'families' => $families,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Families/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:families,name',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Family::create($validated);

        return redirect()->route('admin.families.index')
            ->with('success', 'Familia creada exitosamente.');
    }

    public function edit(Family $family)
    {
        return Inertia::render('Admin/Families/Edit', [
            'family' => $family,
        ]);
    }

    public function update(Request $request, Family $family)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:families,name,' . $family->id,
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $family->update($validated);

        return redirect()->route('admin.families.index')
            ->with('success', 'Familia actualizada exitosamente.');
    }

    public function destroy(Family $family)
    {
        $family->delete();

        return redirect()->route('admin.families.index')
            ->with('success', 'Familia eliminada exitosamente.');
    }
}
