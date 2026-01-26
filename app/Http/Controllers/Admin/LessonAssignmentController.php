<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonAssignment;
use App\Enums\AssignmentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonAssignmentController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'max_points' => 'nullable|integer|min:0',
            'submission_type' => ['required', Rule::in(['file', 'text', 'link', 'file_and_text', 'forum'])],
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string',
            'max_file_size' => 'nullable|integer|min:1',
            'max_files' => 'nullable|integer|min:1|max:10',
            'requires_text' => 'nullable|boolean',
            'enable_comments' => 'nullable|boolean',
        ]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points ?? 100,
            'submission_type' => $request->submission_type ?? 'file',
            'allowed_file_types' => $request->allowed_file_types,
            'max_file_size' => $request->max_file_size ?? 10240,
            'max_files' => $request->max_files ?? 5,
            'requires_text' => $request->requires_text ?? false,
            'enable_comments' => $request->enable_comments ?? false,
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
            'submission_type' => ['required', Rule::in(['file', 'text', 'link', 'file_and_text', 'forum'])],
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string',
            'max_file_size' => 'nullable|integer|min:1',
            'max_files' => 'nullable|integer|min:1|max:10',
            'requires_text' => 'nullable|boolean',
            'enable_comments' => 'nullable|boolean',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points ?? 100,
            'submission_type' => $request->submission_type ?? 'file',
            'allowed_file_types' => $request->allowed_file_types,
            'max_file_size' => $request->max_file_size ?? 10240,
            'max_files' => $request->max_files ?? 5,
            'requires_text' => $request->requires_text ?? false,
            'enable_comments' => $request->enable_comments ?? false,
        ]);

        return back()->with('success', 'Tarea actualizada correctamente');
    }

    public function destroy(LessonAssignment $assignment)
    {
        $assignment->delete();

        return back()->with('success', 'Tarea eliminada correctamente');
    }
}

