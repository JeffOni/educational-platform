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

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('user_id', auth()->id())
            ->with(['category', 'level'])
            ->withCount('sections')
            ->latest()
            ->get()
            ->map(function ($course) {
                // Asegurar que status sea integer
                $course->status = (int) $course->status;
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
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'family_id' => 'nullable|exists:families,id',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'level_id' => 'required|exists:levels,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'price' => $request->price,
            'family_id' => $request->family_id,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
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
            'families' => Family::where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::with('family')->where('is_active', true)->orderBy('name')->get(),
            'subcategories' => Subcategory::with('category')->where('is_active', true)->orderBy('name')->get(),
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
            'family_id' => 'nullable|exists:families,id',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'level_id' => 'required|exists:levels,id',
            'status' => 'required|integer|in:1,2,3',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'subtitle', 'description', 'price', 'family_id', 'category_id', 'subcategory_id', 'level_id', 'status']);

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
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $course->update(['status' => Course::PUBLICADO]);

        return redirect()->back()
            ->with('success', 'Curso publicado correctamente');
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
