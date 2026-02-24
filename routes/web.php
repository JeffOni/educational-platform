<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LessonResourceController;
use App\Http\Controllers\Admin\LessonAssignmentController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController as AdminAssignmentSubmissionController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseDelegationController;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\EnrollmentCodeController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Api\TaxonomyController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\QuestionController as StudentQuestionController;
use App\Http\Controllers\Student\LessonResourceController as StudentLessonResourceController;
use App\Http\Controllers\Student\AssignmentSubmissionController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;

// La ruta raíz redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Validar código de inscripción (público, para el formulario de registro)
Route::post('/enrollment-code/validate', function (\Illuminate\Http\Request $request) {
    $request->validate(['code' => 'required|string|size:8']);

    $code = \App\Models\EnrollmentCode::where('code', strtoupper($request->code))
        ->with('course:id,title')
        ->first();

    if (!$code || !$code->isAvailable()) {
        return response()->json(['valid' => false]);
    }

    return response()->json([
        'valid' => true,
        'course' => $code->course->title,
    ]);
})->name('enrollment-code.validate');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard con redirección según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            return app(StudentDashboardController::class)->index();
        }

        // Dashboard para admin/teacher
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para Administradores y Profesores
    Route::middleware(['role:admin|teacher'])->prefix('admin')->name('admin.')->group(function () {
        // Gestión de Taxonomía
        Route::resource('families', FamilyController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubcategoryController::class);

        // Gestión de Cursos
        Route::resource('courses', AdminCourseController::class);
        Route::put('courses/{course}/publish', [AdminCourseController::class, 'publish'])->name('courses.publish');

        // Rutas para Secciones
        Route::post('courses/{course}/sections', [SectionController::class, 'store'])->name('courses.sections.store');
        Route::put('sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

        // Rutas para Lecciones
        Route::post('sections/{section}/lessons', [LessonController::class, 'store'])->name('sections.lessons.store');
        Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

        // Rutas para Recursos de Lecciones
        Route::post('lessons/{lesson}/resources', [LessonResourceController::class, 'store'])->name('lessons.resources.store');
        Route::delete('resources/{resource}', [LessonResourceController::class, 'destroy'])->name('resources.destroy');

        // Rutas para Tareas de Lecciones
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('lessons/{lesson}/assignments', [LessonAssignmentController::class, 'store'])->name('lessons.assignments.store');
        Route::put('assignments/{assignment}', [LessonAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [LessonAssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Rutas para Calificar Entregas
        Route::get('assignments/{assignment}/submissions', [AdminAssignmentSubmissionController::class, 'index'])->name('assignments.submissions');
        Route::post('submissions/{submission}/grade', [AdminAssignmentSubmissionController::class, 'grade'])->name('submissions.grade');

        // Rutas para Preguntas y Respuestas
        Route::get('questions', [AdminQuestionController::class, 'index'])->name('questions.index');
        Route::post('questions/{question}/answer', [AdminQuestionController::class, 'store'])->name('questions.answer');

        // Rutas para Delegaciones de Cursos
        Route::prefix('courses/{course}/delegations')->name('courses.delegations.')->group(function () {
            Route::get('/', [CourseDelegationController::class, 'index'])->name('index');
            Route::get('/available-teachers', [CourseDelegationController::class, 'availableTeachers'])->name('available-teachers');
            Route::post('/', [CourseDelegationController::class, 'store'])->name('store');
            Route::put('/{delegation}', [CourseDelegationController::class, 'update'])->name('update');
            Route::post('/{delegation}/revoke', [CourseDelegationController::class, 'revoke'])->name('revoke');
            Route::delete('/{delegation}', [CourseDelegationController::class, 'destroy'])->name('destroy');
        });

        // Rutas para Códigos de Inscripción
        Route::resource('enrollment-codes', EnrollmentCodeController::class)->except(['show', 'edit', 'update', 'store']);
        Route::post('enrollment-codes/generate-batch', [EnrollmentCodeController::class, 'generateBatch'])->name('enrollment-codes.generate-batch');
        Route::post('enrollment-codes/{enrollmentCode}/deactivate', [EnrollmentCodeController::class, 'deactivate'])->name('enrollment-codes.deactivate');

        // Rutas para Exámenes de Cursos
        Route::get('courses/{course}/exam', [AdminExamController::class, 'index'])->name('courses.exam.index');
        Route::post('courses/{course}/exam', [AdminExamController::class, 'store'])->name('courses.exam.store');
        Route::put('exams/{exam}', [AdminExamController::class, 'update'])->name('exams.update');
        Route::delete('exams/{exam}', [AdminExamController::class, 'destroy'])->name('exams.destroy');
        Route::post('exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::put('exam-questions/{question}', [AdminExamController::class, 'updateQuestion'])->name('exam-questions.update');
        Route::delete('exam-questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('exam-questions.destroy');

        // Rutas para Certificados
        Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');
        Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    });

    // Rutas solo para Administradores
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('levels', LevelController::class);

        // API Endpoints para filtrado en cascada
        Route::get('api/categories', [TaxonomyController::class, 'getCategoriesByFamily'])->name('api.categories');
        Route::get('api/subcategories', [TaxonomyController::class, 'getSubcategoriesByCategory'])->name('api.subcategories');
    });

    // Rutas para Estudiantes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('my-courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Rutas de lecciones
        Route::get('courses/{course}/lessons/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');
        Route::post('lessons/{lesson}/toggle-complete', [StudentLessonController::class, 'toggleComplete'])->name('lessons.toggle');

        // Rutas de preguntas
        Route::post('lessons/{lesson}/questions', [StudentQuestionController::class, 'store'])->name('lessons.questions.store');
        Route::post('questions/{question}/answer', [StudentQuestionController::class, 'answer'])->name('questions.answer');

        // Rutas de recursos
        Route::get('resources/{resource}/download', [StudentLessonResourceController::class, 'download'])->name('resources.download');

        // Rutas de tareas
        Route::post('assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'store'])->name('assignments.submit');
        Route::get('submissions/{submission}/download', [AssignmentSubmissionController::class, 'download'])->name('submissions.download');

        // Rutas de exámenes
        Route::get('courses/{course}/exam', [StudentExamController::class, 'show'])->name('exam.show');
        Route::post('courses/{course}/exam/start', [StudentExamController::class, 'start'])->name('exam.start');
        Route::post('courses/{course}/exam', [StudentExamController::class, 'submit'])->name('exam.submit');
        Route::get('courses/{course}/exam/result', [StudentExamController::class, 'result'])->name('exam.result');

        // Rutas de certificados
        Route::get('certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('certificates.download');
    });
});

require __DIR__ . '/settings.php';
