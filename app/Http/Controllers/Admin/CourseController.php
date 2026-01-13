<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Category;
use App\Models\Level;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('user_id', auth()->id())
            ->with(['category', 'level'])
            ->withCount('sections')
            ->latest()
            ->get();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Courses/Create', [
            'categories' => Category::orderBy('name')->get(),
            'levels' => Level::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:levels,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'level_id' => $request->level_id,
            'slug' => Str::slug($request->title),
            'user_id' => auth()->id(),
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
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course->load('sections.lessons.resources', 'sections.lessons.assignments'),
            'categories' => Category::all(),
            'levels' => Level::all(),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:levels,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

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

    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
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
