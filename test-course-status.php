<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Obtener todos los cursos
$courses = \App\Models\Course::select('id', 'title', 'status')->get();

echo "=== ESTADO DE CURSOS EN LA BASE DE DATOS ===\n\n";

foreach ($courses as $course) {
    $statusText = match($course->status) {
        1 => 'Borrador',
        2 => 'En Revisión',
        3 => 'Publicado',
        default => 'Desconocido'
    };
    
    echo "ID: {$course->id}\n";
    echo "Título: {$course->title}\n";
    echo "Status (número): {$course->status}\n";
    echo "Status (texto): {$statusText}\n";
    echo "Tipo de dato: " . gettype($course->status) . "\n";
    echo "-----------------------------------\n\n";
}

echo "Total de cursos: " . $courses->count() . "\n";
