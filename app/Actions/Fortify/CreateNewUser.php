<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\EnrollmentCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'enrollment_code' => ['required', 'string', 'size:8'],
        ], [
            'enrollment_code.required' => 'El código de inscripción es obligatorio.',
            'enrollment_code.size' => 'El código de inscripción debe tener 8 caracteres.',
        ])->after(function ($validator) use ($input) {
            if (!isset($input['enrollment_code'])) {
                return;
            }

            $code = EnrollmentCode::where('code', strtoupper($input['enrollment_code']))->first();

            if (!$code) {
                $validator->errors()->add('enrollment_code', 'El código de inscripción no es válido.');
                return;
            }

            if ($code->isUsed()) {
                $validator->errors()->add('enrollment_code', 'Este código ya fue utilizado.');
                return;
            }

            if ($code->isExpired()) {
                $validator->errors()->add('enrollment_code', 'Este código ha expirado.');
                return;
            }

            if (!$code->is_active) {
                $validator->errors()->add('enrollment_code', 'Este código está desactivado.');
                return;
            }
        })->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        // Asignar rol de estudiante
        $user->assignRole('student');

        // Obtener el código y marcarlo como usado
        $code = EnrollmentCode::where('code', strtoupper($input['enrollment_code']))->first();
        $code->markAsUsed($user);

        // Crear inscripción automática al curso del código
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $code->course_id,
            'enrollment_code_id' => $code->id,
            'status' => 'active',
        ]);

        return $user;
    }
}
