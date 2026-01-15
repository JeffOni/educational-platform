<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Purchase;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Overtrue\LaravelShoppingCart\Facade as Cart;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::all();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            foreach ($cartItems as $item) {
                $course = Course::find($item->id);

                if (!$course || $course->status !== Course::PUBLICADO) {
                    continue;
                }

                // Verificar si ya compró el curso
                $alreadyPurchased = Purchase::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if ($alreadyPurchased) {
                    continue;
                }

                // Crear la compra
                Purchase::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'amount' => $course->price,
                    'payment_method' => 'simulated',
                    'status' => 'completed',
                ]);

                // Crear enrollment automático
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);

                $totalAmount += $course->price;
            }

            DB::commit();

            // Limpiar el carrito
            Cart::clear();

            return redirect()->route('student.courses.index')
                ->with('success', '¡Compra realizada exitosamente! Ahora tienes acceso a tus cursos.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Hubo un error al procesar tu compra. Por favor intenta nuevamente.');
        }
    }
}
