<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$section = App\Models\Section::first();

if (!$section) {
    die("No hay secciones en la base de datos");
}

echo "Section ID: {$section->id}\n";
echo "Section Name: {$section->name}\n";

try {
    $lesson = $section->lessons()->create([
        'name' => 'Lección de prueba',
        'video_type' => 'youtube',
        'video_url' => 'https://youtube.com/test',
        'duration' => 300,
        'is_preview' => false,
    ]);

    echo "✓ Lección creada exitosamente!\n";
    echo "Lesson ID: {$lesson->id}\n";
    echo "Lesson Name: {$lesson->name}\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
