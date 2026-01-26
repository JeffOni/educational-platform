<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseDelegation;

class CourseDelegationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el profesor titular
        $teacher = User::role('teacher')->first();

        if (!$teacher) {
            $this->command->info('No hay profesores en la BD. Omitiendo delegaciones.');
            return;
        }

        // Crear un segundo profesor para las delegaciones
        $teacherReplacement = User::create([
            'name' => 'María González (Reemplazo)',
            'email' => 'maria.reemplazo@example.com',
            'password' => bcrypt('password'),
        ]);
        $teacherReplacement->assignRole('teacher');

        // Obtener un curso del profesor titular
        $course = Course::where('user_id', $teacher->id)->first();

        if (!$course) {
            $this->command->info('No hay cursos para crear delegaciones.');
            return;
        }

        // EJEMPLO 1: Delegación temporal por enfermedad (solo calificar)
        CourseDelegation::create([
            'course_id' => $course->id,
            'delegated_by' => $teacher->id,
            'delegated_to' => $teacherReplacement->id,
            'permissions' => [
                CourseDelegation::PERMISSION_VIEW_COURSE,
                CourseDelegation::PERMISSION_GRADE_ASSIGNMENTS,
            ],
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
            'reason' => 'Profesor titular con licencia médica',
            'is_active' => true,
        ]);

        $this->command->info('✅ Delegación temporal creada: María puede calificar por 7 días');

        // EJEMPLO 2: Delegación de vacaciones (más permisos)
        $course2 = Course::where('user_id', $teacher->id)->skip(1)->first();

        if ($course2) {
            CourseDelegation::create([
                'course_id' => $course2->id,
                'delegated_by' => $teacher->id,
                'delegated_to' => $teacherReplacement->id,
                'permissions' => [
                    CourseDelegation::PERMISSION_VIEW_COURSE,
                    CourseDelegation::PERMISSION_GRADE_ASSIGNMENTS,
                    CourseDelegation::PERMISSION_ANSWER_QUESTIONS,
                    CourseDelegation::PERMISSION_VIEW_ANALYTICS,
                ],
                'starts_at' => now()->addDays(30),
                'expires_at' => now()->addDays(44), // 2 semanas después
                'reason' => 'Vacaciones programadas',
                'is_active' => true,
            ]);

            $this->command->info('✅ Delegación programada: Vacaciones en 30 días');
        }

        // EJEMPLO 3: Delegación permanente de asistente (sin fecha de expiración)
        $course3 = Course::where('user_id', $teacher->id)->skip(2)->first();

        if ($course3) {
            CourseDelegation::create([
                'course_id' => $course3->id,
                'delegated_by' => $teacher->id,
                'delegated_to' => $teacherReplacement->id,
                'permissions' => [
                    CourseDelegation::PERMISSION_VIEW_COURSE,
                    CourseDelegation::PERMISSION_ANSWER_QUESTIONS,
                ],
                'starts_at' => now(),
                'expires_at' => null, // Sin expiración
                'reason' => 'Asistente de enseñanza permanente',
                'is_active' => true,
            ]);

            $this->command->info('✅ Delegación permanente: Asistente de enseñanza');
        }
    }
}
