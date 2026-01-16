<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Category;
use App\Models\Level;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('status', Course::PUBLICADO)
            ->with(['teacher', 'category', 'level']);

        // Filtrar por categoría
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filtrar por nivel
        if ($request->has('level') && $request->level) {
            $query->where('level_id', $request->level);
        }

        // Buscar por título
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Ordenar
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('enrollments')->orderByDesc('enrollments_count');
                break;
            default:
                $query->latest();
        }

        $courses = $query->paginate(12);

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'categories' => Category::all(),
            'levels' => Level::all(),
            'filters' => $request->only(['category', 'level', 'search', 'sort']),
        ]);
    }

    public function show(Course $course)
    {
        // Solo permitir ver cursos publicados
        if ($course->status !== Course::PUBLICADO) {
            abort(404);
        }

        $hasPurchased = false;

        if (auth()->check()) {
            $hasPurchased = auth()->user()->purchases()
                ->where('course_id', $course->id)
                ->exists();
        }

        return Inertia::render('Courses/Show', [
            'course' => $course->load('teacher', 'category', 'level', 'sections.lessons', 'reviews'),
            'hasPurchased' => $hasPurchased,
        ]);
    }
}
