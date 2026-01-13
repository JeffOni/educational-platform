<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'featuredCourses' => Course::where('status', Course::PUBLICADO)
                ->with('teacher')
                ->latest()
                ->take(6)
                ->get()
        ]);
    }
}
