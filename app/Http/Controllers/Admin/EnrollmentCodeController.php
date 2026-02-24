<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentCode;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = EnrollmentCode::with(['course', 'creator', 'usedBy']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'available' => $query->available(),
                'used' => $query->used(),
                'expired' => $query->expired(),
                default => null,
            };
        }

        $codes = $query->latest()->paginate(20)->withQueryString();

        $courses = Course::where('status', Course::PUBLICADO)
            ->orderBy('title')
            ->get(['id', 'title']);

        return Inertia::render('Admin/EnrollmentCodes/Index', [
            'codes' => $codes,
            'courses' => $courses,
            'filters' => $request->only(['course_id', 'status']),
        ]);
    }

    public function create()
    {
        $courses = Course::where('status', Course::PUBLICADO)
            ->orderBy('title')
            ->get(['id', 'title']);

        return Inertia::render('Admin/EnrollmentCodes/Create', [
            'courses' => $courses,
        ]);
    }

    public function generateBatch(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'quantity' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $codes = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            $codes[] = EnrollmentCode::create([
                'code' => EnrollmentCode::generateCode(),
                'course_id' => $request->course_id,
                'created_by' => auth()->id(),
                'expires_at' => $request->expires_at,
            ]);
        }

        return redirect()->route('admin.enrollment-codes.index')
            ->with('success', "Se generaron {$request->quantity} códigos correctamente.");
    }

    public function deactivate(EnrollmentCode $enrollmentCode)
    {
        if ($enrollmentCode->isUsed()) {
            return back()->with('error', 'No se puede desactivar un código que ya fue utilizado.');
        }

        $enrollmentCode->update(['is_active' => false]);

        return back()->with('success', 'Código desactivado correctamente.');
    }

    public function destroy(EnrollmentCode $enrollmentCode)
    {
        if ($enrollmentCode->isUsed()) {
            return back()->with('error', 'No se puede eliminar un código que ya fue utilizado.');
        }

        $enrollmentCode->delete();

        return back()->with('success', 'Código eliminado correctamente.');
    }
}
