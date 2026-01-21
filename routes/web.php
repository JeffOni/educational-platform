<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LessonResourceController;
use App\Http\Controllers\Admin\LessonAssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController as AdminAssignmentSubmissionController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Api\TaxonomyController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PurchaseController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\QuestionController as StudentQuestionController;
use App\Http\Controllers\Student\LessonResourceController as StudentLessonResourceController;
use App\Http\Controllers\Student\AssignmentSubmissionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas públicas de cursos
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Rutas del carrito (disponibles para todos)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{course}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{rawId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

Route::middleware(['auth', 'verified'])->group(function () {
    // Checkout (solo autenticados)
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

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
        Route::post('lessons/{lesson}/assignments', [LessonAssignmentController::class, 'store'])->name('lessons.assignments.store');
        Route::put('assignments/{assignment}', [LessonAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [LessonAssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Rutas para Calificar Entregas
        Route::get('assignments/{assignment}/submissions', [AdminAssignmentSubmissionController::class, 'index'])->name('assignments.submissions');
        Route::post('submissions/{submission}/grade', [AdminAssignmentSubmissionController::class, 'grade'])->name('submissions.grade');

        // Rutas para Preguntas y Respuestas
        Route::get('questions', [AdminQuestionController::class, 'index'])->name('questions.index');
        Route::post('questions/{question}/answer', [AdminQuestionController::class, 'store'])->name('questions.answer');
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
        Route::post('courses/{course}/purchase', [PurchaseController::class, 'store'])->name('courses.purchase');

        // Rutas de lecciones
        Route::get('courses/{course}/lessons/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');
        Route::post('lessons/{lesson}/toggle-complete', [StudentLessonController::class, 'toggleComplete'])->name('lessons.toggle');

        // Rutas de preguntas
        Route::post('lessons/{lesson}/questions', [StudentQuestionController::class, 'store'])->name('lessons.questions.store');

        // Rutas de recursos
        Route::get('resources/{resource}/download', [StudentLessonResourceController::class, 'download'])->name('resources.download');

        // Rutas de tareas
        Route::post('assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'store'])->name('assignments.submit');
        Route::get('submissions/{submission}/download', [AssignmentSubmissionController::class, 'download'])->name('submissions.download');
    });
});

require __DIR__ . '/settings.php';
