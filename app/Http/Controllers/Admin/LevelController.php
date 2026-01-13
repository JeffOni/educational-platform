<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::withCount('courses')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Admin/Levels/Index', [
            'levels' => $levels
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Levels/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:levels,name',
        ]);

        Level::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.levels.index')
            ->with('success', 'Nivel creado correctamente');
    }

    public function edit(Level $level)
    {
        return Inertia::render('Admin/Levels/Edit', [
            'level' => $level
        ]);
    }

    public function update(Request $request, Level $level)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:levels,name,' . $level->id,
        ]);

        $level->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.levels.index')
            ->with('success', 'Nivel actualizado correctamente');
    }

    public function destroy(Level $level)
    {
        // Verificar si tiene cursos asociados
        if ($level->courses()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el nivel porque tiene cursos asociados');
        }

        $level->delete();

        return redirect()->route('admin.levels.index')
            ->with('success', 'Nivel eliminado correctamente');
    }
}
