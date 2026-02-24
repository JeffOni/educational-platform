<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExamController extends Controller
{
    public function show(Course $course)
    {
        $user = auth()->user();

        // Verificar inscripción
        $isEnrolled = $user->enrollments()->where('course_id', $course->id)->exists();
        if (!$isEnrolled) {
            abort(403, 'No estás inscrito en este curso.');
        }

        $exam = $course->exam()->where('is_active', true)->first();

        if (!$exam) {
            return back()->with('error', 'Este curso no tiene examen disponible.');
        }

        // Verificar si tiene intento en progreso
        $inProgressAttempt = $exam->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('started_at')
            ->whereNull('completed_at')
            ->first();

        // Si tiene intento en progreso, mostrar el examen con ese intento
        if ($inProgressAttempt) {
            return Inertia::render('Student/Exams/Take', [
                'course' => $course,
                'exam' => $exam->load('questions'),
                'attempt' => $inProgressAttempt,
                'canAttempt' => true,
            ]);
        }

        // Verificar si puede intentar
        $canAttempt = $exam->userCanAttempt($user);
        $hasPassingAttempt = $exam->userHasPassingAttempt($user);
        $previousAttempts = $exam->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->latest()
            ->get();

        return Inertia::render('Student/Exams/Show', [
            'course' => $course,
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'passing_score' => $exam->passing_score,
                'time_limit' => $exam->time_limit,
                'max_attempts' => $exam->max_attempts,
                'total_questions' => $exam->questions()->count(),
                'total_points' => $exam->total_points,
            ],
            'canAttempt' => $canAttempt,
            'hasPassingAttempt' => $hasPassingAttempt,
            'previousAttempts' => $previousAttempts,
        ]);
    }

    public function start(Course $course)
    {
        $user = auth()->user();
        $exam = $course->exam()->where('is_active', true)->firstOrFail();

        if (!$exam->userCanAttempt($user)) {
            return back()->with('error', 'Has alcanzado el número máximo de intentos.');
        }

        // Crear intento
        $attempt = ExamAttempt::create([
            'course_exam_id' => $exam->id,
            'user_id' => $user->id,
            'started_at' => now(),
        ]);

        return Inertia::render('Student/Exams/Take', [
            'course' => $course,
            'exam' => $exam->load('questions'),
            'attempt' => $attempt,
            'canAttempt' => true,
        ]);
    }

    public function submit(Request $request, Course $course)
    {
        $user = auth()->user();
        $exam = $course->exam()->where('is_active', true)->firstOrFail();

        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'answers' => 'required|array',
        ]);

        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->firstOrFail();

        // Calificar automáticamente
        $questions = $exam->questions;
        $totalPoints = 0;
        $earnedPoints = 0;
        $gradedAnswers = [];

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $request->answers[$question->id] ?? null;
            $isCorrect = false;

            if ($userAnswer !== null) {
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
            }

            if ($isCorrect) {
                $earnedPoints += $question->points;
            }

            $gradedAnswers[$question->id] = [
                'answer' => $userAnswer,
                'correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'points_earned' => $isCorrect ? $question->points : 0,
            ];
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $exam->passing_score;

        $attempt->update([
            'answers' => $gradedAnswers,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        // Si aprobó, verificar si debe marcar el enrollment como completado
        if ($passed) {
            $this->checkCourseCompletion($user, $course);
        }

        return redirect()->route('student.exam.result', $course->id)
            ->with($passed ? 'success' : 'warning',
                $passed ? '¡Felicidades! Has aprobado el examen.' : 'No has alcanzado la puntuación mínima.'
            );
    }

    public function result(Course $course)
    {
        $user = auth()->user();
        $exam = $course->exam()->firstOrFail();

        $lastAttempt = $exam->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->firstOrFail();

        $canRetry = $exam->userCanAttempt($user) && !$lastAttempt->passed;

        return Inertia::render('Student/Exams/Result', [
            'course' => $course,
            'exam' => [
                'title' => $exam->title,
                'passing_score' => $exam->passing_score,
                'max_attempts' => $exam->max_attempts,
            ],
            'attempt' => $lastAttempt,
            'canRetry' => $canRetry,
            'totalAttempts' => $exam->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
        ]);
    }

    private function checkCourseCompletion($user, $course)
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = DB::table('lesson_user')
            ->whereIn('lesson_id', $course->lessons()->pluck('lessons.id'))
            ->where('user_id', $user->id)
            ->count();

        if ($completedLessons >= $totalLessons && $totalLessons > 0) {
            $user->enrollments()
                ->where('course_id', $course->id)
                ->update(['status' => 'completed']);
        }
    }
}
