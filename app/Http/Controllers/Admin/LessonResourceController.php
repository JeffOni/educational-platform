<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonResourceController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('lesson-resources', 'public');

        LessonResource::create([
            'lesson_id' => $lesson->id,
            'name' => $request->name,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Recurso agregado correctamente');
    }

    public function destroy(LessonResource $resource)
    {
        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Recurso eliminado correctamente');
    }
}
