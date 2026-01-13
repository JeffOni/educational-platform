<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonResource;
use Illuminate\Support\Facades\Storage;

class LessonResourceController extends Controller
{
    public function download(LessonResource $resource)
    {
        // Verificar que el estudiante esté matriculado en el curso
        $lesson = $resource->lesson;
        $section = $lesson->section;
        $course = $section->course;

        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();

        if (!$enrollment && !$lesson->is_preview) {
            abort(403, 'Debes estar matriculado en el curso para descargar recursos');
        }

        // Verificar que el archivo existe
        if (!Storage::exists($resource->file_path)) {
            abort(404, 'El archivo no existe');
        }

        return Storage::download($resource->file_path, $resource->name);
    }
}
