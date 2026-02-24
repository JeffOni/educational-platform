<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
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
            ->with([
                'course' => function ($query) {
                    $query->with(['user', 'sections.lessons', 'exam']);
                }
            ])
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

                // Verificar si tiene examen y si lo aprobó
                $hasExam = $course->exam && $course->exam->is_active;
                $examPassed = $hasExam && $course->exam->userHasPassingAttempt($user);

                // Verificar si tiene certificado
                $certificate = Certificate::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->first();

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
                    'has_exam' => $hasExam,
                    'exam_passed' => $examPassed,
                    'certificate' => $certificate ? [
                        'id' => $certificate->id,
                        'file_path' => $certificate->file_path,
                    ] : null,
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
            return $course['progress'] === 100 && $course['exam_passed'];
        })->count();

        $totalCertificates = Certificate::where('user_id', $user->id)->count();

        // Calcular horas totales
        $totalHours = DB::table('lesson_user')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->where('lesson_user.user_id', $user->id)
            ->sum('lessons.duration');

        $totalHours = $totalHours ? round($totalHours / 3600) : 0;

        $stats = [
            'total_courses' => $enrolledCourses->count(),
            'completed_courses' => $completedCourses,
            'total_hours' => $totalHours,
            'certificates' => $totalCertificates,
        ];

        return Inertia::render('Student/Dashboard', [
            'enrolledCourses' => $enrolledCourses,
            'pendingAssignments' => $pendingAssignments,
            'stats' => $stats,
        ]);
    }
}
