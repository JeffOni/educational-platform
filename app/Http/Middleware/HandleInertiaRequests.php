<?php

namespace App\Http\Middleware;

use App\Models\CourseDelegation;
use App\Models\LessonAnswer;
use App\Models\LessonQuestion;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
            ],
            'notifications' => fn() => $this->getNotificationData($request),
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'info' => $request->session()->get('info'),
                'warning' => $request->session()->get('warning'),
            ],
        ];
    }

    private function getNotificationData(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return ['pendingQuestions' => 0, 'studentNotifications' => []];
        }

        $roles = $user->getRoleNames();

        // Admin/Teacher: contar preguntas sin responder de sus cursos
        if ($roles->contains('admin') || $roles->contains('teacher')) {
            $pendingQuestions = LessonQuestion::whereDoesntHave('answers')
                ->whereHas('lesson.section.course', function ($query) use ($user, $roles) {
                    if ($roles->contains('admin')) {
                        return;
                    }

                    $delegatedCourseIds = CourseDelegation::active()
                        ->where('delegated_to', $user->id)
                        ->whereJsonContains('permissions', CourseDelegation::PERMISSION_ANSWER_QUESTIONS)
                        ->pluck('course_id');

                    $query->where('user_id', $user->id)
                        ->orWhereIn('id', $delegatedCourseIds);
                })
                ->count();

            return ['pendingQuestions' => $pendingQuestions, 'studentNotifications' => []];
        }

        // Student: obtener respuestas recientes a sus preguntas
        if ($roles->contains('student')) {
            $notifications = LessonAnswer::whereHas('question', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with(['question.lesson:id,name', 'user:id,name'])
                ->latest()
                ->take(10)
                ->get()
                ->map(fn($answer) => [
                    'id' => $answer->id,
                    'teacher_name' => $answer->user->name,
                    'lesson_name' => $answer->question->lesson->name ?? 'Lección',
                    'answer_preview' => \Illuminate\Support\Str::limit($answer->answer, 80),
                    'created_at' => $answer->created_at->toISOString(),
                ]);

            return ['pendingQuestions' => 0, 'studentNotifications' => $notifications];
        }

        return ['pendingQuestions' => 0, 'studentNotifications' => []];
    }
}
