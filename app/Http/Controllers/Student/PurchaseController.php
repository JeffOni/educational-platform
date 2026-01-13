<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Purchase;
use App\Models\Enrollment;

class PurchaseController extends Controller
{
    public function store(Course $course)
    {
        $user = auth()->user();

        // Verificar si ya compró el curso
        if (Purchase::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->back()->with('error', 'Ya has comprado este curso.');
        }

        // Crear la compra
        Purchase::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'payment_method' => 'free', // Simulado
            'status' => 'completed',
        ]);

        // Crear enrollment automático
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return redirect()->route('student.courses.show', $course)->with('success', '¡Curso adquirido exitosamente!');
    }
}
