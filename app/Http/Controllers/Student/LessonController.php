<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        // Verificar que el estudiante haya comprado el curso
        $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();

        if (!$enrollment && !$lesson->is_preview) {
            return redirect("/student/courses/{$course->id}")
                ->with('error', 'Debes estar inscrito en el curso para acceder a esta lección');
        }

        // Cargar curso completo con curriculum
        $course->load([
            'sections.lessons' => function ($query) {
                $query->orderBy('id');
            },
            'sections' => function ($query) {
                $query->orderBy('id');
            },
            'teacher'
        ]);

        // Cargar recursos y tareas de la lección
        $lesson->load([
            'resources',
            'assignments.submissions' => function ($query) {
                $query->where('user_id', auth()->id());
            }
        ]);

        // Cargar preguntas y respuestas de la lección
        $questions = $lesson->questions()
            ->with(['user', 'answers.user'])
            ->latest()
            ->get();

        // Verificar si la lección está completada
        $isCompleted = auth()->user()->lessons_completed()->where('lesson_id', $lesson->id)->exists();

        // Calcular progreso del curso
        $totalLessons = $course->sections->sum(function ($section) {
            return $section->lessons->count();
        });

        $completedLessons = auth()->user()->lessons_completed()
            ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
            ->count();

        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return Inertia::render('Student/Lessons/Show', [
            'course' => $course,
            'lesson' => $lesson,
            'questions' => $questions,
            'isCompleted' => $isCompleted,
            'progress' => $progress,
        ]);
    }

    public function toggleComplete(Lesson $lesson)
    {
        $user = auth()->user();

        if ($user->lessons_completed()->where('lesson_id', $lesson->id)->exists()) {
            $user->lessons_completed()->detach($lesson->id);
            $completed = false;
        } else {
            $user->lessons_completed()->attach($lesson->id);
            $completed = true;
        }

        return back()->with('success', $completed ? 'Lección marcada como completada' : 'Lección marcada como no completada');
    }
}
