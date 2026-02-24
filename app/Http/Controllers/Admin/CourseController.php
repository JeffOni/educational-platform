<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Category;
use App\Models\Family;
use App\Models\Subcategory;
use App\Models\Level;
use App\Models\User;

class CourseController extends Controller
{
    public function index()
    {
        // Si es administrador, mostrar todos los cursos
        // Si es profesor, mostrar solo sus cursos
        $query = Course::with(['subcategory.category', 'level', 'user'])
            ->withCount('sections');

        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        $courses = $query->latest()
            ->get()
            ->map(function ($course) {
                // Asegurar que status sea integer
                $course->status = (int) $course->status;
                // Agregar category desde subcategory para compatibilidad con la vista
                $course->category = $course->subcategory?->category;
                return $course;
            });

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Courses/Create', [
            'families' => Family::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::with('family')->where('is_active', true)->orderBy('name')->get(),
            'subcategories' => Subcategory::with('category')->where('is_active', true)->orderBy('name')->get(),
            'levels' => Level::orderBy('name')->get(),
            'teachers' => User::role('teacher')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'family_id' => 'nullable|exists:families,id',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'level_id' => 'required|exists:levels,id',
            'user_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Si es admin y especifica un profesor, usar ese; si no, usar el autenticado
        $userId = auth()->user()->hasRole('admin') && $request->filled('user_id')
            ? $request->user_id
            : auth()->id();

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'family_id' => $request->family_id,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'level_id' => $request->level_id,
            'slug' => Str::slug($request->title),
            'user_id' => $userId,
            'status' => Course::BORRADOR,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Curso creado correctamente');
    }

    public function edit(Course $course)
    {
        // Permitir edición si es admin o si es el dueño del curso o tiene delegación activa
        if (!$course->userCanEdit(auth()->user())) {
            abort(403, 'No tienes permiso para editar este curso');
        }

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course->load('sections.lessons.resources', 'sections.lessons.assignments'),
            'families' => Family::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::with('family')->where('is_active', true)->orderBy('name')->get(),
            'subcategories' => Subcategory::with('category')->where('is_active', true)->orderBy('name')->get(),
            'levels' => Level::all(),
            'teachers' => User::role('teacher')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        // Permitir actualización si es admin o si es el dueño del curso o tiene delegación activa
        if (!$course->userCanEdit(auth()->user())) {
            abort(403, 'No tienes permiso para actualizar este curso');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'family_id' => 'nullable|exists:families,id',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'level_id' => 'required|exists:levels,id',
            'status' => 'required|integer|in:1,2,3',
            'user_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'subtitle', 'description', 'family_id', 'category_id', 'subcategory_id', 'level_id', 'status']);

        // Solo admin puede cambiar el profesor titular
        if (auth()->user()->hasRole('admin') && $request->filled('user_id')) {
            $data['user_id'] = $request->user_id;
        }

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::delete($course->image_path);
            }
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        $data['slug'] = Str::slug($request->title);

        $course->update($data);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Curso actualizado correctamente');
    }

    public function publish(Course $course)
    {
        // Permitir publicación si es admin o si es el dueño del curso
        // La delegación NO permite publicar, solo el titular
        if (!auth()->user()->hasRole('admin') && $course->user_id !== auth()->id()) {
            abort(403, 'Solo el profesor titular puede publicar el curso');
        }

        $course->update(['status' => Course::PUBLICADO]);

        return redirect()->back()
            ->with('success', 'Curso publicado correctamente');
    }

    public function destroy(Course $course)
    {
        // Permitir eliminación si es admin o si es el dueño del curso
        if (!auth()->user()->hasRole('admin') && $course->user_id !== auth()->id()) {
            abort(403);
        }

        // Eliminar imagen si existe
        if ($course->image_path) {
            Storage::delete($course->image_path);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Curso eliminado correctamente');
    }
}
