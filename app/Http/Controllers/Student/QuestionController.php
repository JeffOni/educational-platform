<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonAnswer;
use App\Models\LessonQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'question' => 'required|string|min:10',
        ]);

        LessonQuestion::create([
            'user_id' => auth()->id(),
            'lesson_id' => $lesson->id,
            'question' => $request->question,
        ]);

        return back()->with('success', 'Pregunta enviada correctamente');
    }

    public function answer(Request $request, LessonQuestion $question)
    {
        // Permitir al autor responder solo si ya hay respuestas de otros (para agradecer, continuar conversación)
        // No permitir responder si no hay respuestas aún (evitar auto-respuesta)
        if ($question->user_id === auth()->id()) {
            $hasOtherAnswers = $question->answers()
                ->where('user_id', '!=', auth()->id())
                ->exists();

            if (!$hasOtherAnswers) {
                return back()->with('error', 'No puedes responder a tu propia pregunta.');
            }
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
