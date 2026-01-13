<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function store(Request $request, Section $section)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'video_type' => 'required|in:youtube,vimeo,file,spaces',
            'video_url' => 'required|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        $section->lessons()->create([
            'name' => $request->name,
            'video_type' => $request->video_type,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back();
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'video_type' => 'required|in:youtube,vimeo,file,spaces',
            'video_url' => 'required|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        $lesson->update([
            'name' => $request->name,
            'video_type' => $request->video_type,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_preview' => $request->boolean('is_preview'),
        ]);

        return back();
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back();
    }
}
