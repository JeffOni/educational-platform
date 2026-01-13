<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Section;

class SectionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section = $course->sections()->create([
            'name' => $request->name,
        ]);

        return back();
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section->update([
            'name' => $request->name,
        ]);

        return back();
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return back();
    }
}
