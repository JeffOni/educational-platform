<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course', 'uploader']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $certificates = $query->latest()->paginate(20)->withQueryString();

        // Estudiantes elegibles: enrollment completed + exam passed + sin certificado
        $eligibleStudents = Enrollment::where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->with(['user', 'course'])
            ->get();

        $courses = Course::where('status', Course::PUBLICADO)
            ->orderBy('title')
            ->get(['id', 'title']);

        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => $certificates,
            'eligibleStudents' => $eligibleStudents,
            'courses' => $courses,
            'filters' => $request->only(['course_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $enrollment = Enrollment::with('user', 'course')->findOrFail($request->enrollment_id);

        // Verificar que el enrollment esté completado
        if ($enrollment->status !== 'completed') {
            return back()->with('error', 'El estudiante no ha completado el curso.');
        }

        // Verificar que no exista certificado previo
        $existingCert = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->first();

        if ($existingCert) {
            return back()->with('error', 'Ya existe un certificado para este estudiante en este curso.');
        }

        $path = $request->file('certificate_file')->store('certificates', 'public');

        Certificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', "Certificado subido para {$enrollment->user->name}.");
    }

    public function destroy(Certificate $certificate)
    {
        Storage::disk('public')->delete($certificate->file_path);
        $certificate->delete();

        return back()->with('success', 'Certificado eliminado correctamente.');
    }
}
