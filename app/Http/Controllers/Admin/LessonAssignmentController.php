<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonAssignment;
use Illuminate\Http\Request;

class LessonAssignmentController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'max_points' => 'nullable|integer|min:0',
        ]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points ?? 100,
        ]);

        return back()->with('success', 'Tarea creada correctamente');
    }

    public function update(Request $request, LessonAssignment $assignment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'max_points' => 'nullable|integer|min:0',
        ]);

        $assignment->update($request->all());

        return back()->with('success', 'Tarea actualizada correctamente');
    }

    public function destroy(LessonAssignment $assignment)
    {
        $assignment->delete();

        return back()->with('success', 'Tarea eliminada correctamente');
    }
}
