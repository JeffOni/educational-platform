<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Inertia\Inertia;
use Overtrue\LaravelShoppingCart\Facade as Cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::all();
        $cartItemsFormatted = [];

        foreach ($cartItems as $item) {
            $course = Course::with('teacher')->find($item->id);
            if ($course) {
                $cartItemsFormatted[] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subtitle' => $course->subtitle,
                    'price' => $course->price,
                    'image_path' => $course->image_path,
                    'teacher' => $course->teacher,
                    '__raw_id' => $item->__raw_id
                ];
            }
        }

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItemsFormatted,
            'total' => Cart::total(),
            'cartCount' => Cart::count()
        ]);
    }

    public function add(Request $request, Course $course)
    {
        // Verificar que el curso esté publicado
        if ($course->status !== Course::PUBLICADO) {
            return redirect()->back()->with('error', 'Este curso no está disponible.');
        }

        // Verificar si el usuario ya compró el curso
        if (auth()->check()) {
            $hasPurchased = auth()->user()->purchases()
                ->where('course_id', $course->id)
                ->exists();

            if ($hasPurchased) {
                return redirect()->back()->with('error', 'Ya has comprado este curso.');
            }
        }

        // Verificar si ya está en el carrito
        $cartItem = Cart::get($course->id);

        if ($cartItem) {
            return redirect()->back()->with('info', 'Este curso ya está en tu carrito.');
        }

        // Agregar al carrito
        Cart::add(
            $course->id,
            $course->title,
            $course->price,
            1,
            [
                'subtitle' => $course->subtitle,
                'image_path' => $course->image_path,
            ]
        );

        return redirect()->back()->with('success', 'Curso agregado al carrito.');
    }

    public function remove($rawId)
    {
        Cart::remove($rawId);
        return redirect()->back()->with('success', 'Curso eliminado del carrito.');
    }

    public function clear()
    {
        Cart::clear();
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado.');
    }

    public function count()
    {
        return response()->json(['count' => Cart::count()]);
    }
}
