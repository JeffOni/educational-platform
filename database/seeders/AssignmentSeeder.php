<?php

namespace Database\Seeders;

use App\Models\LessonAssignment;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener algunas lecciones para crear tareas de ejemplo
        $lessons = Lesson::limit(5)->get();

        if ($lessons->isEmpty()) {
            $this->command->warn('⚠️  No hay lecciones disponibles para crear tareas');
            return;
        }

        $assignmentTypes = [
            [
                'title' => 'Práctica: Implementar funcionalidad',
                'description' => 'Implementa la funcionalidad solicitada siguiendo las mejores prácticas de código limpio.',
                'submission_type' => 'file',
                'max_points' => 100,
                'due_date' => now()->addDays(7),
                'allowed_file_types' => ['zip', 'rar', 'py', 'js', 'java', 'php'],
                'max_file_size' => 10240,
                'max_files' => 3,
                'requires_text' => false,
                'enable_comments' => false,
            ],
            [
                'title' => 'Ensayo: Análisis del tema',
                'description' => 'Escribe un ensayo de 500-1000 palabras analizando los conceptos vistos en la lección.',
                'submission_type' => 'text',
                'max_points' => 80,
                'due_date' => now()->addDays(5),
                'allowed_file_types' => null,
                'max_file_size' => null,
                'max_files' => null,
                'requires_text' => true,
                'enable_comments' => false,
            ],
            [
                'title' => 'Proyecto en GitHub',
                'description' => 'Sube tu código a un repositorio de GitHub y comparte el enlace.',
                'submission_type' => 'link',
                'max_points' => 90,
                'due_date' => now()->addDays(14),
                'allowed_file_types' => null,
                'max_file_size' => null,
                'max_files' => null,
                'requires_text' => false,
                'enable_comments' => false,
            ],
            [
                'title' => 'Presentación con Memoria',
                'description' => 'Crea una presentación y sube tanto el archivo PPT como un documento explicativo.',
                'submission_type' => 'file_and_text',
                'max_points' => 100,
                'due_date' => now()->addDays(10),
                'allowed_file_types' => ['pdf', 'ppt', 'pptx', 'doc', 'docx'],
                'max_file_size' => 20480,
                'max_files' => 2,
                'requires_text' => true,
                'enable_comments' => false,
            ],
            [
                'title' => 'Discusión: Mejores Prácticas',
                'description' => 'Participa en la discusión sobre las mejores prácticas del desarrollo. Mínimo 3 intervenciones.',
                'submission_type' => 'forum',
                'max_points' => 50,
                'due_date' => now()->addDays(20),
                'allowed_file_types' => null,
                'max_file_size' => null,
                'max_files' => null,
                'requires_text' => false,
                'enable_comments' => true,
            ],
        ];

        foreach ($lessons as $index => $lesson) {
            if (isset($assignmentTypes[$index])) {
                $assignment = $assignmentTypes[$index];

                $data = [
                    'lesson_id' => $lesson->id,
                    'title' => $assignment['title'],
                    'description' => $assignment['description'],
                    'submission_type' => $assignment['submission_type'],
                    'max_points' => $assignment['max_points'],
                    'due_date' => $assignment['due_date'],
                    'requires_text' => $assignment['requires_text'],
                    'enable_comments' => $assignment['enable_comments'],
                ];

                // Solo agregar campos si no son null
                if ($assignment['allowed_file_types'] !== null) {
                    $data['allowed_file_types'] = $assignment['allowed_file_types'];
                }
                if ($assignment['max_file_size'] !== null) {
                    $data['max_file_size'] = $assignment['max_file_size'];
                }
                if ($assignment['max_files'] !== null) {
                    $data['max_files'] = $assignment['max_files'];
                }

                LessonAssignment::create($data);

                $this->command->info("✅ Tarea '{$assignment['title']}' creada para: {$lesson->name}");
            }
        }

        $this->command->info('✅ Tareas de ejemplo creadas correctamente');
    }
}
