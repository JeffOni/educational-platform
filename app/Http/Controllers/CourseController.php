<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;

class CourseController extends Controller
{
    public function show(Course $course)
    {
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
