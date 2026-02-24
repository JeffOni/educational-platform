<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseDelegation;
use App\Models\LessonQuestion;
use App\Models\LessonAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $questions = LessonQuestion::with(['user', 'lesson.section.course', 'answers.user'])
            ->whereHas('lesson.section.course', function ($query) use ($user) {
                if ($user->hasRole('admin')) {
                    // Admin ve todas las preguntas
                    return;
                }

                // Profesor titular o delegado con permiso de responder preguntas
                $delegatedCourseIds = CourseDelegation::active()
                    ->where('delegated_to', $user->id)
                    ->whereJsonContains('permissions', CourseDelegation::PERMISSION_ANSWER_QUESTIONS)
                    ->pluck('course_id');

                $query->where('user_id', $user->id)
                    ->orWhereIn('id', $delegatedCourseIds);
            })
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
        ]);
    }

    public function store(Request $request, LessonQuestion $question)
    {
        $user = auth()->user();
        $course = $question->lesson->section->course;

        if (!$course->userHasPermission($user, CourseDelegation::PERMISSION_ANSWER_QUESTIONS)) {
            abort(403);
        }

        $request->validate([
            'answer' => 'required|string|min:5',
        ]);

        LessonAnswer::create([
            'lesson_question_id' => $question->id,
            'user_id' => $user->id,
            'answer' => $request->answer,
        ]);

        return back()->with('success', 'Respuesta enviada correctamente');
    }
}
