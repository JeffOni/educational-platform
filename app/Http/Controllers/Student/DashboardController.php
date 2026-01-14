<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonAssignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Obtener cursos inscritos con progreso
        $enrolledCourses = Enrollment::where('user_id', $user->id)
            ->with(['course' => function ($query) {
                $query->with(['user', 'sections.lessons']);
            }])
            ->get()
            ->map(function ($enrollment) use ($user) {
                $course = $enrollment->course;
                $totalLessons = $course->sections->sum(function ($section) {
                    return $section->lessons->count();
                });

                // Contar lecciones completadas
                $completedLessons = DB::table('lesson_user')
                    ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
                    ->where('user_id', $user->id)
                    ->count();

                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'image_path' => $course->image_path,
                    'progress' => $progress,
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'instructor' => [
                        'name' => $course->user->name,
                    ],
                    'last_accessed' => $enrollment->updated_at,
                ];
            });

        // Obtener tareas pendientes
        $enrolledCourseIds = $enrolledCourses->pluck('id');
        
        $pendingAssignments = LessonAssignment::whereHas('lesson.section.course', function ($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->where('due_date', '>=', now())
            ->whereDoesntHave('submissions', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['lesson.section.course'])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Estadísticas
        $completedCourses = $enrolledCourses->filter(function ($course) {
            return $course['progress'] === 100;
        })->count();

        // Calcular horas totales (estimación basada en duración de lecciones completadas)
        $totalHours = DB::table('lesson_user')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->where('lesson_user.user_id', $user->id)
            ->sum('lessons.duration');

        $totalHours = $totalHours ? round($totalHours / 3600) : 0; // Convertir segundos a horas

        $stats = [
            'total_courses' => $enrolledCourses->count(),
            'completed_courses' => $completedCourses,
            'total_hours' => $totalHours,
            'certificates' => $completedCourses, // Por ahora igual a cursos completados
        ];

        return Inertia::render('Student/Dashboard', [
            'enrolledCourses' => $enrolledCourses,
            'pendingAssignments' => $pendingAssignments,
            'stats' => $stats,
        ]);
    }
}
