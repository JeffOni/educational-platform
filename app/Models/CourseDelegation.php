<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDelegation extends Model
{
    protected $fillable = [
        'course_id',
        'delegated_by',
        'delegated_to',
        'permissions',
        'starts_at',
        'expires_at',
        'reason',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Permisos disponibles
    const PERMISSION_VIEW_COURSE = 'view_course';
    const PERMISSION_GRADE_ASSIGNMENTS = 'grade_assignments';
    const PERMISSION_ANSWER_QUESTIONS = 'answer_questions';
    const PERMISSION_EDIT_CONTENT = 'edit_content';
    const PERMISSION_VIEW_ANALYTICS = 'view_analytics';

    public static function availablePermissions()
    {
        return [
            self::PERMISSION_VIEW_COURSE => 'Ver contenido del curso',
            self::PERMISSION_GRADE_ASSIGNMENTS => 'Calificar tareas',
            self::PERMISSION_ANSWER_QUESTIONS => 'Responder preguntas de estudiantes',
            self::PERMISSION_EDIT_CONTENT => 'Editar contenido del curso',
            self::PERMISSION_VIEW_ANALYTICS => 'Ver estadísticas y reportes',
        ];
    }

    // Relaciones
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function delegatedBy()
    {
        return $this->belongsTo(User::class, 'delegated_by');
    }

    public function delegatedTo()
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            });
    }

    // Métodos helper
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
