<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonQuestion;
use App\Models\LessonAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index()
    {
        // Obtener preguntas de los cursos del profesor
        $questions = LessonQuestion::with(['user', 'lesson.section.course', 'answers.user'])
            ->whereHas('lesson.section.course', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
        ]);
    }

    public function store(Request $request, LessonQuestion $question)
    {
        // Verificar que el profesor es dueño del curso
        $course = $question->lesson->section->course;
        
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'answer' => 'required|string|min:5',
        ]);

        LessonAnswer::create([
            'lesson_question_id' => $question->id,
            'user_id' => auth()->id(),
            'answer' => $request->answer,
        ]);

        return back()->with('success', 'Respuesta enviada correctamente');
    }
}
