<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = LessonAssignment::query()
            ->with([
                'lesson.section.course',
                'submissions' => function ($q) {
                    $q->select('assignment_id', 'id', 'grade', 'graded_at');
                }
            ])
            ->withCount('submissions');

        // Si no es admin, solo ver tareas de sus cursos o con delegación
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $query->whereHas('lesson.section.course', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('activeDelegations', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->where('can_grade_assignments', true);
                    });
            });
        }

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->whereHas('submissions', function ($q) {
                    $q->whereNull('graded_at');
                });
            } elseif ($status === 'graded') {
                $query->whereHas('submissions', function ($q) {
                    $q->whereNotNull('graded_at');
                });
            } elseif ($status === 'overdue') {
                $query->where('due_date', '<', now())
                    ->whereHas('submissions', function ($q) {
                        $q->whereNull('graded_at');
                    });
            }
        }

        $assignments = $query->latest()
            ->get()
            ->map(function ($assignment) {
                $totalSubmissions = $assignment->submissions_count;
                $gradedSubmissions = $assignment->submissions->whereNotNull('graded_at')->count();
                $pendingSubmissions = $totalSubmissions - $gradedSubmissions;

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'submission_type' => $assignment->submission_type,
                    'due_date' => $assignment->due_date,
                    'max_points' => $assignment->max_points,
                    'course' => [
                        'id' => $assignment->lesson->section->course->id,
                        'title' => $assignment->lesson->section->course->title,
                    ],
                    'lesson' => [
                        'id' => $assignment->lesson->id,
                        'name' => $assignment->lesson->name,
                    ],
                    'section' => [
                        'id' => $assignment->lesson->section->id,
                        'name' => $assignment->lesson->section->name,
                    ],
                    'total_submissions' => $totalSubmissions,
                    'graded_submissions' => $gradedSubmissions,
                    'pending_submissions' => $pendingSubmissions,
                    'is_overdue' => $assignment->due_date && $assignment->due_date < now(),
                ];
            });

        return Inertia::render('Admin/Assignments/Index', [
            'assignments' => $assignments,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ]
        ]);
    }
}
