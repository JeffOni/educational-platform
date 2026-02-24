<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $enrollments = $user->enrollments()
            ->with(['course.teacher', 'course.sections.lessons'])
            ->get()
            ->map(function ($enrollment) use ($user) {
                $course = $enrollment->course;
                $totalLessons = $course->sections->sum(fn($s) => $s->lessons->count());

                $completedLessons = DB::table('lesson_user')
                    ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
                    ->where('user_id', $user->id)
                    ->count();

                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                return [
                    'id' => $enrollment->id,
                    'course' => [
                        'id' => $course->id,
                        'title' => $course->title,
                        'image_path' => $course->image_path,
                        'teacher' => [
                            'name' => $course->teacher->name ?? 'Sin instructor',
                        ],
                    ],
                    'progress' => $progress,
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                ];
            });

        return Inertia::render('Student/Courses/Index', [
            'enrolledCourses' => $enrollments,
        ]);
    }

    public function show(Course $course)
    {
        $user = auth()->user();

        $isEnrolled = $user->enrollments()
            ->where('course_id', $course->id)
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'No estás inscrito en este curso.');
        }

        $course->load(['teacher', 'category', 'level', 'sections.lessons', 'exam']);

        // Calcular progreso
        $allLessonIds = $course->sections->flatMap->lessons->pluck('id');
        $totalLessons = $allLessonIds->count();

        $completedLessonIds = DB::table('lesson_user')
            ->whereIn('lesson_id', $allLessonIds)
            ->where('user_id', $user->id)
            ->pluck('lesson_id');

        $completedLessons = $completedLessonIds->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Verificar examen
        $hasExam = $course->exam && $course->exam->is_active;
        $examPassed = $hasExam && $course->exam->userHasPassingAttempt($user);

        // Verificar certificado
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return Inertia::render('Student/Courses/Show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'progress' => $progress,
            'totalLessons' => $totalLessons,
            'completedLessons' => $completedLessons,
            'completedLessonIds' => $completedLessonIds->values(),
            'hasExam' => $hasExam,
            'examPassed' => $examPassed,
            'certificate' => $certificate ? [
                'id' => $certificate->id,
                'file_path' => $certificate->file_path,
            ] : null,
        ]);
    }
}
