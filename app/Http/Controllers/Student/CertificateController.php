<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para descargar este certificado.');
        }

        $path = Storage::disk('public')->path($certificate->file_path);

        if (!file_exists($path)) {
            abort(404, 'El archivo del certificado no fue encontrado.');
        }

        $extension = pathinfo($certificate->file_path, PATHINFO_EXTENSION);
        $fileName = "certificado-{$certificate->course->title}.{$extension}";

        return response()->download($path, $fileName);
    }
}
