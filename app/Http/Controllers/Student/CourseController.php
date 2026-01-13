<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $enrollments = auth()->user()->enrollments()->with('course.teacher')->get();

        return Inertia::render('Student/Courses/Index', [
            'enrolledCourses' => $enrollments
        ]);
    }

    public function show(Course $course)
    {
        $hasPurchased = auth()->user()->purchases()
            ->where('course_id', $course->id)
            ->exists();

        return Inertia::render('Student/Courses/Show', [
            'course' => $course->load('teacher', 'category', 'level', 'sections.lessons'),
            'hasPurchased' => $hasPurchased,
        ]);
    }
}
