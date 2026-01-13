<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
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
}
