<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamController extends Controller
{
    public function index(Course $course)
    {
        if (!$course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $exam = $course->exam()->with('questions')->first();

        return Inertia::render('Admin/Exams/Index', [
            'course' => $course,
            'exam' => $exam,
        ]);
    }

    public function store(Request $request, Course $course)
    {
        if (!$course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
        ]);

        $exam = $course->exam()->create($request->only([
            'title', 'description', 'passing_score', 'time_limit', 'max_attempts',
        ]));

        return back()->with('success', 'Examen creado correctamente.');
    }

    public function update(Request $request, CourseExam $exam)
    {
        if (!$exam->course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        $exam->update($request->only([
            'title', 'description', 'passing_score', 'time_limit', 'max_attempts', 'is_active',
        ]));

        return back()->with('success', 'Examen actualizado correctamente.');
    }

    public function storeQuestion(Request $request, CourseExam $exam)
    {
        if (!$exam->course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $maxOrder = $exam->questions()->max('order') ?? 0;

        $exam->questions()->create([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $request->options,
            'correct_answer' => $request->correct_answer,
            'points' => $request->points,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Pregunta agregada correctamente.');
    }

    public function updateQuestion(Request $request, ExamQuestion $question)
    {
        if (!$question->exam->course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $question->update($request->only([
            'question_text', 'question_type', 'options', 'correct_answer', 'points',
        ]));

        return back()->with('success', 'Pregunta actualizada correctamente.');
    }

    public function destroyQuestion(ExamQuestion $question)
    {
        if (!$question->exam->course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $question->delete();

        return back()->with('success', 'Pregunta eliminada correctamente.');
    }

    public function destroy(CourseExam $exam)
    {
        if (!$exam->course->userCanEdit(auth()->user())) {
            abort(403);
        }

        $exam->delete();

        return back()->with('success', 'Examen eliminado correctamente.');
    }
}
