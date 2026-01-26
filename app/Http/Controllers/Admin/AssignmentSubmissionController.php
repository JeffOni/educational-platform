<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonAssignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssignmentSubmissionController extends Controller
{
    public function index(LessonAssignment $assignment)
    {
        // Verificar permisos: Admin puede todo, profesor solo sus cursos o delegaciones
        $course = $assignment->lesson->section->course;

        $user = auth()->user();

        // Admin y super admin tienen acceso total
        if (!$user->hasRole(['admin', 'super-admin'])) {
            // Profesor debe ser dueño o tener delegación
            $hasAccess = $course->user_id === $user->id ||
                $course->userCanGrade($user->id);

            if (!$hasAccess) {
                abort(403, 'No tienes permiso para ver estas entregas');
            }
        }

        // Cargar entregas con información del estudiante
        $submissions = $assignment->submissions()
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('Admin/Assignments/Submissions', [
            'assignment' => $assignment->load('lesson.section.course'),
            'submissions' => $submissions,
        ]);
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        // Verificar permisos: Admin puede todo, profesor solo sus cursos o delegaciones
        $course = $submission->assignment->lesson->section->course;

        $user = auth()->user();

        // Admin y super admin tienen acceso total
        if (!$user->hasRole(['admin', 'super-admin'])) {
            // Profesor debe ser dueño o tener delegación
            $hasAccess = $course->user_id === $user->id ||
                $course->userCanGrade($user->id);

            if (!$hasAccess) {
                abort(403, 'No tienes permiso para calificar esta entrega');
            }
        }

        $request->validate([
            'grade' => 'required|numeric|min:0|max:' . $submission->assignment->max_points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Tarea calificada correctamente');
    }
}
