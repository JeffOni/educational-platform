<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'integer',
        'price' => 'decimal:2',
    ];

    const BORRADOR = 1;
    const REVISION = 2;
    const PUBLICADO = 3;

    // Relaciones
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias para la relación teacher
    public function user()
    {
        return $this->teacher();
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function delegations()
    {
        return $this->hasMany(CourseDelegation::class);
    }

    public function activeDelegations()
    {
        return $this->hasMany(CourseDelegation::class)->active();
    }

    // Métodos helper para verificar permisos
    public function userCanEdit(User $user)
    {
        // Admin siempre puede
        if ($user->hasRole('admin')) {
            return true;
        }

        // Profesor titular puede
        if ($this->user_id === $user->id) {
            return true;
        }

        // Verificar si tiene delegación activa con permiso de editar
        $delegation = $this->activeDelegations()
            ->where('delegated_to', $user->id)
            ->first();

        if ($delegation && $delegation->hasPermission(CourseDelegation::PERMISSION_EDIT_CONTENT)) {
            return true;
        }

        return false;
    }

    public function userCanGrade(User $user)
    {
        // Admin siempre puede
        if ($user->hasRole('admin')) {
            return true;
        }

        // Profesor titular puede
        if ($this->user_id === $user->id) {
            return true;
        }

        // Verificar si tiene delegación activa con permiso de calificar
        $delegation = $this->activeDelegations()
            ->where('delegated_to', $user->id)
            ->first();

        if ($delegation && $delegation->hasPermission(CourseDelegation::PERMISSION_GRADE_ASSIGNMENTS)) {
            return true;
        }

        return false;
    }

    public function userHasPermission(User $user, $permission)
    {
        // Admin siempre puede
        if ($user->hasRole('admin')) {
            return true;
        }

        // Profesor titular tiene todos los permisos
        if ($this->user_id === $user->id) {
            return true;
        }

        // Verificar delegación activa con el permiso específico
        $delegation = $this->activeDelegations()
            ->where('delegated_to', $user->id)
            ->first();

        return $delegation && $delegation->hasPermission($permission);
    }
}
