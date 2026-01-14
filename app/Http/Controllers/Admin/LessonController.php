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
        \Log::info('Creando lección', [
            'section_id' => $section->id,
            'datos_recibidos' => $request->all()
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'video_type' => 'required|in:youtube,vimeo,file,spaces',
            'video_url' => 'required|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        \Log::info('Datos validados', $validated);

        $lesson = $section->lessons()->create([
            'name' => $request->name,
            'video_type' => $request->video_type,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'is_preview' => $request->boolean('is_preview'),
        ]);

        \Log::info('Lección creada', ['lesson_id' => $lesson->id]);

        return back()->with('success', 'Lección creada correctamente');
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

        return back()->with('success', 'Lección actualizada correctamente');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Lección eliminada correctamente');
    }
}
