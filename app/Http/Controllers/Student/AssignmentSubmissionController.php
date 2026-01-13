<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonAssignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function store(Request $request, LessonAssignment $assignment)
    {
        // Verificar que el estudiante esté matriculado en el curso
        $lesson = $assignment->lesson;
        $section = $lesson->section;
        $course = $section->course;

        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();

        if (!$enrollment) {
            return back()->with('error', 'Debes estar matriculado en el curso para enviar tareas');
        }

        // Validar
        $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        // Verificar si ya existe una entrega
        $submission = AssignmentSubmission::where([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
        ])->first();

        // Preparar datos
        $data = [
            'content' => $request->content,
        ];

        // Manejar archivo si se subió uno
        if ($request->hasFile('file')) {
            // Si hay una entrega anterior con archivo, eliminarlo
            if ($submission && $submission->file_path) {
                Storage::delete($submission->file_path);
            }

            $data['file_path'] = $request->file('file')->store('assignment-submissions');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        if ($submission) {
            // Actualizar entrega existente (solo si no ha sido calificada)
            if ($submission->grade !== null) {
                return back()->with('error', 'No puedes modificar una tarea que ya ha sido calificada');
            }

            $submission->update($data);
            $message = 'Tarea actualizada correctamente';
        } else {
            // Crear nueva entrega
            $data['assignment_id'] = $assignment->id;
            $data['user_id'] = auth()->id();
            AssignmentSubmission::create($data);
            $message = 'Tarea enviada correctamente';
        }

        return back()->with('success', $message);
    }

    public function download(AssignmentSubmission $submission)
    {
        // Verificar que el usuario sea el dueño de la entrega o el profesor
        $lesson = $submission->assignment->lesson;
        $course = $lesson->section->course;

        if ($submission->user_id !== auth()->id() && $course->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para descargar este archivo');
        }

        // Verificar que el archivo existe
        if (!$submission->file_path || !Storage::exists($submission->file_path)) {
            abort(404, 'El archivo no existe');
        }

        return Storage::download($submission->file_path, $submission->file_name);
    }
}
