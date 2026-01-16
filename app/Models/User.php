<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_type',
    ];

    // Tipos de estudiante
    const STUDENT_EXTERNAL = 'external'; // Estudiante que solo compra cursos
    const STUDENT_INTERNAL = 'internal'; // Estudiante de institución con tareas

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Helper methods para tipo de estudiante
    public function isExternalStudent()
    {
        return $this->student_type === self::STUDENT_EXTERNAL;
    }

    public function isInternalStudent()
    {
        return $this->student_type === self::STUDENT_INTERNAL;
    }

    public function canSubmitAssignments()
    {
        return $this->student_type === self::STUDENT_INTERNAL;
    }

    // Relaciones
    public function courses_dictated()
    {
        return $this->hasMany(Course::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessons_completed()
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
